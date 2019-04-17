<?php
  require_once('backend/lib/db.php');

  /* Obtener parametros  */
  $accion   = $_POST['param1'];
  $idpromo  = $_POST['idpromo'];

  //$accion   = $_GET['param1'];
  //$idpromo  = $_GET['idpromo'];

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
function validarpromo ($idpromo,$ip) {
  $result=0; /* promo valida */
  $count =0;
  $count2 =0;
  $cads;
  $estatus=0;
  $val=validafechas($cads,$idpromo,$estatus);
  //echo 'validafechas: idpromo='.$idpromo.' ip='.$ip.PHP_EOL;
  if($val[0]>0.000001&&$val[1]<0.00000001) { /* ya comenzo */
    //echo 'voy a validalistanegra...'.PHP_EOL;
    $count = validalistanegra($ip);
    if($count<1) { /* No esta en la lista Negra */
      //echo 'voy a validaregion...'.PHP_EOL;
      $count=validaregion($idpromo); /* validar region */
      if($count>1) {
        //echo 'voy a promvalidestado...'.PHP_EOL;
        $count2 = promvalidestado($idpromo,$ip);
        if($count2<1) { $result=1; } /* ubicación no valida */
      } else { $result=1;} /* ubicación no valida */
    } else { $result=2;} /* esta en lista negra */
  }
  else {
    if ($val[0]<0.000001) {
      if ($estatus==1) /* por activar */ {
         $result=3; // no disponible
      } else { $result=4; } // no ha comenzado
    }
    else {  $result=5; }// ya finalizo
  }
  //echo 'validarpromo: '.$resul.PHP_EOL;
  return $result;
}

?>
