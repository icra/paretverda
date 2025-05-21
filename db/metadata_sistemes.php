<?php

//llista dels noms i ids dels sistemes
$db = new SQLite3("db.sqlite",SQLITE3_OPEN_READONLY);

//query
$sql="
  SELECT
    id,
    json_extract(json,'$.nom') AS nom,
    json_extract(json,'$.lloc') AS lloc,
    json_extract(json,'$.descripcio') AS descripcio
  FROM sistemes
";

$payload=[];
$res=$db->query($sql) or die(print_r($db->errorInfo(), true));
while($row=$res->fetchArray(SQLITE3_ASSOC)){
  $obj=(object)$row;
  $payload[]=$obj;
}
echo json_encode($payload);
?>
