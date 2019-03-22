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
  <body class="login body">
    <ul id="menuMobile" class="displayNone trans5">
      <li>Hola <span class="userName">Juliana</span></li>
      <li><a href="#">Tutoriales</a></li>
      <li><a role="button" onclick="closeLog()">Cerrar Sesión</a></li>
    </ul>
    <nav class="home displayFlex">
      <a href="#" class="logo trans5">
        <div class="displayFlex">
          <img src="ui/img/dragonf-ic.svg" width="25" height="25">
          <img src="ui/img/dragonf-title.svg" width="166.2" height="15">
        </div>
      </a>
      <a role="contentinfo" class="trans5 desktopNav">Hola <span class="userName">Juliana</span></a>
      <a href="#" class="trans5 desktopNav" target="_blank" >Tutoriales</a>
      <a role="button" class="trans5 desktopNav" target="_blank" onclick="closeLog()">Cerrar Sesión</a>
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
          <li><a role="button" class="trans5 tabButtons selectTab" onclick="promoTabs('0', this)">Activas</a></li>
          <li><a role="button" class="trans5 tabButtons" onclick="promoTabs('-100%', this)">Por Activar</a></li>
          <li><a role="button" class="trans5 tabButtons" onclick="promoTabs('-200%', this)">Pasadas</a></li>
        </ul>
        <div>
          <a role="button" id="newPromo" class="button trans5">
            <span class="mobileNav">Nueva Promoción</span>
            <span class="desktopNav">Nueva Promoción</span>
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
            <div class="promoItemDash displayFlex">
              <div>
                <p class="promoItemDash_Name">Que No Te Falte Gatorade</p>
              </div>
              <div>
                <p class="promoItemDash_Brand">Gatorade</p>
              </div>
              <div>
                <span>
                <p class="promoItemDash_Validity">18-02-18 <br> 23-04-18</p>
                </span>
              </div>
              <div class="actions displayFlex">
                <a class="itemDash_action_dashboard" class="trans5"></a>
                <a class="itemDash_action_modify" class="trans5"></a>
                <a class="itemDash_action_end" class="trans5"></a>
              </div>
            </div>
          </li>
          <li id="forActivationWrap">
            <div class="promoItemDash displayFlex">
              <div>
                <p class="promoItemDash_Name">Esta También Es Tu Pepsi</p>
              </div>
              <div>
                <p class="promoItemDash_Brand">Pepsi</p>
              </div>
              <div>
                <span>
                <p class="promoItemDash_Validity">18-04-18 <br> 23-06-18</p>
                </span>
              </div>
              <div class="actions displayFlex">
                <a class="itemDash_action_modify" class="trans5"></a>
                <a class="itemDash_action_publish" class="trans5"></a>
                <a class="itemDash_action_delete" class="trans5"></a>
              </div>
            </div>
          </li>
          <li id="pastPromotionsWrap">
            <div class="promoItemDash displayFlex">
              <div>
                <p class="promoItemDash_Name">Que No Te Falte Gatorade</p>
              </div>
              <div>
                <p class="promoItemDash_Brand">Gatorade</p>
              </div>
              <div>
                <span>
                <p class="promoItemDash_Validity">18-02-18 <br> 23-04-18</p>
                </span>
              </div>
              <div class="actions displayFlex">
                <a class="itemDash_action_report" class="trans5"></a>
              </div>
            </div>
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
        putUserName();
      }
    </script>
  </body>
</html>
