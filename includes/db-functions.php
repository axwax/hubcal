<?php

/* db_auth($database, $user, $password)
 *
 * Connects to mysql database
 *
 * @author Axel Minet axel@gigx.co.uk
 * @param string $database mysql database name
 * @param string $user mysql username
 * @param string $password mysql password
 * @return NULL
 */
function db_auth($database, $user, $password) {
  mysql_connect("localhost", $user, $password) or die ( "Unable to connect to database");
  mysql_set_charset('utf8'); // force utf-8, as some old entries (jan-april 2016) weren't encoded properly, causing json_encode to fail
  @mysql_select_db($database) or die( "Unable to select database");
}

/* db_insert($sTable, $aFields = array())
 *
 * Adds a database row, escaping values and adding appropriate quotes.
 * Keys are assumed to be sane and values will still need to be validated first!
 *
 * @author Axel Minet axel@gigx.co.uk
 * @param string $sTable mysql table name
 * @param array $aFields associative array with "column_name" as key and "cell_value" as value
 * @return array returns array("action" => "add", "ID" => the_new_row_id) on success,and array("error" => mysql_error) on fail
 */

function db_insert($sTable, $aFields = array()) {

  // escape quotes etc
  foreach($aFields as $key=>$value){
    $aFields[$key] = mysql_real_escape_string($value);
  }

  // construct query
  $sQuery = "INSERT INTO `" . $sTable . "` (";
  $sQuery .= "`" . implode("`, `", array_keys($aFields)) . "`";
  $sQuery .= ") VALUES (";
  $sQuery .= "'" . implode("', '", $aFields) . "'";
  $sQuery .= ")";

  $result = mysql_query($sQuery);

  if (!$result) {
    $out = array("error" => mysql_error().": ". $sQuery);
  }
  else {
    $out = array("action" => "insert", "ID" => mysql_insert_id());
  }
  return $out;
}

/* db_update($sTable, $aFields = array())
 *
 * Updates a database row, escaping values and adding appropriate quotes.
 * Keys are assumed to be sane and values will still need to be validated first!
 *
 * @author Axel Minet axel@gigx.co.uk
 * @param string $sTable mysql table name
 * @param array $aFields associative array with "column_name" as key and "new_cell_value" as value
 * @param string $aIDField associative array with "id_field_name" as key and "id_field_value" as value
 * @return array returns array("action" => "edit", "ID" => the_new_row_id) on success,and array("error" => mysql_error) on fail
 */

function db_update($sTable, $aFields = array(), $aIDField) {

  // escape quotes etc
  foreach($aFields as $key=>$value){
    $aFields[$key] = mysql_real_escape_string($value);
  }

  // get and check ID field
  $sIDKey = key($aIDField);
  $sIDValue = current($aIDField);

  //if(empty($sIDKey) || !is_numeric($sIDValue)){  // this failed for non-numeric id values!!!
  if(empty($sIDKey) || empty($sIDValue)){
    return array("error" => "bad id field");
  }

  // construct query

  $sQuery ="UPDATE  `$sTable` SET ";
  foreach ($aFields as $key => $value){
    $sQuery .= "`$key` = '$value', ";
  }
  $sQuery = substr($sQuery, 0, -2); // remove last comma
  $sQuery .= " WHERE `$sIDKey` ='$sIDValue' LIMIT 1;";

  $result = mysql_query($sQuery);

  if (!$result) {
    $out = array("error" => mysql_error().": ". $sQuery);
  }
  else {
    $out = array("action" => "update", "ID" => $sIDValue, "query" => $sQuery);
  }
  return $out;
}

/*
 * Selects one, more or all columns where all conditions in $aWhere are met,
 * ordering by fields/directions defined in $aOrderby.
 *
 * @author Axel Minet axel@gigx.co.uk
 * @param string $sTable Mysql table name.
 * @param string $aColumns 1D array. Each value is a column name for the SELECT part. Use "*" for all.
 * @param array $aWhere Associative array with "column_name" as key and "value_to_be_met" as value
 * @param array $aOrderby Associative array with "column_name" as key and "direction" as value
 * @return array returns array("action" => "select", "result" => array(row_1_array, row_2_array, etc)) on success,and array("error" => mysql_error) on fail
 */

function db_select($sTable, $aColumns=array("*"), $aWhere = array(), $aOrderby = array()) {
  //db_auth();

  // asterisk is a special case that doesn't want backticks
  $sColumn = ($aColumns==array("*") ? "*" : ("`" . implode("`, `", $aColumns) . "`"));

  $sQuery = "SELECT " . $sColumn;
  $sQuery .= " FROM `" . $sTable . "`";

  // where clause
  $sQuery .= " WHERE ";
  if(is_array($aWhere)){
    foreach ($aWhere as $key => $value){
      $sQuery .= "`$key` = '$value' AND";
    }
    $sQuery = substr($sQuery, 0, -4); // remove last ' AND'
  }
  else{
    if(!$aWhere) $aWhere = 1;
    $sQuery .= $aWhere;
  }

  // order clause
  if (count($aOrderby)) {
    $sQuery .= " ORDER BY ";
    foreach ($aOrderby as $key => $value){
      $direction = ((strtoupper($value) == "DESC") ? "DESC" : "ASC");
      $sQuery .= "`$key` $direction";
    }
  }
  //print_r($sQuery);
  $rResult = mysql_query($sQuery);

  $aResult = array();
  while ( $aRow = mysql_fetch_assoc( $rResult ) ) {
    $aResult[] = $aRow;
  }

    if (!$rResult) {
      $out = array("error" => mysql_error().": ". $sQuery);
    }
    else {
      $out = array("action" => "select", "result" => $aResult);
    }
    return $out;
}




