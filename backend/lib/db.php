<?php
date_default_timezone_set('America/Mexico_City');

require_once('conexion.php');
require_once('funciones.php');
require_once('barcode.php');


//mysql_set_charset('utf8');
//Inicio Gatorade
function validafechas(&$cad,$promo)
{
  $reg;
  $contador=0;
  $link=connect();
  $consulta = "select 'fecha_inicio',fecha_inicio,NOW(),TIME_TO_SEC(TIMEDIFF(NOW(), fecha_inicio)) valor from gtrd_promociones where id=".$promo." union select 'fecha_fin',fecha_fin,NOW(),TIME_TO_SEC(TIMEDIFF(NOW(), fecha_fin)) valor from gtrd_promociones where id=".$promo;
  if ($resultado = mysqli_query($link, $consulta)) {
   while ($fila = mysqli_fetch_row($resultado)) {
     $reg[$contador]=$fila[3];
     $cad[$contador]=$fila[1];
     $contador++;
    }
   /* liberar el conjunto de resultados */
    mysqli_free_result($resultado);
  }
  Close($link);
  return $reg;
}
function validaregion($idprom,$ip,$link)
{
  $count=0;
  $consulta = "SELECT * from gtrd_promociones_estados where id_promo=".$idprom.";";
  if ($resultado = mysqli_query($link, $consulta)) {
    while ($fila = mysqli_fetch_row($resultado)) {
        $count++;
     }
     if($count<1)
     {
       //getcupon($link,$ip,$idClient,$idprom);
       //Es valido
       echo "SI";
     } else {
        promvalidestado($ip,$idprom,$link);
     }
  }
  else {
    echo 'ERROR';
  }
}
function validalista($idprom,$ip)
{
  $count=0;
  $link=connect();
  $consulta = "SELECT * from gtrd_listanegra where ip='".$ip."';";
  if ($resultado = mysqli_query($link, $consulta)) {
    while ($fila = mysqli_fetch_row($resultado)) {
        $count++;
        echo '<nav id="menu" class="flexDisplay trans7" style="opacity: 1;>
          <h1>
            <a href="index.php"> <!-- CAMBIAR!!!!! -->
              <img src="/ui/img/logotipo-gatorade.svg" alt="Gatorade ®| Sigue Sudando | Promociones" title="Gatorade ®| Sigue Sudando | Promociones" width="60px">
            </a>
          </h1>
          <p id="stateText"></p>
          <div id="blk"></div>
        </nav><div id="horasDiv" class="mensaje standarWidth" style="display:block">
          <div class="flexDisplay">
            <p>
              <span>¡Ups!</span>
            </p>
            <p>
              La promoción no es válida <span>para tu ubicación</span>
            </p>
            <div id="social" class="flexDisplay socialWidth">
              <a href="https://www.facebook.com/GatoradeMexico/" target="_blank">
                <img src="/ui/img/social/fb.svg" width="50" height="50">
              </a>
              <a href="https://www.instagram.com/gatorademexico/" target="_blank">
                <img src="/ui/img/social/ig.svg" width="50" height="50">
              </a>
              <a class="whatsapp" href="whatsapp://send?text=https://siguesudando.com" data-action="share/whatsapp/share" style="display:none">
                <img src="/ui/img/social/wspp.svg" width="50" height="50">
              </a>
            </div>
          </div>
        </div><footer id="footer" class="flexDisplay trans7" style="opacity: 1;">
          <a class="flexDisplay" href="terminos-condiciones.html" target="_blank">Consulta Bases, Términos y Condiciones</a>
          <p><span>  |  </span>Hidrátate sanamente | ® Marca Registrada </p>
        </footer>';
     }
     if($count<1)
     {
       //getcupon($link,$ip,$idClient,$idprom);
       //Es valido
       validaregion($idprom,$ip,$link);
     }
  }
  else {
    echo 'ERROR';
  }
  Close($link);
}
function promvalidestado($ip,$idprom,$link)
{
  $salida          = 0;
  $country_code    = '';
  $ip_address      = $ip;
  $country_name         = 'Local';
  $country_city    = '';
  $country_region  = '';
  $estado='';
  $codpais='';
  $count=0;
  //$salida = get_country_local($country_code,$ip_address,$lang,$country_name,$id_group); // busqueda en BD local
  //if ($salida==0) {
  $salida = get_country_api($country_code,$ip_address,$country_region,$codpais); // busqueda en api de google
  $estado=$country_region;//equivalencia_estados_api($country_code,$region);
  //$query2 = "SELECT * from gtrd_promociones_estados where id_promo=".$promo." and estado='".$estado."' and  pais='".$country_code."';";
  $query2="select * from gtrd_promociones_estados a inner join gtrd_estados b on a.id_estado=b.id where (b.codigo_estado='".$estado."' or b.codigo_estado='ALL') and b.pais='".$codpais."' and id_promo=".$idprom.";";
  if ($resultado = mysqli_query($link, $query2)) {
    while ($fila = mysqli_fetch_row($resultado)) {
      $count++;
      //getcupon($link,$ip,$idClient,$idprom);
      //Es valido
      echo "SI";
    }
    if($count<1)
    {

      $date= date("Y-m-d H:i:s");
      $comple='IP:['.$ip.'] PAIS:['.$codpais.']  ESTADO:['.$country_region.']  Fecha['.$date.'] Ejecucion:[La promoción no es válida para tu ubicación]';
      writelog($comple);

      echo '<nav id="menu" class="flexDisplay trans7" style="opacity: 1;>
        <h1>
          <a href="index.php"> <!-- CAMBIAR!!!!! -->
            <img src="/ui/img/logotipo-gatorade.svg" alt="Gatorade ®| Sigue Sudando | Promociones" title="Gatorade ®| Sigue Sudando | Promociones" width="60px">
          </a>
        </h1>
        <p id="stateText"></p>
        <div id="blk"></div>
      </nav><div id="horasDiv" class="mensaje standarWidth" style="display:block">
        <div class="flexDisplay">
          <p>
            <span>¡Ups!</span>
          </p>
          <p>
            La promoción no es válida <span>para tu ubicación</span>
          </p>
          <div id="social" class="flexDisplay socialWidth">
            <a href="https://www.facebook.com/GatoradeMexico/" target="_blank">
              <img src="/ui/img/social/fb.svg" width="50" height="50">
            </a>
            <a href="https://www.instagram.com/gatorademexico/" target="_blank">
              <img src="/ui/img/social/ig.svg" width="50" height="50">
            </a>
            <a class="whatsapp" href="whatsapp://send?text=https://siguesudando.com" data-action="share/whatsapp/share" style="display:none">
              <img src="/ui/img/social/wspp.svg" width="50" height="50">
            </a>
          </div>
        </div>
      </div><footer id="footer" class="flexDisplay trans7" style="opacity: 1;">
        <a class="flexDisplay" href="terminos-condiciones.html" target="_blank">Consulta Bases, Términos y Condiciones</a>
        <p><span>  |  </span>Hidrátate sanamente | ® Marca Registrada </p>
      </footer>';
    }
  }
  else {
     echo 'ERROR';
  }
}
function getcupon($client,$idClient,$promo)
{
  $count=0;
  $link=connect();
  $query1 = "SELECT ((TIME_TO_SEC(TIMEDIFF(NOW(), fecha_entregado))>(1*1*1)) and ('".$client."'  not in (select ip from gtrd_listanegra))) Entregar,TIMEDIFF(NOW(), fecha_entregado) TiempoTranscurrido,TIMEDIFF( TIMEDIFF('2018-08-01 00:00:00', '2018-07-31 00:00:00'),TIMEDIFF(NOW(), fecha_entregado)) TiempoRestante from gtrd_cupones where estatus=1 and ip='".$client."' and huella_digital='".$idClient."' and id_promo=".$promo." order by fecha_entregado desc LIMIT 1;";
  //echo $query1;
  if ($resultado = mysqli_query($link, $query1)) {
    while ($fila = mysqli_fetch_row($resultado)) {
        $count++;
        if($fila[0]=='1')
        {
          getcodigo($link,$promo,$client,$idClient);
        }
        else
        {
        	echo 'VUELVE';
        }
     }
     if($count<1)
     {
       getcodigo($link,$promo,$client,$idClient);
     }
  }
  else {
     echo 'ERROR';
  }
}
function getcodigo($link,$promo,$ip,$huella)
{
  $count=0;
  mysqli_autocommit($link, FALSE);
  $query2= "SELECT codigo FROM gtrd_cupones where estatus=0 and id_promo=".$promo." LIMIT 1 FOR UPDATE;";
  if ($resultado = mysqli_query($link, $query2)) {
    while ($fila = mysqli_fetch_row($resultado)) {
      $count++;
      $filepath = "";
      $text ="".$fila[0]."";
      $size = "450";
      $orientation = "horizontal";
      $code_type = "code128";
      $print = true;
      $sizefactor = "3";
      $ismob = true;
      barcode( $filepath, $text, $size, $orientation, $code_type, $print, $sizefactor,$ismob);
      update_codigos($fila[0],$ip,$huella,$link);
      echo $fila[0];

    }
    if($count<1)
    {
      echo 'AGOTADO';
    }
  }
  else {
     echo 'ERROR';
  }
  if (!mysqli_commit($link)) {
    //echo "Falló la consignación de la transacción<br>";
    exit();
  }
  else
  {

  }

}
function update_codigos($codigo,$client,$idClient,$link)
{

  $salida          = 0;
  $country_code    = '';
  $ip_address      = $client;
  $country_name    = 'Local';
  $country_city    = '';
  $country_region  = '';
  $estado='';
  $codpais='';
  $count=0;
  //$salida = get_country_local($country_code,$ip_address,$lang,$country_name,$id_group); // busqueda en BD local
  //if ($salida==0) {
  $salida = get_country_api($country_code,$ip_address,$country_region,$codpais);

	//$query ="UPDATE  bdlt_registro SET fecha_update =CURRENT_TIMESTAMP WHERE usuario = '".$usuario."' or idfb='".$idfb."'";
  $query ="UPDATE  gtrd_cupones SET estatus = 1,ip='".$client."',pais='".$codpais."',estado='".$country_region."',huella_digital='".$idClient."',fecha_entregado=NOW() WHERE codigo = '".$codigo."'";
  $ip=$client;
  $date= date("Y-m-d H:i:s");
  $comple='IP:['.$ip.'] PAIS:['.$codpais.'] ESTADO:['.$country_region.'] Fecha['.$date.'] Ejecucion:['.$query.']';
  writelog($comple);
  if (mysqli_query($link, $query)) {
    //echo "Updated record successfully<br>";
  }
  if (!mysqli_commit($link)) {
    //echo "Falló la consignación de la transacción<br>";
    exit();
  }
}
// Agregar para Login

function login($usr,$pwd)
{
  $valid='';
  $valid='Error con usuario';
  $link=connect();

  $username = mysqli_real_escape_string($link, $usr);
  $password = mysqli_real_escape_string($link, $pwd);
  $password = md5($password);
  $query = "SELECT * FROM gtrd_settings WHERE Module='Admin' AND  setting = '$username' AND value = '$password'";
  $result = mysqli_query($link, $query);
  while ($fila = mysqli_fetch_row($result)) {
        $datos=$fila[4];
        $valid='SI,'.$datos;
  }
  mysqli_free_result($result);
  Close($link);
  return $valid;
}

function getpromociones($estatus)
{
  $html='';
  $link=connect();
  $query = "SELECT gtrd_promociones.nombre Promocion,gtrd_marca.nombre Marca,fecha_inicio,fecha_fin,gtrd_promociones.id FROM gtrd_promociones inner join gtrd_marca on gtrd_marca.Id=gtrd_promociones.id_marca where estatus='$estatus'";
  $result = mysqli_query($link, $query);
  while ($fila = mysqli_fetch_row($result)) {
        $htmldat='<div class="promoItemDash displayFlex">
          <div>
            <p class="promoItemDash_Name">'.$fila[0].'</p>
          </div>
          <div>
            <p class="promoItemDash_Brand">'.$fila[1].'</p>
          </div>
          <div>
            <span>
            <p class="promoItemDash_Validity">'.$fila[2].' <br> '.$fila[3].'</p>
            </span>
          </div>';
          if($estatus==1)
          {
            $htmlact='<div class="actions displayFlex">
                <a class="itemDash_action_link" class="trans5" onclick="openLinks(\'open\' ,this)"></a>
                <a class="itemDash_action_dashboard" href="dash.php?id='.encrypt_decrypt('e', $fila[4]).'" class="trans5"></a>';
                if($_SESSION['Rol']=='Admin')
                {
                  $htmlact=$htmlact.'<a class="itemDash_action_modify" href="mod.php?id='.encrypt_decrypt('e', $fila[4]).'" class="trans5"></a>
                  <a class="itemDash_action_end" href="end.php?id='.encrypt_decrypt('e', $fila[4]).'" class="trans5"></a>';
                }

            $htmlact=$htmlact.'</div>
            <ul class="linksWrap trans5">
              <li class="displayFlex">
                <h3>Página de Desarrollo:</h3>
                <a href="./'.$fila[0].'" target="_blank">http://siguesudando.com/'.$fila[0].'</a>
              </li>
              <li class="displayFlex">
                <h3>Página de Producción:</h3>
                <a href="./'.$fila[0].'" target="_blank">http://siguesudando.com/'.$fila[0].'</a>
              </li>
            </ul>
             </div>';
          }
          else if($estatus==2){
            $htmlact='<div class="actions displayFlex">';
            if($_SESSION['Rol']=='Admin')
            {
              $htmlact=$htmlact.'<a class="itemDash_action_modify" href="mod.php?id='.encrypt_decrypt('e', $fila[4]).'" class="trans5"></a>
              <a class="itemDash_action_publish" href="pub.php?id='.encrypt_decrypt('e', $fila[4]).'" class="trans5"></a>
              <a class="itemDash_action_delete" href="delete.php?id='.encrypt_decrypt('e', $fila[4]).'"  class="trans5"></a>';
            }
            $htmlact=$htmlact.'</div>
            </div>';
          }
          else {
            $htmlact='<div class="actions displayFlex">
              <a class="itemDash_action_report" href="dash.php?id='.encrypt_decrypt('e', $fila[4]).'" class="trans5"></a>
            </div>
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
function cuponesEntregadosHoy($link,$promo) {
  /* recuperar todas las filas de myCity */
   $score=0;
   $consulta = "SELECT count(*) FROM gtrd_cupones WHERE estatus = 1 and id_promo = ".$promo." and Date_format(fecha_entregado,'%d-%m-%Y') = Date_format(now(),'%d-%m-%Y');";
   if ($resultado = mysqli_query($link, $consulta)) {
     while ($fila = mysqli_fetch_row($resultado)) {
         $score=$fila[0];
      }
      /* liberar el conjunto de resultados */
      mysqli_free_result($resultado);
    }
    return $score;
}
function cuponesEntregados($link,$promo) {
  /* recuperar todas las filas de myCity */
   $score=0;
   $consulta = "SELECT count(*) FROM gtrd_cupones WHERE estatus = 1 and id_promo = ".$promo.";";
   if ($resultado = mysqli_query($link, $consulta)) {
     while ($fila = mysqli_fetch_row($resultado)) {
         $score=$fila[0];
      }
      /* liberar el conjunto de resultados */
      mysqli_free_result($resultado);
    }
    return $score;
}
function cuponesDisponibles($link,$promo) {
  /* recuperar todas las filas de myCity */
   $score=0;
   $consulta = "SELECT count(*) FROM gtrd_cupones WHERE estatus = 0 and id_promo = ".$promo.";";
   if ($resultado = mysqli_query($link, $consulta)) {
     while ($fila = mysqli_fetch_row($resultado)) {
         $score=$fila[0];
      }
      /* liberar el conjunto de resultados */
      mysqli_free_result($resultado);
    }
    return $score;
}
function cuponesUltimo($link,$promo) {
  /* recuperar todas las filas de myCity */
   $score="";
   $consulta = "SELECT max(fecha_entregado) FROM gtrd_cupones WHERE estatus = 1 and id_promo = ".$promo.";";
   if ($resultado = mysqli_query($link, $consulta)) {
     while ($fila = mysqli_fetch_row($resultado)) {
         $score=$fila[0];

         $date = new DateTime($score);
         $new_date_format = $date->format('d-m-Y H:i:s');
      }
      /* liberar el conjunto de resultados */
      mysqli_free_result($resultado);
    }
    return $new_date_format;
}
function createhtml($link,$promo)
{
  $des_promo = descPromo($link,$promo);
  $cup_entregadoshoy = cuponesEntregadosHoy($link,$promo);
  $cup_entregados = cuponesEntregados($link,$promo);
  $cup_disponibles = cuponesDisponibles($link,$promo);
  $cup_ultimo = cuponesUltimo($link,$promo);
  $porc_disponibles = 0;
  $porc_entregados = 0;
  if ($cup_disponibles+$cup_entregados > 0) {
    $porc_entregados = ($cup_entregados*100)/($cup_disponibles+$cup_entregados);
    $porc_disponibles = ($cup_disponibles*100)/($cup_disponibles+$cup_entregados);
  }
  $color = "green";

  if ($porc_disponibles < 25) { $color = "red";}
  if ($porc_disponibles >= 25 and $porc_disponibles < 40) { $color = "orange";}

  echo '<!-- Tab content -->
<div id="Consolidados" class="tabcontent"  style="text-align: center;">
  <p class="descPromo">'.$des_promo.'</p><br />
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
    $link=connect();
    $promdecr=encrypt_decrypt('d',$promo);
    $cup_entregadoshoy = cuponesEntregadosHoy($link,$promdecr);
    $cup_entregados = cuponesEntregados($link,$promdecr);
    $cup_disponibles = cuponesDisponibles($link,$promdecr);
    $cup_ultimo = cuponesUltimo($link,$promdecr);
    $porc_disponibles = 0;
    $porc_entregados = 0;
    if ($cup_disponibles+$cup_entregados > 0) {
      $porc_entregados = ($cup_entregados*100)/($cup_disponibles+$cup_entregados);
      $porc_disponibles = ($cup_disponibles*100)/($cup_disponibles+$cup_entregados);
    }
    $color = "green";

    if ($porc_disponibles < 25) { $color = "red";}
    if ($porc_disponibles >= 25 and $porc_disponibles < 40) { $color = "orange";}

    $salida = number_format($cup_entregadoshoy, 0, '.', ',').";".number_format($cup_entregados, 0, '.', ',').";".number_format($cup_disponibles, 0, '.', ',').";Total Entregados (".number_format($porc_entregados, 2, '.', ',')."%);Total Disponibles (".number_format($porc_disponibles, 2, '.', ',')."%);".$color.";".$cup_ultimo.";";

    return $salida;
}

function dashboard($promo)
{
  $reg=0;
  $link=connect();
  $estatus=getestatuspromo($link,$promo);
  $salida='<div id="disclaimerIndex" class="">
             <div id="content">
              <section id_estatus="'.$estatus.'"  id_promo="'.$promo.'" id="disclaimer">'.createhtml($link,$promo);
               if($estatus==1)
               {
                 $salida=$salida.'
                 <div id="interface" class="flexDisplay">
                   <a role="button" class="buttonG trans7 btnActualizar"   onclick="actualizaDatos(\''.encrypt_decrypt('e',$promo).'\')">Actualizar</a>
                 </div>';
               }
              $salida=$salida.'
             </section>
             </div>
            </div>';
  Close($link);
  return $salida;

}

//encrypt_decrypt('d','aTlCQkkyK1p2dHU5Z2pYY0NEcnN0UT09')
function getestatuspromo($link,$promo)
{
  $estatus=1;
  $query = "SELECT gtrd_promociones.estatus FROM gtrd_promociones where id=$promo";
  $result = mysqli_query($link, $query);
  while ($fila = mysqli_fetch_row($result)) {
        $estatus=$fila[0];
   }
  mysqli_free_result($result);
  return $estatus;
}
function dasboard_report($promo)
{
  $reg=0;
  $salida='';
  $link=connect();
  $consulta ="SELECT  gtrd_cupones.codigo Cupon,gtrd_cupones.fecha_entregado Entregado_El,gtrd_cupones.ip IPSolicitud, gtrd_cupones.pais,gtrd_estados.estado
FROM gtrd_cupones
INNER JOIN gtrd_estados on gtrd_cupones.estado=gtrd_estados.codigo_estado
WHERE id_promo=".$promo." AND estatus=1 AND gtrd_cupones.estado NOT IN ('ALL')
union
SELECT  gtrd_cupones.codigo Cupon,gtrd_cupones.fecha_entregado Entregado_El,gtrd_cupones.ip IPSolicitud, 'MX' pais,'CDMX' estado
FROM gtrd_cupones
WHERE (estado IS NULL OR estado IN ('ALL')) AND id_promo=".$promo." AND estatus=1";
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
        $salida='<div id="contentreport">No se encontraron resultados</div';
      }
      /* liberar el conjunto de resultados */
      mysqli_free_result($resultado);

   }

  Close($link);
  return $salida;

}
function marcas()
{
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
function proveedores()
{
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
function createfile($data,$name)
{
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

function insertageneral($fi,$ff,$nom,$desc,$mar,$pro,$idnvaprom){
  $salida='';
  $link=connect();
  mysqli_autocommit($link, FALSE);
  $marcade=encrypt_decrypt('d',$mar);
  $prodec=encrypt_decrypt('d',$pro);
  if($idnvaprom>0)
  {
    $consulta ="update gtrd_promociones set nombre='".$nom."',descripcion='".$desc."',id_marca=".$marcade.",id_proveedor=".$prodec.",fecha_inicio='".$fi." 00:00:01',fecha_fin='".$ff." 23:59:59', estatus=2 where id=".$idnvaprom;
  }
    else {
      $consulta ="insert into gtrd_promociones(nombre,descripcion,id_marca,id_proveedor,fecha_inicio,fecha_fin,estatus) VALUES('".$nom."','".$desc."',".$marcade.",".$prodec.",'".$fi." 00:00:01','".$ff." 23:59:59',2)";
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
     $salida='fallo sql insert';
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
?>
