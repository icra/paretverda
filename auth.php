<?php
  session_start();

  if($_SERVER['SERVER_NAME']!="localhost"){
    //aquesta part necessita fitxer .htaccess següent:
    /*
      RewriteEngine on
      RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
    */
    list($_SERVER['PHP_AUTH_USER'],$_SERVER['PHP_AUTH_PW']) =
      explode(':',base64_decode(substr($_SERVER['HTTP_AUTHORIZATION'],6)));
  }

  /*autentificació*/
  $valid_passwords=array(
    "admin"=>"icra123",
  );
  $valid_users = array_keys($valid_passwords);

  $user = isset($_SERVER['PHP_AUTH_USER']) ? $_SERVER['PHP_AUTH_USER'] : false;
  $pass = isset($_SERVER['PHP_AUTH_PW'])   ? $_SERVER['PHP_AUTH_PW']   : false;

  $validated = (in_array($user, $valid_users)) && ($pass==$valid_passwords[$user]);

  if(!$validated){
    /*
    */
    header('WWW-Authenticate: Basic realm="paretverda.icradev.cat"');
    header('HTTP/1.0 401 Unauthorized');
    die("error: usuari i/o password incorrectes");
  }
  //si arriba aquí és usuari vàlid, pots carregar la pàgina

  //fes servir SESSION
  $_SESSION["auth"]=true;
?>
