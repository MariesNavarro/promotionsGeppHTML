<!--
Theme: Index plantillas - Dragonfly City
Version: 1
Author: OETCapital
9 de Abril
-->

<?php
require_once('backend/lib/db.php');
$debug = true;
$error = 0;
$error_msg = "";
$idprom = 0;
$config = 0;

/***************** GET PARAMETROS ******************/
if (isset($_GET['id'])) {   $idprom = $_GET['id']; }  /* si viene una id_promo */
if (isset($_GET['cf'])) { $config = $_GET['cf']; }    /* si viene del configurador */

/* Obtener datos segun parametros */
if ($idprom>0) {  /* viene una promo, obtener datos promo  */
    $promo = getpromocion($idprom);
    $promo_nombre             = $promo['promo_nombre'];
    $marca_id                 = $promo['id_marca'];
    $plantilla_id             = $promo['id_plantilla'];
    $promo_legales            = $promo['archivo_legales'];
    $promo_version            = $promo['version'];
    $proveedor_id             = $promo['id_proveedor'];
}

if ($config>0 && $promo==0) { /* viene del configurador y no viene promo , inicilaizar plantilla por defecto */
    $promo_nombre = "Descuento Pepsi";
    $marca_id = 1;
    $plantilla_id =1;
    $promo_legales ="";
    $promo_version = 0;
    $proveedor_id = 1;
}

if ($idprom>0 || ($idprom==0 && $config>0)) { /* viene promo o no viene promo pero vengo del confiuradorm hay que obtener datos de la plantilla */
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

if ($debug) { echo 'idprom: '.$idprom.' config: '.$config.' proveedor_id: '.$proveedor_id.' error: '.$error; }

switch ($error) {
  case 0: /* plantilla uno */
      require_once('plantilla-'.$plantilla_id.'.php');
      break;
  case 1: /* redireccionar a la página de GEPP */
      echo 'Voy a redirect...';
      header('Location: https://gepp.com.mx');
      exit();
      break;
  default: /* Login */
      require_once('home.php');
}

echo 'FIN...';

?>
