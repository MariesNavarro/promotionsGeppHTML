<?php
require_once('backend/lib/funciones.php');
if (isset($_FILES) && !empty($_FILES)) {
    $id=encrypt_decrypt('d',$_POST["id"]);
    $file = $_FILES['fileName'];
    if(isset($_POST["ext"]))
    {
      $name=$id.'.'.$_POST["ext"];
    }
    else {
      $name = $id.'_'.$file['name'];
    }
    $fecha = date_create();

    $name = $id.'_'.date_timestamp_get($fecha).'_'.$file['name'];
    $path = $file['tmp_name'];
    $dir_subida = $_POST["dir"];
    $fichero_subido = $dir_subida.$name;
    if (move_uploaded_file($path, $fichero_subido)) {
      echo $name;
    } else {
      echo "¡Posible ataque de subida de ficheros!\n";
    }
    // process your file
}
?>
