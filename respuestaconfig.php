<?php
session_start();
// $connect = mysqli_connect("localhost", "root", "", "testing");
require_once('backend/lib/dbconfig.php');
if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
  $ip = $_SERVER['HTTP_CLIENT_IP'];
}
elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
  $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
}
else {
  $ip = $_SERVER['REMOTE_ADDR'];
}

/*  para pruebas
if (!empty($_GET["prom"])) {
  $id = encrypt_decrypt('d',$_GET["prom"]);
  echo $id;
  $promo_info   = PromoValores($id);
  $result=dashboard($id,$promo_info);
  echo $result;
}
*/


/* Login */
if($_POST["m"]==1){
       $valid = "";
       if(empty($_POST["usr"]) || empty($_POST["pwd"])) {
            $valid = 'Los datos de inicio de sesión son requeridos. Vuelve a intentar.';
       }
       else  {
            $valid=login($_POST["usr"],$_POST["pwd"]);
            $array=explode(",", $valid);
            $valid=$array[0];
            if($valid=='success') {
              $result=checkusersession($_POST['huella'],$_POST["usr"]);
              if($result=='success') {
                $result2=updateusersession($_POST['huella'],$_POST["usr"]);
                if($result2=='success') {
                  $_SESSION['userName'] = $_POST["usr"];
                  $_SESSION['Nombre']   = $array[1];
                  $_SESSION['Email']    = $array[2];
                  $_SESSION['Rol']      = $array[3];
                }
                else { $valid=$result; } /* Error actualizando la sessión */
              }
              else {  $valid=$result;  } /* Existe una sesión en otro dispositivo */
            }
       }
       echo $valid;
}

/* Logout */
if($_POST["m"]==2){
  $result=checkusersession($_POST['huella'],$_SESSION['userName']);
  $valid='error';
  if($result=='success')
  {
    $result2=updateusersessionclose($_SESSION['userName']);
    if($result2=='success')
    {
      session_destroy();
      $valid=$result2;
    }
    else {
      $valid='No se pudo actualizar';
    }
  }
  else {
    $valid='No es el mismo dispositivo';
  }
  echo $valid;
}

/* Dashboard consolidado */
if($_POST["m"]==3){
  $id = encrypt_decrypt('d',$_POST["prom"]);
  $promo_info   = PromoValores($id);
  $result=dashboard($id,$promo_info);
  echo $result;
}

/* Dashboard report entregados */
if($_POST["m"]==4){
  $count = 0;
  $id = encrypt_decrypt('d',$_POST["prom"]);
  //$result=dasboard_report($id);
  $promo_info   = PromoValores($id);
  $result=dashboard_entregados($id,$count,$promo_info);
  echo $result;
}

/* Insert datos generales */
if($_POST["m"]==5) {
  $fi=$_POST["fi"];
  $ff=$_POST["ff"];
  $nom=$_POST["nom"];
  $desc=$_POST["desc"];
  $mar=$_POST["mar"];
  $pro=$_POST["pro"];
  $tagmg=$_POST["tagmg"];
  $idnvaprom=encrypt_decrypt('d',$_POST["idnvaprom"]);
  $result=insertageneral($fi,$ff,$nom,$desc,$mar,$pro,$idnvaprom,$tagmg);
  echo encrypt_decrypt('e',$result);
}

/* Actualizar legales */
if($_POST["m"]==6){
  $id=encrypt_decrypt('d',$_POST["id"]);
  $url=$_POST["url"];
  $result=actualizalegales($id,$url);
  echo $result;
}

 /* Actualizar Funcionalidad */
if($_POST["m"]==7){
  $id=$_POST["fun"];
  $prom=encrypt_decrypt('d',$_POST["prom"]);
  $result=actualizafuncionalidad($id,$prom);
  echo $result;
}

/*Validación existe cupón */
if($_POST["m"]==8) {
  $id=$_POST["cup"];
  $prom=encrypt_decrypt('d',$_POST["prom"]);
  $result=existecupon($id,$prom);
  echo $result;
}

/* Cargar cupones */
if($_POST["m"]==9) {
  $id=$_POST["cup"];
  $prom=encrypt_decrypt('d',$_POST["prom"]);
  $result=loadcupons($id,$prom);
  echo $result;
}

/* Actuaizar plantilla HTML */
if($_POST["m"]==10) {
  //'m=' + m+'&fun=' + id+'&prom=' + idnvaprom;
  $id=encrypt_decrypt('d',$_POST["fun"]);
  $dis=$_POST["disa"];
  $disable='';
  if($dis=='disabled')
  {
    $disable=$dis;
  }
  $result=plantillas($id,$disable);
  echo $result;
}

/* Actuaizar plantilla */
if($_POST["m"]==11) {
  $id=$_POST["plan"];
  $prom=encrypt_decrypt('d',$_POST["prom"]);
  $result=actualizaplantillabd($id,$prom);
  echo $result;
}

/* Cambiar estatus la promo pasada como parametro */
if($_POST["m"]==12) {
  $id    = $_POST["id"];
  $st    = $_POST["st"];
  $result= actualizarstatus($id,$st);
  echo $result;
}

/* eliminar promocion */
if($_POST["m"]==13) {
  $id    = $_POST["id"];
  $result= eliminarpromo($id);
  echo $result.' '.$id;
}

/* actualizar plantilla promocion */
if($_POST["m"]==14) {
  $updcre    = $_POST["updcre"];
  $data    = $_POST["data"];
  $result= actualizaplantillaversion($updcre,$data);
  echo $result;
}

/* cancelar promocion */
if($_POST["m"]==15) {
  $id    = encrypt_decrypt('d',$_POST["id"]);
  $result= eliminarpromo($id);
  echo $result.' '.$id;
}

/* Crear directorio promocion */
if($_POST["m"]==16) {
  $idpromo  = $_POST["idpromo"];
  $dir      = $_POST["dir"];
  $result   = creaactualizadir($idpromo,$dir);
  echo $result;
}

/* Crear directorio promocion */
if($_POST["m"]==17) {
  $idpromo    = $_POST["prom"];
  $result     = getpromocioneditdata($idpromo);
  echo $result;
}

/* Crear directorio promocion */
if($_POST["m"]==18) {
  $idprov   = encrypt_decrypt('d',$_POST["prov"]);
  $result   = getproveedordata($idprov);
  echo $result;
}

/* CheckSession Validar sesión del usuario */
if($_POST["m"]==19) {
  $huella   = $_POST["huella"];
  if(isset($_SESSION["userName"])) {
    $result=checkusersession($huella,$_SESSION["userName"]);
    if($result=='success') {
      $result2=updateusersession($huella,$_SESSION["userName"]);
      if($result2=='success') {
        $result=$result2;
      } else {
        session_destroy();
        $result='Error Usuario';
      }
    } else {
      session_destroy();
      $result='Error Usuario';
    }
  }
  else { $result='success'; }
  echo $result;
}
/* recuperar contraseña */
if($_POST["m"]==20){
       $valid = "";
       if(empty($_POST["usr"])) {
            $valid = 'El nombre del usuario es requerido.';
       }
       else  {
            $user = $_POST["usr"];
            $valid=recuperar($user);
            $array=explode(",", $valid);
            $correo=$array[2];
            $pass=$array[4];
            send_email($correo,$user,$pass);
            $valid=$array[0];
       }
       echo $valid;
}
/* liberar cupones */
if($_POST["m"]==21){
  $id       = encrypt_decrypt('d',$_POST["id"]);
  $cupones  = $_POST["c"];
  $result= liberarcupones($id,$cupones);
  echo $result;
}

/* Dashboard report disponibles */
if($_POST["m"]==22){
  $count = 0;
  $id = encrypt_decrypt('d',$_POST["prom"]);
  //$result=dasboard_report($id);
  $promo_info   = PromoValores($id);
  $result=dashboard_disponibles($id,$count,$promo_info);
  echo $result;
}

/* Dashboard report entregados count */
if($_POST["m"]==23){
  $count = 0;
  $id = encrypt_decrypt('d',$_POST["prom"]);
  //$result=dasboard_report($id);
  $promo_info   = PromoValores($id);
  $promo_generica     = $promo_info['ind_generico'];
  $link    = connect();
  $result  = cuponesEntregados($link,$id,$promo_generica);
  Close($link);
  echo $result;
}

/* Dashboard report disponibles count */
if($_POST["m"]==24){
  $count = 0;
  $id = encrypt_decrypt('d',$_POST["prom"]);
  //$result=dasboard_report($id);
  $promo_info   = PromoValores($id);
  $promo_generica     = $promo_info['ind_generico'];
  $promo_generica_max = $promo_info['max_generico'];
  $link    = connect();
  $result  = cuponesDisponibles($link,$id,$promo_generica,$promo_generica_max);
  Close($link);
  echo $result;
}

?>
