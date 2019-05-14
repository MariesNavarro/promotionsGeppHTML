<?php
date_default_timezone_set('America/Mexico_City');

require_once('db.php');

// Agregar para Login

function login($usr,$pwd)
{
  $valid='';
  $valid='Los datos de inicio de sesión son incorrectos. Vuelve a intentar.';
  $link=connect();

  $username = mysqli_real_escape_string($link, $usr);
  $password = mysqli_real_escape_string($link, $pwd);
  //$password = md5($password);
  $password=encrypt_decrypt('e',$password);
  $query = "SELECT * FROM gtrd_settings WHERE Module='Admin' AND  setting = '$username' AND value = '$password'";
  $result = mysqli_query($link, $query);
  while ($fila = mysqli_fetch_row($result)) {
        $datos=$fila[4];
        $valid='success,'.$datos;
  }
  mysqli_free_result($result);
  Close($link);
  return $valid;
}
function recuperar($usr)
{
  $valid='';
  $valid='El nombre de usuario es requerido.';
  $link=connect();

  $username = mysqli_real_escape_string($link, $usr);
  //$password = md5($password);
  //$password=encrypt_decrypt('e',$password);
  $query = "SELECT * FROM gtrd_settings WHERE Module='Admin' AND  setting = '$username'";
  $result = mysqli_query($link, $query);
  while ($fila = mysqli_fetch_row($result)) {
        $datos=$fila[4].','.encrypt_decrypt('d',$fila[3]);
        $valid='success,'.$datos;
  }
  mysqli_free_result($result);
  Close($link);
  return $valid;
}
function getpromociones($estatus,&$count){
  $html    = '';
  $dominio = getdominio();
  $link    = connect();
  $msg     = '';
  $estatus2= 0;
  $today       = date('Y-m-d');
  $today_time  = strtotime($today);

  if ($estatus==2) { $estatus2=5;}; /* Si es estatus por activar, tambien traer las pausadas */

  $query= "SELECT gtrd_promociones.nombre Promocion,gtrd_marca.nombre Marca,
                  DATE_FORMAT(fecha_inicio,'%d/%m/%Y'),DATE_FORMAT(fecha_fin,'%d/%m/%Y'),
                  gtrd_promociones.id, gtrd_promociones.dir, gtrd_promociones.archivo_legales,
                  NC.numcupones, fecha_fin, gtrd_proveedor.ind_legales, gtrd_proveedor.nombre,
                  ind_generico, codigo_generico, max_generico
               FROM gtrd_promociones
         INNER JOIN gtrd_marca ON gtrd_marca.Id=gtrd_promociones.id_marca
         INNER JOIN gtrd_proveedor ON gtrd_proveedor.Id=gtrd_promociones.id_proveedor
          LEFT JOIN (select id_promo,COUNT(*) numcupones from gtrd_cupones group by id_promo) NC On NC.id_promo=gtrd_promociones.id
              WHERE gtrd_promociones.estatus in (".$estatus.",".$estatus2.")
           ORDER BY gtrd_promociones.fecha_update DESC";
/*
  $query= "SELECT gtrd_promociones.nombre Promocion,gtrd_marca.nombre Marca,
                  DATE_FORMAT(fecha_inicio,'%d/%m/%Y'),DATE_FORMAT(fecha_fin,'%d/%m/%Y'),
                  gtrd_promociones.id, gtrd_promociones.dir, gtrd_promociones.archivo_legales,
                  count(gtrd_cupones.codigo) cupones
             FROM gtrd_promociones
       INNER JOIN gtrd_marca ON gtrd_marca.Id=gtrd_promociones.id_marca
        LEFT JOIN gtrd_cupones On gtrd_cupones.id_promo=gtrd_promociones.id
            WHERE gtrd_promociones.estatus='$estatus'
         GROUP BY gtrd_promociones.nombre,gtrd_marca.nombre,
                  DATE_FORMAT(fecha_inicio,'%d/%m/%Y'),DATE_FORMAT(fecha_fin,'%d/%m/%Y'),
                  gtrd_promociones.id, gtrd_promociones.dir, gtrd_promociones.archivo_legales
         ORDER BY gtrd_promociones.fecha_update DESC";
  */
  $result = mysqli_query($link, $query);
  $count  = mysqli_num_rows($result);
  while ($fila = mysqli_fetch_row($result)) {
        $htmldat='<div class="promoItemDash displayFlex">
          <div>
            <p class="promoItemDash_Name">'.$fila[0].'</p>
          </div>
          <div>
            <p class="promoItemDash_Brand">'.$fila[1].'/'.$fila[10].'</p>
          </div>
          <div>
            <p class="promoItemDash_Validity">'.$fila[2].' al '.$fila[3].'</p>
          </div>';
          /*
            <span>
            <p class="promoItemDash_Validity">'.$fila[2].' <br> '.$fila[3].'</p>
            </span>
          </div>';
          */
          if($estatus==1) { // Activas
            $htmlact='<div class="actions displayFlex">
                <a class="itemDash_action_link" class="trans5" onclick="openLinks(\'open\' ,this)"></a>
                <a class="itemDash_action_dashboard" href="dash.php?id='.encrypt_decrypt('e', $fila[4]).'" class="trans5"></a>';
                if($_SESSION['Rol']=='Admin') {
                  //$htmlact=$htmlact.'<a class="itemDash_action_modify" href="mod.php?id='.encrypt_decrypt('e', $fila[4]).'" class="trans5"></a>
                  //<a class="itemDash_action_end" href="end.php?id='.encrypt_decrypt('e', $fila[4]).'" class="trans5"></a>';
                  $htmlact=$htmlact.'<a class="itemDash_action_modify" href="config.php?id='.encrypt_decrypt('e', $fila[4]).'" class="trans5"></a>
                  <a class="itemDash_action_stop" href="#" class="trans5" onclick="popActionFun(\'show\', \'¿Estás seguro que quieres PAUSAR la promo '.$fila[0].' ? <br> Pasará a por activar y luego se podrá volver a publicar.\',\'actualizarstatus('.$fila[4].',5)\')"></a>
                  <a class="itemDash_action_end" href="#" class="trans5" onclick="popActionFun(\'show\', \'¿Estás seguro que quieres FINALIZAR la promo '.$fila[0].' ? <br> Pasará a finalizar y NO se podrá volver a activar.\',\'actualizarstatus('.$fila[4].',3)\')"></a>';
                }

                $htmlact=$htmlact.'</div>
                <ul class="linksWrap trans5">
                  <li class="displayFlex">
                    <h3>Página de Prueba:</h3>
                    <a href="./?id='.encrypt_decrypt('e', $fila[4]).'&ts=1" target="_blank">'.$dominio.'/?id='.encrypt_decrypt('e', $fila[4]).'&ts=1</a>
                  </li>
                  <li class="displayFlex">
                    <h3>Página de Producción:</h3>
                    <a href="./?id='.encrypt_decrypt('e', $fila[4]).'" target="_blank">'.$dominio.'/?id='.encrypt_decrypt('e', $fila[4]).'</a>
                  </li>
                  <li class="displayFlex">
                    <h3>Página de Distribución:</h3>
                    <a href="./'.$fila[5].'" target="_blank">'.$dominio.'/'.$fila[5].'</a>
                  </li>
                </ul>
                 </div>';
          }
          else if($estatus==2){ // Por activar
            $htmlact='<div class="actions displayFlex">
                      <a class="itemDash_action_link" class="trans5" onclick="openLinks(\'open\' ,this)"></a>
                      <a class="itemDash_action_dashboard" href="dash.php?id='.encrypt_decrypt('e', $fila[4]).'" class="trans5"></a>';
            if($_SESSION['Rol']=='Admin')
            {
              if ((($fila[6] != null && $fila[6] != "" && $fila[9] == 1) || $fila[9] == 0) && $fila[7] > 0) {  /* verificar que tenga legales y cupones cargados */
                $htmlact=$htmlact.'<a class="itemDash_action_modify" href="config.php?id='.encrypt_decrypt('e', $fila[4]).'" class="trans5"></a>
                <a class="itemDash_action_delete" href="#" class="trans5" onclick="popActionFun(\'show\', \'¿Estás seguro que quieres ELIMINAR la promo '.$fila[0].' ? <br> Ya no la podrás ver.\',\'eliminarpromo('.$fila[4].')\')"></a>
                <a class="itemDash_action_publish" href="#" class="trans5" onclick="popActionFun(\'show\', \'¿Estás seguro que quieres PUBLICAR la promo '.$fila[0].' ? <br> Pasará a activas y la podrás volver a pausar o finalizar.\',\'actualizarstatus('.$fila[4].',1)\')"></a>
                ';
              } else {
                // VALIDACIONES PARA PODER PUBLICAR
                // 1-legales cargados  (dependiendo de la config del proveedor)
                // 2-Cupones cargados
                // 3-fecha fin mayor a la de hoy
                $msg = "";
                $fecha_fin_time = strtotime($fila[8]);
                if (($fila[6] == null || $fila[6] == "") && $fila[9] == 1) { $msg = "&#8226; cargar los legales"; }
                if ($fila[7] == 0) { if ($msg != null) { $msg .= "<br>";}  $msg .="&#8226; cargar los cupones"; }
                if ($fecha_fin_time < $today_time) { if ($msg != null) { $msg .= "<br>";}  $msg .="&#8226; verificar fecha fin ".$fila[3].' '.$now; }
                //$msg .="&#8226; verificar fecha fin ".$fila[3]." es menor que ".$now;
                $htmlact=$htmlact.'<a class="itemDash_action_modify" href="config.php?id='.encrypt_decrypt('e', $fila[4]).'" class="trans5"></a>
                <a class="itemDash_action_delete" href="#" class="trans5" onclick="popActionFun(\'show\', \'¿Estás seguro que quieres ELIMINAR la promo '.$fila[0].' ? <br> Ya no la podrás ver.\',\'eliminarpromo('.$fila[4].')\')"></a>
                <a class="itemDash_action_question" href="#" class="trans5" onclick="popInfoFun(\'show\', \''.$msg.'\')" ></a>
                ';
              }
            }
            $htmlact=$htmlact.'</div>
            <ul class="linksWrap trans5">
              <li class="displayFlex">
                <h3>Página de Prueba:</h3>
                <a href="./?id='.encrypt_decrypt('e', $fila[4]).'&ts=1" target="_blank">'.$dominio.'/?id='.encrypt_decrypt('e', $fila[4]).'&ts=1</a>
              </li>
              <li class="displayFlex">
                <h3>Página de Producción:</h3>
                <a href="./?id='.encrypt_decrypt('e', $fila[4]).'" target="_blank">'.$dominio.'/?id='.encrypt_decrypt('e', $fila[4]).'</a>
              </li>
              <li class="displayFlex">
                <h3>Página de Distribución:</h3>
                <a href="./'.$fila[5].'" target="_blank">'.$dominio.'/'.$fila[5].'</a>
              </li>
            </ul>
            </div>';
          }
          else { // Finalizadas
            $htmlact='<div class="actionsFin displayFlex">
              <a class="itemDash_action_link" class="trans5" onclick="openLinks(\'open\' ,this)"></a>
              <a class="itemDash_action_report" class="trans5" href="dash.php?id='.encrypt_decrypt('e', $fila[4]).'" class="trans5"></a>';
              $htmlact=$htmlact.'</div>
              <ul class="linksWrap trans5">
                <li class="displayFlex">
                  <h3>Página de Prueba:</h3>
                  <a href="./?id='.encrypt_decrypt('e', $fila[4]).'&ts=1" target="_blank">'.$dominio.'/?id='.encrypt_decrypt('e', $fila[4]).'&ts=1</a>
                </li>
              </ul>
              </div>';
          }
          $html=$html.$htmldat.$htmlact;
  }
  mysqli_free_result($result);
  Close($link);
  return $html;
}

function descPromo($link,$promo) {
  /* recuperar todas las filas de myCity */
   $data="";
   $consulta = "SELECT descripcion FROM gtrd_promociones WHERE id = ".$promo.";";
   if ($resultado = mysqli_query($link, $consulta)) {
     while ($fila = mysqli_fetch_row($resultado)) {
         $data=$fila[0];
      }
      /* liberar el conjunto de resultados */
      mysqli_free_result($resultado);
    }
    return $data;
}
function PromoValores($promo) {
  /* recuperar todas las filas de myCity */
   $data="";
   $link    = connect();
   $consulta = "SELECT a.nombre, a.descripcion,
                       b.nombre marca, b.logo_excel marca_logo,
                       c.nombre proveedor, c.logo_excel proveedor_logo,
                       DATE_FORMAT(a.fecha_inicio,'%d/%m/%Y') fecha_inicio,
                       DATE_FORMAT(a.fecha_fin,'%d/%m/%Y') fecha_fin,
                       a.ind_generico, a.codigo_generico, a.max_generico
                  FROM gtrd_promociones a
             LEFT JOIN gtrd_marca b ON a.id_marca = b.id
             LEFT JOIN gtrd_proveedor c ON a.id_proveedor = c.id
                 WHERE a.id = ".$promo;
   if ($resultado = mysqli_query($link, $consulta)) {
     $data=mysqli_fetch_array($resultado);
     //while ($fila = mysqli_fetch_row($resultado)) {
     //     $data=$fila[0];
     //  }
      /* liberar el conjunto de resultados */
      mysqli_free_result($resultado);
    }
    Close($link);
    return $data;
}
function cuponesEntregadosHoy($link,$promo,$promo_generica) {
  /* recuperar todas las filas de myCity */
   $score=0;
   $tabla = "gtrd_cupones";
   if ($promo_generica==1) { $tabla = "gtrd_cupones_genericos"; }
   $consulta = "SELECT count(*) FROM ".$tabla." WHERE estatus = 1 and id_promo = ".$promo." and Date_format(fecha_entregado,'%d-%m-%Y') = Date_format(now(),'%d-%m-%Y');";
   if ($resultado = mysqli_query($link, $consulta)) {
     while ($fila = mysqli_fetch_row($resultado)) {
         $score=$fila[0];
      }
      /* liberar el conjunto de resultados */
      mysqli_free_result($resultado);
    }
    return $score;
}
function cuponesEntregados($link,$promo,$promo_generica) {
  /* recuperar todas las filas de myCity */
   $score=0;
   $tabla = "gtrd_cupones";
   if ($promo_generica==1) { $tabla = "gtrd_cupones_genericos"; }
   $consulta = "SELECT count(*) FROM ".$tabla." WHERE estatus = 1 and id_promo = ".$promo.";";
   if ($resultado = mysqli_query($link, $consulta)) {
     while ($fila = mysqli_fetch_row($resultado)) {
         $score=$fila[0];
      }
      /* liberar el conjunto de resultados */
      mysqli_free_result($resultado);
    }
    return $score;
}
function cuponesDisponibles($link,$promo,$promo_generica,$promo_generica_max) {
  /* recuperar todas las filas de myCity */
   $score=0;
   if ($promo_generica==1) {
     $consulta = "SELECT ".$promo_generica_max."-count(*) FROM gtrd_cupones_genericos WHERE estatus = 1 and id_promo = ".$promo.";";
   } else {
     $consulta = "SELECT count(*) FROM gtrd_cupones WHERE estatus = 0 and id_promo = ".$promo.";";
   }
   if ($resultado = mysqli_query($link, $consulta)) {
     while ($fila = mysqli_fetch_row($resultado)) {
         $score=$fila[0];
      }
      /* liberar el conjunto de resultados */
      mysqli_free_result($resultado);
    }
    return $score;
}
function cuponesUltimo($link,$promo,$promo_generica) {
  /* recuperar todas las filas de myCity */
   $score="";
   $tabla = "gtrd_cupones";
   if ($promo_generica==1) { $tabla = "gtrd_cupones_genericos"; }
   $consulta = "SELECT max(fecha_entregado) FROM ".$tabla." WHERE estatus = 1 and id_promo = ".$promo.";";
   if ($resultado = mysqli_query($link, $consulta)) {
     while ($fila = mysqli_fetch_row($resultado)) {
         $score=$fila[0];
         if ($score!=null) {
           $date = new DateTime($score);
           $new_date_format = $date->format('d-m-Y H:i:s');
         } else {$new_date_format="(No entregado)";}
      }
      /* liberar el conjunto de resultados */
      mysqli_free_result($resultado);
    }
    return $new_date_format;
}
function createhtml($link,$promo,$promo_info)
{
  //$des_promo          = descPromo($link,$promo);
  $promo_generica     = $promo_info['ind_generico'];
  $promo_generica_max = $promo_info['max_generico'];
  $cup_entregadoshoy  = cuponesEntregadosHoy($link,$promo,$promo_generica);
  $cup_entregados     = cuponesEntregados($link,$promo,$promo_generica);
  $cup_disponibles    = cuponesDisponibles($link,$promo,$promo_generica,$promo_generica_max);
  $cup_ultimo         = cuponesUltimo($link,$promo,$promo_generica);
  $porc_disponibles   = 0;
  $porc_entregados    = 0;
  if ($cup_disponibles+$cup_entregados > 0) {
    $porc_entregados = ($cup_entregados*100)/($cup_disponibles+$cup_entregados);
    $porc_disponibles = ($cup_disponibles*100)/($cup_disponibles+$cup_entregados);
  }
  $color = "green";

  if ($porc_disponibles < 25) { $color = "red";}
  if ($porc_disponibles >= 25 and $porc_disponibles < 40) { $color = "orange";}

  echo '<!-- Tab content -->
        <div id="Consolidados" class="tabcontent"  style="text-align: center;">
          <p class="descPromo" style="font-size: 1.9rem;font-weight: bold;">'.$promo_info['nombre'].'</p>
          <p style="font-size: 1.3rem;margin-top: 5px;">'.$promo_info['marca'].'</p>
          <p style="font-size: 1.3rem;margin-top: 5px;">'.$promo_info['proveedor'].'</p>
          <p style="font-size: 1.0rem;margin-top: 5px;">'.$promo_info['fecha_inicio'].' al '.$promo_info['fecha_fin'].'</p>
          <p style="font-size: 1.9rem;margin-top: 20px;">Cupones</p><br />
          <p id="cupEntregadosHoy" style="font-size: 4.6rem; font-weight: 300; margin-top: -15px;">'.number_format($cup_entregadoshoy, 0, '.', ',').'</p><br />
          <p style="font-size: 15px; margin-top: -32px;color:black;">Entregados Hoy</p><br />
          <p id="cupEntregados" style="font-size: 4.6rem; font-weight: 300; margin-top: -15px;">'.number_format($cup_entregados, 0, '.', ',').'</p><br />
          <p id="cupEntregadosPorc" style="font-size: 15px; margin-top: -32px;color:black;">Total Entregados ('.number_format($porc_entregados, 2, '.', ',').'%)</p><br />
          <p id="cupDisponibles" style="font-size: 4.6rem; font-weight: 300; margin-top: -15px;color: '.$color.';">'.number_format($cup_disponibles, 0, '.', ',').'</p><br />
          <p id="cupDisponiblesPorc" style="font-size: 15px; margin-top: -32px;color:black;">Total Disponibles ('.number_format($porc_disponibles, 2, '.', ',').'%)</p><br />
          <p style="font-size: 15px; margin-top: -5px;color:black;">Último cupón entregado el <span id="cupUltimo" style="color:white;">'.$cup_ultimo.'</span</p><br />
        </div>';
}

  function  getDatos($promo) {
    $link               = connect();
    $promdecr           = encrypt_decrypt('d',$promo);
    $cup_entregadoshoy  = cuponesEntregadosHoy($link,$promdecr);
    $cup_entregados     = cuponesEntregados($link,$promdecr);
    $cup_disponibles    = cuponesDisponibles($link,$promdecr);
    $cup_ultimo         = cuponesUltimo($link,$promdecr);
    $porc_disponibles   = 0;
    $porc_entregados    = 0;
    if ($cup_disponibles+$cup_entregados > 0) {
      $porc_entregados = ($cup_entregados*100)/($cup_disponibles+$cup_entregados);
      $porc_disponibles = ($cup_disponibles*100)/($cup_disponibles+$cup_entregados);
    }
    $color = "green";

    if ($porc_disponibles < 25) { $color = "red";}
    if ($porc_disponibles >= 25 and $porc_disponibles < 40) { $color = "orange";}

    $salida = number_format($cup_entregadoshoy, 0, '.', ',').";".number_format($cup_entregados, 0, '.', ',').";".number_format($cup_disponibles, 0, '.', ',').";Total Entregados (".number_format($porc_entregados, 2, '.', ',')."%);Total Disponibles (".number_format($porc_disponibles, 2, '.', ',')."%);".$color.";".$cup_ultimo.";";
    Close($link);
    return $salida;
}

function dashboard($promo,$promo_info)
{
  $reg=0;
  $link=connect();
  $estatus=getestatuspromo($link,$promo);
  $salida='<div id="disclaimerIndex" class="">
             <div id="content">
              <section id_estatus="'.$estatus.'"  id_promo="'.$promo.'" id="disclaimer">'.createhtml($link,$promo,$promo_info);
              /*
               if($estatus==1)
               {
                 $salida=$salida.'
                 <div id="interface" class="flexDisplay">
                   <a role="button" class="buttonG trans7 btnActualizar"   onclick="actualizaDatos(\''.encrypt_decrypt('e',$promo).'\')">Actualizar</a>
                 </div>';
               }
               */
              $salida=$salida.'
             </section>
             </div>
            </div>';
  Close($link);
  return $salida;
}

function dashboard_entregados($promo,&$count,$promo_info){
  $reg=0;
  $salida='';
  $promo_generica = $promo_info['ind_generico'];
  $tabla = "gtrd_cupones";
  if ($promo_generica==1) { $tabla = "gtrd_cupones_genericos"; }
  $link=connect();
  $consulta ="SELECT codigo Cupon, DATE_FORMAT(fecha_entregado,'%d/%m/%Y %H:%i:%s') Entregado_El,ip IPSolicitud, a.pais,gtrd_estados.estado
              FROM   ".$tabla." a
              INNER JOIN gtrd_estados on a.estado=gtrd_estados.codigo_estado
              WHERE id_promo=".$promo." and estatus=1 and a.estado NOT IN ('ALL')
              UNION
              SELECT codigo Cupon,fecha_entregado Entregado_El,ip IPSolicitud, 'MX' pais,'(No registrado)' estado
              FROM   ".$tabla." a
              WHERE (a.estado IS NULL OR a.estado IN ('ALL')) AND id_promo=".$promo." and estatus=1
              ORDER BY Entregado_El DESC";

  if ($resultado = mysqli_query($link, $consulta)) {
    $count  = mysqli_num_rows($resultado);
    while ($fila = mysqli_fetch_row($resultado)) {
         $reg++;
         $salida=$salida.'<div class="promoItemDash displayFlex">
                          <div><p class="promoItemDash_Validity">'.$fila[1].'</p></div>
                          <div><p class="promoItemDash_Brand">'.$fila[0].'</p></div>
                          <div><p class="promoItemDash_Brand">'.$fila[2].'</p></div>
                          <div><p class="promoItemDash_Brand">'.$fila[3].'</p></div>
                          <div><p class="promoItemDash_Brand">'.$fila[4].'</p></div>
                          </div>';
      }
      if($reg<1) {
        $salida='<div id="contentreport" style="margin-top: 8%;margin-left: 33%; font-size: 1.7rem;">(No se encontraron resultados)</div';
      }
      /* liberar el conjunto de resultados */
      mysqli_free_result($resultado);
   }
  Close($link);
  return $salida;
}
function dashboard_disponibles($promo,&$count,$promo_info){
  $reg=0;
  $salida='';
  $promo_generica = $promo_info['ind_generico'];
  $promo_generica_max = $promo_info['max_generico'];
  $link=connect();
  if ($promo_generica==0) {
    $consulta ="SELECT codigo Cupon
                FROM   gtrd_cupones
                WHERE id_promo=".$promo." and estatus=0
                ORDER BY codigo";
    if ($resultado = mysqli_query($link, $consulta)) {
      $count  = mysqli_num_rows($resultado);
      while ($fila = mysqli_fetch_row($resultado)) {
           $reg++;
           $salida=$salida.'<div class="promoItemDash displayFlex">
                            <div><p class="promoItemDash_Brand"><input type="checkbox" class="cuponcheck" name="cuponcheck_'.$reg.'" value="'.$fila[0].'"> '.$fila[0].'</p></div>
                            </div>';
        }
        if($reg<1) {
          $salida='<div id="contentreport" style="margin-top: 8%;margin-left: 33%; font-size: 1.7rem;">(No se encontraron resultados)</div';
        }
        /* liberar el conjunto de resultados */
        mysqli_free_result($resultado);
     }
  } else {
    $count = cuponesDisponibles($link,$promo,$promo_generica,$promo_generica_max);
  }
  Close($link);
  return $salida;


  <?php if ($promo_generica == 0) { ?>
      <input type="checkbox" id="primeros" name="" value=""> Seleccionar los primeros <input  id="numerocupones" class="" style="width: 100px;" type="text" value="<?php echo $count2; ?>"/>
  <?php  } else { ?>
      <p class="descPromo" style="font-size: 1.3rem;">Código genérico: <?php echo $promo_generica_cod ?> Disponibles: <?php echo $count2 ?></p>
  <?php  }  ?>

}

//encrypt_decrypt('d','aTlCQkkyK1p2dHU5Z2pYY0NEcnN0UT09')
function getestatuspromo($link,$promo){
  $estatus=1;
  $query = "SELECT gtrd_promociones.estatus FROM gtrd_promociones where id=$promo";
  $result = mysqli_query($link, $query);
  while ($fila = mysqli_fetch_row($result)) {
        $estatus=$fila[0];
   }
  mysqli_free_result($result);
  return $estatus;
}
function dasboard_report($promo){
  $reg=0;
  $salida='';
  $link=connect();
  $consulta ="select gtrd_cupones.codigo Cupon,gtrd_cupones.fecha_entregado Entregado_El,gtrd_cupones.ip IPSolicitud, gtrd_cupones.pais,gtrd_estados.estado FROM gtrd_cupones  INNER JOIN gtrd_estados on gtrd_cupones.estado=gtrd_estados.codigo_estado WHERE id_promo=".$promo." and estatus=1 and gtrd_cupones.estado NOT IN ('ALL') union select gtrd_cupones.codigo Cupon,gtrd_cupones.fecha_entregado Entregado_El,gtrd_cupones.ip IPSolicitud, 'MX' pais,'CDMX' estado from gtrd_cupones WHERE (estado IS NULL OR estado IN ('ALL')) AND id_promo=".$promo." and estatus=1";
  if ($resultado = mysqli_query($link, $consulta)) {
    while ($fila = mysqli_fetch_row($resultado)) {
         $reg++;
         $salida=$salida.'<div class="promoItemDash displayFlex">
           <div>
             <p class="promoItemDash_Brand">'.$fila[0].'</p>
           </div>
           <div>
             <p class="promoItemDash_Validity">'.$fila[1].'</p>
           </div>
           <div>
             <p class="promoItemDash_Brand">'.$fila[2].'</p>
           </div>
           <div>
             <p class="promoItemDash_Brand">'.$fila[3].'</p>
           </div>
           <div>
             <p class="promoItemDash_Brand">'.$fila[4].'</p>
           </div>
           </div>';

      }
      if($reg<1)
      {
        $salida='<div id="contentreport" style="margin-top: 8%;margin-left: 33%; font-size: 1.7rem;">(No se encontraron resultados)</div';
      }
      /* liberar el conjunto de resultados */
      mysqli_free_result($resultado);

   }

  Close($link);
  return $salida;

}
function marcas(){
  $reg=0;
  $salida='<option class="brandPromo" value="">Selecciona una marca</option>';
  $link=connect();
  $consulta ="SELECT * FROM gtrd_marca";
  /* gtrd_proveedor
  <option class="brandPromo" value="">Selecciona una marca</option>
  <option class="brandPromo" value="">Pepsi</option>
  <option class="brandPromo" value="">Epura</option>
  <option class="brandPromo" value="">Gatorade</option>
  <option class="brandPromo" value="">Seven Up</option>
  <option class="brandPromo" value="">Lipton</option>
  */
  if ($resultado = mysqli_query($link, $consulta)) {
    while ($fila = mysqli_fetch_row($resultado)) {
         $salida=$salida.'<option class="brandPromo" value="'.encrypt_decrypt('e',$fila[0]).'">'.$fila[1].'</option>';
      }
      /* liberar el conjunto de resultados */
      mysqli_free_result($resultado);

   }

  Close($link);
  return $salida;

}
function proveedores(){
  $reg=0;
  $salida='  <option class="providerDefault" value="">Selecciona un Proveedor</option>';
  $link=connect();
  $consulta ="SELECT * FROM gtrd_proveedor";
  /* gtrd_proveedor
  <option class="providerDefault" value="">Selecciona un Proveedor</option>
  <option class="providerDefault" value="">OXXO</option>
  <option class="providerDefault" value="">Seven Eleven</option>
  */
  if ($resultado = mysqli_query($link, $consulta)) {
    while ($fila = mysqli_fetch_row($resultado)) {
         $salida=$salida.'<option class="providerDefault" value="'.encrypt_decrypt('e',$fila[0]).'">'.$fila[1].'</option>';
      }
      /* liberar el conjunto de resultados */
      mysqli_free_result($resultado);

   }

  Close($link);
  return $salida;

}
function funcionalidades($varconfig){
  $reg=0;
  $salida='';
  $link=connect();
  $consulta ="SELECT * FROM gtrd_funcionalidades";
  /*
  <div class="rowConfig displayFlex">
    <!-- Funcionalidad 1 -->
    <div class="containerRectW displayFlex">
      <p>Códigos de Descuento</p>
      <div class="containerRect">
        <div class="picRect" style="background-image:url('ui/img/covers/cupon.jpg')"></div>
        <div class="overRect">
          <p>Esta funcionalidad permite cargar una base de códigos de descuento que serán entregados a los usuarios que accedan a la promoción.</p>
          <a href="#" target="_blank"><span>Ver Más</span></a>
        </div>
      </div>
      <div class="selectionContainer">
        <label>Seleccionar</label>
        <input class="checkBoxFunction" name="" type="checkbox">
      </div>
    </div>
    <!-- Funcionalidad 2 -->
    <div class="containerRectW rectGrey displayFlex">
      <p class="opacityZero">Funcionalidad 2</p>
      <div class="containerRect">
        <div class="picRect"></div>
        <div class="overRect"></div>
      </div>
      <div class="selectionContainer opacityZero">
        <label>Seleccionar</label>
        <!-- <input id="" name="" type="checkbox"> -->
      </div>
    </div>
  </div>

  <div class="rowConfig hideOnMobile">
    <!-- Funcionalidad 3 -->
    <div class="containerRectW rectGrey displayFlex">
      <p class="opacityZero">Funcionalidad 3</p>
      <div class="containerRect"></div>
      <div class="selectionContainer opacityZero">
        <label>Seleccionar</label>
        <!-- <input id="" name="" type="checkbox"> -->
      </div>
    </div>
    <div class="containerRectW rectGrey displayFlex">
      <!-- Funcionalidad 4 -->
      <p class="opacityZero">Funcionalidad 4</p>
      <div class="containerRect"></div>
      <div class="selectionContainer opacityZero">
        <label>Seleccionar</label>
        <!-- <input id="" name="" type="checkbox"> -->
      </div>
    </div>
  </div>
  */
  if ($resultado = mysqli_query($link, $consulta)) {
    while ($fila = mysqli_fetch_row($resultado)) {
          $reg++;
         if ($reg%2==0)
         {
           $salida=$salida.'<div class="containerRectW displayFlex">
             <p>'.$fila[1].'</p>
             <div class="containerRect">
               <div class="picRect" style="background-image:url(\''.$fila[3].'\')"></div>
               <div class="overRect">
                 <p>'.$fila[2].'</p>
                 <a href="#" target="_blank"><span>Ver Más</span></a>
               </div>
             </div>
             <div class="selectionContainer">
               <label>Seleccionar</label>
               <input '.$varconfig.' onclick="uncheckedfunctionall(this)" class="checkBoxFunction" value="'.encrypt_decrypt('e',$fila[0]).'" name="" type="checkbox">
             </div>
           </div>
           </div>';
         }
         else {
           $salida=$salida.'<div class="rowConfig displayFlex">
             <div class="containerRectW displayFlex">
               <p>'.$fila[1].'</p>
               <div class="containerRect">
                 <div class="picRect" style="background-image:url(\''.$fila[3].'\')"></div>
                 <div class="overRect">
                   <p>'.$fila[2].'</p>
                   <a href="#" target="_blank"><span>Ver Más</span></a>
                 </div>
               </div>
               <div class="selectionContainer">
                 <label>Seleccionar</label>
                 <input '.$varconfig.' onclick="uncheckedfunctionall(this)" class="checkBoxFunction" value="'.encrypt_decrypt('e',$fila[0]).'" name="" type="checkbox">
               </div>
             </div>';
         }
      }

      if($reg%2!=0)
      {
          $salida=$salida.'<div class="containerRectW rectGrey displayFlex">
            <p class="opacityZero">Funcionalidad 2</p>
            <div class="containerRect">
              <div class="picRect"></div>
              <div class="overRect"></div>
            </div>
            <div class="selectionContainer opacityZero">
              <label>Seleccionar</label>
              <!-- <input id="" name="" type="checkbox"> -->
            </div>
          </div>
        </div>';
      }
      if($reg<3)
      {
          $salida=$salida.'<div class="rowConfig hideOnMobile">
            <!-- Funcionalidad 3 -->
            <div class="containerRectW rectGrey displayFlex">
              <p class="opacityZero">Funcionalidad 3</p>
              <div class="containerRect"></div>
              <div class="selectionContainer opacityZero">
                <label>Seleccionar</label>
                <!-- <input id="" name="" type="checkbox"> -->
              </div>
            </div>
            <div class="containerRectW rectGrey displayFlex">
              <!-- Funcionalidad 4 -->
              <p class="opacityZero">Funcionalidad 4</p>
              <div class="containerRect"></div>
              <div class="selectionContainer opacityZero">
                <label>Seleccionar</label>
                <!-- <input id="" name="" type="checkbox"> -->
              </div>
            </div>
          </div>';
      }
      /* liberar el conjunto de resultados */
      mysqli_free_result($resultado);

   }

  Close($link);
  return $salida;

}
function plantillas($funcionalidad,$dis){
  $reg=0;
  $salida='';

  /*
  <div class="rowConfig displayFlex">
    <!-- Plantilla 1 -->
    <div class="containerRectW displayFlex">
      <p>Plantilla Entrega de Cupón 1</p>
      <div class="containerRect">
        <div class="picRect" style="background-image:url('ui/img/covers/plantilla-1.jpg')"></div>
        <div class="overRect displayFlex">
          <a href="#" target="_blank"><span>Ver Plantilla</span></a>
        </div>
      </div>
      <div class="selectionContainer">
        <label>Seleccionar</label>
        <input class="checkBoxTheme" id="" name="" type="checkbox">
      </div>
    </div>
    <!-- Plantilla 2 -->
    <div class="containerRectW rectGrey displayFlex">
      <p class="opacityZero">Plantilla 2 Producto</p>
      <div class="containerRect"></div>
      <div class="selectionContainer opacityZero">
        <label>Seleccionar</label>
        <!-- <input id="" name="" type="checkbox"> -->
      </div>
    </div>
  </div>
  <div class="rowConfig hideOnMobile">
    <!-- Plantilla 3 -->
    <div class="containerRectW rectGrey displayFlex">
      <p class="opacityZero">Plantilla 1 Producto</p>
      <div class="containerRect"></div>
      <div class="selectionContainer opacityZero">
        <label>Seleccionar</label>
        <!-- <input id="" name="" type="checkbox"> -->
      </div>
    </div>
    <!-- Plantilla 4 -->
    <div class="containerRectW rectGrey displayFlex">
      <p class="opacityZero">Plantilla 2 Producto</p>
      <div class="containerRect"></div>
      <div class="selectionContainer opacityZero">
        <label>Seleccionar</label>
        <!-- <input id="" name="" type="checkbox"> -->
      </div>
    </div>
  </div>
  <button class="buttonConfig leftButton" type="button" name="button" onclick="sliderConfigFun(2)">Anterior</button>
  <button class="buttonConfig rightButton" type="button" name="button" onclick="checkSteps(4, this)">Siguiente</button>



  */

  $link=connect();
  if($funcionalidad == NULL) {
  $consulta ="SELECT * FROM gtrd_plantilla";
  }
  else {
    $consulta="SELECT * FROM gtrd_plantilla where id_funcionalidad=".$funcionalidad;
  }
  if ($resultado = mysqli_query($link, $consulta)) {
    while ($fila = mysqli_fetch_row($resultado)) {
          $reg++;
         if ($reg%2==0)
         {
           $salida=$salida.'<div class="containerRectW displayFlex">
             <p>'.$fila[2].'</p>
             <div class="containerRect">
               <div class="picRect" style="background-image:url(\''.$fila[6].'\')"></div>
               <div class="overRect">
                 <p>'.$fila[3].'</p>
                 <a href="#" target="_blank"><span>Ver Más</span></a>
               </div>
             </div>
             <div class="selectionContainer">
               <label>Seleccionar</label>
               <input '.$dis.' onclick="uncheckedthemeall(this)" class="checkBoxTheme" value="'.encrypt_decrypt('e',$fila[0]).'" name="" type="checkbox">
             </div>
           </div>
           </div>';
         }
         else {
           $salida=$salida.'<div class="rowConfig displayFlex">
             <div class="containerRectW displayFlex">
               <p>'.$fila[2].'</p>
               <div class="containerRect">
                 <div class="picRect" style="background-image:url(\''.$fila[6].'\')"></div>
                 <div class="overRect">
                   <p>'.$fila[3].'</p>
                   <a href="#" target="_blank"><span>Ver Más</span></a>
                 </div>
               </div>
               <div class="selectionContainer">
                 <label>Seleccionar</label>
                 <input '.$dis.' onclick="uncheckedthemeall(this)" class="checkBoxTheme" value="'.encrypt_decrypt('e',$fila[0]).'" name="" type="checkbox">
               </div>
             </div>';
         }
      }

      if($reg%2!=0)
      {
          $salida=$salida.'<div class="containerRectW rectGrey displayFlex">
            <p class="opacityZero">Funcionalidad 2</p>
            <div class="containerRect">
              <div class="picRect"></div>
              <div class="overRect"></div>
            </div>
            <div class="selectionContainer opacityZero">
              <label>Seleccionar</label>
              <!-- <input id="" name="" type="checkbox"> -->
            </div>
          </div>
        </div>';
      }
      if($reg<3)
      {
          $salida=$salida.'<div class="rowConfig hideOnMobile">
            <!-- Funcionalidad 3 -->
            <div class="containerRectW rectGrey displayFlex">
              <p class="opacityZero">Funcionalidad 3</p>
              <div class="containerRect"></div>
              <div class="selectionContainer opacityZero">
                <label>Seleccionar</label>
                <!-- <input id="" name="" type="checkbox"> -->
              </div>
            </div>
            <div class="containerRectW rectGrey displayFlex">
              <!-- Funcionalidad 4 -->
              <p class="opacityZero">Funcionalidad 4</p>
              <div class="containerRect"></div>
              <div class="selectionContainer opacityZero">
                <label>Seleccionar</label>
                <!-- <input id="" name="" type="checkbox"> -->
              </div>
            </div>
          </div>';
      }

      $salida=$salida.'<button class="buttonConfig leftButton" type="button" name="button" onclick="sliderConfigFun(2)">Anterior</button>
        <button class="buttonConfig rightButton" type="button" name="button" onclick="checkSteps(4, this)">Siguiente</button>';
      /* liberar el conjunto de resultados */
      mysqli_free_result($resultado);

   }

  Close($link);
  return $salida;

}
function createfile($data,$name){
  $res='No inicio';
  if(!empty($data)){
    $data1 = substr($data1,strpos($data1, ",") + 1);
   // decode it
  $decodedData = base64_decode($data);
  $filename = $name;
  // write the data out to the file
  $fp = fopen("legales/temp_".$filename, 'wb');
  fwrite($fp, $decodedData);
  fclose($fp);
  $res= "legales/temp_".$filename;
}
else {
    $res= 'No se guardo';
}
return $res;
}

function insertageneral($fi,$ff,$nom,$desc,$mar,$pro,$idnvaprom,$tagmg){
  $username = $_SESSION["Nombre"];
  $salida='';
  $link=connect();
  mysqli_autocommit($link, FALSE);
  $marcade=encrypt_decrypt('d',$mar);
  $prodec=encrypt_decrypt('d',$pro);
  if($idnvaprom>0)
  {
    $consulta ="update gtrd_promociones set nombre='".$nom."',descripcion='".$desc."',id_marca=".$marcade.",id_proveedor=".$prodec.",fecha_inicio='".$fi." 00:00:01',fecha_fin='".$ff." 23:59:59', fecha_update = now(),codigo_tagmanager='".$tagmg."', usuario='".$username."' where id=".$idnvaprom;
  }
    else {
      $consulta ="insert into gtrd_promociones(nombre,descripcion,id_marca,id_proveedor,fecha_inicio,fecha_fin,estatus,codigo_tagmanager,usuario,fecha_update) VALUES('".$nom."','".$desc."',".$marcade.",".$prodec.",'".$fi." 00:00:01','".$ff." 23:59:59',2,'".$tagmg."','".$username."',now())";
    }

  if (mysqli_query($link, $consulta)) {
    if($idnvaprom>0){
      $salida=$idnvaprom;
    }
      else {
        $salida=mysqli_insert_id($link);
      }

   }
   else {
     $salida=$consulta.'fallo sql insert';
   }
   if($idnvaprom>0)
   {
     $consulta1 ="ya existian estados";
   }
     else {
       $consulta1 ="insert into gtrd_promociones_estados(id_promo,id_estado) VALUES(".$salida.",33),(".$salida.",34)";
       if (mysqli_query($link, $consulta1)) {
            //creadirectoriopromo($salida,$nom);
        }
        else {

        }
     }


   mysqli_commit($link);
  Close($link);
  return $salida;
}
function actualizalegales($id,$url){
  $salida="";
  $link=connect();
  mysqli_autocommit($link, FALSE);
  $consulta ="update gtrd_promociones SET archivo_legales='".$url."' WHERE id=".$id;
  if (mysqli_query($link, $consulta)) {
      $salida="success";
   }
   else {
     $salida="error";
   }
   mysqli_commit($link);

  Close($link);
  return $consulta;
}
function actualizafuncionalidad($id,$prom){
  $salida="";
  $link=connect();
  mysqli_autocommit($link, FALSE);
  $consulta ="update gtrd_promociones SET id_funcionalidad=".encrypt_decrypt('d',$id)." WHERE id=".$prom;
  if (mysqli_query($link, $consulta)) {
      $salida="success";
   }
   else {
     $salida="error";
   }
   mysqli_commit($link);

  Close($link);
  return $consulta;
}
function actualizaplantillabd($id,$prom){
  $salida="";
  $link=connect();
  mysqli_autocommit($link, FALSE);
  $consulta ="update gtrd_promociones SET id_plantilla=".encrypt_decrypt('d',$id).",version=0 WHERE id=".$prom;
  if (mysqli_query($link, $consulta)) {
      $salida="success";
   }
   else {
     $salida="error";
   }
   mysqli_commit($link);

  Close($link);
  if($salida=="success")
  {
    $consulta=getpromocioneditdata($prom);
  }

  return $consulta;
}
function existecupon($id,$prom)
{
  $reg=0;
  $salida='';
  $link=connect();
  $consulta ="select codigo FROM gtrd_cupones where id_promo=".$prom." and codigo in (".$id.")";
  /* gtrd_proveedor
  <option class="providerDefault" value="">Selecciona un Proveedor</option>
  <option class="providerDefault" value="">OXXO</option>
  <option class="providerDefault" value="">Seven Eleven</option>
  */
  if ($resultado = mysqli_query($link, $consulta)) {
    while ($fila = mysqli_fetch_row($resultado)) {
        $salida=$salida.$fila[0].',';
      }
      /* liberar el conjunto de resultados */
      mysqli_free_result($resultado);

   }

  Close($link);
  return $salida;

}
function loadcupons($id,$prom)
{
  $salida='';
  $link=connect();
  $cupones = explode(",", $id);
  $cuponesstring=str_replace('\'', '', $id);
  $cuponesarray = explode(",", $cuponesstring);
  $stradd=",".$prom.",0\n";
  $txt=join($stradd,$cuponesarray);
  $stradd2=",".$prom.",0".PHP_EOL;
  $txt=$txt.$stradd2;
  $path=writetxtcupons($txt,$prom);
  $fullpath=dirname(__FILE__,3);
  $fullpath=$fullpath.'/'.$path;
   $inserted="LOAD DATA LOCAL INFILE '".$fullpath."' INTO TABLE gtrd_cupones
        FIELDS TERMINATED BY ','
       LINES TERMINATED BY '\\n'
        (codigo,id_promo,estatus)";
  mysqli_autocommit($link, FALSE);
  if ($resultadoins = mysqli_query($link, $inserted)) {

  }
   mysqli_commit($link);
   $salida=$fullpath;
  mysqli_free_result($resultadoins);
  Close($link);
  return $salida;

}
function getmarca_redessocialesimgchange($idmarca,$idplantilla,$version)
{
  $link=connect();
  $resultado = null;
  //$consulta = "SELECT * FROM gtrd_marca_redessociales WHERE id_marca = ".$idmarca." AND activo =1 ";

  $consulta = "SELECT a.url, a.nombre, b.valor_componente logo,a.codigo
                FROM gtrd_marca_redessociales a
               LEFT JOIN gtrd_plantilla_config_producto b ON  a.codigo = b.id_componente  AND b.id_plantilla = ".$idplantilla." AND a.id_marca = b.id_marca AND b.version = ".$version." AND b.producto = 1
                WHERE a.id_marca = ".$idmarca." AND a.activo =1";

  if ($registros = mysqli_query($link, $consulta)) {
    while ($fila = mysqli_fetch_array($registros)) {
        $resultado .= $fila['nombre'].'?'.$fila['codigo'].'?'.$fila['logo'].'|';
     }
  }

  Close($link);
  return $resultado;
}
function getmarca_redessocialesinterfaz($idmarca)
{
  $link=connect();
  $resultado = null;
  $consulta = "SELECT * FROM gtrd_marca_redessociales WHERE id_marca = ".$idmarca." AND activo =1";

  if ($registros = mysqli_query($link, $consulta)) {
    while ($fila = mysqli_fetch_array($registros)) {
        switch($fila['nombre'])
        {
          case 'Facebook':
          $label='labeFb';
          $file='unoFb';
          break;
          case 'Twitter':
          $label='labeTw';
          $file='unoTw';
          break;
          case 'Instagram':
          $label='labeIg';
          $file='unoIg';
          break;
          case 'Youtube':
          $label='labeYt';
          $file='unoYt';
          break;
        }
        $resultado .= '<li>
        <label for="'.$file.'" id="'.$label.'" class="bordersetup colorBorderWhite">
          <p class="title backWhite colorBlack">'.$fila['nombre'].'</p>
          <div class="hoverLabel displayFlex trans5" style="background:rgba(0,0,0,0.8)">
            <svg viewBox="0 0 30 30"> <g> <path class="uploadFillWhite" d="M0.6,26c0-3,0-6,0-9.1c0.9,0,1.8,0,2.7,0c0,2.1,0,4.2,0,6.4c7.8,0,15.6,0,23.4,0c0-2.1,0-4.2,0-6.4 c0.9,0,1.8,0,2.7,0c0,3,0,6,0,9.1C19.8,26,10.2,26,0.6,26z"/> <path class="uploadFillWhite" d="M7.9,10.9c0.3-0.3,0.6-0.5,0.8-0.8c2-2,4-4,6-6c0.2-0.2,0.3-0.2,0.5,0c2.2,2.2,4.4,4.4,6.6,6.6 c0.1,0.1,0.1,0.1,0.2,0.2c-0.1,0.1-0.1,0.1-0.2,0.2c-0.5,0.5-1,1-1.5,1.5c-0.2,0.2-0.3,0.1-0.4,0c-1.1-1.1-2.2-2.2-3.2-3.2 c-0.1-0.1-0.2-0.1-0.3-0.3c0,0.2,0,0.3,0,0.5c0,4,0,7.9,0,11.9c0,0.3-0.1,0.3-0.3,0.3c-0.8,0-1.5,0-2.3,0c0-4.2,0-8.5,0-12.8 c-0.1,0.1-0.2,0.2-0.3,0.3c-1.1,1.1-2.2,2.1-3.2,3.2c-0.2,0.2-0.3,0.2-0.5,0C9.1,12,8.5,11.5,7.9,10.9z"/> </g> </svg>
          </div>
        </label>
        <input id="'.$file.'" type="file" onchange="updateimageplantilla(this,32,\'ic'.$fila['nombre'].'\',\'ui/img/ic/\')" class="hideInput">
      </li>';
     }
  }

  Close($link);
  return $resultado;
}
function getpromocioneditdata($idpromo)
{
  $result;

  $promo = getpromocion($idpromo);
  $promo_nombre             = $promo['promo_nombre'];
  $producto                 = $promo['producto'];
  $descripcion              = $promo['promo_descripcion'];
  $marca_id                 = $promo['id_marca'];
  $plantilla_id             = $promo['id_plantilla'];
  $promo_legales            = $promo['archivo_legales'];
  $promo_version            = $promo['version'];
  $estatus                  = $promo['estatus'];
  $proveedor_id             = $promo['id_proveedor'];
  $id_funcionalidad         = $promo['id_funcionalidad'];
  $fecha_inicio             = $promo['fecha_inicio'];
  $fecha_fin                = $promo['fecha_fin'];
  $dir_promo                = $promo['dir'];
  $codigo_tagmanager        = $promo['codigo_tagmanager'];
  $promo_generica           = $promo['ind_generico'];
  $promo_generica_max       = $promo['max_generico'];

  $plantilla = getplatilla($marca_id,$promo_version,$plantilla_id,1,$proveedor_id);
  $marca                    = $plantilla['marca_codigo'];
  $marca_descripcion        = $plantilla['marca_descripcion'];
  $marca_logo               = 'img_logomarca?'.$plantilla['marca_logo'];
  $proveedor_logo           = 'img_logoproveedor?'.$plantilla['proveedor_logo'];
  $promo_img_back           = 'img_back?'.$plantilla['promo_img_back'];
  $promo_img_prod           = 'img_prod?'.$plantilla['promo_img_prod'];
  $promo_font               = 'font?'.$plantilla['promo_font'];
  $promo_color              = 'color?'.$plantilla['promo_color'];
  $promo_color_load         = 'color_load?'.$plantilla['promo_color_load'];
  $promo_txt_footer         = 'txt_footer?'.$plantilla['promo_txt_footer'];
  $promo_img_inicio         = 'img_inicio?'.$plantilla['promo_img_inicio'];
  $promo_img_precio         = 'img_precio?'.$plantilla['promo_img_precio'];
  $promo_img_obtenercupon     = 'img_obtenercupon?'.$plantilla['promo_img_obtenercupon'];
  $promo_img_cupon          = 'img_cupon?'.$plantilla['promo_img_cupon'];
  $promo_img_descargarcupon = 'img_descargarcupon?'.$plantilla['promo_img_descargarcupon'];
  $promo_img_exito          = 'img_exito?'.$plantilla['promo_img_exito'];
  $promo_img_hashtag        = 'img_hashtag?'.$plantilla['promo_img_hashtag'];
  $promo_img_error          = 'img_error?'.$plantilla['promo_img_error'];
  $interfazmarca            = getmarca_redessocialesinterfaz($marca_id);
  $plantillamarca           = getmarca_redessociales($marca_id,$plantilla_id,$promo_version);
  $plantillamarcaimg        = getmarca_redessocialesimgchange($marca_id,$plantilla_id,$promo_version);
  $link                     = connect();
  $disponibles              = cuponesDisponibles($link,$idpromo,$promo_generica,$promo_generica_max);
  Close($link);

  $result=$promo_nombre.'&@;'.$producto.'&@;'.$descripcion.'&@;'.encrypt_decrypt('e',$marca_id).'&@;'.encrypt_decrypt('e',$plantilla_id);
  $result.='&@;'.$promo_legales.'&@;'.$promo_version.'&@;'.$estatus.'&@;'.encrypt_decrypt('e',$proveedor_id);
  $result.='&@;'.encrypt_decrypt('e',$id_funcionalidad).'&@;'.$fecha_inicio.'&@;'.$fecha_fin.'&@;'.$marca;
  $result.='&@;'.$marca_descripcion.'&@;'.$marca_logo.'&@;'.$proveedor_logo.'&@;'.$promo_img_back;
  $result.='&@;'.$promo_img_prod.'&@;'.$promo_font.'&@;'.$promo_color.'&@;'.$promo_color_load.'&@;'.$promo_txt_footer;
  $result.='&@;'.$promo_img_inicio.'&@;'.$promo_img_precio.'&@;'.$promo_img_obtenercupon;
  $result.='&@;'.$promo_img_cupon.'&@;'.$promo_img_descargarcupon.'&@;'.$promo_img_exito;
  $result.='&@;'.$promo_img_hashtag.'&@;'.$promo_img_error.'&@;'.$interfazmarca;
  $result.='&@;'.$plantillamarca.'&@;'.$plantillamarcaimg.'&@;'.$dir_promo.'&@;'.$disponibles.'&@;'.$codigo_tagmanager;
  return $result;
}
function actualizaplantillaversion($updcre,$data)
{
  $salida='';
  $link=connect();
  $arraydata=explode(',',$data);
  $marca=encrypt_decrypt('d',explode('-',$arraydata[0])[0]);
  $idpromo=encrypt_decrypt('d',explode('-',$arraydata[0])[1]);
  $plantilla=encrypt_decrypt('d',$arraydata[1]);
  $version=$arraydata[2];
  $nvomax=0;
  if($updcre!='update')
  {
  mysqli_autocommit($link, FALSE);
  $maximo="SELECT version FROM gtrd_secuencias_version where id_plantilla=".$plantilla." and id_marca=".$marca." and producto=1 LIMIT 1 FOR UPDATE;";
  if ($registrosv = mysqli_query($link, $maximo)) {
    while ($fila = mysqli_fetch_array($registrosv)) {
        $nvomax=$fila[0]+1;
         $maximoupd="update gtrd_secuencias_version set version=".$nvomax." where id_plantilla=".$plantilla." and id_marca=".$marca." and producto=1";
         if (mysqli_query($link, $maximoupd)) {
              mysqli_commit($link);
           }
             else {
             }

          }
     }
 }
 else {
   $nvomax=$version;
 }
  if($nvomax!=0)
  {
    $salida=''.$nvomax;
  mysqli_autocommit($link, FALSE);
  $count=count($arraydata);
  for ($i=3;$i<$count;$i++) {
    $clv=explode('-',$arraydata[$i]);
    if(count($clv)>2)
    {
      $idcomp=$clv[0];
      unset($clv[0]);
      $valcomp=implode("-", $clv);
    }
    else {
      $idcomp=$clv[0];
      $valcomp=$clv[1];
    }

    if($updcre=='update')
    {
      $consulta ="update gtrd_plantilla_config_producto set valor_componente='".$valcomp."' where id_plantilla=".$plantilla." and id_marca=".$marca." and version=".$version." and producto=1 and id_componente='".$idcomp."'";
    }
      else {
        $consulta ="insert into gtrd_plantilla_config_producto(id_plantilla,id_marca,version,producto,id_componente,valor_componente) VALUES(".$plantilla.",".$marca.",".$nvomax.",1,'".$idcomp."','".$valcomp."')";
      }

    if (mysqli_query($link, $consulta)) {
      $salida.='Exito:'.$idcomp.'-'.$valcomp.',';
     }
     else {
       $salida.='Fallo:'.$consulta.mysqli_error($link).$idcomp.'-'.$valcomp.',';
     }
  }

  $consultapromo ="update gtrd_promociones set version=".$nvomax." where id=".$idpromo;
  if (mysqli_query($link, $consultapromo)) {
    $salida.='Exito:'.$consultapromo;
   }
   else {
     $salida.='Fallo:'.$consultapromo;
   }
  mysqli_commit($link);
}
else {
  $salida='No se pudo obtener el siguiente';
}


  Close($link);
  return $salida;
}

function actualizarstatus($id,$st){
  $salida   = "";
  $username = $_SESSION["Nombre"];

  $link=connect();
  mysqli_autocommit($link, FALSE);
  $consulta ="update gtrd_promociones SET estatus=".$st.", fecha_update = now(), usuario = '".$username."' WHERE id=".$id;
  if (mysqli_query($link, $consulta)) {
      $salida="success";
   }
   else {
     $salida="error";
   }
  mysqli_commit($link);
  Close($link);
  return $salida;
}

function eliminarpromo($id){
  $salida="";
  actualizarstatus($id,4); /* Cambiar a estatus = 4 (eliminado)*/
  return $consulta;
}

function creaactualizadir($idpromo,$dir)
{
    $dominio = getdominio();
    $link=connect();
    $idpromodecryp=encrypt_decrypt('d',$idpromo);
    $resultado = null;
    $consulta = "SELECT dir FROM gtrd_promociones WHERE id = ".$idpromodecryp;

    if ($registros = mysqli_query($link, $consulta)) {
      while ($fila = mysqli_fetch_array($registros)) {
          if(empty($fila["dir"]))
          {

            $salida   = "";
            $username = $_SESSION["Nombre"];
            $dirname=creadirectoriopromo($idpromo,$dir,$dominio);
            mysqli_autocommit($link, FALSE);
            $consulta ="update gtrd_promociones SET dir='".$dirname."', fecha_update = now(), usuario = '".$username."' WHERE id=".$idpromodecryp;
            if (mysqli_query($link, $consulta)) {
                $salida="success".$consulta.$dirname;
             }
             else {
               $salida="error".$consulta;
             }
            mysqli_commit($link);

          }
       }
    }

    Close($link);
    return $salida;
}

function getdominio()
{
  $result="https://siguesudando.com";  /* por defecto */
  $link=connect();

  $query = "SELECT value FROM gtrd_settings WHERE Module='Config' AND  setting = 'dominio'";
  $registros = mysqli_query($link, $query);
  while ($fila = mysqli_fetch_array($registros)) {
        $result=$fila['value'];
  }
  Close($link);
  return $result;
}

function getproveedordata($idproveedor)
{

  $count=0;
  $link=connect();
  $resultado = null;
  $consulta = "SELECT * FROM gtrd_proveedor where id=".$idproveedor;

  if ($registros = mysqli_query($link, $consulta)) {
    while ($fila = mysqli_fetch_array($registros)) {
        $resultado = $fila[0].'&@'. $fila[1].'&@'. $fila[2];
     }
  }
  Close($link);
  return $resultado;
}

function checkusersession($huella,$name)
{
  $link=connect();
  $resultado = null;
  $consulta = "select observa,fecha_update,TIME_TO_SEC(	TIMEDIFF(DATE_ADD(IFNULL(fecha_update,NOW()),INTERVAL 1 HOUR),NOW())) segundos
                 from gtrd_settings where Module='Admin' and setting='".$name."'";

  if ($registros = mysqli_query($link, $consulta)) {
    while ($fila = mysqli_fetch_array($registros)) {
        if($fila[0]!=''&&$fila[0]!=null)
        {
          if($fila[0]==$huella)
          {
            $resultado='success';
          }
          else {
            if($fila[1]!=''&&$fila[1]!=null)
            {
              if($fila[2]<=0)
              {
                $resultado='success';
              }
              else {
                $resultado='Existe otra sesión abierta con este usuario. Favor verificar.';
              }
            }
            else {
              $resultado='success';
            }
          }
        }
        else {
          $resultado='success';
        }
     }
  }
  Close($link);
  return $resultado;
}

function updateusersession($huella,$name)
{
  $salida="";
  $link=connect();
  mysqli_autocommit($link, FALSE);
  $consulta ="update gtrd_settings SET observa='".$huella."',fecha_update=NOW() WHERE Module='Admin' and setting='".$name."' LIMIT 1";
  if (mysqli_query($link, $consulta)) {
      $salida="success";
   }
   else {
     $salida="error";
   }
   mysqli_commit($link);

  Close($link);
  return $salida;
}

function updateusersessionclose($name)
{
  $salida="";
  $link=connect();
  mysqli_autocommit($link, FALSE);
  $consulta ="update gtrd_settings SET observa=null,fecha_update=null WHERE Module='Admin' and setting='".$name."' LIMIT 1";
  if (mysqli_query($link, $consulta)) {
      $salida="success";
   }
   else {
     $salida="error";
   }
   mysqli_commit($link);

  Close($link);
  return $salida;
}

function liberarcupones($id,$cupones)
{
  $cupones_query = "";
  $cupones_arr = explode (",", $cupones);
  for ($i = 0; $i < sizeof($cupones_arr); $i++) {
    if ($i < sizeof($cupones_arr) - 1) { $cupones_query .= "'".$cupones_arr[$i]."',"; } else { $cupones_query .= "'".$cupones_arr[$i]."'";}
  }

  $link=connect();
  mysqli_autocommit($link, FALSE);
  $consulta ="DELETE FROM gtrd_cupones WHERE id_promo=".$id." and estatus =0 and codigo in (".$cupones_query.")";

  if (mysqli_query($link, $consulta)) {
      //$salida="success";
      /* generar archivo con los cupones eliminados */
      $salida=writetxtcupons_liberados($id,$cupones_arr);
  } else {
     $salida="error";
  }
  mysqli_commit($link);
  Close($link);

  return $salida;
}



?>
