<?php
date_default_timezone_set('America/Mexico_City');

class foo_mysqli extends mysqli {
    public function __construct($host, $usuario, $contraseña, $bd) {
        parent::init();

        if (!parent::options(MYSQLI_INIT_COMMAND, 'SET AUTOCOMMIT = 0')) {
            die('Falló la configuración de MYSQLI_INIT_COMMAND');
        }

        if (!parent::options(MYSQLI_OPT_CONNECT_TIMEOUT, 5)) {
            die('Falló la configuración de MYSQLI_OPT_CONNECT_TIMEOUT');
        }

        if (!parent::real_connect($host, $usuario, $contraseña, $bd)) {
            die('Error de conexión (' . mysqli_connect_errno() . ') '
                    . mysqli_connect_error());
        }
    }
}
function connect()
{
  $hostname_conexion = "oetcapital.com";
  //$database_conexion = "admin_gatoradedev";   // DESARROLLO
  $database_conexion = "admin_gatorade";    // PRODUCCION
  $username_conexion = "admin_gatorade";
  $password_conexion = "#i-SexW_[MBE";
  $link  = new foo_mysqli($hostname_conexion, $username_conexion, $password_conexion, $database_conexion);
  mysqli_set_charset ($link , 'utf8' );
  mysqli_query($link, 'SET time_zone = "-05:00";');
  mysqli_options($link, MYSQLI_OPT_LOCAL_INFILE, true);
  return $link;
}
function Close($link)
{
	mysqli_close($link);
}


function PromoValores($promo) {
   $data="";
   $link    = connect();
   $consulta = "SELECT a.nombre, a.descripcion,
                       b.nombre marca, b.logo_excel marca_logo,
                       c.nombre proveedor, c.logo_excel proveedor_logo,
                       DATE_FORMAT(a.fecha_inicio,'%d/%m/%Y') fecha_inicio,
                       DATE_FORMAT(a.fecha_fin,'%d/%m/%Y') fecha_fin,
                       a.ind_generico, a.codigo_generico, a.max_generico,
                       gtrd_plantilla_config_producto.valor_componente thumbnail
                  FROM gtrd_promociones a
             LEFT JOIN gtrd_marca b ON a.id_marca = b.id
             LEFT JOIN gtrd_proveedor c ON a.id_proveedor = c.id
             LEFT JOIN gtrd_plantilla_config_producto ON
                       gtrd_plantilla_config_producto.id_plantilla = a.id_plantilla AND
                       gtrd_plantilla_config_producto.id_marca     = a.id_marca AND
                       gtrd_plantilla_config_producto.version      = a.version AND
                       gtrd_plantilla_config_producto.producto     = 1 AND
                       gtrd_plantilla_config_producto.id_componente= 'img_thumbnail'
                 WHERE a.id = ".$promo;
   
   if ($resultado = mysqli_query($link, $consulta)) {
     $data=mysqli_fetch_array($resultado);
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


?>
