<!--
Theme: Plantilla Uno - Dragonfly City
Version: 1
Author: OETCapital
4 de Abril
-->
<?php
    $rutaurl='';
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
    <script src="ui/js/globales.js" charset="utf-8"></script>
    <script src="https://code.jquery.com/jquery-latest.min.js" defer></script>
    <?php
    if($codigo_tagmanager!=''&&$codigo_tagmanager!=null)
    {
      if($test==0)
    {
      $rutaurl=getdominio()."/".$subdirpromo;
      echo "<script>
      dataLayer = [];
      (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
      new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
      j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','".$codigo_tagmanager."');</script>
          <script>
                  dataLayer.push({
                          'event': 'checkout',
                          'ecommerce': {
                                        'checkout': {
                                                    'actionField': {
                                                         'step': 1,
                                                         'page': 'Home',
                                                         'site': '".$rutaurl."'
                                                        }
                                                    }
                                        }
                              });
      </script>";
    }
  }
    ?>
    <link id="prefetchLogo" rel="prefetch" href="ui/img/logotipo/<?php echo $marca_logo; ?>">
    <style>
      #plantillaUno>#loading {position: fixed;top: 0;left:0;width:100vw;height:100vh;-webkit-justify-content: center;-moz-justify-content: center;-ms-justify-content: center;-o-justify-content: center;justify-content: center;z-index: 5000;opacity: 1;}#plantillaUno>#loading>div {margin-top: 10vh;text-align: center}#plantillaUno>#loading>div>img {opacity: 0;-webkit-animation: fadeIn .7s ease-in .7s forwards;-moz-animation: fadeIn .7s ease-in .7s forwards;-ms-animation: fadeIn .7s ease-in .7s forwards;-o-animation: fadeIn .7s ease-in .7s forwards;animation: fadeIn .7s ease-in .7s forwards}#plantillaUno>#loading>div>div {margin: 0 auto;width: 60px;height: 30px;-webkit-justify-content: space-between;-moz-justify-content: space-between;-ms-justify-content: space-between;-o-justify-content: space-between;justify-content: space-between;-webkit-align-items: flex-end;-moz-align-items: flex-end;-ms-align-items: flex-end;-o-align-items: flex-end;align-items: flex-end;opacity: 0;-webkit-animation: fadeIn .7s ease-in 1.2s forwards;-moz-animation: fadeIn .7s ease-in 1.2s forwards;-ms-animation: fadeIn .7s ease-in 1.2s forwards;-o-animation: fadeIn .7s ease-in 1.2s forwards;animation: fadeIn .7s ease-in 1.2s forwards}#plantillaUno>#loading>div>div>span {display: block;width: 15px;height: 15px;background: #fff;border-radius: 50%;margin: 10px 2px 8px;-webkit-animation: scaleDot 1.5s infinite;-moz-animation: scaleDot 1.5s infinite;-ms-animation: scaleDot 1.5s infinite;-o-animation: scaleDot 1.5s infinite;animation: scaleDot 1.5s infinite}#plantillaUno>#loading>div>div>span: nth-child(2) {-webkit-animation-delay: 1s;-moz-animation-delay: 1s;-ms-animation-delay: 1s;-o-animation-delay: 1s;animation-delay: 1s}#plantillaUno>#loading>div>div>span: nth-child(3) {-webkit-animation-delay: 1.5s;-moz-animation-delay: 1.5s;-ms-animation-delay: 1.5s;-o-animation-delay: 1.5s;animation-delay: 1.5s}#plantillaUno>#loading>div>div>span: nth-child(4) {-webkit-animation-delay: 2s;-moz-animation-delay: 2s;-ms-animation-delay: 2s;-o-animation-delay: 2s;animation-delay: 2s}@media (min-width: 880px) {#plantillaUno>#loading>div {margin-top: 20vh}}@-webkit-keyframes fadeIn {from { opacity: 0 }to { opacity: 1 }}@keyframes fadeIn {from { opacity: 0 }to { opacity: 1 }}@-webkit-keyframes scaleDot {0%,100% { opacity: 1; -webkit-transform: scale(1) }50% { opacity: 0; -webkit-transform: scale(0) }}@keyframes scaleDot {0%,100% { opacity: 1; transform: scale(1) }50% { opacity: 0; transform: scale(0) }}
    </style>
    <meta name="description" content="<?php echo $marca_descripcion; ?>">
    <meta name="author" content="GEPP">
    <meta property="og:title" content="<?php echo $promo_nombre; ?>">
    <meta property="og:site_name" content="">
    <meta property="og:type" content="website">
    <meta property="og:description" content="<?php echo $marca_descripcion; ?>">
    <meta property="og:image" content="ui/img/og/fb-<?php echo $marca; ?>.jpg">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="650">
    <meta name="twitter:card" content=“summary_large_image”>
    <meta name="twitter:title" content="<?php echo $promo_descrip; ?>">
    <meta name="twitter:description" content="<?php echo $marca_descripcion; ?>">
    <meta name="twitter:image" content="ui/img/og/tw-<?php echo $marca; ?>.jpg">
    <meta name="twitter:image:width" content="750">
    <meta name="twitter:image:height" content="392">
    <link rel="apple-touch-icon" sizes="180x180" href="ui/img/fav/<?php echo $marca; ?>/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="ui/img/fav/<?php echo $marca; ?>/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="ui/img/fav/<?php echo $marca; ?>/favicon-16x16.png">
    <link rel="manifest" href="ui/img/fav/<?php echo $marca; ?>/site.webmanifest">
    <link rel="mask-icon" href="ui/img/fav/<?php echo $marca; ?>/safari-pinned-tab.svg" color="#000000">
    <link rel="shortcut icon" href="ui/img/fav/<?php echo $marca; ?>/favicon.ico" type="image/x-icon"/>
    <link rel="stylesheet" href="ui/css/style.css">
  </head>
  <body id="plantillaUno" class="<?php echo $promo_font; ?> <?php echo $promo_color; ?>" style="background-image:url('ui/img/back/<?php echo $promo_img_back; ?>')">
    <nav class="displayFlex">
      <a href="#">
        <img id="navLogo" src="ui/img/logotipo/<?php echo $marca_logo; ?>" height="45">
      </a>
      <a id="proveedorUno" class="trans5" style="opacity:0">
        <img id="proveedorUnoLogo" src="ui/img/proveedor/<?php echo $proveedor_logo; ?>" height="45">
      </a>
    </nav>
    <div id="textoEdo" class="trans5" style="opacity:0">
      <p></p>
    </div>
    <section id="loading" class="displayFlex trans5 <?php echo $promo_color_load; ?>">
      <div>
        <img id="loadLogo" src="ui/img/logotipo/<?php echo $marca_logo; ?>" width="150" height="150">
        <div class="displayFlex">
          <span></span>
          <span></span>
          <span></span>
        </div>
      </div>
    </section>
    <section id="homeUno" class="trans5 displayNone">
      <div class="wrapSuperior displayFlex">
        <img id="productoImg" src="ui/img/producto/<?php echo $promo_img_prod; ?>">
        <div class="productoInfo">
          <div class="infoTitle">
            <img id="textoInicioImg" src="ui/img/textoInicio/<?php echo $promo_img_inicio; ?>">
          </div>
          <div class="prize">
            <img id="prizeImg" src="ui/img/precio/<?php echo $promo_img_precio; ?>">
          </div>
        </div>
      </div>
      <div class="wrapInferiorButton">
        <a role="button" onclick="ctaCoupon('<?php echo encrypt_decrypt('e', $idpromo); ?>','<?php echo $promo_img_cupon ?>',<?php echo $proveedor_id ?>,<?php echo $test ?>)">
          <img id="btCouponImg" class="trans5" src="ui/img/botonCupon/<?php echo $promo_img_obtenercupon; ?>">
        </a>
      </div>
    </section>
    <section id="cuponUno" class="trans5" style="display:none">
      <div class="wrapSuperior displayFlex">
        <img id="couponImg" src="ui/img/cupon/<?php echo $promo_img_cupon; ?>">
      </div>
      <div class="wrapInferiorButton">
        <a id="couponImgCaptureScreen"  role="button" onclick="ctaDownloadImg()" href="" download="cupon.jpg">
          <img id="btCaptureScreen" class="trans5" src="ui/img/botonDescarga/<?php echo $promo_img_descargarcupon; ?>">
        </a>
      </div>
    </section>
    <section id="mensajeUno" class="trans5" style="display:none">
      <ul class="wrapSuperiorMensajeExito" class="displayFlex" style="display:none">
        <li ><img id="msgLogo" src="ui/img/logotipo/<?php echo $marca_logo; ?>"></li>
        <li><img id="msgExitoImg" src="ui/img/mensajeExito/<?php echo $promo_img_exito; ?>"></li>
        <li><img id="msgHashtagImg" src="ui/img/hashtag/<?php echo $promo_img_hashtag; ?>"></li>
      </ul>
      <ul class="wrapSuperiorMensajeError" style="display:none">
        <li><img id="msgErrorImg" src="ui/img/mensajeError/<?php echo $promo_img_error; ?>"></li>
      </ul>
      <div class="wrapInferiorSocial displayFlex">
        <?php echo getmarca_redessociales($marca_id,$plantilla_id,$promo_version); ?>
        <!--
        <a href="https://www.facebook.com/pepsimexico/" target="_blank"><img id="icFacebook" src="ui/img/ic/facebook.png" width="45" height="45"></a>
        <a href="https://twitter.com/PepsiMEX" target="_blank"><img id="icInstagram" src="ui/img/ic/twitter.png" width="45" height="45"></a>
        <a href="https://www.instagram.com/pepsimex/" target="_blank"><img id="icTwitter" src="ui/img/ic/instagram.png" width="45" height="45"></a>
        <a href="https://www.youtube.com/channel/UCIa9XW9bbvfWKfU97COtCbw" target="_blank"><img id="icYoutube" src="ui/img/ic/youtube.png" width="45" height="45"></a>
      -->
      </div>
    </section>
    <div id="prevent" class="displayNone" style="background-image:url('ui/img/back/<?php echo $promo_img_back; ?>')">
      <p></p>
    </div>
    <footer class="displayFlex">
      <p><a href="legales/<?php echo $promo_legales; ?>" target="_blank">Bases Términos y Condiciones</a></p>
      <span>|</span>
      <p id="footerPromoCopy"><?php echo $promo_txt_footer; ?> </p>
      <span>|</span>
      <p>® Marca Registrada</p>
    </footer>
    <script src="ui/js/front-min.js" charset="utf-8"></script>
    <script type="text/javascript">
      var config          = "<?php echo $config ?>";
      var idpromo         = "<?php echo encrypt_decrypt('e', $idpromo); ?>";
      var test            = "<?php echo $test ?>";
      var promo_img_cupon = "<?php echo $promo_img_cupon ?>";
      var proveedor_id    = "<?php echo $proveedor_id ?>";
      var rutapromourl="<?php if($codigo_tagmanager!=''&&$codigo_tagmanager!=null){ if($test==0) { echo $rutaurl;} } ?>";
      tagmanager=<?php if($codigo_tagmanager!=''&&$codigo_tagmanager!=null){ if($test==0) { echo 1;} else{ echo 0;} } else { echo 0;} ?>;
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
          if (config == 0){   /* no viene del configurador */
            loadingImages();  /* quitar el loader */
            if (test== 0) { /* validar si no es test */
              validarpromo(idpromo);   /* validar promo */
            }
          }
      };
    </script>
  </body>
</html>
