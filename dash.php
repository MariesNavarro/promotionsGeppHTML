<?php
session_start();
require_once('backend/lib/dbconfig.php');

if(!isset($_SESSION["userName"]))
{
  header("Location:login.php");
}
else {
  $username     = $_SESSION["Nombre"];
  $rol          = $_SESSION['Rol'];
  $id_encry     = $_GET['id'];
  $id           = encrypt_decrypt('d',$id_encry);
  $count1       = 0;
  $count2       = 0;
  $promo_info   = PromoValores($id);
  $promo_generica = $promo_info['ind_generico'];
  $promo_generica_cod = $promo_info['codigo_generico'];
  //$consolidado  = dashboard($id,$promo_info);
  $encabezado   = dashboard_head($promo_info);
  $entregados   = dashboard_entregados($id,$count1,$promo_info);
  $disponibles  = dashboard_disponibles($id,$count2,$promo_info);
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
    <script src="https://code.jquery.com/jquery-latest.min.js"></script>
  </head>
  <body class="login body">
    <a href="#" download id="download" hidden></a>
    <div id="popAction" class="displayNone">
      <div>
        <p>¿Estás seguro que quieres realizar esta acción?</p>
        <div class="displayFlex">
          <button class="doAction trans5">Sí</button>
          <button class="trans5" onclick="popActionFun('hide', 0, null)">No</button>
        </div>
      </div>
    </div>
    <div id="popInfo" class="displayNone">
      <div>
        <span style="font-size: 18px;">Para liberar los cupones debes primero seleccionarlos.</span>
        <p></p>
        <div class="displayFlex">
          <button class="trans5" onclick="popInfoFun('hide', 0)">Cerrar</button>
        </div>
      </div>
    </div>
    <ul id="menuMobile" class="displayNone trans5">
      <li>Hola <span id="userNameMobile"><?php echo $username; ?></span></li>
      <!--<li><a href="#">Tutoriales</a></li>-->
      <li><a id="closeLog"  onclick="logout()" role="button">Cerrar Sesión</a></li>
    </ul>
    <nav class="home displayFlex">
      <a href="home.php" class="logo trans5">
        <div class="displayFlex">
          <img src="ui/img/dragonf-ic.svg" width="25" height="25">
          <img src="ui/img/dragonf-title.svg" width="166.2" height="15">
        </div>
      </a>
      <a role="contentinfo" class="trans5 desktopNav">Hola <span id="userNameDesktop"><?php echo $username; ?></span></a>
      <!--<a href="#" class="trans5 desktopNav" target="_blank" >Tutoriales</a>-->
      <a role="button" class="trans5 desktopNav" onclick="logout()" target="_blank" >Cerrar Sesión</a>
      <a role="button" class="mobileNav" onclick="menuMobile('open', this)">
        <div class="hamburgerMenu hamburgerHover displayFlex">
          <span class="trans5"></span>
          <span class="trans5"></span>
          <span class="trans5"></span>
        </div>
      </a>
    </nav>
    <main class="home displayFlex" style="margin-top: 20px;">
      <?php echo $encabezado;?>
      <nav class="displayFlex">
        <ul class="displayFlex">
          <li><a role="button" class="trans5 tabButtons selectTab" onclick="promoTabsrep('0', this);topFunction();" >Consolidado</a></li>
          <li><a role="button" class="trans5 tabButtons" onclick="promoTabsrep('-100%', this);topFunction();" id="tabEntregados">Entregados (<?php echo $count1; ?>)</a></li>
          <!--<li><a role="button" class="trans5 tabButtons" onclick="promoTabsrep('-200%', this);topFunction();" id="tabDisponibles">Disponibles (<?php echo $count2; ?>)</a></li>-->
        </ul>
      </nav>
      <header id='headerreport' class=desktopNav style="display:none">
        <ul class="displayFlex">
          <li class="displayFlex"><span class="desktopNav"><img src="ui/img/ic/list.svg" height="15"></span><p>Fecha</p></li>
          <li class="displayFlex"><p>Cupón</p></li>
          <li class="displayFlex"><p>IP Solicitud</p></li>
          <li class="displayFlex"><p>País</p></li>
          <li class="displayFlex"><p>Estado</p></li>
        </ul>
      </header>
      <div id="promosW">
        <ul id="promoTabs" class="displayFlex trans5">
          <li id="consolidado">
            <?php echo dashboard($id,$promo_info); ?>
          </li>
          <li id="entregados">
            <?php echo $entregados; ?>
          </li>
          <!--
          <li id="disponibles">
            <?php echo $disponibles; ?>
          </li>
          -->
        </ul>
      </div>
      <div class="displayFlex">
        <button class="btnDashboard" onclick="window.location.href='home.php';">Regresar</button>
        <button class="btnDashboard" onclick="actualizaDatos('<?php echo $id_encry; ?>')" style="display:none" id="btnActuaizar">Actualizar</button>
        <a href="export_excel.php?id=<?php echo $id_encry; ?>"><button class="btnDashboard">Descargar</button></a>
        <?php if ($rol=='Admin') { ?><a href="#"  onclick="btnLiberarCupones('<?php echo $id_encry; ?>');"><button class="btnDashboard" id="btnDashboard" style="display:none">Liberar</button></a><?php } ?>
      </div>
    </main>
    <footer class="login">
      <p>2019 © OETCapital S.A.P.I.de C.V. (Ver 1.0)</p>
    </footer>
    <script src="ui/js/main.js" charset="utf-8" async></script>
    <script>
      $(document).ready(function(){
        /* Solo si esta cativo, mostrar el boton de Actualizar */
        if ($("#disclaimer").attr("id_estatus")==1) { $("#btnActuaizar").css("display","block");  }
        if ($("#disclaimer").attr("id_estatus")!=1) { $("#btnDashboard").css("display","block");  }

        $('#disponibles').on('click', '#todos', function () {
            if (this.checked) { $('.cuponcheck').each(function () { this.checked = true; }); }
            else {  $('.cuponcheck').each(function () {this.checked = false; }); }
        });

        $('#disponibles').on('click', '#primeros', function () {
            var i=0;
            var max=$('#numerocupones').val();
            $('.cuponcheck').each(function () {this.checked = false; });
            if (this.checked) {
              $('.cuponcheck').each(function () {
                this.checked = true; i++;  return (i<max);
              });
            }
            else {
              $('.cuponcheck').each(function () {
                this.checked = false; i++; return (i<max);
              });
            }
        });
      });

      function btnLiberarCupones(id) {
        var cont = contarCuponesSeleccionados();
        console.log('btnLiberarCupones: '+cont);
        if (cont > 0) {
          var cupones=obtenerCuponesSeleccionados();
          popActionFun('show', '¿Estás seguro que quieres LIBERAR los cupones seleccionados ('+cont+')? <br> Estos serán descargados y eliminados de la promoción.','liberarCuponesSeleccionados("'+id+'","'+cupones+'")');
        } else {
          popInfoFun('show', 'En la pestaña de disponibles, debes indicar la cantidad de cupones y darle clic al check de Seleccionar');
        }
      }

      function contarCuponesSeleccionados() {
        var i=0;
        $('.cuponcheck').each(function () {
          if (this.checked == true) i++;
        });
        return i;
      }

      function obtenerCuponesSeleccionados() {
        var cupones="";
        $('.cuponcheck').each(function () {
          if (this.checked == true) cupones = cupones + this.value+",";
        });
        if (cupones.length>0){ cupones = cupones.substring(0, cupones.length-1) ;}
        return cupones;
      }

      function liberarCuponesSeleccionados(id,cupones) {
        console.log('id:'+id+' cupones: '+cupones);
        limpiarcupones(id,cupones)
      }

    </script>
  </body>
</html>
