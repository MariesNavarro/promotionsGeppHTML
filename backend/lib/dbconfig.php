<?php
date_default_timezone_set('America/Mexico_City');

require_once('conexion.php');
require_once('funciones.php');
require_once('barcode.php');

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

function getpromociones($estatus){
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
function getestatuspromo($link,$promo){
  $estatus=1;
  $query = "SELECT gtrd_promociones.estatus FROM gtrd_promociones where id=$promo";
  $result = mysqli_query($link, $query);
  while ($fila = mysqli_fetch_row($result)) {
        $estatus=$fila[0];
   }
  mysqli_free_result($result);
  return $estatus;
}
function dasboard_report($promo){
  $reg=0;
  $salida='';
  $link=connect();
  $consulta ="select gtrd_cupones.codigo Cupon,gtrd_cupones.fecha_entregado Entregado_El,gtrd_cupones.ip IPSolicitud, gtrd_cupones.pais,gtrd_estados.estado FROM gtrd_cupones  INNER JOIN gtrd_estados on gtrd_cupones.estado=gtrd_estados.codigo_estado WHERE id_promo=".$promo." and estatus=1 and gtrd_cupones.estado NOT IN ('ALL') union select gtrd_cupones.codigo Cupon,gtrd_cupones.fecha_entregado Entregado_El,gtrd_cupones.ip IPSolicitud, 'MX' pais,'CDMX' estado from gtrd_cupones WHERE (estado IS NULL OR estado IN ('ALL')) AND id_promo=".$promo." and estatus=1";
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
function marcas(){
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
function proveedores(){
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
function funcionalidades(){
  $reg=0;
  $salida='';
  $link=connect();
  $consulta ="SELECT * FROM gtrd_funcionalidades";
  /*
  <div class="rowConfig displayFlex">
    <!-- Funcionalidad 1 -->
    <div class="containerRectW displayFlex">
      <p>Códigos de Descuento</p>
      <div class="containerRect">
        <div class="picRect" style="background-image:url('ui/img/covers/cupon.jpg')"></div>
        <div class="overRect">
          <p>Esta funcionalidad permite cargar una base de códigos de descuento que serán entregados a los usuarios que accedan a la promoción.</p>
          <a href="#" target="_blank"><span>Ver Más</span></a>
        </div>
      </div>
      <div class="selectionContainer">
        <label>Seleccionar</label>
        <input class="checkBoxFunction" name="" type="checkbox">
      </div>
    </div>
    <!-- Funcionalidad 2 -->
    <div class="containerRectW rectGrey displayFlex">
      <p class="opacityZero">Funcionalidad 2</p>
      <div class="containerRect">
        <div class="picRect"></div>
        <div class="overRect"></div>
      </div>
      <div class="selectionContainer opacityZero">
        <label>Seleccionar</label>
        <!-- <input id="" name="" type="checkbox"> -->
      </div>
    </div>
  </div>

  <div class="rowConfig hideOnMobile">
    <!-- Funcionalidad 3 -->
    <div class="containerRectW rectGrey displayFlex">
      <p class="opacityZero">Funcionalidad 3</p>
      <div class="containerRect"></div>
      <div class="selectionContainer opacityZero">
        <label>Seleccionar</label>
        <!-- <input id="" name="" type="checkbox"> -->
      </div>
    </div>
    <div class="containerRectW rectGrey displayFlex">
      <!-- Funcionalidad 4 -->
      <p class="opacityZero">Funcionalidad 4</p>
      <div class="containerRect"></div>
      <div class="selectionContainer opacityZero">
        <label>Seleccionar</label>
        <!-- <input id="" name="" type="checkbox"> -->
      </div>
    </div>
  </div>
  */
  if ($resultado = mysqli_query($link, $consulta)) {
    while ($fila = mysqli_fetch_row($resultado)) {
          $reg++;
         if ($reg%2==0)
         {
           $salida=$salida.'<div class="containerRectW displayFlex">
             <p>'.$fila[1].'</p>
             <div class="containerRect">
               <div class="picRect" style="background-image:url(\''.$fila[3].'\')"></div>
               <div class="overRect">
                 <p>'.$fila[2].'</p>
                 <a href="#" target="_blank"><span>Ver Más</span></a>
               </div>
             </div>
             <div class="selectionContainer">
               <label>Seleccionar</label>
               <input onclick="uncheckedfunctionall(this)" class="checkBoxFunction" value="'.encrypt_decrypt('e',$fila[0]).'" name="" type="checkbox">
             </div>
           </div>
           </div>';
         }
         else {
           $salida=$salida.'<div class="rowConfig displayFlex">
             <div class="containerRectW displayFlex">
               <p>'.$fila[1].'</p>
               <div class="containerRect">
                 <div class="picRect" style="background-image:url(\''.$fila[3].'\')"></div>
                 <div class="overRect">
                   <p>'.$fila[2].'</p>
                   <a href="#" target="_blank"><span>Ver Más</span></a>
                 </div>
               </div>
               <div class="selectionContainer">
                 <label>Seleccionar</label>
                 <input onclick="uncheckedfunctionall(this)" class="checkBoxFunction" value="'.encrypt_decrypt('e',$fila[0]).'" name="" type="checkbox">
               </div>
             </div>';
         }
      }

      if($reg%2!=0)
      {
          $salida=$salida.'<div class="containerRectW rectGrey displayFlex">
            <p class="opacityZero">Funcionalidad 2</p>
            <div class="containerRect">
              <div class="picRect"></div>
              <div class="overRect"></div>
            </div>
            <div class="selectionContainer opacityZero">
              <label>Seleccionar</label>
              <!-- <input id="" name="" type="checkbox"> -->
            </div>
          </div>
        </div>';
      }
      if($reg<3)
      {
          $salida=$salida.'<div class="rowConfig hideOnMobile">
            <!-- Funcionalidad 3 -->
            <div class="containerRectW rectGrey displayFlex">
              <p class="opacityZero">Funcionalidad 3</p>
              <div class="containerRect"></div>
              <div class="selectionContainer opacityZero">
                <label>Seleccionar</label>
                <!-- <input id="" name="" type="checkbox"> -->
              </div>
            </div>
            <div class="containerRectW rectGrey displayFlex">
              <!-- Funcionalidad 4 -->
              <p class="opacityZero">Funcionalidad 4</p>
              <div class="containerRect"></div>
              <div class="selectionContainer opacityZero">
                <label>Seleccionar</label>
                <!-- <input id="" name="" type="checkbox"> -->
              </div>
            </div>
          </div>';
      }
      /* liberar el conjunto de resultados */
      mysqli_free_result($resultado);

   }

  Close($link);
  return $salida;

}
function plantillas($funcionalidad){
  $reg=0;
  $salida='';

  /*
  <div class="rowConfig displayFlex">
    <!-- Plantilla 1 -->
    <div class="containerRectW displayFlex">
      <p>Plantilla Entrega de Cupón 1</p>
      <div class="containerRect">
        <div class="picRect" style="background-image:url('ui/img/covers/plantilla-1.jpg')"></div>
        <div class="overRect displayFlex">
          <a href="#" target="_blank"><span>Ver Plantilla</span></a>
        </div>
      </div>
      <div class="selectionContainer">
        <label>Seleccionar</label>
        <input class="checkBoxTheme" id="" name="" type="checkbox">
      </div>
    </div>
    <!-- Plantilla 2 -->
    <div class="containerRectW rectGrey displayFlex">
      <p class="opacityZero">Plantilla 2 Producto</p>
      <div class="containerRect"></div>
      <div class="selectionContainer opacityZero">
        <label>Seleccionar</label>
        <!-- <input id="" name="" type="checkbox"> -->
      </div>
    </div>
  </div>
  <div class="rowConfig hideOnMobile">
    <!-- Plantilla 3 -->
    <div class="containerRectW rectGrey displayFlex">
      <p class="opacityZero">Plantilla 1 Producto</p>
      <div class="containerRect"></div>
      <div class="selectionContainer opacityZero">
        <label>Seleccionar</label>
        <!-- <input id="" name="" type="checkbox"> -->
      </div>
    </div>
    <!-- Plantilla 4 -->
    <div class="containerRectW rectGrey displayFlex">
      <p class="opacityZero">Plantilla 2 Producto</p>
      <div class="containerRect"></div>
      <div class="selectionContainer opacityZero">
        <label>Seleccionar</label>
        <!-- <input id="" name="" type="checkbox"> -->
      </div>
    </div>
  </div>
  <button class="buttonConfig leftButton" type="button" name="button" onclick="sliderConfigFun(2)">Anterior</button>
  <button class="buttonConfig rightButton" type="button" name="button" onclick="checkSteps(4, this)">Siguiente</button>



  */

  $link=connect();
  if($funcionalidad == NULL) {
  $consulta ="SELECT * FROM gtrd_plantilla";
  }
  else {
    $consulta="SELECT * FROM gtrd_plantilla where id_funcionalidad=".$funcionalidad;
  }
  if ($resultado = mysqli_query($link, $consulta)) {
    while ($fila = mysqli_fetch_row($resultado)) {
          $reg++;
         if ($reg%2==0)
         {
           $salida=$salida.'<div class="containerRectW displayFlex">
             <p>'.$fila[2].'</p>
             <div class="containerRect">
               <div class="picRect" style="background-image:url(\''.$fila[6].'\')"></div>
               <div class="overRect">
                 <p>'.$fila[3].'</p>
                 <a href="#" target="_blank"><span>Ver Más</span></a>
               </div>
             </div>
             <div class="selectionContainer">
               <label>Seleccionar</label>
               <input onclick="uncheckedthemeall(this)" class="checkBoxTheme" value="'.encrypt_decrypt('e',$fila[0]).'" name="" type="checkbox">
             </div>
           </div>
           </div>';
         }
         else {
           $salida=$salida.'<div class="rowConfig displayFlex">
             <div class="containerRectW displayFlex">
               <p>'.$fila[2].'</p>
               <div class="containerRect">
                 <div class="picRect" style="background-image:url(\''.$fila[6].'\')"></div>
                 <div class="overRect">
                   <p>'.$fila[3].'</p>
                   <a href="#" target="_blank"><span>Ver Más</span></a>
                 </div>
               </div>
               <div class="selectionContainer">
                 <label>Seleccionar</label>
                 <input onclick="uncheckedthemeall(this)" class="checkBoxTheme" value="'.encrypt_decrypt('e',$fila[0]).'" name="" type="checkbox">
               </div>
             </div>';
         }
      }

      if($reg%2!=0)
      {
          $salida=$salida.'<div class="containerRectW rectGrey displayFlex">
            <p class="opacityZero">Funcionalidad 2</p>
            <div class="containerRect">
              <div class="picRect"></div>
              <div class="overRect"></div>
            </div>
            <div class="selectionContainer opacityZero">
              <label>Seleccionar</label>
              <!-- <input id="" name="" type="checkbox"> -->
            </div>
          </div>
        </div>';
      }
      if($reg<3)
      {
          $salida=$salida.'<div class="rowConfig hideOnMobile">
            <!-- Funcionalidad 3 -->
            <div class="containerRectW rectGrey displayFlex">
              <p class="opacityZero">Funcionalidad 3</p>
              <div class="containerRect"></div>
              <div class="selectionContainer opacityZero">
                <label>Seleccionar</label>
                <!-- <input id="" name="" type="checkbox"> -->
              </div>
            </div>
            <div class="containerRectW rectGrey displayFlex">
              <!-- Funcionalidad 4 -->
              <p class="opacityZero">Funcionalidad 4</p>
              <div class="containerRect"></div>
              <div class="selectionContainer opacityZero">
                <label>Seleccionar</label>
                <!-- <input id="" name="" type="checkbox"> -->
              </div>
            </div>
          </div>';
      }

      $salida=$salida.'<button class="buttonConfig leftButton" type="button" name="button" onclick="sliderConfigFun(2)">Anterior</button>
        <button class="buttonConfig rightButton" type="button" name="button" onclick="checkSteps(4, this)">Siguiente</button>';
      /* liberar el conjunto de resultados */
      mysqli_free_result($resultado);

   }

  Close($link);
  return $salida;

}
function createfile($data,$name){
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
function actualizafuncionalidad($id,$prom){
  $salida="";
  $link=connect();
  mysqli_autocommit($link, FALSE);
  $consulta ="update gtrd_promociones SET id_funcionalidad=".encrypt_decrypt('d',$id)." WHERE id=".$prom;
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
function actualizaplantillabd($id,$prom){
  $salida="";
  $link=connect();
  mysqli_autocommit($link, FALSE);
  $consulta ="update gtrd_promociones SET id_plantilla=".encrypt_decrypt('d',$id)." WHERE id=".$prom;
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
function existecupon($id,$prom)
{
  $reg=0;
  $salida='';
  $link=connect();
  $consulta ="select codigo FROM gtrd_cupones where id_promo=".$prom." and codigo in (".$id.")";
  /* gtrd_proveedor
  <option class="providerDefault" value="">Selecciona un Proveedor</option>
  <option class="providerDefault" value="">OXXO</option>
  <option class="providerDefault" value="">Seven Eleven</option>
  */
  if ($resultado = mysqli_query($link, $consulta)) {
    while ($fila = mysqli_fetch_row($resultado)) {
        $salida=$salida.$fila[0].',';
      }
      /* liberar el conjunto de resultados */
      mysqli_free_result($resultado);

   }

  Close($link);
  return $salida;

}
function loadcupons($id,$prom)
{
  $ban=0;
  $salida='';
  $existen='';
  $nuevos='';
  $noinserted='';
  $link=connect();
  mysqli_autocommit($link, FALSE);
  $cupones = explode(",", $id);
  foreach( $cupones as $value ){
        $inserted ="insert into gtrd_cupones(id_promo,codigo,estatus) values (".$prom.",".$value.",0)";
        if (mysqli_query($link,$inserted)) {
             $ins=str_replace('\'', '', $value);
             $nuevos=$nuevos.$ins.',';
         }
         else {
           $noins=str_replace('\'', '', $value);
           $noinserted=$noinserted.$noins.',';
         }
          mysqli_commit($link);


   }

   $salida=$nuevos.';'.$existen.';'.$noinserted;
  /* gtrd_proveedor
  <option class="providerDefault" value="">Selecciona un Proveedor</option>
  <option class="providerDefault" value="">OXXO</option>
  <option class="providerDefault" value="">Seven Eleven</option>
  */

  Close($link);
  return $salida;

}

?>
