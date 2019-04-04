<?php

session_start();
// $connect = mysqli_connect("localhost", "root", "", "testing");
require_once('backend/lib/db.php');
if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
  $ip = $_SERVER['HTTP_CLIENT_IP'];
}
elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
  $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
}
else {
  $ip = $_SERVER['REMOTE_ADDR'];
}


if($_POST["m"]==1){
       if(empty($_POST["usr"]) || empty($_POST["pwd"]))
       {
            echo 'Ambos valores son requerido';
       }
       else
       {
            /*$username = mysqli_real_escape_string($connect, $_POST["username"]);
            $password = mysqli_real_escape_string($connect, $_POST["password"]);
            $password = md5($password);
            $query = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
            $result = mysqli_query($connect, $query);
            if(mysqli_num_rows($result) > 0)
            {
                 $_SESSION['username'] = $username;
                 header("location:home.php");
            }
            else
            {
                 echo '<script>alert("Wrong User Details")</script>';
            } */
            $valid=login($_POST["usr"],$_POST["pwd"]);
            $array=explode(",", $valid);
            $valid=$array[0];
            if($valid=='SI')
            {
              $_SESSION['userName'] = $_POST["usr"];
              $_SESSION['Nombre']=$array[1];
              $_SESSION['Email']=$array[2];
              $_SESSION['Rol']=$array[3];
            }
           echo $valid;
       }
}
if($_POST["m"]==2){
    session_destroy();
    echo 'success';
}
if($_POST["m"]==3){
  $result=dashboard(encrypt_decrypt('d',$_POST["prom"]));
  echo $result;
}
if($_POST["m"]==4){
  $result=dasboard_report(encrypt_decrypt('d',$_POST["prom"]));
  echo $result;
}

if($_POST["m"]==5)
{
  $fi=$_POST["fi"];
  $ff=$_POST["ff"];
  $nom=$_POST["nom"];
  $desc=$_POST["desc"];
  $mar=$_POST["mar"];
  $pro=$_POST["pro"];
  $idnvaprom=$_POST["idnvaprom"];
  $result=insertageneral($fi,$ff,$nom,$desc,$mar,$pro,$idnvaprom);
  echo $result;
}
if($_POST["m"]==6)
{
  $id=$_POST["id"];
  $url=$_POST["url"];
  $result=actualizalegales($id,$url);
  echo $result;
}
/*if(isset($_POST["register"]))
{
     if(empty($_POST["username"]) && empty($_POST["password"]))
     {
          echo '<script>alert("Both Fields are required")</script>';
     }
     else
     {
          $username = mysqli_real_escape_string($connect, $_POST["username"]);
          $password = mysqli_real_escape_string($connect, $_POST["password"]);
          $password = md5($password);
          $query = "INSERT INTO users (username, password) VALUES('$username', '$password')";
          if(mysqli_query($connect, $query))
          {
               echo '<script>alert("Registration Done")</script>';
          }
     }
}
*/

?>
