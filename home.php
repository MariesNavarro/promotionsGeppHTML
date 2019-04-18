<?php
session_start();
require_once('backend/lib/dbconfig.php');
if(!isset($_SESSION["userName"]))
{
  header("Location:login.php");
}
else {
  $username = $_SESSION["Nombre"];
  $active   = getpromociones2(1);
  $foractive= getpromociones2(2);
  $past     = getpromociones2(3);
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
    <script src="https://code.jquery.com/jquery-latest.min.js" defer></script>
  </head>
  <body class="login body">
    <div id="popAction" class="displayNone">
      <div>
        <p>¿Estás seguro que quieres realizar esta acción?</p>
        <div class="displayFlex">
          <button class="doAction trans5">Sí</button>
          <button class="trans5" onclick="popActionFun('hide', 0, null)">No</button>
        </div>
      </div>
    </div>
    <ul id="menuMobile" class="displayNone trans5">
      <li>Hola <span class="userName""><?php echo $username; ?></span></li>
      <li><a href="#">Tutoriales</a></li>
      <li><a id="closeLog"  onclick="logout()" role="button">Cerrar Sesión</a></li>
    </ul>
    <nav class="home displayFlex">
      <a href="#" class="logo trans5">
        <div class="displayFlex">
          <img src="ui/img/dragonf-ic.svg" width="25" height="25">
          <img src="ui/img/dragonf-title.svg" width="166.2" height="15">
        </div>
      </a>
      <a role="contentinfo" class="trans5 desktopNav">Hola <span class="userName""><?php echo $username; ?></span></a>
      <a href="#" class="trans5 desktopNav" target="_blank" >Tutoriales</a>
      <a role="button" class="trans5 desktopNav" onclick="logout()" target="_blank" >Cerrar Sesión</a>
      <a role="button" class="mobileNav" onclick="menuMobile('open', this)">
        <div class="hamburgerMenu hamburgerHover displayFlex">
          <span class="trans5"></span>
          <span class="trans5"></span>
          <span class="trans5"></span>
        </div>
      </a>
    </nav>
    <main class="home displayFlex">
      <nav class="displayFlex">
        <ul class="displayFlex">
          <li><a role="button" class="trans5 tabButtons selectTab" onclick="promoTabs('0', this);topFunction();">Activas</a></li>
          <li><a role="button" class="trans5 tabButtons" onclick="promoTabs('-100%', this);topFunction();">Por Activar</a></li>
          <li><a role="button" class="trans5 tabButtons" onclick="promoTabs('-200%', this);topFunction();">Pasadas</a></li>
        </ul>
        <div>
          <a role="button" href="config.php" id="newPromo" class="button trans5">
            <span  class="mobileNav">Nueva Promoción</span>
            <span  class="desktopNav">Nueva Promoción</span>
          </a>
        </div>
      </nav>
      <header class=desktopNav>
        <ul class="displayFlex">
          <li class="displayFlex">
            <span class="desktopNav"><img src="ui/img/ic/list.svg" height="15"></span>
            <p>Nombre</p>
          </li>
          <li class="displayFlex">
            <p>Marca</p>
          </li>
          <li class="displayFlex">
            <p>Vigencia</p>
          </li>
          <li class="displayFlex">
            <p>Acciones</p>
          </li>
        </ul>
      </header>
      <div id="promosW">
        <ul id="promoTabs" class="displayFlex trans5">
          <li id="activePromoWrap">
            <?php echo $active;?>
          </li>
          <li id="forActivationWrap">
            <?php echo $foractive;?>
          </li>
          <li id="pastPromotionsWrap">
            <?php echo $past;?>
          </li>
        </ul>
      </div>
    </main>
    <footer class="login">
      <p>2019 © OETCapital S.A.P.I.de C.V.</p>
    </footer>
    <script src="ui/js/main.js" charset="utf-8" async></script>
    <script type="text/javascript">
      window.onload = function(){
        //putUserName();
      }

      /******** LUEGO MOVERLO PARA EL main.js *********/

      /* actualizar estatus de la promo */
      function actualizarstatus(idpromo,estatus) {
        var param1=12;
        var dataString = 'm=' + param1+ '&id=' + idpromo +'&st=' + estatus;
        $.ajax({
          type    : 'POST',
          url     : 'respuestaconfig.php',
          data    :  dataString,
          success :  function(data) {
            console.log('actualizarstatus Result: '+data);
            location.reload();
          }
        });
      }

      /* eliminar promo */
      function eliminarpromo(idpromo) {
        var param1=13;
        var dataString = 'm=' + param1+ '&id=' + idpromo;
        $.ajax({
          type    : 'POST',
          url     : 'respuestaconfig.php',
          data    :  dataString,
          success :  function(data) {
            console.log('eliminarpromo Result: '+data);
            location.reload();
          }
        });
      }

      function topFunction() {
          $('#promosW').scrollTop(0);
      }

    </script>
  </body>
</html>
