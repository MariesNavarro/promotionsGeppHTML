<?php
date_default_timezone_set('America/Mexico_City');

require_once('conexion.php');
require_once('funciones.php');
require_once('barcode.php');

//echo  'Valida lista negra: '.validalistanegra('187.188.22.208').PHP_EOL;
//echo 'validaregion: '.validaregion(1).PHP_EOL;
//echo 'promvalidestado  :'.promvalidestado(1,'187.188.22.208').PHP_EOL;

function validafechas(&$cad,$promo,&$estatus){
  $reg;
  $contador=0;
  $link=connect();

  $consulta = "select 'fecha_inicio',fecha_inicio,NOW(),TIME_TO_SEC(TIMEDIFF(NOW(), fecha_inicio)) valor, estatus
               from gtrd_promociones where id=".$promo."
               union
               select 'fecha_fin',fecha_fin,NOW(),TIME_TO_SEC(TIMEDIFF(NOW(), fecha_fin)) valor, estatus
               from gtrd_promociones where id=".$promo;

  if ($resultado = mysqli_query($link, $consulta)) {
   while ($fila = mysqli_fetch_row($resultado)) {
     $reg[$contador]=$fila[3];
     $cad[$contador]=$fila[1];
     $estatus = $fila[4];
     $contador++;
    }
   /* liberar el conjunto de resultados */
    mysqli_free_result($resultado);
  }
  Close($link);
  return $reg;
}

function validaregion($idprom)
{
  $count=0;
  $link=connect();
  $consulta = "SELECT * from gtrd_promociones_estados where id_promo=".$idprom.";";
  if ($resultado = mysqli_query($link, $consulta)) {
    while ($fila = mysqli_fetch_row($resultado)) {
        $count++;
     }
  }
  Close($link);
  return $count;
}

function validalistanegra($ip)
{
  $count=0;
  $link=connect();
  $consulta = "SELECT * from gtrd_listanegra where ip='".$ip."';";
  if ($resultado = mysqli_query($link, $consulta)) {
    while ($fila = mysqli_fetch_row($resultado)) {
        $count++;
     }
  }
  Close($link);
  return $count;
}

function promvalidestado($idprom,$ip)
{
  $link=connect();
  $salida          = 0;
  $country_code    = '';
  $ip_address      = $ip;
  $country_name    = 'Local';
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
    }
    if($count<1)  { /* No se encontro, nos es valida */
      $date= date("Y-m-d H:i:s");
      $comple='IP:['.$ip.'] PAIS:['.$codpais.']  ESTADO:['.$country_region.']  Fecha['.$date.'] Ejecucion:[La promoción no es válida para tu ubicación]';
      writelog($comple);
    }
  }
  Close($link);
  return $count;

}

function getcupon($client,$idClient,$idpromo,$promo_imgcupon,$idproveedor,$test)
{
  $result ='';
  $count=0;
  $link=connect();
  $promo=getpromocionCodigogenerico($idpromo);
  $ind_generico = $promo['ind_generico'];

  if ($test==0 && $ind_generico==0) {/* No es test y no es código generico*/
    $query1 = "SELECT ((TIME_TO_SEC(TIMEDIFF(NOW(), fecha_entregado))>(1*1*1)) and ('".$client."'  not in (select ip from gtrd_listanegra))) Entregar,TIMEDIFF(NOW(), fecha_entregado) TiempoTranscurrido,TIMEDIFF( TIMEDIFF('2018-08-01 00:00:00', '2018-07-31 00:00:00'),TIMEDIFF(NOW(), fecha_entregado)) TiempoRestante from gtrd_cupones where estatus=1 and ip='".$client."' and huella_digital='".$idClient."' and id_promo=".$idpromo." order by fecha_entregado desc LIMIT 1;";
    if ($resultado = mysqli_query($link, $query1)) {
      while ($fila = mysqli_fetch_row($resultado)) {
          $count++;
          if($fila[0]=='1') {
            $result = getcodigo($link,$idpromo,$client,$idClient,$promo_imgcupon,$idproveedor,$test,$ind_generico);
          }
          else {	$result = 'VUELVE';  }
       }
       if($count<1) {
         $result = getcodigo($link,$idpromo,$client,$idClient,$promo_imgcupon,$idproveedor,$test,$ind_generico);
       }
    }
    else {
       $result = 'ERROR';
    }
  } else {
      $result = getcodigo($link,$idpromo,$client,$idClient,$promo_imgcupon,$idproveedor,$test,$ind_generico);
  }
  return $result;
}

function getcodigo($link,$idpromo,$ip,$huella,$promo_imgcupon,$idproveedor,$test,$ind_generico)
{
  $count=0;
  $result='';
  $query2='';

  mysqli_autocommit($link, FALSE);
  //$query2= "SELECT codigo FROM gtrd_cupones where estatus=0 and id_promo=".$idpromo." LIMIT 1 FOR UPDATE;";

  if ($test==0 && $ind_generico==0) {  // no es test, no codifo generico
    $query2= "SELECT codigo FROM gtrd_cupones  WHERE estatus=0 AND id_promo=".$idpromo." LIMIT 1 FOR UPDATE";
  } else {
    if ($test==0 && $ind_generico==1) {  // es codigo generico
      $query2= "SELECT codigo_generico FROM gtrd_promocion WHERE id = ".$idpromo." LIMIT 1";
    } else { // es test
      $query2= "SELECT cupon_test FROM gtrd_proveedor WHERE id = ".$idproveedor." LIMIT 1";
    }
  }

 if ($resultado = mysqli_query($link, $query2)) {
      while ($fila = mysqli_fetch_row($resultado)) {
        $count++;
        $filepath       = "";
        $text           = "".$fila[0]."";
        $size           = "450";
        $orientation    = "horizontal";
        $code_type      = "code128";
        $print          = true;
        $sizefactor     = "3";
        $ismob          = true;
        barcode( $filepath, $text, $size, $orientation, $code_type, $print, $sizefactor,$ismob,$idpromo,$promo_imgcupon);
        if ($test==0) { update_codigos($fila[0],$ip,$huella,$link); }  // no es test, actualizar entrega de código
        $result =  $idpromo.'_'.$fila[0];
        //$result = $fila[0].' '.$promo_imgcupon.' '.$idproveedor;
      }
      if($count<1)  { $result = 'AGOTADO'; }
    }
    else { $result = 'ERROR'; }
    if (!mysqli_commit($link)) { //echo "Falló la consignación de la transacción<br>";
      exit();
    }
    else {  }
  return $result;
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
  $consulta = "SELECT a.producto, a.nombre promo_nombre, a.descripcion promo_descripcion, a.fecha_inicio, a.fecha_fin, a.id_marca, a.id_plantilla, a.version, a.estatus, a.archivo_legales,a.id_funcionalidad, a.id_proveedor, a.dir
                FROM gtrd_promociones a
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

function getpromocionCodigogenerico($idprom)
{
  $count=0;
  $link=connect();
  $resultado = null;
  $consulta = "SELECT a.ind_generico, a.codigo_generico
                FROM gtrd_promociones a
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

function getplatilla($idmarca,$version,$idplantilla,$producto,$idproveedor)
{

  $count=0;
  $link=connect();
  $resultado = null;
  $consulta = "SELECT  a.id_plantilla id_plantilla,a.id_marca id_marca,a.version version,a.valor_componente promo_img_back, e.valor_componente promo_img_prod,
                       f.valor_componente promo_font, g.valor_componente promo_color, h.valor_componente promo_color_load, i.valor_componente promo_txt_footer, j.valor_componente promo_img_inicio,
                       k.valor_componente promo_img_precio, l.valor_componente promo_img_obtenercupon, m.valor_componente promo_img_cupon,
                       n.valor_componente promo_img_descargarcupon, o.valor_componente promo_img_exito, p.valor_componente promo_img_hashtag,
                       q.valor_componente promo_img_error, t.valor_componente marca_logo, s.logo proveedor_logo,
                       r.nombre marca_nombre, r.codigo marca_codigo, r.descripcion marca_descripcion
                FROM
           (SELECT * FROM gtrd_plantilla_config_producto WHERE id_componente = 'img_back') a
           LEFT JOIN (SELECT * FROM gtrd_plantilla_config_producto WHERE id_componente = 'img_prod') e ON a.id_plantilla = e.id_plantilla AND a.id_marca = e.id_marca AND a.version = e.version  AND a.producto = e.producto
           LEFT JOIN (SELECT * FROM gtrd_plantilla_config_producto WHERE id_componente = 'font') f ON a.id_plantilla = f.id_plantilla AND a.id_marca = f.id_marca AND a.version = f.version AND a.producto = f.producto
           LEFT JOIN (SELECT * FROM gtrd_plantilla_config_producto WHERE id_componente = 'color') g ON a.id_plantilla = g.id_plantilla AND a.id_marca = g.id_marca AND a.version = g.version AND a.producto = g.producto
           LEFT JOIN (SELECT * FROM gtrd_plantilla_config_producto WHERE id_componente = 'color_load') h ON a.id_plantilla = h.id_plantilla AND a.id_marca = h.id_marca AND a.version = h.version AND a.producto = h.producto
           LEFT JOIN (SELECT * FROM gtrd_plantilla_config_producto WHERE id_componente = 'txt_footer') i ON a.id_plantilla = i.id_plantilla AND a.id_marca = i.id_marca AND a.version = i.version AND a.producto = i.producto
           LEFT JOIN (SELECT * FROM gtrd_plantilla_config_producto WHERE id_componente = 'img_inicio') j ON a.id_plantilla = j.id_plantilla AND a.id_marca = j.id_marca AND a.version = j.version AND a.producto = j.producto
           LEFT JOIN (SELECT * FROM gtrd_plantilla_config_producto WHERE id_componente = 'img_precio') k ON a.id_plantilla = k.id_plantilla AND a.id_marca = k.id_marca AND a.version = k.version AND a.producto = k.producto
           LEFT JOIN (SELECT * FROM gtrd_plantilla_config_producto WHERE id_componente = 'img_obtenercupon') l ON a.id_plantilla = l.id_plantilla AND a.id_marca = l.id_marca AND a.version = l.version AND a.producto =l.producto
           LEFT JOIN (SELECT * FROM gtrd_plantilla_config_producto WHERE id_componente = 'img_cupon') m ON a.id_plantilla = m.id_plantilla AND a.id_marca = m.id_marca AND a.version = m.version AND a.producto =m.producto
           LEFT JOIN (SELECT * FROM gtrd_plantilla_config_producto WHERE id_componente = 'img_descargarcupon') n ON a.id_plantilla = n.id_plantilla AND a.id_marca = n.id_marca AND a.version = n.version AND a.producto =n.producto
           LEFT JOIN (SELECT * FROM gtrd_plantilla_config_producto WHERE id_componente = 'img_exito') o ON a.id_plantilla = o.id_plantilla AND a.id_marca = o.id_marca AND a.version = o.version AND a.producto =o.producto
           LEFT JOIN (SELECT * FROM gtrd_plantilla_config_producto WHERE id_componente = 'img_error') q ON a.id_plantilla = q.id_plantilla AND a.id_marca = q.id_marca AND a.version = q.version AND a.producto =q.producto
           LEFT JOIN (SELECT * FROM gtrd_plantilla_config_producto WHERE id_componente = 'img_hashtag') p ON a.id_plantilla = p.id_plantilla AND a.id_marca = p.id_marca AND a.version = p.version AND a.producto =p.producto
           LEFT JOIN (SELECT * FROM gtrd_plantilla_config_producto WHERE id_componente = 'img_logomarca') t ON a.id_plantilla = t.id_plantilla AND a.id_marca = t.id_marca AND a.version = t.version AND a.producto =t.producto
           LEFT JOIN gtrd_marca r on a.id_marca=r.id
           LEFT JOIN gtrd_proveedor s on 1=1
           where a.id_plantilla=".$idplantilla." and a.id_marca=".$idmarca." and a.version=".$version." and s.id=".$idproveedor." and a.producto = ".$producto;

  if ($registros = mysqli_query($link, $consulta)) {
    while ($fila = mysqli_fetch_array($registros)) {
        $resultado = $fila;
     }
  }
  Close($link);
  return $resultado;
}

function getmarca_redessociales($idmarca,$idplantilla,$version)
{
  $link=connect();
  $resultado = null;
  //$consulta = "SELECT * FROM gtrd_marca_redessociales WHERE id_marca = ".$idmarca." AND activo =1 ";

  $consulta = "SELECT a.url, a.nombre, b.valor_componente logo
                FROM gtrd_marca_redessociales a
               LEFT JOIN gtrd_plantilla_config_producto b ON  a.codigo = b.id_componente  AND b.id_plantilla = ".$idplantilla." AND a.id_marca = b.id_marca AND b.version = ".$version." AND b.producto = 1 WHERE a.id_marca = ".$idmarca." AND a.activo =1";

  if ($registros = mysqli_query($link, $consulta)) {
    while ($fila = mysqli_fetch_array($registros)) {
        $resultado .= "<a href='".$fila['url']."' target='_blank'><img id='ic".$fila['nombre']."' src='ui/img/ic/".$fila['logo']."' width='45' height='45'></a>";
     }
  }

  Close($link);
  return $resultado;
}

/**************** PASAR A dbconfig.php **************************/


//dasboard_entregados_excel(164);
?>
