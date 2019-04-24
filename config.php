<?php
session_start();
require_once('backend/lib/dbconfig.php');
if(!isset($_SESSION["userName"]))
{
  header("Location:login.php");
}
else {
  $username=$_SESSION["Nombre"];
  $marcas= marcas();
  $proveedores=proveedores();
  $funcionalidades=funcionalidades();
  $plantillas=plantillas(null);

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
  <body>
    <div id="loadingConf" style="display:none">
      <img src="ui/img/dragonfly.gif" width="80">
      <p>Cargando...</p>
    </div>
    <div id="popAction" class="displayNone">
      <div>
        <p>¿Estás seguro que quieres realizar esta acción?</p>
        <div class="displayFlex">
          <button class="doAction trans5">Sí</button>
          <button class="trans5" onclick="popActionFun('hide', 0, null)">No</button>
        </div>
      </div>
    </div>
    <div id="extMenu" class="trans5 displayFlex">
      <a class="closeMenu" role="button" onclick="menuConfig('close')">
        <div class="closeConfig">
          <span class="trans5"></span>
          <span class="trans5"></span>
        </div>
      </a>
      <ul class="displayFlex">
        <li><a role="button" class="trans5">Hola <span class="userName"><?php echo $username; ?></span></a></li>
        <li><a role="button" class="trans5">Promociones</a></li>
        <li><a role="button" class="trans5">Tutoriales</a></li>
        <li><a role="button" onclick="logout()" class="trans5">Cerrar Sesión</a></li>
      </ul>
      <p>2019 © OETCapital S.A.P.I.de C.V.</p>
    </div>
    <main class="config">
      <nav id="menuConfigNav" class="config trans5">
        <div id="menuConfig" class="displayFlex">
          <a href="home.php" class="logo trans5">
            <div class="displayFlex">
              <img src="ui/img/dragonf-ic.svg" width="25" height="25">
              <img src="ui/img/dragonf-title.svg" width="166.2" height="15">
            </div>
          </a>
          <div id="indexConfig">
            <ul class="displayFlex">
              <li class="displayFlex">
                <span>
                  <svg class="bulletStep" viewBox="0 0 30 30" style="enable-background:new 0 0 30 30;" xml:space="preserve">
                    <path fill="#FFFFFF" d="M15,3c6.6,0,12,5.4,12,12s-5.4,12-12,12S3,21.6,3,15S8.4,3,15,3 M15,1C7.3,1,1,7.3,1,15s6.3,14,14,14 s14-6.3,14-14S22.7,1,15,1L15,1z"/>
                    <circle class="stepInProgress trans5" fill="#FFFFFF" cx="15" cy="15"/>
                  </svg>
                </span>
                <div>
                  <h3>1. Datos Básicos</h3>
                  <a href="#" target="_blank">Ver Tutorial</a>
                </div>
              </li>
              <li class="displayFlex">
                <span>
                  <svg class="bulletStep" viewBox="0 0 30 30" style="enable-background:new 0 0 30 30;" xml:space="preserve">
                    <path fill="#FFFFFF" d="M15,3c6.6,0,12,5.4,12,12s-5.4,12-12,12S3,21.6,3,15S8.4,3,15,3 M15,1C7.3,1,1,7.3,1,15s6.3,14,14,14 s14-6.3,14-14S22.7,1,15,1L15,1z"/>
                    <circle class="stepUncomplete trans5" fill="#FFFFFF" cx="15" cy="15"/>
                  </svg>
                </span>
                <div>
                  <h3>2. Funcionalidad</h3>
                  <a href="#" target="_blank">Ver Tutorial</a>
                </div>
              </li>
              <li class="displayFlex">
                <span>
                  <svg class="bulletStep" viewBox="0 0 30 30" style="enable-background:new 0 0 30 30;" xml:space="preserve">
                    <path fill="#FFFFFF" d="M15,3c6.6,0,12,5.4,12,12s-5.4,12-12,12S3,21.6,3,15S8.4,3,15,3 M15,1C7.3,1,1,7.3,1,15s6.3,14,14,14 s14-6.3,14-14S22.7,1,15,1L15,1z"/>
                    <circle class="stepUncomplete trans5" fill="#FFFFFF" cx="15" cy="15"/>
                  </svg>
                </span>
                <div>
                  <h3>3. Conf. Funcionalidad</h3>
                  <a href="#" target="_blank">Ver Tutorial</a>
                </div>
              </li>
              <li class="displayFlex">
                <span>
                  <svg class="bulletStep" viewBox="0 0 30 30" style="enable-background:new 0 0 30 30;" xml:space="preserve">
                    <path fill="#FFFFFF" d="M15,3c6.6,0,12,5.4,12,12s-5.4,12-12,12S3,21.6,3,15S8.4,3,15,3 M15,1C7.3,1,1,7.3,1,15s6.3,14,14,14 s14-6.3,14-14S22.7,1,15,1L15,1z"/>
                    <circle class="stepUncomplete trans5" fill="#FFFFFF" cx="15" cy="15"/>
                  </svg>
                </span>
                <div>
                  <h3>4.Plantilla</h3>
                  <a href="#">Ver Tutorial</a>
                </div>
              </li>
              <li class="displayFlex">
                <span>
                  <svg class="bulletStep" viewBox="0 0 30 30" style="enable-background:new 0 0 30 30;" xml:space="preserve">
                    <path fill="#FFFFFF" d="M15,3c6.6,0,12,5.4,12,12s-5.4,12-12,12S3,21.6,3,15S8.4,3,15,3 M15,1C7.3,1,1,7.3,1,15s6.3,14,14,14 s14-6.3,14-14S22.7,1,15,1L15,1z"/>
                    <circle class="stepUncomplete trans5" fill="#FFFFFF" cx="15" cy="15"/>
                  </svg>
                </span>
                <div>
                  <h3>5. Edición de Plantilla</h3>
                  <a href="#">Ver Tutorial</a>
                </div>
              </li>
            </ul>
          </div>
          <ul class="displayFlex">
            <li id="dragonConfig"> <img src="ui/img/dragonf-ic.svg" width="30"> </li>
            <li>
              <a role="button" onclick="menuConfig('open')">
                <div class="hamburgerMenu hamburgerHover displayFlex">
                  <span class="trans5"></span>
                  <span class="trans5"></span>
                  <span class="trans5"></span>
                </div>
              </a>
            </li>
            <li>
              <a id="toggleMenuArrow" role="button" onclick="hideMenuConfig('hide', this)">
              <img class="trans5" src="ui/img/ic/menu-arrow.svg" width="30">
              </a>
            </li>
          </ul>
        </div>
        <div id="headerMob" class="displayFlex">
          <h1>Datos Básicos</h1>
          <a href="#">Ver Tutorial</a>
        </div>
        <footer id="configFoot" class="login"> <p>2019 © OETCapital S.A.P.I.de C.V.</p> </footer>
      </nav>
      <div id="configWrap">
        <ul id="sliderConfig" class="displayFlex trans5">
          <li>
            <!-- 1 Configurador Datos Básicos -->
            <div class="errorWConfig trans5">
              <p>Por favor llena los campos en rojo.</p>
            </div>
            <header>
              <h2>Configurador de Promoción</h2>
              <h3>Datos Básicos</h3>
            </header>
            <form action="" method="" autocomplete="off">
              <div class="rowConfig displayFlex">
                <div class="fieldConfigWrap">
                  <label class="labelData1">Nombre de la Promoción</label>
                  <input id="nombrePromo" class="textInput inputData1" type="text" required/>
                </div>
                <div class="fieldConfigWrap">
                  <label class="labelData1">Descripción de la promoción</label>
                  <textarea id="descripcionPromo" class="textInput inputData1" name=""></textarea>
                </div>
              </div>

              <div class="rowConfig displayFlex">
                <div class="fieldConfigWrap">
                  <label class="labelData1">Marca</label>
                  <select id="selectBrand" name="" class="inputData1">
                    <?php echo $marcas; ?>
                  </select>
                </div>
                <div class="fieldConfigWrap">
                  <label class="labelData1">Proveedor</label>
                  <select id="selectProvider" name="" class="inputData1">
                  <?php echo $proveedores; ?>
                  </select>
                </div>
              </div>

              <div class="rowConfig displayFlex">
                <div class="fieldConfigWrap">
                  <label class="labelData1">Fecha de Inicio</label>
                  <input id="fechaInicio" class="textInput dateConfig inputData1" type="date" min="2018-12-31" required>
                </div>
                <div class="fieldConfigWrap">
                  <label class="labelData1">Fecha de Final</label>
                  <input id="fechaFin" class="textInput dateConfig inputData1" type="date" min="2018-12-31" required>
                </div>
              </div>

              <div class="rowConfig displayFlex">
                <div class="fieldConfigWrap">
                  <label class="labelData1">Nombre de la URL</label>
                  <input id="nombreURL" class="textInput inputData1" type="text" required/>
                </div>
                <div class="fieldConfigWrap">
                  <label>Cargar Legales</label>
                  <label class="legalesConfigButton trans5 displayFlex" for="legalesUpload" onclick="loadFileName(this)">
                  <img src="ui/img/ic/upload.svg" width="20" height="20">
                  <p id="legalFileTx">Subir PDF</p>
                  </label>
                  <input id="legalesUpload" class="textInput legalesConfig" type="file" name="">
                </div>
              </div>

              <button class="buttonConfig centerButton" type="button" name="button" onclick="checkSteps(1, this);">Siguiente</button>
            </form>
          </li>
          <li>
            <!-- 2 Configurador Funcionalidad -->
            <div class="errorWConfig trans5">
              <p>Tienes que seleccionar una opción.</p>
            </div>
            <header>
              <h2>Configurador de Promoción</h2>
              <h3>Selección de Funcionalidad</h3>
            </header>
            <form action="" method="" autocomplete="off">
              <?php echo $funcionalidades ?>

              <button class="buttonConfig leftButton" type="button" name="button" onclick="sliderConfigFun(0)">Anterior</button>
              <button class="buttonConfig rightButton" type="button" name="button" onclick="checkSteps(2, this)">Siguiente</button>
            </form>
          </li>
          <li>
            <!-- 3 Configurador Conf Funcionalidad -->
            <div class="errorWConfig trans5">
              <p>Hubo un error para cargar tu archivo.</p>
            </div>
            <header>
              <h2>Configurador de Promoción</h2>
              <h3>Configuración de Funcionalidad</h3>
            </header>
            <form id="formCSV" action="" method="" autocomplete="off">
              <div class="rowCenterConfig displayFlex">
                <label for="couponsUpload" class="displayFlex" onclick="loadFileCSV(this)">
                  <img id="imgCheck" class="trans5" src="ui/img/ic/upload.svg" width="40" height="40">
                  <p>Subir</p>
                </label>
                <input id="couponsUpload" type="file" value=" ">
                <div class="infoFileCoupon displayFlex">
                  <p id="msgCsvUpload">Escoge un archivo .csv</p>
                  <a role="button" class="displayNone trans5" title="Limpiar archivo" onclick="cleanCsv(this)">
                    <img src="ui/img/ic/menu-close.svg" width="14" height="14">
                  </a>
                </div>
                <p class="noneNumCSV infoLoadCSV trans5">Aún no has cargado un archivo.</p>
                <p class="numCSV infoLoadCSV trans5">Tienes <span id="numCSV">200</span> códigos para esta promoción.</p>
              </div>
              <button id="loadCSVGet" class="buttonConfig centerButton" type="button" name="button" onclick="getNumCSV()">Cargar</button>
              <button class="buttonConfig leftButton" type="button" name="button" onclick="sliderConfigFun(1)">Anterior</button>
              <button class="buttonConfig rightButton" type="button" name="button" onclick="checkSteps(3, this)">Siguiente</button>
            </form>
          </li>
          <li>
            <!-- 4 Configurador Plantilla -->
            <div class="errorWConfig trans5">
              <p>Tienes que seleccionar una opción.</p>
            </div>
            <header>
              <h2>Configurador de Promoción</h2>
              <h3>Selección de Plantilla</h3>
            </header>
            <form action="" method="" id="plantillas" autocomplete="off">
              <?php echo $plantillas ?>
            </form>
          </li>
          <li id="editorPlantilla">
            <!-- Configurador Edición de Plantilla -->
            <div id="wrapEditorPlantilla">
              <div id="contEditorPlantilla">
                <iframe id="iframePlantilla" src="index.php?cf=1"></iframe>
              </div>
              <!-- index Edicion Plantilla -->
              <div id="editorPlantillaInterfaz">
                <iframe id="iframeInterfaz" src="interfaz-uno.php"></iframe>
                <a role="button" id="hideInterfaz" title="Ocultar Interfaz" onclick="hideInterfaz('hide', this)">
                  <img class="trans5" src="ui/img/ic/show_interfaz.svg" width="40" height="40">
                  <img class="trans5" src="ui/img/ic/hide_interfaz.svg" width="40" height="40">
                </a>
              </div>
            </div>
            <!-- index Edicion Plantilla -->
            <div id="indexEditorPlantilla" class="displayFlex">
              <a role="button" onclick="sliderMobScreens('back')"><img class="cw180" src="ui/img/ic/menu-arrow-blue.svg" height="20"></a>
              <div class="mobileIndexEditor">
                <p><b id="pagScreen">Carga</b></p>
                <span><b id="indexPagScreen">1</b>/5</span>
              </div>
              <a role="button" onclick="sliderMobScreens('next')"><img src="ui/img/ic/menu-arrow-blue.svg" height="20"></a>
              <ul>
                <li><a role="button" onclick="changeScreen(0)" class="indexScreenDesk screenDeskSelect">1.Carga</a></li>
                <li><a role="button" onclick="changeScreen(1)" class="indexScreenDesk screenDeskUnselect">2.Inicio</a></li>
                <li><a role="button" onclick="changeScreen(2)" class="indexScreenDesk screenDeskUnselect">3.Cupón</a></li>
                <li><a role="button" onclick="changeScreen(3)" class="indexScreenDesk screenDeskUnselect">4.Mensaje Éxito</a></li>
                <li><a role="button" onclick="changeScreen(4)" class="indexScreenDesk screenDeskUnselect">5.Mensaje Error</a></li>
              </ul>
            </div>
            <!-- More options -->
            <div id="optionsEditorPlantilla" class="trans5">

              <ul id="optionsCargando" class="optionStep displayNone">
                <li class="displayFlex">
                  <p><b>Color de fondo:</b></p>

                  <form class="optionsColor displayFlex">
                    <label for="backBlackConf" style="background:#000"></label>
                    <input id="backBlackConf" onclick="changecolorback(this);" type="checkbox" value=".negroBack" class="hideInput">

                    <label for="backWhiteConf" style="background:#FFF"></label>
                    <input id="backWhiteConf" onclick="changecolorback(this);" type="checkbox" value=".blancoBack" class="hideInput">
                  </form>

                  <form class="optionsColor displayNone">
                    <label for="backAzulUnoPepsiConf" style="background:#15355b"></label>
                    <input id="backAzulUnoPepsiConf" onclick="changecolorback(this);" type="checkbox" value=".azulUnoPepsiBack" class="hideInput">

                    <label for="backAzulDosPepsiConf" style="background:#005cb9"></label>
                    <input id="backAzulDosPepsiConf" onclick="changecolorback(this);" type="checkbox" value=".azulDosPepsiBack" class="hideInput">

                    <label for="backRojoPepsiConf" style="background:#e71d2f"></label>
                    <input id="backRojoPepsiConf" onclick="changecolorback(this);" type="checkbox" value=".rojoPepsiBack" class="hideInput">
                  </form>

                  <form class="optionsColor displayNone">
                    <label for="backNaranjaUnoGatoradeConf" style="background:#FF671F"></label>
                    <input id="backNaranjaUnoGatoradeConf" onclick="changecolorback(this);" type="checkbox" value=".naranjaUnoGatoradeBack" class="hideInput">

                    <label for="backNaranjaDosGatoradeConf" style="background:#F9A350"></label>
                    <input id="backNaranjaDosGatoradeConf" onclick="changecolorback(this);" type="checkbox" value=".naranjaDosGatoradeBack" class="hideInput">

                    <label for="backRojoGatoradeConf" style="background:#EF3E42"></label>
                    <input id="backRojoGatoradeConf" onclick="changecolorback(this);" type="checkbox" value=".rojoGatoradeBack" class="hideInput">

                    <label for="backVerdeGatoradeConf" style="background:#046A38"></label>
                    <input id="backVerdeGatoradeConf" onclick="changecolorback(this);" type="checkbox" value=".verdeGatoradeBack" class="hideInput">

                    <label for="backGrisGatoradeConf" style="background:#B7B9BB"></label>
                    <input id="backGrisGatoradeConf" onclick="changecolorback(this);" type="checkbox" value=".grisGatoradeBack" class="hideInput">
                  </form>

                  <form class="optionsColor displayNone">
                    <label for="backVerdeUnoSevenConf" style="background:#225D38"></label>
                    <input id="backVerdeUnoSevenConf" onclick="changecolorback(this);" type="checkbox" value=".verdeSevenUpUnoBack" class="hideInput">

                    <label for="backVerdeDosSevenConf" style="background:#00AB51"></label>
                    <input id="backVerdeDosSevenConf" onclick="changecolorback(this);" type="checkbox" value=".verdeSevenUpDosBack" class="hideInput">

                    <label for="backVerdeTresSevenConf" style="background:#00AB51"></label>
                    <input id="backVerdeTresSevenConf" onclick="changecolorback(this);" type="checkbox" value=".verdeSevenUpTresBack" class="hideInput">

                    <label for="backRojoSevenConf" style="background:#EF3E42"></label>
                    <input id="backRojoSevenConf" onclick="changecolorback(this);" type="checkbox" value=".rojoSevenUpBack" class="hideInput">

                    <label for="backAmarilloSevenConf" style="background:#FFDD00"></label>
                    <input id="backAmarilloSevenConf" onclick="changecolorback(this);" type="checkbox" value=".amarilloSevenUpBack" class="hideInput">
                  </form>

                  <form class="optionsColor displayNone">
                    <label for="backVioletaUnoEpuraConf" style="background:#1B1F6C"></label>
                    <input id="backVioletaUnoEpuraConf" onclick="changecolorback(this);" type="checkbox" value=".violetaUnoEpuraBack" class="hideInput">

                    <label for="backVioletaDosEpuraConf" style="background:#472F8B"></label>
                    <input id="backVioletaDosEpuraConf" onclick="changecolorback(this);" type="checkbox" value=".violetaDosEpuraBack" class="hideInput">

                    <label for="backCyanEpuraConf" style="background:#27B2E3"></label>
                    <input id="backCyanEpuraConf" onclick="changecolorback(this);" type="checkbox" value=".cyanEpuraBack" class="hideInput">

                    <label for="backAmarilloEpuraConf" style="background:#FAEC3C"></label>
                    <input id="backAmarilloEpuraConf" onclick="changecolorback(this);" type="checkbox" value=".yellowEpuraBack" class="hideInput">

                    <label for="backRosaEpuraConf" style="background:#E4238C"></label>
                    <input id="backRosaEpuraConf" onclick="changecolorback(this);" type="checkbox" value=".pinkEpuraBack" class="hideInput">
                  </form>

                  <form class="optionsColor displayNone">
                    <label for="backAmarilloFrutzzoConf" style="background:#FDCE07"></label>
                    <input id="backAmarilloFrutzzoConf" onclick="changecolorback(this);" type="checkbox" value=".amarilloFrutzzoBack" class="hideInput">

                    <label for="backAzulUnoFrutzzoConf" style="background:#18234C"></label>
                    <input id="backAzulUnoFrutzzoConf" onclick="changecolorback(this);" type="checkbox" value=".azulUnoFrutzzoBack" class="hideInput">

                    <label for="backAzulDosFrutzzoConf" style="background:#2BA8E0"></label>
                    <input id="backAzulDosFrutzzoConf" onclick="changecolorback(this);" type="checkbox" value=".azulDosFrutzzoBack" class="hideInput">
                  </form>
                </li>
              </ul>

              <ul id="optionsInicio" class="optionStep displayNone">
                <li class="displayFlex">
                  <p><b>Color Texto:</b></p>
                  <form class="optionsColor displayFlex">
                    <label for="colorBlackConf" style="background:#000"></label>
                    <input id="colorBlackConf" onclick="changecolortext(this);" type="checkbox" value=".negroTx" class="hideInput">

                    <label for="colorWhiteConf" style="background:#FFF"></label>
                    <input id="colorWhiteConf" onclick="changecolortext(this);" type="checkbox" value=".blancoTx" class="hideInput">
                  </form>

                  <form class="optionsColor displayNone">
                    <label for="colorAzulUnoPepsiConf" style="background:#15355b"></label>
                    <input id="colorAzulUnoPepsiConf" onclick="changecolortext(this);" type="checkbox" value=".azulUnoPepsiTx" class="hideInput">

                    <label for="colorAzulDosPepsiConf" style="background:#005cb9"></label>
                    <input id="colorAzulDosPepsiConf" onclick="changecolortext(this);" type="checkbox" value=".azulDosPepsiTx" class="hideInput">

                    <label for="colorRojoPepsiConf" style="background:#e71d2f"></label>
                    <input id="colorRojoPepsiConf" onclick="changecolortext(this);" type="checkbox" value=".rojoPepsiTx" class="hideInput">
                  </form>

                  <form class="optionsColor displayNone">
                    <label for="colorNaranjaUnoGatoradeConf" style="background:#FF671F"></label>
                    <input id="colorNaranjaUnoGatoradeConf" onclick="changecolortext(this);" type="checkbox" value=".naranjaUnoGatoradeTx" class="hideInput">

                    <label for="colorNaranjaDosGatoradeConf" style="background:#F9A350"></label>
                    <input id="colorNaranjaDosGatoradeConf" onclick="changecolortext(this);" type="checkbox" value=".naranjaDosGatoradeTx" class="hideInput">

                    <label for="colorRojoGatoradeConf" style="background:#EF3E42"></label>
                    <input id="colorRojoGatoradeConf" onclick="changecolortext(this);" type="checkbox" value=".rojoGatoradeTx" class="hideInput">

                    <label for="colorVerdeGatoradeConf" style="background:#046A38"></label>
                    <input id="colorVerdeGatoradeConf" onclick="changecolortext(this);" type="checkbox" value=".verdeGatoradeTx" class="hideInput">

                    <label for="colorGrisGatoradeConf" style="background:#B7B9BB"></label>
                    <input id="colorGrisGatoradeConf" onclick="changecolortext(this);" type="checkbox" value=".grisGatoradeTx" class="hideInput">
                  </form>

                  <form class="optionsColor displayNone">
                    <label for="colorVerdeUnoSevenConf" style="background:#225D38"></label>
                    <input id="colorVerdeUnoSevenConf" onclick="changecolortext(this);" type="checkbox" value=".verdeSevenUpUnoTx" class="hideInput">

                    <label for="colorVerdeDosSevenConf" style="background:#00AB51"></label>
                    <input id="colorVerdeDosSevenConf" onclick="changecolortext(this);" type="checkbox" value=".verdeSevenUpDosTx" class="hideInput">

                    <label for="colorVerdeTresSevenConf" style="background:#00AB51"></label>
                    <input id="colorVerdeTresSevenConf" onclick="changecolortext(this);" type="checkbox" value=".verdeSevenUpTresTx" class="hideInput">

                    <label for="colorRojoSevenConf" style="background:#EF3E42"></label>
                    <input id="colorRojoSevenConf" onclick="changecolortext(this);" type="checkbox" value=".rojoSevenUpTx" class="hideInput">

                    <label for="colorAmarilloSevenConf" style="background:#FFDD00"></label>
                    <input id="colorAmarilloSevenConf" onclick="changecolortext(this);" type="checkbox" value=".amarilloSevenUpTx" class="hideInput">
                  </form>

                  <form class="optionsColor displayNone">
                    <label for="colorVioletaUnoEpuraConf" style="background:#1B1F6C"></label>
                    <input id="colorVioletaUnoEpuraConf" onclick="changecolortext(this);" type="checkbox" value=".violetaUnoEpuraTx" class="hideInput">

                    <label for="colorVioletaDosEpuraConf" style="background:#472F8B"></label>
                    <input id="colorVioletaDosEpuraConf" onclick="changecolortext(this);" type="checkbox" value=".violetaDosEpuraTx" class="hideInput">

                    <label for="colorCyanEpuraConf" style="background:#27B2E3"></label>
                    <input id="colorCyanEpuraConf" onclick="changecolortext(this);" type="checkbox" value=".cyanEpuraTx" class="hideInput">

                    <label for="colorAmarilloEpuraConf" style="background:#FAEC3C"></label>
                    <input id="colorAmarilloEpuraConf" onclick="changecolortext(this);" type="checkbox" value=".yellowEpuraTx" class="hideInput">

                    <label for="colorRosaEpuraConf" style="background:#E4238C"></label>
                    <input id="colorRosaEpuraConf" onclick="changecolortext(this);" type="checkbox" value=".pinkEpuraTx" class="hideInput">
                  </form>

                  <form class="optionsColor displayNone">
                    <label for="colorAmarilloFrutzzoConf" style="background:#FDCE07"></label>
                    <input id="colorAmarilloFrutzzoConf" onclick="changecolortext(this);" type="checkbox" value=".amarilloFrutzzoTx" class="hideInput">

                    <label for="colorAzulUnoFrutzzoConf" style="background:#18234C"></label>
                    <input id="colorAzulUnoFrutzzoConf" onclick="changecolortext(this);" type="checkbox" value=".azulUnoFrutzzoTx" class="hideInput">

                    <label for="colorAzulDosFrutzzoConf" style="background:#2BA8E0"></label>
                    <input id="colorAzulDosFrutzzoConf" onclick="changecolortext(this);" type="checkbox" value=".azulDosFrutzzoTx" class="hideInput">
                  </form>
                </li>
                <li class="displayFlex">
                  <p><b>Fuente Texto:</b></p>
                  <form class="displayFlex">
                    <label for="fontArialConf">
                      <p style="font-family:'Arial', sans-serif">Arial</p>
                    </label>
                    <input id="fontArialConf" onclick="changefont(this)" type="checkbox" value=".fontArial" class="hideInput">


                    <label for="fontArialBlackConf">
                      <p style="font-family:'Arial Black', sans-serif">A. Black</p>
                    </label>
                    <input id="fontArialBlackConf" onclick="changefont(this)" type="checkbox" value=".fontArialBlack" class="hideInput">

                    <label for="fontHelveticaConf">
                      <p style="font-family:'Helvetica', sans-serif">Helvetica</p>
                    </label>
                    <input id="fontHelveticaConf" onclick="changefont(this)" type="checkbox" value=".fontHelvetica" class="hideInput">

                    <label for="fontCourierConf">
                      <p style="font-family:'Courier', serif">Courier</p>
                    </label>
                    <input id="fontCourierConf" onclick="changefont(this)" type="checkbox" value=".fontCourier" class="hideInput">

                    <label for="fontVerdanaConf">
                      <p style="font-family:'Verdana', sans-serif">Verdana</p>
                    </label>
                    <input id="fontVerdanaConf" onclick="changefont(this)" type="checkbox" value=".fontVerdana" class="hideInput">

                    <label for="fontGeorgiaConf">
                      <p style="font-family:'Georgia', serif">Georgia</p>
                    </label>
                    <input id="fontGeorgiaConf" onclick="changefont(this)" type="checkbox" value=".fontGeorgia" class="hideInput">

                    <label for="fontGaramondConf">
                      <p style="font-family:'Garamond', serif">Garamond</p>
                    </label>
                    <input id="fontGaramondConf" onclick="changefont(this)" type="checkbox" value=".fontGaramond" class="hideInput">

                    <label for="fontTrebuchetConf">
                      <p style="font-family:'Trebuchet', serif">Trebuchet</p>
                    </label>
                    <input id="fontTrebuchetConf" onclick="changefont(this)" type="checkbox" value=".fontTrebuchet" class="hideInput">

                    <label for="fontImpactConf">
                      <p style="font-family:'Impact', sans-serif">Impact</p>
                    </label>
                    <input id="fontImpactConf" onclick="changefont(this)" type="checkbox" value=".fontImpact" class="hideInput">
                  </form>
                </li>
                <li class="displayFlex">
                  <p><b>Texto Footer:</b></p>
                  <input id="textFootConf" onkeyup="changetxt(this)" class="textConf" type="text">
                </li>
              </ul>
            </div>
            <form action="" method="" autocomplete="off">
              <button class="buttonConfig leftButton" type="button" name="button" onclick="sliderConfigFun(3)">Anterior</button>
              <button class="buttonConfig centerButton" type="button" name="button" onclick="popActionFun('show', '¿Estás seguro que quieres cancelar?','cancelarpromo()')">Cancelar</button>
              <button class="buttonConfig rightButton" type="button" name="button" onclick="checkSteps(5, this)">Guardar</button>
            </form>
          </li>
        </ul>
      </div>
    </main>
    <script src="https://code.jquery.com/jquery-latest.min.js" defer></script>
    <script src="ui/js/main.js" charset="utf-8" async></script>
    <script type="text/javascript">
      //Quitar ejemplo
      function ifSayYes(n){
        if(n == 0){
          console.log("Dijo que si :) ");
        } else {
          console.log("Dijo que no ): ");
        }
      }
      //Quitar ejemplo
      window.onload = function(){
        rContEditor("#contEditorPlantilla");

        /*
        infopromocrear=data.split('&@;');
        infopromoedit=data.split('&@;');
        bancarga=1;
        */
      <?php if(isset($_GET["id"])) {echo "getpromoplantillabd('".encrypt_decrypt('d', $_GET['id'])."')";}?>;
       idnvaprom='<?php if(isset($_GET["id"])) {echo $_GET["id"] ;} else{echo '0';}?>';

      }
      window.onresize = function(){
        rContEditor("#contEditorPlantilla");
        var w = window.innerWidth;
        if(compactMenu){
          if(w < 880){
            compactConfigMenu(1);
          } else if(w >= 880){
            compactConfigMenu(0);
          }
        }
      }
    </script>
  </body>
</html>
