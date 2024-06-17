<?php
//sobreescriu base de dades

session_start();

if(!isset($_SESSION["auth"])){
  die("no autoritzat");
}

$db = new SQLite3("db.sqlite");

//camps entrada
$id   = isset($_POST['id'])   ? $db->escapeString($_POST['id'])   : 0;
$json = isset($_POST['json']) ? $db->escapeString($_POST['json']) : false;

if(!$json){
  die("json no definit");
}

$sql="
  UPDATE sistemes
  SET   json = '$json'
  WHERE id   = $id;
";
$db->exec($sql) or die(print_r($db->lastErrorMsg(), true));

//final tot OK
echo "OK"; //el client comprovarà si responseText=='OK'
?>
