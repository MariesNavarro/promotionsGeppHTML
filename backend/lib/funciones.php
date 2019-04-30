<?php
date_default_timezone_set('America/Mexico_City');
 // equivalancias de los estados (regiones) de la api e geoplugin y la BD de estados
function equivalencia_estados_api($country_code,$region) {
    $edo = '';

    $region = strtoupper($region);

    if ($country_code=='MX') { // mexico

      if ($region=='THE FEDERAL DISTRICT')  {
        $edo = 'CDMX';
      } else if ($region=='SINALOA') {
         $edo = 'SINALOA';
      } else if ($region=='BAJA CALIFORNIA') {
         $edo = 'BAJACALI';
      } else if ($region=='NUEVO LEÓN') {
         $edo = 'NUEVOLEON';
      } else if ($region=='VERACRUZ-LLAVE') {
         $edo = 'VERACRUZ';
      } else if (strpos($region, 'AGUAS') !== false) {
         $edo = 'AGUASCALIE';
      } else if (strpos($region, 'CAMPECHE') !== false) {
         $edo = 'CAMPECHE';
      } else if (strpos($region, 'CIUDAD DE MÉXICO') !== false) {
         $edo = 'CDMX';
      } else if (strpos($region, 'CHIAPAS') !== false) {
         $edo = 'CHIAPAS';
       } else if (strpos($region, 'CHIHUAHUA') !== false) {
         $edo = 'CHIHUAHUA';
       } else if (strpos($region, 'COAHUILA') !== false) {
         $edo = 'COAHUILA';
       } else if (strpos($region, 'COLIMA') !== false) {
         $edo = 'COLIMA';
       } else if (strpos($region, 'DURANGO') !== false) {
         $edo = 'DURANGO';
       } else if (strpos($region, 'GUANAJUATO') !== false) {
         $edo = 'GUANAJUATO';
       } else if (strpos($region, 'GUERRERO') !== false) {
         $edo = 'GUERRERO';
       } else if (strpos($region, 'HIDALGO') !== false) {
         $edo = 'HIDALGO';
       } else if (strpos($region, 'MICHOACAN') !== false) {
         $edo = 'MICHOACAN';
       } else if (strpos($region, 'MORELOS') !== false) {
         $edo = 'MORELOS';
       } else if (strpos($region, 'NAYARIT')!== false) {
         $edo = 'NAYARIT';
       } else if (strpos($region, 'OAXACA') !== false) {
         $edo = 'OAXACA';
       } else if (strpos($region, 'PUEBLA') !== false) {
         $edo = 'PUEBLA';
       } else if (strpos($region, 'QUERETARO') !== false) {
         $edo = 'QUERETARO';
       } else if (strpos($region, 'QUINTANA') !== false) {
         $edo = 'QUINTANARO';
       } else if (strpos($region, 'POTOSÍ') !== false) {
         $edo = 'SANLUISPOT';
       } else if (strpos($region, 'SONORA') !== false) {
         $edo = 'SONORA';
       } else if (strpos($region, 'TABASCO') !== false) {
         $edo = 'TABASCO';
       } else if (strpos($region, 'TAMAULIPAS') !== false) {
         $edo = 'TAMAULIPAS';
       } else if (strpos($region, 'TLAXCALA') !== false) {
         $edo = 'TLAXCALA';
       } else if (strpos($region, 'VERACRUZ') !== false) {
         $edo = 'VERACRUZ';
       } else if (strpos($region, 'YUCATAN')!== false) {
         $edo = 'YUCATAN';
       } else if (strpos($region, 'ZACATECAS') !== false) {
         $edo = 'ZACATECAS';
      } else {
         $edo = 'SINALOA';   // por defecto
      }
    }
    else
    {
    	$edo=$region;
    }
    // strpos($mystring, $findme);
    return $edo;
}
function get_country_api(&$country_code,&$ip_address,&$country_region,&$codpais){
    $salida     = 0;
    $geopluginURL   = 'http://www.geoplugin.net/php.gp?ip='.$ip_address;
    if ( function_exists('curl_init') ) {

      //use cURL to fetch data
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $geopluginURL);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_USERAGENT, 'geoPlugin PHP Class v1.1');
      $response = curl_exec($ch);
      curl_close ($ch);

    } else if ( ini_get('allow_url_fopen') ) {

      //fall back to fopen()
      $response = file_get_contents($geopluginURL, 'r');

    } else {

      //trigger_error ('geoPlugin class Error: Cannot retrieve data. Either compile PHP with cURL support or enable allow_url_fopen in php.ini ', E_USER_ERROR);
      //return;

    }
    $addrDetailsArr = unserialize($response);
    $country_code   = $addrDetailsArr['geoplugin_countryName'];

    if ($country_code!='')
    {
      $country_region = $addrDetailsArr['geoplugin_regionCode'];
      $codpais = $addrDetailsArr['geoplugin_countryCode'];
      $salida = 1;
    }
    else {
      $country_code   = 'Mexico';
      $country_region = 'ALL';
      $codpais = 'MX';
    }
    return $salida;
}
function writelog($string){
  $ban=0;
  do{
   $fichero = fopen("log/logquerys.txt", "a+");
    if (flock($fichero, LOCK_EX)) {
        fwrite($fichero,$string.PHP_EOL);
        flock($fichero, LOCK_UN);
        fclose($fichero);
        $ban=1;
    }
    else
    {
      sleep(1);
       $ban=1;
    }
    $ban=1;
  }while($ban==0);
}
function encrypt_decrypt($action, $string) {
       $output = false;

       $encrypt_method = "AES-256-CBC";
       $secret_key = 'G3pp2019';  // por defecto
       $secret_iv  = 'O3tcapital';   // por defecto

       // hash
       $key = hash('sha256', $secret_key);

       // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
       $iv = substr(hash('sha256', $secret_iv), 0, 16);

       if( $action == 'e' ) {
           $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
           $output = base64_encode($output);
       }
       else if( $action == 'd' ){
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
       }
       return $output;
}
function writetxtcupons($string,$promo){
  $filepath="cupones/txt/".$promo.".txt";
   $fichero = fopen($filepath, "w");
    if (flock($fichero, LOCK_EX)) {
        fwrite($fichero,$string);
        flock($fichero, LOCK_UN);
        fclose($fichero);
    }
    return $filepath;

}
function preparedirname($string){
    $string = trim($string);
    $string = str_replace(
        array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
        array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
        $string
    );
    $string = str_replace(
        array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
        array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
        $string
    );
    $string = str_replace(
        array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
        array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
        $string
    );
    $string = str_replace(
        array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
        array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
        $string
    );
    $string = str_replace(
        array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
        array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
        $string
    );
    $string = str_replace(
        array('ñ', 'Ñ', 'ç', 'Ç'),
        array('n', 'N', 'c', 'C',),
        $string
    );
    //Esta parte se encarga de eliminar cualquier caracter extraño  "#",
    $string = str_replace(
        array("\\", "¨", "º", "-", "~",
            "@", "|", "!", "\"",
             "·", "$", "%", "&", "/",
             "(", ")", "?", "'", "¡",
             "¿", "[", "^", "<code>", "]",
             "+", "}", "{", "¨", "´",
             ">", "< ", ";", ",", ":","#",
             ".", " "),
        "",
        $string
    );
    return strtolower($string);
}
function creadirectoriopromo($idpromo,$nombredir,$dominio){
  $carpeta = 'promos/'.preparedirname($nombredir);
  if (!file_exists($carpeta)) {
    mkdir($carpeta, 0777, true);
    $filepath=$carpeta."/index.php";
    $txtinfile="<?php header(\"Location:".$dominio."/?id=".$idpromo."\"); ?>";
     $fichero = fopen($filepath, "w");
      if (flock($fichero, LOCK_EX)) {
          fwrite($fichero,$txtinfile);
          flock($fichero, LOCK_UN);
          fclose($fichero);
      }
      return $carpeta;
}
}
function send_email($email,$parametro) {

 		$texto_mail ='Tu contraseña es: '.$parametro;
        $para  		= $email;
        $de    		="info@oetcapital.com";   // email que envia
        $titulo='	=?UTF-8?B?'.base64_encode("Colocar aqui el titulo del email").'?=';

        // Para enviar un correo HTML mail, la cabecera Content-type debe fijarse
        $cabeceras  = 'MIME-Version: 1.0' . "\r\n";
        $cabeceras .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
        $cabeceras .= 'Content-Transfer-Encoding: 7bit' . "\r\n";

        $cabeceras .= 'From: javier.corona@oetcapital.com '. $de . "\r\n";

        // con copia oculta
        //$cabeceras .= 'BCC: xxxxxx@gmail.com';

         mail($para, $titulo, $texto_mail, $cabeceras);
        // fin envio email
}

?>
