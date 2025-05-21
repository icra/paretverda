<?php

//delete un sistema

session_start();
if(!isset($_SESSION["auth"])){
  die("no autoritzat");
}

$db = new SQLite3("db.sqlite");

//input
$id = isset($_GET['id']) ? $db->escapeString($_GET['id']) : 0;

//query
$sql="DELETE FROM sistemes WHERE id=$id";
$db->exec($sql) or die(print_r($db->lastErrorMsg(), true));

//final
echo "OK"; //el client comprovarà si responseText=='OK'
?>
