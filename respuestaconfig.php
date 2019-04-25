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


if($_POST["m"]==1){
       if(empty($_POST["usr"]) || empty($_POST["pwd"]))
       {
            echo 'Ambos valores son requerido';
       }
       else
       {

            $valid=login($_POST["usr"],$_POST["pwd"]);
            $array=explode(",", $valid);
            $valid=$array[0];
            if($valid=='SI')
            {
              $result=checkusersession($_POST['huella'],$_POST["usr"]);
              if($result=='success')
              {
                $result2=updateusersession($_POST['huella'],$_POST["usr"]);
                if($result2=='success')
                {
                  $_SESSION['userName'] = $_POST["usr"];
                  $_SESSION['Nombre']=$array[1];
                  $_SESSION['Email']=$array[2];
                  $_SESSION['Rol']=$array[3];
                }
                else {
                  $valid='Error con usuario';
                }
              }
              else {
                $valid='Error con usuario';
              }

            }
           echo $valid;
       }
}
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
if($_POST["m"]==3){
  $result=dashboard(encrypt_decrypt('d',$_POST["prom"]));
  echo $result;
}
if($_POST["m"]==4){
  $result=dasboard_report(encrypt_decrypt('d',$_POST["prom"]));
  echo $result;
}

if($_POST["m"]==5)
{
  $fi=$_POST["fi"];
  $ff=$_POST["ff"];
  $nom=$_POST["nom"];
  $desc=$_POST["desc"];
  $mar=$_POST["mar"];
  $pro=$_POST["pro"];
  $idnvaprom=encrypt_decrypt('d',$_POST["idnvaprom"]);
  $result=insertageneral($fi,$ff,$nom,$desc,$mar,$pro,$idnvaprom);
  echo encrypt_decrypt('e',$result);
}
if($_POST["m"]==6)
{
  $id=encrypt_decrypt('d',$_POST["id"]);
  $url=$_POST["url"];
  $result=actualizalegales($id,$url);
  echo $result;
}
if($_POST["m"]==7){
  $id=$_POST["fun"];
  $prom=encrypt_decrypt('d',$_POST["prom"]);
  $result=actualizafuncionalidad($id,$prom);
  echo $result;
}
if($_POST["m"]==8)
{
  $id=$_POST["cup"];
  $prom=encrypt_decrypt('d',$_POST["prom"]);
  $result=existecupon($id,$prom);
  echo $result;
}
if($_POST["m"]==9)
{
  $id=$_POST["cup"];
  $prom=encrypt_decrypt('d',$_POST["prom"]);
  $result=loadcupons($id,$prom);
  echo $result;
}
if($_POST["m"]==10)
{
  //'m=' + m+'&fun=' + id+'&prom=' + idnvaprom;
  $id=encrypt_decrypt('d',$_POST["fun"]);
  $result=plantillas($id);
  echo $result;
}
if($_POST["m"]==11)
{
  $id=$_POST["plan"];
  $prom=encrypt_decrypt('d',$_POST["prom"]);
  $result=actualizaplantillabd($id,$prom);
  echo $result;
}

if($_POST["m"]==12) /* Cambiar estatus la promo pasada como parametro */
{
  $id    = $_POST["id"];
  $st    = $_POST["st"];
  $result= actualizarstatus($id,$st);
  echo $result;
}

if($_POST["m"]==13) /* eliminar promocion */
{
  $id    = $_POST["id"];
  $result= eliminarpromo($id);
  echo $result;
}
if($_POST["m"]==14) /* actualizar plantilla promocion */
{
  $updcre    = $_POST["updcre"];
  $data    = $_POST["data"];
  $result= actualizaplantillaversion($updcre,$data);
  echo $result;
}
if($_POST["m"]==15) /* cancelar promocion */
{
  $id    = encrypt_decrypt('d',$_POST["id"]);
  $result= eliminarpromo($id);
  echo $result;
}
if($_POST["m"]==16) /* Crear directorio promocion */
{
  $idpromo    = $_POST["idpromo"];
  $dir    = $_POST["dir"];
  $result= creaactualizadir($idpromo,$dir);
  echo $result;
}
if($_POST["m"]==17) /* Crear directorio promocion */
{
  $idpromo    = $_POST["prom"];
  $result=getpromocioneditdata($idpromo);
  echo $result;
}
if($_POST["m"]==18) /* Crear directorio promocion */
{
  $idprov   = encrypt_decrypt('d',$_POST["prov"]);
  $result=getproveedordata($idprov);
  echo $result;
}
if($_POST["m"]==19) /* Crear directorio promocion */
{
  $huella   = $_POST["huella"];
  if(isset($_SESSION["userName"])) {
    $result=checkusersession($huella,$_SESSION["userName"]);
    if($result=='success')
    {
      $result2=updateusersession($huella,$_SESSION["userName"]);
      if($result2=='success')
      {
        $result=$result2;
      }
      else {
        session_destroy();
        $result='Error Usuario';
      }
    }
    else {
      session_destroy();
      $result='Error Usuario';
    }
  }
  else {
    $result='success';
  }

  echo $result;
}
?>
