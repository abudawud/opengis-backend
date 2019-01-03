<?php
/*
* LIBRARY TO HELP WORK
*/

include "config/config.php";

$db = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
$dataAuth = array();
$ms_result = null;

function cmp_string($str1, $str2){
  if(strcmp($str1, $str2) == 0)
    return true;

  return false;
}

function pisset($item){
  if(!isset($_POST[$item]))
      return false;

  return true;
}

function pisset_s($items){
  foreach($items as $item){
      if(!isset($_POST[$item]))
          return false;
  }

  return true;
}

function gisset($item){
  if(!isset($_GET[$item]))
      return false;

  return true;
}

function gisset_s($items){
  foreach($items as $item){
      if(!isset($_GET[$item]))
          return false;
  }

  return true;
}

function fisset($file){
  if(!isset($_FILES[$file]))
      return false;

  return true;
}

function ms_escape($str){
  global $db;

  return mysqli_escape_string($db, $str);
}

function ms_query($sql){
  global $ms_result;
  global $db;

  $ms_result = mysqli_query($db, $sql);

  if(!($ms_result instanceof mysqli_result))
    return $ms_result;

  return(mysqli_num_rows($ms_result));
}

function ms_fetch(){
  global $ms_result;
  return $ms_result->fetch_assoc();
}

function ms_fetch_all(){
  global $ms_result;

  if($ms_result)
    return mysqli_fetch_all($ms_result, MYSQLI_ASSOC);
  else
    return [];
}

function msq_fetch_all($sql){
  ms_query($sql);

  return ms_fetch_all();
}

function msq_fetch($sql){
  ms_query($sql);

  return ms_fetch();
}

?>
