<!DOCTYPE html>
<html>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="cache-control" content="no-cache">
<meta http-equiv="pragma" content="no-cache">
<meta name="google" content="notranslate">
<meta name="viewport" content="width=device-width, initial-scale=1.0000, minimum-scale=1.0000, maximum-scale=1.0000, user-scalable=no">
<title> Interfaz Uno </title>
<link rel="stylesheet" href="ui/css/master.css">
<link rel="stylesheet" href="ui/css/interfaz.css">
<body data-num="1">
  <section id="loadingIF" class="displayFlex trans5">
    <div>
      <form>
        <label for="unoChangeLogo" class="bordersetup colorBorderWhite">
          <p class="title backWhite colorBlack">Imagen Logotipo</p>
          <div class="hoverLabel displayFlex trans5" style="background:rgba(0,0,0,0.8)">
            <p>
              W:300px * H:300px <br>
              <b>.PNG ó .SVG</b>
            </p>
            <svg viewBox="0 0 30 30"> <g> <path class="uploadFillWhite" d="M0.6,26c0-3,0-6,0-9.1c0.9,0,1.8,0,2.7,0c0,2.1,0,4.2,0,6.4c7.8,0,15.6,0,23.4,0c0-2.1,0-4.2,0-6.4 c0.9,0,1.8,0,2.7,0c0,3,0,6,0,9.1C19.8,26,10.2,26,0.6,26z"/> <path class="uploadFillWhite" d="M7.9,10.9c0.3-0.3,0.6-0.5,0.8-0.8c2-2,4-4,6-6c0.2-0.2,0.3-0.2,0.5,0c2.2,2.2,4.4,4.4,6.6,6.6 c0.1,0.1,0.1,0.1,0.2,0.2c-0.1,0.1-0.1,0.1-0.2,0.2c-0.5,0.5-1,1-1.5,1.5c-0.2,0.2-0.3,0.1-0.4,0c-1.1-1.1-2.2-2.2-3.2-3.2 c-0.1-0.1-0.2-0.1-0.3-0.3c0,0.2,0,0.3,0,0.5c0,4,0,7.9,0,11.9c0,0.3-0.1,0.3-0.3,0.3c-0.8,0-1.5,0-2.3,0c0-4.2,0-8.5,0-12.8 c-0.1,0.1-0.2,0.2-0.3,0.3c-1.1,1.1-2.2,2.1-3.2,3.2c-0.2,0.2-0.3,0.2-0.5,0C9.1,12,8.5,11.5,7.9,10.9z"/> </g> </svg>
          </div>
        </label>
      </form>
      <input id="unoChangeLogo" class="hideInput" type="file" onchange="updateimageplantilla(this,14,'prefetchLogo,navLogo,msgLogo,loadLogo','ui/img/logotipo/')">
    </div>
  </section>
  <section id="homeIF" class="displayNone">
    <label for="unoBack" id="labelBack" class="backWhite">
      <p>Imagen de Fondo</p>
    </label>
    <input id="unoBack" type="file" onchange="updateimageplantilla(this,16,'plantillaUno','ui/img/back/')"  class="hideInput">
    <div class="superior">
      <div class="botella displayFlex">
          <label for="unoBottle" id="labelBottle" class="bordersetup colorBorderWhite">
            <p class="title backWhite colorBlack">Imagen Botella</p>
            <div class="hoverLabel displayFlex trans5" style="background:rgba(0,0,0,0.8)">
              <p>
                W:561px * H:1920px <br>
                <b>.PNG</b>
              </p>
              <svg viewBox="0 0 30 30"> <g> <path class="uploadFillWhite" d="M0.6,26c0-3,0-6,0-9.1c0.9,0,1.8,0,2.7,0c0,2.1,0,4.2,0,6.4c7.8,0,15.6,0,23.4,0c0-2.1,0-4.2,0-6.4 c0.9,0,1.8,0,2.7,0c0,3,0,6,0,9.1C19.8,26,10.2,26,0.6,26z"/> <path class="uploadFillWhite" d="M7.9,10.9c0.3-0.3,0.6-0.5,0.8-0.8c2-2,4-4,6-6c0.2-0.2,0.3-0.2,0.5,0c2.2,2.2,4.4,4.4,6.6,6.6 c0.1,0.1,0.1,0.1,0.2,0.2c-0.1,0.1-0.1,0.1-0.2,0.2c-0.5,0.5-1,1-1.5,1.5c-0.2,0.2-0.3,0.1-0.4,0c-1.1-1.1-2.2-2.2-3.2-3.2 c-0.1-0.1-0.2-0.1-0.3-0.3c0,0.2,0,0.3,0,0.5c0,4,0,7.9,0,11.9c0,0.3-0.1,0.3-0.3,0.3c-0.8,0-1.5,0-2.3,0c0-4.2,0-8.5,0-12.8 c-0.1,0.1-0.2,0.2-0.3,0.3c-1.1,1.1-2.2,2.1-3.2,3.2c-0.2,0.2-0.3,0.2-0.5,0C9.1,12,8.5,11.5,7.9,10.9z"/> </g> </svg>
            </div>
          </label>
          <input id="unoBottle" type="file" onchange="updateimageplantilla(this,17,'productoImg','ui/img/producto/')" class="hideInput">
        <div>
          <div class="infoTitleIF">
            <label for="unoTextoInicio" id="labelTxInicio" class="bordersetup colorBorderWhite labelTxInicio">
              <p class="title backWhite colorBlack">Imagen Texto</p>
              <div class="hoverLabel displayFlex trans5" style="background:rgba(0,0,0,0.8)">
                <p>
                  W:500px * H:113px <br>
                  <b>.PNG</b>
                </p>
                <svg viewBox="0 0 30 30"> <g> <path class="uploadFillWhite" d="M0.6,26c0-3,0-6,0-9.1c0.9,0,1.8,0,2.7,0c0,2.1,0,4.2,0,6.4c7.8,0,15.6,0,23.4,0c0-2.1,0-4.2,0-6.4 c0.9,0,1.8,0,2.7,0c0,3,0,6,0,9.1C19.8,26,10.2,26,0.6,26z"/> <path class="uploadFillWhite" d="M7.9,10.9c0.3-0.3,0.6-0.5,0.8-0.8c2-2,4-4,6-6c0.2-0.2,0.3-0.2,0.5,0c2.2,2.2,4.4,4.4,6.6,6.6 c0.1,0.1,0.1,0.1,0.2,0.2c-0.1,0.1-0.1,0.1-0.2,0.2c-0.5,0.5-1,1-1.5,1.5c-0.2,0.2-0.3,0.1-0.4,0c-1.1-1.1-2.2-2.2-3.2-3.2 c-0.1-0.1-0.2-0.1-0.3-0.3c0,0.2,0,0.3,0,0.5c0,4,0,7.9,0,11.9c0,0.3-0.1,0.3-0.3,0.3c-0.8,0-1.5,0-2.3,0c0-4.2,0-8.5,0-12.8 c-0.1,0.1-0.2,0.2-0.3,0.3c-1.1,1.1-2.2,2.1-3.2,3.2c-0.2,0.2-0.3,0.2-0.5,0C9.1,12,8.5,11.5,7.9,10.9z"/> </g> </svg>
              </div>
            </label>
            <input id="unoTextoInicio" type="file" onchange="updateimageplantilla(this,22,'textoInicioImg','ui/img/textoInicio/')"  class="hideInput">
          </div>
          <div class="prizeIF">
            <label for="unoPrize" id="labelPrize" class="bordersetup colorBorderWhite">
              <p class="titleDown backWhite colorBlack">Imagen Precio</p>
              <div class="hoverLabel displayFlex trans5" style="background:rgba(0,0,0,0.8)">
                <p>
                  W:500px * H:500px <br>
                  <b>.PNG</b>
                </p>
                <svg viewBox="0 0 30 30"> <g> <path class="uploadFillWhite" d="M0.6,26c0-3,0-6,0-9.1c0.9,0,1.8,0,2.7,0c0,2.1,0,4.2,0,6.4c7.8,0,15.6,0,23.4,0c0-2.1,0-4.2,0-6.4 c0.9,0,1.8,0,2.7,0c0,3,0,6,0,9.1C19.8,26,10.2,26,0.6,26z"/> <path class="uploadFillWhite" d="M7.9,10.9c0.3-0.3,0.6-0.5,0.8-0.8c2-2,4-4,6-6c0.2-0.2,0.3-0.2,0.5,0c2.2,2.2,4.4,4.4,6.6,6.6 c0.1,0.1,0.1,0.1,0.2,0.2c-0.1,0.1-0.1,0.1-0.2,0.2c-0.5,0.5-1,1-1.5,1.5c-0.2,0.2-0.3,0.1-0.4,0c-1.1-1.1-2.2-2.2-3.2-3.2 c-0.1-0.1-0.2-0.1-0.3-0.3c0,0.2,0,0.3,0,0.5c0,4,0,7.9,0,11.9c0,0.3-0.1,0.3-0.3,0.3c-0.8,0-1.5,0-2.3,0c0-4.2,0-8.5,0-12.8 c-0.1,0.1-0.2,0.2-0.3,0.3c-1.1,1.1-2.2,2.1-3.2,3.2c-0.2,0.2-0.3,0.2-0.5,0C9.1,12,8.5,11.5,7.9,10.9z"/> </g> </svg>
              </div>
            </label>
            <input id="unoPrize" type="file" onchange="updateimageplantilla(this,23,'prizeImg','ui/img/precio/')" class="hideInput">
          </div>
        </div>
      </div>
      <div class="inferior">
        <label for="unoBtCupon" id="labelObtenerCupon" class="bordersetup colorBorderWhite">
          <p class="titleDown backWhite colorBlack">Imagen Botón</p>
          <div class="hoverLabel displayFlex trans5" style="background:rgba(0,0,0,0.8)">
            <p>
              W:500px * H:100px <br>
              <b>.PNG</b>
            </p>
            <svg viewBox="0 0 30 30"> <g> <path class="uploadFillWhite" d="M0.6,26c0-3,0-6,0-9.1c0.9,0,1.8,0,2.7,0c0,2.1,0,4.2,0,6.4c7.8,0,15.6,0,23.4,0c0-2.1,0-4.2,0-6.4 c0.9,0,1.8,0,2.7,0c0,3,0,6,0,9.1C19.8,26,10.2,26,0.6,26z"/> <path class="uploadFillWhite" d="M7.9,10.9c0.3-0.3,0.6-0.5,0.8-0.8c2-2,4-4,6-6c0.2-0.2,0.3-0.2,0.5,0c2.2,2.2,4.4,4.4,6.6,6.6 c0.1,0.1,0.1,0.1,0.2,0.2c-0.1,0.1-0.1,0.1-0.2,0.2c-0.5,0.5-1,1-1.5,1.5c-0.2,0.2-0.3,0.1-0.4,0c-1.1-1.1-2.2-2.2-3.2-3.2 c-0.1-0.1-0.2-0.1-0.3-0.3c0,0.2,0,0.3,0,0.5c0,4,0,7.9,0,11.9c0,0.3-0.1,0.3-0.3,0.3c-0.8,0-1.5,0-2.3,0c0-4.2,0-8.5,0-12.8 c-0.1,0.1-0.2,0.2-0.3,0.3c-1.1,1.1-2.2,2.1-3.2,3.2c-0.2,0.2-0.3,0.2-0.5,0C9.1,12,8.5,11.5,7.9,10.9z"/> </g> </svg>
          </div>
        </label>
        <input id="unoBtCupon" type="file" onchange="updateimageplantilla(this,24,'btCouponImg','ui/img/botonCupon/')" class="hideInput">
      </div>
    </div>
  </section>
  <section id="cuponIF" class="displayNone">
    <div>
      <div class="cupon displayFlex">
        <label for="unoCupon" id="labelCupon" class="bordersetup colorBorderWhite labelCupon">
          <p class="title backWhite colorBlack">Imagen Cupón</p>
          <div class="hoverLabel displayFlex trans5" style="background:rgba(0,0,0,0.8)">
            <p>
              W:968px * H:1578px <br>
              <b>.JPG</b>
            </p>
            <svg viewBox="0 0 30 30"> <g> <path class="uploadFillWhite" d="M0.6,26c0-3,0-6,0-9.1c0.9,0,1.8,0,2.7,0c0,2.1,0,4.2,0,6.4c7.8,0,15.6,0,23.4,0c0-2.1,0-4.2,0-6.4 c0.9,0,1.8,0,2.7,0c0,3,0,6,0,9.1C19.8,26,10.2,26,0.6,26z"/> <path class="uploadFillWhite" d="M7.9,10.9c0.3-0.3,0.6-0.5,0.8-0.8c2-2,4-4,6-6c0.2-0.2,0.3-0.2,0.5,0c2.2,2.2,4.4,4.4,6.6,6.6 c0.1,0.1,0.1,0.1,0.2,0.2c-0.1,0.1-0.1,0.1-0.2,0.2c-0.5,0.5-1,1-1.5,1.5c-0.2,0.2-0.3,0.1-0.4,0c-1.1-1.1-2.2-2.2-3.2-3.2 c-0.1-0.1-0.2-0.1-0.3-0.3c0,0.2,0,0.3,0,0.5c0,4,0,7.9,0,11.9c0,0.3-0.1,0.3-0.3,0.3c-0.8,0-1.5,0-2.3,0c0-4.2,0-8.5,0-12.8 c-0.1,0.1-0.2,0.2-0.3,0.3c-1.1,1.1-2.2,2.1-3.2,3.2c-0.2,0.2-0.3,0.2-0.5,0C9.1,12,8.5,11.5,7.9,10.9z"/> </g> </svg>
          </div>
        </label>
        <input id="unoCupon" type="file" onchange="updateimageplantilla(this,25,'couponImg','ui/img/cupon/')"  class="hideInput">
      </div>
      <div class="captura">
        <label for="unoCaptura" id="labelObtenerCaptura" class="bordersetup colorBorderWhite">
          <p class="titleDown backWhite colorBlack">Imagen Botón</p>
          <div class="hoverLabel displayFlex trans5" style="background:rgba(0,0,0,0.8)">
            <p>
              W:500px * H:103px <br>
              <b>.PNG</b>
            </p>
            <svg viewBox="0 0 30 30"> <g> <path class="uploadFillWhite" d="M0.6,26c0-3,0-6,0-9.1c0.9,0,1.8,0,2.7,0c0,2.1,0,4.2,0,6.4c7.8,0,15.6,0,23.4,0c0-2.1,0-4.2,0-6.4 c0.9,0,1.8,0,2.7,0c0,3,0,6,0,9.1C19.8,26,10.2,26,0.6,26z"/> <path class="uploadFillWhite" d="M7.9,10.9c0.3-0.3,0.6-0.5,0.8-0.8c2-2,4-4,6-6c0.2-0.2,0.3-0.2,0.5,0c2.2,2.2,4.4,4.4,6.6,6.6 c0.1,0.1,0.1,0.1,0.2,0.2c-0.1,0.1-0.1,0.1-0.2,0.2c-0.5,0.5-1,1-1.5,1.5c-0.2,0.2-0.3,0.1-0.4,0c-1.1-1.1-2.2-2.2-3.2-3.2 c-0.1-0.1-0.2-0.1-0.3-0.3c0,0.2,0,0.3,0,0.5c0,4,0,7.9,0,11.9c0,0.3-0.1,0.3-0.3,0.3c-0.8,0-1.5,0-2.3,0c0-4.2,0-8.5,0-12.8 c-0.1,0.1-0.2,0.2-0.3,0.3c-1.1,1.1-2.2,2.1-3.2,3.2c-0.2,0.2-0.3,0.2-0.5,0C9.1,12,8.5,11.5,7.9,10.9z"/> </g> </svg>
          </div>
        </label>
        <input id="unoCaptura" type="file" onchange="updateimageplantilla(this,26,'btCaptureScreen','ui/img/botonDescarga/')" class="hideInput">
      </div>
    </div>
  </section>
  <section id="mensajeExitoIF" class="displayNone">
    <ul class="superior">
      <li>
        <img src="ui/img/interfaz/logoplaceholder.svg">
      </li>
      <li>
        <label for="unoMsgExito" id="labelMsgExito" class="bordersetup colorBorderWhite">
          <p class="title backWhite colorBlack">Imagen Mensaje Éxito</p>
          <div class="hoverLabel displayFlex trans5" style="background:rgba(0,0,0,0.8)">
            <p>
              W:900px * H:130px <br>
              <b>.PNG</b>
            </p>
            <svg viewBox="0 0 30 30"> <g> <path class="uploadFillWhite" d="M0.6,26c0-3,0-6,0-9.1c0.9,0,1.8,0,2.7,0c0,2.1,0,4.2,0,6.4c7.8,0,15.6,0,23.4,0c0-2.1,0-4.2,0-6.4 c0.9,0,1.8,0,2.7,0c0,3,0,6,0,9.1C19.8,26,10.2,26,0.6,26z"/> <path class="uploadFillWhite" d="M7.9,10.9c0.3-0.3,0.6-0.5,0.8-0.8c2-2,4-4,6-6c0.2-0.2,0.3-0.2,0.5,0c2.2,2.2,4.4,4.4,6.6,6.6 c0.1,0.1,0.1,0.1,0.2,0.2c-0.1,0.1-0.1,0.1-0.2,0.2c-0.5,0.5-1,1-1.5,1.5c-0.2,0.2-0.3,0.1-0.4,0c-1.1-1.1-2.2-2.2-3.2-3.2 c-0.1-0.1-0.2-0.1-0.3-0.3c0,0.2,0,0.3,0,0.5c0,4,0,7.9,0,11.9c0,0.3-0.1,0.3-0.3,0.3c-0.8,0-1.5,0-2.3,0c0-4.2,0-8.5,0-12.8 c-0.1,0.1-0.2,0.2-0.3,0.3c-1.1,1.1-2.2,2.1-3.2,3.2c-0.2,0.2-0.3,0.2-0.5,0C9.1,12,8.5,11.5,7.9,10.9z"/> </g> </svg>
          </div>
        </label>
        <input id="unoMsgExito" type="file" onchange="updateimageplantilla(this,27,'msgExitoImg','ui/img/mensajeExito/')" class="hideInput">
      </li>
      <li>
        <label for="unoHashtag" id="labelHashtag" class="bordersetup colorBorderWhite">
          <p class="titleDown backWhite colorBlack">Imagen Hashtag</p>
          <div class="hoverLabel displayFlex trans5" style="background:rgba(0,0,0,0.8)">
            <svg viewBox="0 0 30 30"> <g> <path class="uploadFillWhite" d="M0.6,26c0-3,0-6,0-9.1c0.9,0,1.8,0,2.7,0c0,2.1,0,4.2,0,6.4c7.8,0,15.6,0,23.4,0c0-2.1,0-4.2,0-6.4 c0.9,0,1.8,0,2.7,0c0,3,0,6,0,9.1C19.8,26,10.2,26,0.6,26z"/> <path class="uploadFillWhite" d="M7.9,10.9c0.3-0.3,0.6-0.5,0.8-0.8c2-2,4-4,6-6c0.2-0.2,0.3-0.2,0.5,0c2.2,2.2,4.4,4.4,6.6,6.6 c0.1,0.1,0.1,0.1,0.2,0.2c-0.1,0.1-0.1,0.1-0.2,0.2c-0.5,0.5-1,1-1.5,1.5c-0.2,0.2-0.3,0.1-0.4,0c-1.1-1.1-2.2-2.2-3.2-3.2 c-0.1-0.1-0.2-0.1-0.3-0.3c0,0.2,0,0.3,0,0.5c0,4,0,7.9,0,11.9c0,0.3-0.1,0.3-0.3,0.3c-0.8,0-1.5,0-2.3,0c0-4.2,0-8.5,0-12.8 c-0.1,0.1-0.2,0.2-0.3,0.3c-1.1,1.1-2.2,2.1-3.2,3.2c-0.2,0.2-0.3,0.2-0.5,0C9.1,12,8.5,11.5,7.9,10.9z"/> </g> </svg>
          </div>
        </label>
        <input id="unoHashtag" type="file" onchange="updateimageplantilla(this,28,'msgHashtagImg','ui/img/hashtag/')" class="hideInput">
      </li>
    </ul>
    <ul class="inferior displayFlex">
      <!--
      <li>
        <label for="unoFb" id="labelFb" class="bordersetup colorBorderWhite">
          <p class="title backWhite colorBlack">Facebook</p>
          <div class="hoverLabel displayFlex trans5" style="background:rgba(0,0,0,0.8)">
            <p>
              S:92px <br>
              <b>.PNG</b>
            </p>
            <svg viewBox="0 0 30 30"> <g> <path class="uploadFillWhite" d="M0.6,26c0-3,0-6,0-9.1c0.9,0,1.8,0,2.7,0c0,2.1,0,4.2,0,6.4c7.8,0,15.6,0,23.4,0c0-2.1,0-4.2,0-6.4 c0.9,0,1.8,0,2.7,0c0,3,0,6,0,9.1C19.8,26,10.2,26,0.6,26z"/> <path class="uploadFillWhite" d="M7.9,10.9c0.3-0.3,0.6-0.5,0.8-0.8c2-2,4-4,6-6c0.2-0.2,0.3-0.2,0.5,0c2.2,2.2,4.4,4.4,6.6,6.6 c0.1,0.1,0.1,0.1,0.2,0.2c-0.1,0.1-0.1,0.1-0.2,0.2c-0.5,0.5-1,1-1.5,1.5c-0.2,0.2-0.3,0.1-0.4,0c-1.1-1.1-2.2-2.2-3.2-3.2 c-0.1-0.1-0.2-0.1-0.3-0.3c0,0.2,0,0.3,0,0.5c0,4,0,7.9,0,11.9c0,0.3-0.1,0.3-0.3,0.3c-0.8,0-1.5,0-2.3,0c0-4.2,0-8.5,0-12.8 c-0.1,0.1-0.2,0.2-0.3,0.3c-1.1,1.1-2.2,2.1-3.2,3.2c-0.2,0.2-0.3,0.2-0.5,0C9.1,12,8.5,11.5,7.9,10.9z"/> </g> </svg>
          </div>
        </label>
        <input id="unoFb" type="file" class="hideInput">
      </li>
      <li>
        <label for="unoIg" id="labeIg" class="bordersetup colorBorderWhite">
          <p class="title backWhite colorBlack">Instagram</p>
          <div class="hoverLabel displayFlex trans5" style="background:rgba(0,0,0,0.8)">
          <p>
            S:92px <br>
            <b>.PNG</b>
          </p>
            <svg viewBox="0 0 30 30"> <g> <path class="uploadFillWhite" d="M0.6,26c0-3,0-6,0-9.1c0.9,0,1.8,0,2.7,0c0,2.1,0,4.2,0,6.4c7.8,0,15.6,0,23.4,0c0-2.1,0-4.2,0-6.4 c0.9,0,1.8,0,2.7,0c0,3,0,6,0,9.1C19.8,26,10.2,26,0.6,26z"/> <path class="uploadFillWhite" d="M7.9,10.9c0.3-0.3,0.6-0.5,0.8-0.8c2-2,4-4,6-6c0.2-0.2,0.3-0.2,0.5,0c2.2,2.2,4.4,4.4,6.6,6.6 c0.1,0.1,0.1,0.1,0.2,0.2c-0.1,0.1-0.1,0.1-0.2,0.2c-0.5,0.5-1,1-1.5,1.5c-0.2,0.2-0.3,0.1-0.4,0c-1.1-1.1-2.2-2.2-3.2-3.2 c-0.1-0.1-0.2-0.1-0.3-0.3c0,0.2,0,0.3,0,0.5c0,4,0,7.9,0,11.9c0,0.3-0.1,0.3-0.3,0.3c-0.8,0-1.5,0-2.3,0c0-4.2,0-8.5,0-12.8 c-0.1,0.1-0.2,0.2-0.3,0.3c-1.1,1.1-2.2,2.1-3.2,3.2c-0.2,0.2-0.3,0.2-0.5,0C9.1,12,8.5,11.5,7.9,10.9z"/> </g> </svg>
          </div>
        </label>
        <input id="unoIg" type="file" class="hideInput">
      </li>
      <li>
        <label for="unoTw" id="labeTw" class="bordersetup colorBorderWhite">
          <p class="title backWhite colorBlack">Twitter</p>
          <div class="hoverLabel displayFlex trans5" style="background:rgba(0,0,0,0.8)">
            <p>
              S:92px <br>
              <b>.PNG</b>
            </p>
            <svg viewBox="0 0 30 30"> <g> <path class="uploadFillWhite" d="M0.6,26c0-3,0-6,0-9.1c0.9,0,1.8,0,2.7,0c0,2.1,0,4.2,0,6.4c7.8,0,15.6,0,23.4,0c0-2.1,0-4.2,0-6.4 c0.9,0,1.8,0,2.7,0c0,3,0,6,0,9.1C19.8,26,10.2,26,0.6,26z"/> <path class="uploadFillWhite" d="M7.9,10.9c0.3-0.3,0.6-0.5,0.8-0.8c2-2,4-4,6-6c0.2-0.2,0.3-0.2,0.5,0c2.2,2.2,4.4,4.4,6.6,6.6 c0.1,0.1,0.1,0.1,0.2,0.2c-0.1,0.1-0.1,0.1-0.2,0.2c-0.5,0.5-1,1-1.5,1.5c-0.2,0.2-0.3,0.1-0.4,0c-1.1-1.1-2.2-2.2-3.2-3.2 c-0.1-0.1-0.2-0.1-0.3-0.3c0,0.2,0,0.3,0,0.5c0,4,0,7.9,0,11.9c0,0.3-0.1,0.3-0.3,0.3c-0.8,0-1.5,0-2.3,0c0-4.2,0-8.5,0-12.8 c-0.1,0.1-0.2,0.2-0.3,0.3c-1.1,1.1-2.2,2.1-3.2,3.2c-0.2,0.2-0.3,0.2-0.5,0C9.1,12,8.5,11.5,7.9,10.9z"/> </g> </svg>
          </div>
        </label>
        <input id="unoTw" type="file" class="hideInput">
      </li>
      <li>
        <label for="unoYt" id="labeYt" class="bordersetup colorBorderWhite">
          <p class="title backWhite colorBlack">Youtube</p>
          <div class="hoverLabel displayFlex trans5" style="background:rgba(0,0,0,0.8)">
            <p>
              S:92px <br>
              <b>.PNG</b>
            </p>
            <svg viewBox="0 0 30 30"> <g> <path class="uploadFillWhite" d="M0.6,26c0-3,0-6,0-9.1c0.9,0,1.8,0,2.7,0c0,2.1,0,4.2,0,6.4c7.8,0,15.6,0,23.4,0c0-2.1,0-4.2,0-6.4 c0.9,0,1.8,0,2.7,0c0,3,0,6,0,9.1C19.8,26,10.2,26,0.6,26z"/> <path class="uploadFillWhite" d="M7.9,10.9c0.3-0.3,0.6-0.5,0.8-0.8c2-2,4-4,6-6c0.2-0.2,0.3-0.2,0.5,0c2.2,2.2,4.4,4.4,6.6,6.6 c0.1,0.1,0.1,0.1,0.2,0.2c-0.1,0.1-0.1,0.1-0.2,0.2c-0.5,0.5-1,1-1.5,1.5c-0.2,0.2-0.3,0.1-0.4,0c-1.1-1.1-2.2-2.2-3.2-3.2 c-0.1-0.1-0.2-0.1-0.3-0.3c0,0.2,0,0.3,0,0.5c0,4,0,7.9,0,11.9c0,0.3-0.1,0.3-0.3,0.3c-0.8,0-1.5,0-2.3,0c0-4.2,0-8.5,0-12.8 c-0.1,0.1-0.2,0.2-0.3,0.3c-1.1,1.1-2.2,2.1-3.2,3.2c-0.2,0.2-0.3,0.2-0.5,0C9.1,12,8.5,11.5,7.9,10.9z"/> </g> </svg>
          </div>
        </label>
        <input id="unoYt" type="file" class="hideInput">
      </li>
    -->
      <?php echo getmarca_redessocialesinterfaz($marca_id); ?>
    </ul>
  </section>
  <section id="mensajeErrorIF" class="displayNone">
    <div class="displayFlex">
      <label for="unoError" id="labelError" class="bordersetup colorBorderWhite">
        <p class="title backWhite colorBlack">Mensaje de Error</p>
        <div class="hoverLabel displayFlex trans5" style="background:rgba(0,0,0,0.8)">
          <p>
            W:900px * H:195px <br>
            <b>.PNG</b>
          </p>
          <svg viewBox="0 0 30 30"> <g> <path class="uploadFillWhite" d="M0.6,26c0-3,0-6,0-9.1c0.9,0,1.8,0,2.7,0c0,2.1,0,4.2,0,6.4c7.8,0,15.6,0,23.4,0c0-2.1,0-4.2,0-6.4 c0.9,0,1.8,0,2.7,0c0,3,0,6,0,9.1C19.8,26,10.2,26,0.6,26z"/> <path class="uploadFillWhite" d="M7.9,10.9c0.3-0.3,0.6-0.5,0.8-0.8c2-2,4-4,6-6c0.2-0.2,0.3-0.2,0.5,0c2.2,2.2,4.4,4.4,6.6,6.6 c0.1,0.1,0.1,0.1,0.2,0.2c-0.1,0.1-0.1,0.1-0.2,0.2c-0.5,0.5-1,1-1.5,1.5c-0.2,0.2-0.3,0.1-0.4,0c-1.1-1.1-2.2-2.2-3.2-3.2 c-0.1-0.1-0.2-0.1-0.3-0.3c0,0.2,0,0.3,0,0.5c0,4,0,7.9,0,11.9c0,0.3-0.1,0.3-0.3,0.3c-0.8,0-1.5,0-2.3,0c0-4.2,0-8.5,0-12.8 c-0.1,0.1-0.2,0.2-0.3,0.3c-1.1,1.1-2.2,2.1-3.2,3.2c-0.2,0.2-0.3,0.2-0.5,0C9.1,12,8.5,11.5,7.9,10.9z"/> </g> </svg>
        </div>
      </label>
      <input id="unoError" type="file" onchange="updateimageplantilla(this,29,'msgErrorImg','ui/img/mensajeError/')"  class="hideInput">
    </div>
  </section>
  <script src="https://code.jquery.com/jquery-latest.min.js" defer></script>
  <script src="ui/js/interfaz.js" charset="utf-8"></script>
  <script type="text/javascript">
    window.onresize = function(){
      calculateR("#labelBottle", "alto", 1920, 609);
      calculateR("#labelTxInicio", "alto", 171, 500);
      calculateR("#labelObtenerCupon", "alto", 100, 500);
      calculateR("#labelCupon", "alto", 1578, 968);
      calculateR("#labelObtenerCaptura", "alto", 100, 500);
      calculateR("#labelHashtag", "alto", 42, 480);
      calculateR("#labelError", "ancho", 900, 195);
    }
  </script>
</body>
</html>
