<?PHP
date_default_timezone_set('America/Mexico_City');

require_once('backend/lib/conexion.php');
require_once('backend/lib/funciones.php');

If (!empty($_GET['id'])) {
  $id  = encrypt_decrypt('d', $_GET['id']);
  //$id  = $_GET['id'];

  $reg=0;
  $salida='';
  $link=connect();
  $filename = 'cupones_entregados'.date('d_m_Y').'.xls';

  /* Obtener nombre de la promo */
  $consulta = "SELECT nombre FROM gtrd_promociones WHERE id=".$id;
  if ($resultado = mysqli_query($link, $consulta)) {
    while ($fila = mysqli_fetch_array($resultado)) {
        $filename = $fila['nombre']." (cupones entregados al ".date('d_m_Y').").xls";
     }
  }
  mysqli_free_result($resultado);

  /* Obtener cupones entregados */
  $consulta ="SELECT gtrd_cupones.codigo Cupón,
                     gtrd_cupones.fecha_entregado Fecha,
                     gtrd_cupones.ip IP,
                     gtrd_cupones.pais Pais,
                     gtrd_estados.estado Estado
              FROM   gtrd_cupones
              INNER JOIN gtrd_estados on gtrd_cupones.estado=gtrd_estados.codigo_estado
              WHERE id_promo=".$id." and estatus=1 and gtrd_cupones.estado NOT IN ('ALL')
              UNION
              SELECT gtrd_cupones.codigo Cupon,
                     gtrd_cupones.fecha_entregado Fecha,
                     gtrd_cupones.ip IP,
                     'MX' Pais,
                     '(No registrado)' Estado
              FROM   gtrd_cupones
              WHERE (estado IS NULL OR estado IN ('ALL')) AND id_promo=".$id." and estatus=1
              ORDER BY Fecha DESC";
  // /echo $consulta;
  if ($resultado = mysqli_query($link, $consulta)) {
      if(!empty($resultado)) {

        //header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        header("Content-Type: application/vnd.ms-excel charset=iso-8859-1");
        header('Content-Disposition: attachment; filename='.$filename);
        $isPrintHeader = false;
        foreach($resultado as $registro) {
          if (! $isPrintHeader) { echo  utf8_decode(implode("\t", array_keys($registro)) . "\n");$isPrintHeader = true;}
          echo utf8_decode(implode("\t", array_values($registro)). "\n");
        }
      }
      /* liberar el conjunto de resultados */
      mysqli_free_result($resultado);
   }
  Close($link);
  exit;
}

?>
