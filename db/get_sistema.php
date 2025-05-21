<?php

//obtenir un sistema concret en format json

$db = new SQLite3("db.sqlite",SQLITE3_OPEN_READONLY);

$id = isset($_GET['id']) ? $db->escapeString($_GET['id']) : 0;

//query
$sql="SELECT * FROM sistemes WHERE id=$id";
$payload=[];
$res=$db->query($sql) or die(print_r($db->errorInfo(), true));
while($row=$res->fetchArray(SQLITE3_ASSOC)){
  $obj=(object)$row;
  $payload[]=$obj;
}
echo json_encode($payload);
?>
