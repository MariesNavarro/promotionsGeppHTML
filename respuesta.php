<?php
  require_once('backend/lib/db.php');

  /* Obtener parametros  */
  $accion   = $_POST['param1'];
  $idpromo  = $_POST['idpromo'];

  /* Ontener la IP */
  $ip = '';
  if (!empty($_SERVER['HTTP_CLIENT_IP'])) {  $ip = $_SERVER['HTTP_CLIENT_IP']; }
  elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) { $ip = $_SERVER['HTTP_X_FORWARDED_FOR']; }
  else {  $ip = $_SERVER['REMOTE_ADDR']; }

  //$accion = $_GET['param1'];
  //echo "accion=".$accion;

  switch ($accion) {
    case 1:  /* Validar Promo */
        echo validarpromo($idpromo,$ip);
        break;
    case 2: /* Obtener cupon */
        $idClient       = $_POST['codigo'];
        $test           = $_POST['test'];
        $promo_imgcupon = $_POST['promo_imgcupon'];
        $idproveedor    = $_POST['idproveedor'];
        echo getcupon($ip,$idClient,$idpromo,$promo_imgcupon,$idproveedor,$test);
        break;
    default:
        echo "Sin parametros";
   }


/* Validar Promo: vigencia, lista negra, regiones, estados */
function validarpromo ($idprom,$ip) {
  $result=0; /* promo valida */
  $count =0;
  $cads;
  $val=validafechas($cads,$idprom);
  if($val[0]>0.000001&&$val[1]<0.00000001) { /* ya comenzo */
    $count = validalistanegra($idprom,$ip);
    if($count<1) { /* No esta en la lista Negra */
      $count=validaregion($idprom,$ip); /* validar region */
      if($count<1) {
        $count = promvalidestado($ip,$idprom);
        if($count<1) { $result=1; } /* ubicación no valida */
      }
    } else { $result=2;} /* esta en lista negra */
  }
  else {
    if ($val[0]<0.000001) {  $result=3; } // no ha comenzado
    else {  $result=4; }// ya finalizo
  }
  return $result;
}

?>
