<?php

//UPSERT: update i insert un sol sistema

session_start();
if(!isset($_SESSION["auth"])){
  die("no autoritzat");
}

$db = new SQLite3("db.sqlite");

//inputs: sistema id i json
$id   = isset($_POST['id'])   ? $db->escapeString($_POST['id'])   : 0;
$json = isset($_POST['json']) ? $db->escapeString($_POST['json']) : false;

//check inputs
if(!$json){
  die("json no definit");
}

//check si el sistema existeix
$existeix = $db->querySingle("SELECT COUNT(id) FROM sistemes WHERE id=$id");

//upsert
if($existeix){
  //update
  $sql="
    UPDATE sistemes
    SET   json = '$json'
    WHERE id   = $id;
  ";
}else{
  //insert
  $sql="INSERT INTO sistemes (id,json) VALUES ($id,'$json')";
}

$db->exec($sql) or die(print_r($db->lastErrorMsg(), true));

//final tot OK
echo "OK"; //el client comprovarà si responseText=='OK'
?>
