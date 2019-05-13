<!--
Theme: Index plantillas - Dragonfly City
Version: 1
Author: OETCapital
9 de Abril
-->

<?php
require_once('backend/lib/db.php');
require_once('backend/lib/dbconfig.php');
$debug      = false;
$error      = 0;
$error_msg  = "";
$idpromo    = 0;
$config     = 0;
$test       = 0;
$agregatag  ="";
$rutaurl    ="";
$tienetag =0;

/***************** GET PARAMETROS ******************/
if (isset($_GET['id'])) { $idpromo  = $_GET['id'];  $idpromo = encrypt_decrypt('d', $idpromo); }  /* si viene una id_promo */
if (isset($_GET['cf'])) { $config   = $_GET['cf']; }    /* si viene del configurador */
if (isset($_GET['ts'])) { $test     = $_GET['ts']; }    /* si es test */

/* Obtener datos segun parametros */
if ($idpromo>0) {  /* viene una promo, obtener datos promo  */
    $promo = getpromocion($idpromo);
    $promo_nombre             = $promo['promo_nombre'];
    $marca_id                 = $promo['id_marca'];
    $plantilla_id             = $promo['id_plantilla'];
    $promo_legales            = $promo['archivo_legales'];
    $promo_version            = $promo['version'];
    $proveedor_id             = $promo['id_proveedor'];
    $codigo_tagmanager        =$promo['codigo_tagmanager'];
    $subdirpromo              =$promo['dir'];
}

if ($config>0 && $promo==0) { /* viene del configurador y no viene promo , inicilaizar plantilla por defecto */
    $promo_nombre       = "Descuento Pepsi";
    $marca_id           = 1; /* PEPSI */
    $plantilla_id       = 1; /* PLANTILLA 1 */
    $promo_legales      = "";
    $promo_version      = 0; /* VER. 0 */
    $proveedor_id       = 1; /* OXXO */
    $codigo_tagmanager  ='';
    $subdirpromo        ='';
}

if ($idpromo>0 || ($idpromo==0 && $config>0)) { /* viene promo o no viene promo pero vengo del confiuradorm hay que obtener datos de la plantilla */
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
}

/* Si no viene promo y no viene del config, error, redireccionar a la pagina de GEPP */
if ($promo==0 && $config==0) {  $error = 1;  $error_msg ="Promo no encontrada";}
if($codigo_tagmanager!=''&&$codigo_tagmanager!=null)
{
  if($test==0&&$config==0)
{
  $rutaurl=getdominio()."/".$subdirpromo;
  $agregatag="<script>
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
  $tienetag=1;
}
}
if ($debug) { echo 'idpromo: '.$idpromo.' config: '.$config.' proveedor_id: '.$proveedor_id.' error: '.$error.' test: '.$test; }

switch ($error) {
  case 0: /* plantilla uno */
      require_once('plantilla-'.$plantilla_id.'.php');
      break;
  case 1:
      //echo '<script>window.location.href = "https://gepp.com.mx";</script>';  /* redireccionar a la página de GEPP */
      echo '<script>window.location.href = "result.php";</script>';
      break;
  default: /* Login */
      header('Location: login.php');
 }
?>
