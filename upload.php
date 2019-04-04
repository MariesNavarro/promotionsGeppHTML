<?php
if (isset($_FILES) && !empty($_FILES)) {
    $id=$_POST["id"];
    $file = $_FILES['fileName'];
    $name = $file['name'];
    $path = $file['tmp_name'];
    $dir_subida = 'legales/';
    $fichero_subido = $dir_subida .$id.'_'.$name;
    if (move_uploaded_file($path, $fichero_subido)) {
      echo $fichero_subido;
    } else {
      echo "¡Posible ataque de subida de ficheros!\n";
    }
    // process your file
}
?>
