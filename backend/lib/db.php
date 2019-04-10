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

function getpromocion($idprom)
{
  $count=0;
  $link=connect();
  $resultado = null;
  $consulta = "SELECT a.producto, a.nombre promo_nombre, a.descripcion promo_descripcion, a.fecha_inicio, a.fecha_fin, a.id_marca, a.id_plantilla, a.version, a.estatus, a.archivo_legales,a.id_funcionalidad,
                      b.nombre marca_nombre, b.logo marca_logo, b.codigo marca_codigo, b.descripcion marca_descripcion,  c.logo proveedor_logo, d.valor_componente promo_img_back, e.valor_componente promo_img_prod,
                      f.valor_componente promo_font, g.valor_componente promo_color, h.valor_componente promo_color_load, i.valor_componente promo_txt_footer, j.valor_componente promo_img_inicio,
                      k.valor_componente promo_img_precio, l.valor_componente promo_img_obtenercupon, m.valor_componente promo_img_cupon,
                      n.valor_componente promo_img_descargarcupon, o.valor_componente promo_img_exito, p.valor_componente promo_img_hashtag,
                      q.valor_componente promo_img_error
                FROM gtrd_promociones a
           LEFT JOIN gtrd_marca b ON a.id_marca = b.id
           LEFT JOIN gtrd_proveedor c ON a.id_proveedor = c.id
           LEFT JOIN gtrd_plantilla_config_producto d ON a.id_plantilla = d.id_plantilla AND a.id_marca = d.id_marca AND a.version = d.version AND d.producto = 1 AND d.id_componente = 'img_back'
           LEFT JOIN gtrd_plantilla_config_producto e ON a.id_plantilla = e.id_plantilla AND a.id_marca = e.id_marca AND a.version = e.version AND e.producto = 1 AND e.id_componente = 'img_prod'
           LEFT JOIN gtrd_plantilla_config_producto f ON a.id_plantilla = f.id_plantilla AND a.id_marca = f.id_marca AND a.version = f.version AND f.producto = 1 AND f.id_componente = 'font'
           LEFT JOIN gtrd_plantilla_config_producto g ON a.id_plantilla = g.id_plantilla AND a.id_marca = g.id_marca AND a.version = g.version AND g.producto = 1 AND g.id_componente = 'color'
           LEFT JOIN gtrd_plantilla_config_producto h ON a.id_plantilla = h.id_plantilla AND a.id_marca = h.id_marca AND a.version = h.version AND h.producto = 1 AND h.id_componente = 'color_load'
           LEFT JOIN gtrd_plantilla_config_producto i ON a.id_plantilla = i.id_plantilla AND a.id_marca = i.id_marca AND a.version = i.version AND i.producto = 1 AND i.id_componente = 'txt_footer'
           LEFT JOIN gtrd_plantilla_config_producto j ON a.id_plantilla = j.id_plantilla AND a.id_marca = j.id_marca AND a.version = j.version AND j.producto = 1 AND j.id_componente = 'img_inicio'
           LEFT JOIN gtrd_plantilla_config_producto k ON a.id_plantilla = k.id_plantilla AND a.id_marca = k.id_marca AND a.version = k.version AND k.producto = 1 AND k.id_componente = 'img_precio'
           LEFT JOIN gtrd_plantilla_config_producto l ON a.id_plantilla = l.id_plantilla AND a.id_marca = l.id_marca AND a.version = l.version AND l.producto = 1 AND l.id_componente = 'img_obtenercupon'
           LEFT JOIN gtrd_plantilla_config_producto m ON a.id_plantilla = m.id_plantilla AND a.id_marca = m.id_marca AND a.version = m.version AND m.producto = 1 AND m.id_componente = 'img_cupon'
           LEFT JOIN gtrd_plantilla_config_producto n ON a.id_plantilla = n.id_plantilla AND a.id_marca = n.id_marca AND a.version = n.version AND n.producto = 1 AND n.id_componente = 'img_descargarcupon'
           LEFT JOIN gtrd_plantilla_config_producto o ON a.id_plantilla = o.id_plantilla AND a.id_marca = o.id_marca AND a.version = o.version AND o.producto = 1 AND o.id_componente = 'img_exito'
           LEFT JOIN gtrd_plantilla_config_producto p ON a.id_plantilla = p.id_plantilla AND a.id_marca = p.id_marca AND a.version = p.version AND p.producto = 1 AND p.id_componente = 'img_hashtag'
           LEFT JOIN gtrd_plantilla_config_producto q ON a.id_plantilla = q.id_plantilla AND a.id_marca = q.id_marca AND a.version = q.version AND q.producto = 1 AND q.id_componente = 'img_error'
               WHERE a.id=".$idprom;
  if ($registros = mysqli_query($link, $consulta)) {
    //echo  $consulta;
    while ($fila = mysqli_fetch_array($registros)) {
        $resultado = $fila;
     }
  }
  Close($link);
  return $resultado;
}

function getmarca_redessociales($idmarca)
{
  $link=connect();
  $resultado = null;
  $consulta = "SELECT * FROM gtrd_marca_redessociales WHERE id_marca = ".$idmarca." AND activo =1";

  if ($registros = mysqli_query($link, $consulta)) {
    while ($fila = mysqli_fetch_array($registros)) {
        $resultado .= "<a href='".$fila['url']."' target='_blank'><img id='ic".$fila['nombre']."' src='ui/img/ic/".$fila['logo']."' width='45' height='45'></a>";
     }
  }

  Close($link);
  return $resultado;
}
?>
