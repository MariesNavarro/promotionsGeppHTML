<?php
  require_once('backend/lib/db.php');
  $mensaje = "";
  $idpromo = 0;
  $idmsg = 0;

  /***************** GET PARAMETROS ******************/
  if (isset($_GET['id'])) { $idpromo = $_GET['id'];  $idpromo = encrypt_decrypt('d', $idpromo);}
  if (isset($_GET['idmsg'])) { $idmsg = $_GET['idmsg'];}

  /***************** SET MENSAJE ******************/
  switch ($idmsg) {
    case 1:  $mensaje = 'Promoción no es válida para tu ubicación'; break;
    case 2:  $mensaje = 'Promoción no disponible'; /* lista negra */  break;
    case 3:  $mensaje = 'Promoción no ha iniciado todavía'; break;  /* ya publicada, pero no ha iniciado */
    case 4:  $mensaje = 'Promoción disponible próximamente'; break; /* no esta publicada */
    case 5:  $mensaje = 'Promoción ya finalizó'; break;
    default: $mensaje = "Página en construcción";
   }

   if ($idpromo>0) {  /* viene una promo, obtener datos promo  */
       $promo = getpromocion($idpromo);
       $promo_nombre             = $promo['promo_nombre'];
       $promo_descripcion        = $promo['promo_descripcion'];
       $marca_id                 = $promo['id_marca'];
       $plantilla_id             = $promo['id_plantilla'];
       $promo_legales            = $promo['archivo_legales'];
       $promo_version            = $promo['version'];
       $proveedor_id             = $promo['id_proveedor'];
       $fecha_inicio             = str_replace("-","/",$promo['fecha_inicio']);
   } else {
       $promo_nombre = "Promo Gatorade";
       $marca_id = 2;
       $plantilla_id =1;
       $promo_legales ="";
       $promo_version = 0;
       $proveedor_id = 1;
  }

   $plantilla = getplatilla($marca_id,$promo_version,$plantilla_id,1,$proveedor_id);
   $marca                    = $plantilla['marca_codigo'];
   $marca_descripcion        = $plantilla['marca_descripcion'];
   $marca_logo               = $plantilla['marca_logo'];
   $proveedor_logo           = $plantilla['proveedor_logo'];
   $promo_img_back           = $plantilla['promo_img_back'];
   $promo_img_prod           = $plantilla['promo_img_prod'];
   $promo_font               = $plantilla['promo_font'];
   $promo_color              = $plantilla['promo_color'];
   $promo_color_load         = $plantilla['promo_color_load'];
   $promo_txt_footer         = $plantilla['promo_txt_footer'];
   $promo_img_inicio         = $plantilla['promo_img_inicio'];
   $promo_img_precio         = $plantilla['promo_img_precio'];
   $promo_img_obtenercupon   = $plantilla['promo_img_obtenercupon'];
   $promo_img_cupon          = $plantilla['promo_img_cupon'];
   $promo_img_descargarcupon = $plantilla['promo_img_descargarcupon'];
   $promo_img_exito          = $plantilla['promo_img_exito'];
   $promo_img_hashtag        = $plantilla['promo_img_hashtag'];
   $promo_img_error          = $plantilla['promo_img_error'];

//echo 'idpromo: '.$idpromo.' idplantilla: '.$plantilla_id.' idmarca: '.$marca_id.' version: '.$promo_version.' idproveedor: '.$proveedor_id.' promo_color: '.$promo_color;

?>

<!DOCTYPE html>
<html id="plantillaUnoHTML" lang="en" dir="ltr" data-marca="<?php echo $marca; ?>">
  <head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta http-equiv="cache-control" content="no-cache">
    <meta http-equiv="pragma" content="no-cache">
    <meta name="google" content="notranslate">
    <meta name="viewport" content="width=device-width, initial-scale=1.0000, minimum-scale=1.0000, maximum-scale=1.0000, user-scalable=yes">
    <title> <?php echo $promo_nombre; ?> </title>
    <script src="/ui/js/globales.js" charset="utf-8"></script>
    <link id="prefetchLogo" rel="prefetch" href="/ui/img/logotipo/<?php echo $marca_logo; ?>">
    <style>
      #plantillaUno>#loading {background: #fff; position: fixed;top: 0;left:0;width:100vw;height:100vh;-webkit-justify-content: center;-moz-justify-content: center;-ms-justify-content: center;-o-justify-content: center;justify-content: center;z-index: 5000;opacity: 1;}#plantillaUno>#loading>div {margin-top: 10vh;text-align: center}#plantillaUno>#loading>div>img {opacity: 0;-webkit-animation: fadeIn .7s ease-in .7s forwards;-moz-animation: fadeIn .7s ease-in .7s forwards;-ms-animation: fadeIn .7s ease-in .7s forwards;-o-animation: fadeIn .7s ease-in .7s forwards;animation: fadeIn .7s ease-in .7s forwards}#plantillaUno>#loading>div>div {margin: 0 auto;width: 60px;height: 30px;-webkit-justify-content: space-between;-moz-justify-content: space-between;-ms-justify-content: space-between;-o-justify-content: space-between;justify-content: space-between;-webkit-align-items: flex-end;-moz-align-items: flex-end;-ms-align-items: flex-end;-o-align-items: flex-end;align-items: flex-end;opacity: 0;-webkit-animation: fadeIn .7s ease-in 1.2s forwards;-moz-animation: fadeIn .7s ease-in 1.2s forwards;-ms-animation: fadeIn .7s ease-in 1.2s forwards;-o-animation: fadeIn .7s ease-in 1.2s forwards;animation: fadeIn .7s ease-in 1.2s forwards}#plantillaUno>#loading>div>div>span {display: block;width: 15px;height: 15px;background: #fff;border-radius: 50%;margin: 10px 2px 8px;-webkit-animation: scaleDot 1.5s infinite;-moz-animation: scaleDot 1.5s infinite;-ms-animation: scaleDot 1.5s infinite;-o-animation: scaleDot 1.5s infinite;animation: scaleDot 1.5s infinite}#plantillaUno>#loading>div>div>span: nth-child(2) {-webkit-animation-delay: 1s;-moz-animation-delay: 1s;-ms-animation-delay: 1s;-o-animation-delay: 1s;animation-delay: 1s}#plantillaUno>#loading>div>div>span: nth-child(3) {-webkit-animation-delay: 1.5s;-moz-animation-delay: 1.5s;-ms-animation-delay: 1.5s;-o-animation-delay: 1.5s;animation-delay: 1.5s}#plantillaUno>#loading>div>div>span: nth-child(4) {-webkit-animation-delay: 2s;-moz-animation-delay: 2s;-ms-animation-delay: 2s;-o-animation-delay: 2s;animation-delay: 2s}@media (min-width: 880px) {#plantillaUno>#loading>div {margin-top: 20vh}}@-webkit-keyframes fadeIn {from { opacity: 0 }to { opacity: 1 }}@keyframes fadeIn {from { opacity: 0 }to { opacity: 1 }}@-webkit-keyframes scaleDot {0%,100% { opacity: 1; -webkit-transform: scale(1) }50% { opacity: 0; -webkit-transform: scale(0) }}@keyframes scaleDot {0%,100% { opacity: 1; transform: scale(1) }50% { opacity: 0; transform: scale(0) }}
    </style>
    <meta name="description" content="<?php echo $marca_descripcion; ?>">
    <meta name="author" content="GEPP">
    <meta property="og:title" content="<?php echo $promo_nombre; ?>">
    <meta property="og:site_name" content="">
    <meta property="og:type" content="website">
    <meta property="og:description" content="<?php echo $marca_descripcion; ?>">
    <meta property="og:image" content="/ui/img/og/fb-<?php echo $marca; ?>.jpg">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="650">
    <meta name="twitter:card" content=“summary_large_image”>
    <meta name="twitter:title" content="<?php echo $promo_descrip; ?>">
    <meta name="twitter:description" content="<?php echo $marca_descripcion; ?>">
    <meta name="twitter:image" content="/ui/img/og/tw-<?php echo $marca; ?>.jpg">
    <meta name="twitter:image:width" content="750">
    <meta name="twitter:image:height" content="392">
    <link rel="apple-touch-icon" sizes="180x180" href="/ui/img/fav/<?php echo $marca; ?>/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/ui/img/fav/<?php echo $marca; ?>/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/ui/img/fav/<?php echo $marca; ?>/favicon-16x16.png">
    <link rel="manifest" href="/ui/img/fav/<?php echo $marca; ?>/site.webmanifest">
    <link rel="mask-icon" href="/ui/img/fav/<?php echo $marca; ?>/safari-pinned-tab.svg" color="#000000">
    <link rel="shortcut icon" href="/ui/img/fav/<?php echo $marca; ?>/favicon.ico" type="image/x-icon"/>
    <link rel="stylesheet" href="/ui/countdown/count.min.css">
    <link rel="stylesheet" href="/ui/css/style.css">
  </head>
  <body id="plantillaUno" class="<?php echo $promo_font; ?> <?php echo $promo_color_load; ?>" style="">
    <nav class="displayFlex">
      <a href="#">
        <img id="navLogo" src="/ui/img/logotipo/<?php echo $marca_logo; ?>" height="45">
      </a>
      <a id="proveedorUno" class="trans5" style="opacity:0">
        <img src="/ui/img/proveedor/<?php echo $proveedor_logo; ?>" height="45">
      </a>
    </nav>
    <div id="textoEdo" class="trans5" style="opacity:0">
      <p></p>
    </div>
    <section id="loading" class="displayFlex trans5 <?php echo $promo_color; ?>">
      <div>
        <img id="loadLogo" src="/ui/img/logotipo/<?php echo $marca_logo; ?>" width="150" height="150">
        <div class="displayFlex">
          <span></span>
          <span></span>
          <span></span>
        </div>
      </div>
    </section>

    <section id="mensajeUno" class="trans5" style="display:none">

      <ul class="countdown displayFlex <?php echo $promo_color; ?>" style="display:none">
          <ul class="wrapSuperiorMensajeExito">
            <li><span style="font-size: 30px;" >La promoción <?php echo $promo_descripcion; ?> comienza en</span></li>
          </ul>
          <li><span class="days">00</span> <p class="days_ref">Días</p></li>
          <li class="seperator">.</li>
          <li><span class="hours">00</span><p class="hours_ref">Horas</p></li>
          <li class="seperator">:</li>
          <li><span class="minutes">00</span><p class="minutes_ref">Minutos</p></li>
          <li class="seperator">:</li>
          <li><span class="seconds">00</span><p class="seconds_ref">Segundos</p></li>
      </ul>

      <ul class="wrapSuperiorMensajeError" style="display:none">
        <?php if ($idmsg!=0) { echo '<li><img id="msgErrorImg" src="/ui/img/mensajeError/result.png"></li>'; } ?>
        <li><span style="font-size: 45px; <?php echo $promo_color; ?>"><?php echo $mensaje; ?></span></li>
      </ul>
    </section>
      <div id="prevent" class="displayNone" style="background-image:url('/ui/img/back/<?php echo $promo_img_back; ?>')">
      <p></p>
    </div>
    <footer class="displayFlex">
      <!--
      <p><a href="/legales/<?php echo $promo_legales; ?>" target="_blank">Bases Términos y Condiciones</a></p>
      <span>|</span>
      <p id="footerPromoCopy"><?php echo $promo_txt_footer; ?> </p>
      <span>|</span>
      <p>® Marca Registrada</p>
    -->
    </footer>
    <script src="https://code.jquery.com/jquery-latest.min.js"></script>
    <script src="/ui/js/front-min.js" charset="utf-8"></script>
    <script src="/ui/countdown/jquery.downCount.js"></script>
    <script type="text/javascript">
      var idmsg = "<?php echo $idmsg ?>";
      var idpromo = "<?php echo $idpromo ?>";
      var fecha_inic =  "<?php echo $fecha_inicio ?>";
      //console.log(idmsg+' '+idpromo+' '+fecha_inic);
      preventHeight();
      preventHeight();
      preventRot();
      window.onresize = function(){
        preventHeight();
      };
      window.onorientationchange = function(){
        preventRot();
      };

      window.onload = function(){
          loadingImagesresult();      /* quitar el loader */
          if (idmsg==3) {
            $('.countdown').downCount({ date: fecha_inic, offset: -5  }, function () { window.location.href = "index.php?id="+idpromo; });
             showMsg(0); /* mostrar coutdown */
           } else {
             showMsg(1); /* mostrar mensaje error */
           }
      };

    </script>
  </body>
</html>
