<!--
Theme: Index plantillas - Dragonfly City
Version: 1
Author: OETCapital
9 de Abril
-->

<?php
require_once('backend/lib/db.php');
$debug = false;
$error = 0;
$error_msg = "";

if (isset($_GET['id'])) {
    $idprom = $_GET['id']; // id de la promoción encryptado
    //$id = (encrypt_decrypt('d',$id));
    $promo = getpromocion($idprom);
    $promo_nombre             = $promo['promo_nombre'];
    $marca_id                 = $promo['id_marca'];
    $marca                    = $promo['marca_codigo'];
    $marca_descripcion        = $promo['marca_descripcion'];
    $marca_logo               = $promo['marca_logo'];
    $plantilla_id             = $promo['id_plantilla'];
    $proveedor_logo           = $promo['proveedor_logo'];
    $promo_img_back           = $promo['promo_img_back'];
    $promo_img_prod           = $promo['promo_img_prod'];
    $promo_font               = $promo['promo_font'];
    $promo_color              = $promo['promo_color'];
    $promo_color_load         = $promo['promo_color_load'];
    $promo_txt_footer         = $promo['promo_txt_footer'];
    $promo_img_inicio         = $promo['promo_img_inicio'];
    $promo_img_precio         = $promo['promo_img_precio'];
    $promo_img_obtenercupon   = $promo['promo_img_obtenercupon'];
    $promo_img_cupon          = $promo['promo_img_cupon'];
    $promo_img_descargarcupon = $promo['promo_img_descargarcupon'];
    $promo_img_exito          = $promo['promo_img_exito'];
    $promo_img_hashtag        = $promo['promo_img_hashtag'];
    $promo_img_error          = $promo['promo_img_error'];
    $promo_legales            = $promo['archivo_legales'];

    if ($debug) {
      echo 'id: '.$idprom.' promo: '.$promo_nombre.' marca: '.$marca.' '.$marca_descripcion.' '.$marca_logo.' plantilla: '.$plantilla_id.' img_back: '.$promo_img_back.' img_prod: '.$promo_img_prod;
      //foreach($promo as $value) { echo $value; }
    }
    if (count($promo)== 0) { $error = 1; $error_msg = "Promo no encontrada ".$idprom;}
} else {
    $error = 2;  $error_msg ="Falta especificar un id de promo";
    //echo 'Favor especificar un id';
}
 if ($error==0) {
   require_once('plantilla-'.$plantilla_id.'.php');
} else { echo 'Error: '.$error.' '.$error_msg;}
?>
