<?php
session_start();
if(isset($_SESSION["userName"]))
{
  header("Location:home.php");
}
?>
<!--
v1
14 de Marzo 2019
[Dragonfly City]
http://dragonflycity.com/
-->
<!DOCTYPE html>
<html lang="es" dir="ltr">
  <head>
    <style>
    </style>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta http-equiv="cache-control" content="no-cache">
    <meta http-equiv="pragma" content="no-cache">
    <meta name="google" content="notranslate">
    <meta name="viewport" content="width=device-width, initial-scale=1.0000, minimum-scale=1.0000, maximum-scale=1.0000, user-scalable=yes">
    <title>Promotions and Redemption Tools | GEPP | Dragonfly City</title>
    <meta name="description" content="Modulo de DragonFly City para la gestión de promociones que permite configurar y monitorear campañas manera centralizada.">
    <meta name="keywords" content="DragonFly City">
    <meta name="author" content="DragonFly City">
    <meta name="robots" content="index">
    <meta property="og:title" content="Promotions and Redemption Tools | GEPP | Dragonfly City">
    <meta property="og:url" content=""> <!-- FALTA -->
    <meta property="og:site_name" content="Promotions and Redemption Tools | GEPP | Dragonfly City">
    <meta property="og:type" content="website">
    <meta property="og:description" content="Modulo de DragonFly City para la gestión de promociones que permite configurar y monitorear campañas manera centralizada.">
    <meta property="og:image" content="ui/img/social_thumbnail_fb.jpg">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="650">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Promotions and Redemption Tools | GEPP | Dragonfly City">
    <meta name="twitter:site" content=""> <!-- FALTA -->
    <meta name="twitter:description" content="Modulo de DragonFly City para la gestión de promociones que permite configurar y monitorear campañas manera centralizada.">
    <meta name="twitter:image" content="ui/img/social_thumbnail_tw.jpg">
    <meta name="twitter:image:width" content="750">
    <meta name="twitter:image:height" content="392">
    <link rel="apple-touch-icon" sizes="180x180" href="ui/img/fav/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="ui/img/fav/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="ui/img/fav/favicon-16x16.png">
    <link rel="manifest" href="ui/img/fav/site.webmanifest">
    <link rel="mask-icon" href="ui/img/fav/safari-pinned-tab.svg" color="#000000">
    <meta name="msapplication-TileColor" content="#2b5797">
    <meta name="theme-color" content="#ffffff">
    <link rel="stylesheet" href="ui/css/master.css">
  </head>
  <body class="login">
    <nav class="login displayFlex">
      <a href="#" class="logo trans5">
        <div class="displayFlex">
          <img src="ui/img/dragonf-ic.svg" width="25" height="25">
          <img src="ui/img/dragonf-title.svg" width="166.2" height="15">
        </div>
      </a>
      <!--<a href="#" class="trans5" target="_blank" >Tutoriales</a>-->
    </nav>
    <main class="displayFlex login">
      <div class="displayFlex">
        <div id="infoHomeW">
          <h1>Promotions and Redemption Tools</h1>
          <h2>Herramienta de gestión de promociones que permite configurar y monitorear de manera centralizada.</h2>
        </div>
      </div>
      <div class="displayFlex">
        <div id="logHomeW">
          <div id="errorLog" class="trans5">
            <p id="errormsg">El nombre de usuario es requerido.</p>
            <!--<p>Si continúas con problemas, ingresa <a class="forgotPass" role="button">aquí.</a></p>-->
          </div>
          <form action="" method="" autocomplete="on">
            <div class="formDiv displayFlex">
              <span class="displayFlex">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 39.8 43.96">
                <path class="ic-user-style" d="M38.67,33.41c-1.19-5.15-4.49-8.44-9.06-10.68-1.06-.54-2.2-.9-3.41-1.38A11.05,11.05,0,0,0,31.06,13a11.06,11.06,0,0,0-3.48-9A11.19,11.19,0,1,0,13.65,21.41l-.93.33A19.66,19.66,0,0,0,6.55,25,14,14,0,0,0,1,34.53,1.55,1.55,0,0,0,1.61,36a26.14,26.14,0,0,0,9.67,5.55c9.58,2.94,18.31,1.21,26.24-4.92a2.78,2.78,0,0,0,1.15-3.23"/>
                </svg>
              </span>
              <input id="userNameLog" name="userName" type="text"  placeholder="Nombre de Usuario" required>
            </div>
            <a id="submitLogin" onclick="recuperar()" role="button" class="button login">Recuperar</a>
            <a class="forgotPass" href="login.php">Ir a Login</a>
          </form>
        </div>
      </div>
    </main>
    <footer class="login">
      <p>2019 © OETCapital S.A.P.I.de C.V.</p>
    </footer>
    <script src="https://code.jquery.com/jquery-latest.min.js" defer></script>
    <script src="ui/js/main.js" charset="utf-8" async></script>
  </body>
</html>
