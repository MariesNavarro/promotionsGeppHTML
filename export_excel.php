<?PHP
date_default_timezone_set('America/Mexico_City');

require_once('backend/lib/funciones.php');
require_once('backend/lib/dbconfig.php');
require_once('backend/Classes/PHPExcel.php');

If (!empty($_GET['id'])) {
  $id  = encrypt_decrypt('d', $_GET['id']);
  //$id  = $_GET['id'];

  /* Obtener datos de la promo*/
  $link=connect();
  $promo              = PromoValores($link,$id);
  $cup_entregadoshoy  = cuponesEntregadosHoy($link,$id);
  $cup_entregados     = cuponesEntregados($link,$id);
  $cup_disponibles    = cuponesDisponibles($link,$id);
  $cup_ultimo         = cuponesUltimo($link,$id);
  $filename           = $promo['nombre']." (cupones entregados al ".date('d_m_Y').").xls";

  $objPHPExcel = new PHPExcel();
  /* header */
  /* propiedades de la hoja */

  $objPHPExcel->setActiveSheetIndex(0);
  $objPHPExcel->getActiveSheet()->setTitle('Consolidado');
  $objPHPExcel->createSheet();
  $objPHPExcel->setActiveSheetIndex(1);
  $objPHPExcel->getActiveSheet()->setTitle('Cupones Entregados');
  $objPHPExcel->createSheet();
  $objPHPExcel->setActiveSheetIndex(2);
  $objPHPExcel->getActiveSheet()->setTitle('Cupones Disponibles');

  /* Hoja Entregados */
  $objPHPExcel->setActiveSheetIndex(1)
              ->setCellValue('A1', 'Cupón')
              ->setCellValue('B1', 'Fecha')
              ->setCellValue('C1', 'IP')
              ->setCellValue('D1', 'País')
              ->setCellValue('E1', 'Estado');
  $letters = range('A','E');
  $i =0;
  for ($i = 0; $i < 5; $i++) {
    $objPHPExcel->getActiveSheet()->getColumnDimension($letters[$i])->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getStyle($letters[$i]."1")->getFont()->setBold(true);
  }

  /* Hoja Disponibles */
  $objPHPExcel->setActiveSheetIndex(2)
              ->setCellValue('A1', 'Cupón');
  $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
  $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);


  //$objPHPExcel->getActiveSheet()->setTitle('Entregados');
  //$objPHPExcel->setActiveSheetIndex(1);

  /* Consolidado */
  $objPHPExcel->setActiveSheetIndex(0);
  $objPHPExcel->getActiveSheet()
              ->setCellValueExplicit('A2','Promoción:')
              ->setCellValueExplicit('B2',$promo['nombre'] )
              ->setCellValueExplicit('A3','Marca:')
              ->setCellValueExplicit('B3',$promo['marca'].' '.$promo['marca_logo'])
              ->setCellValueExplicit('A4','Proveedor:')
              ->setCellValueExplicit('B4',$promo['proveedor'] )
              ->setCellValueExplicit('A5','Vigencia:')
              ->setCellValueExplicit('B5',$promo['fecha_inicio'].' al '.$promo['fecha_fin'] )
              ->setCellValueExplicit('A6','Entregados hoy ('.date('d-m-Y').'):')
              ->setCellValueExplicit('B6', $cup_entregadoshoy)
              ->setCellValueExplicit('A7','Total entregados:')
              ->setCellValueExplicit('B7', $cup_entregados)
              ->setCellValueExplicit('A8','Total disponibles:')
              ->setCellValueExplicit('B8', $cup_disponibles)
              ->setCellValueExplicit('A9','Último entregado:')
              ->setCellValueExplicit('B9', $cup_ultimo);
  $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
  $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
  $objPHPExcel->getActiveSheet()->getStyle('A2:A9')->getFont()->setBold(true);
  $objPHPExcel->getActiveSheet()->getStyle('B2:B9')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
  $objPHPExcel->getActiveSheet()->getStyle('A0');

  $objPHPExcel->setActiveSheetIndex(0);
  /*
  $objDrawing = new PHPExcel_Worksheet_Drawing();
  $objDrawing->setName('Logo Marca');
  $objDrawing->setDescription('Logo Marca');
  $objDrawing->setPath('./ui/img/logotipo/'.$promo['marca_logo']);

  $objDrawing->setCoordinates('A10');
  $objDrawing->setOffsetX(10);
  $objDrawing->setWidth(20);
  $objDrawing->setHeight(20);
  */
  //$objDrawing->setOffsetX(10);
  //$objDrawing->setCoordinates('A1');
  //$objDrawing->setWidth(20);
  //$objDrawing->setHeight(36);

  //$objDrawing->setHeight(100);
  //$objDrawing->setWidth(100);
  //$objDrawing->setResizeProportional(true);

  //$objDrawing->setOffsetX(36);
  //$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());

/*
  $i =0;
  for ($i = 1; $i <= 5; $i++) {
    $objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getFont()->setBold(true);
  }
  */

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

  if ($resultado = mysqli_query($link, $consulta)) {
      if(!empty($resultado)) {
          $i=2;
          $objPHPExcel->setActiveSheetIndex(1);
          while ($fila = mysqli_fetch_array($resultado)) {
            $objPHPExcel->getActiveSheet()
                        ->setCellValueExplicit('A'.$i, $fila['Cupón'], PHPExcel_Cell_DataType::TYPE_STRING)
                        ->setCellValue('B'.$i, $fila['Fecha'])
                        ->setCellValue('C'.$i, $fila['IP'])
                        ->setCellValue('D'.$i, $fila['Pais'])
                        ->setCellValue('E'.$i, $fila['Estado']);
            $i++;
        }
      }
      /* liberar el conjunto de resultados */
      mysqli_free_result($resultado);
   }

   /* Obtener cupones disponibles */
   $consulta ="SELECT codigo Cupón
                 FROM gtrd_cupones
                WHERE id_promo=".$id." and estatus=0
               ORDER BY codigo";

   if ($resultado = mysqli_query($link, $consulta)) {
       if(!empty($resultado)) {
           $i=2;
           $objPHPExcel->setActiveSheetIndex(2);
           while ($fila = mysqli_fetch_array($resultado)) {
             $objPHPExcel->getActiveSheet()
                         ->setCellValueExplicit('A'.$i, $fila['Cupón'],  PHPExcel_Cell_DataType::TYPE_STRING);
             $i++;
         }
       }
       /* liberar el conjunto de resultados */
       mysqli_free_result($resultado);
    }

  Close($link);


  /* descargar archivo */
  $objPHPExcel->setActiveSheetIndex(0);
  header('Content-Type: application/vnd.ms-excel');
  header('Content-Disposition: attachment;filename='.$filename);
  header('Cache-Control: max-age=0');
  $objWriter=PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel5');
  $objWriter->save('php://output');
  exit;

}

function PromoValores($link,$promo) {
  /* recuperar todas las filas de myCity */
   $data="";
   $consulta = "SELECT a.nombre, a.descripcion,
                       b.nombre marca, b.logo_excel marca_logo,
                       c.nombre proveedor, c.logo_excel proveedor_logo,
                       DATE_FORMAT(a.fecha_inicio,'%d/%m/%Y') fecha_inicio,
                       DATE_FORMAT(a.fecha_fin,'%d/%m/%Y') fecha_fin
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
    return $data;
}

?>
