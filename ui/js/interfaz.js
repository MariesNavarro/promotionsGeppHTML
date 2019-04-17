function _(el){return document.querySelector(el); }
function __(el){return document.querySelectorAll(el); }

function changeScreenInterfaz(n){
  var wLoadingIF = _("#loadingIF"),
      wHomeIF = _("#homeIF"),
      cuponIF = _("#cuponIF"),
      mensajeExitoIF = _("#mensajeExitoIF"),
      mensajeErrorIF = _("#mensajeErrorIF");
  switch (n) {
    case 0:
      clearAllScreensIo();
      wLoadingIF.setAttribute("class", "displayFlex");
    break;
    case 1:
      clearAllScreensIo();
      wHomeIF.setAttribute("class", "displayFlex");
      calculateR("#labelBottle", "alto", 1920, 609);
      calculateR("#labelTxInicio", "alto", 171, 500);
      calculateR("#labelObtenerCupon", "alto", 100, 500);
    break;
    case 2:
      clearAllScreensIo();
      cuponIF.setAttribute("class", "displayFlex");
      calculateR("#labelCupon", "alto", 1578, 990);
      calculateR("#labelObtenerCaptura", "alto", 100, 500);

    break;
    case 3:
      clearAllScreensIo();
      mensajeExitoIF.setAttribute("class", "displayFlex");
      calculateR("#labelHashtag", "alto", 42, 480);
    break;
    case 4:
    clearAllScreensIo();
      mensajeErrorIF.setAttribute("class", "displayFlex");
      calculateR("#labelError", "ancho", 900, 195);
    break;
  }
  function clearAllScreensIo(){
    wLoadingIF.setAttribute("class", "displayNone");
    wHomeIF.setAttribute("class", "displayNone");
    cuponIF.setAttribute("class", "displayNone");
    mensajeExitoIF.setAttribute("class", "displayNone");
    mensajeErrorIF.setAttribute("class", "displayNone");
  }
}

function calculateR(el, side, sideConst, sideVar){
  var el = _(el),
      sideC, ratio;
  if(side === "alto"){
      ratio = sideVar/sideConst;
      sideC = el.getBoundingClientRect().height;
      el.style.width = sideC * ratio + "px";
  } else if (side === "ancho") {
      ratio = sideVar/sideConst;
      sideC = el.getBoundingClientRect().width;
      el.style.height = sideC * ratio + "px";
  }
}
var srcfolder='';
var srcsource='';
var indexarray=0;
function getidComponente(idelement)
{
  switch(idelement)
  {
    case '':
    return '';
    break;
    case '':
    return '';
    break;
    case '':
    return '';
    break;
    case '':
    return '';
    break;
    case '':
    return '';
    break;
    case '':
    return '';
    break;
    case '':
    return '';
    break;
    case '':
    return '';
    break;
    case '':
    return '';
    break;
    case '':
    return '';
    break;
    case '':
    return '';
    break;
    case '':
    return '';
    break;
    case '':
    return '';
    break;
    case '':
    return '';
    break;
  }
}
function updateimageplantilla(input,index,idelement,ruta){
  if(input.files.length>0)
  {
    let files = new FormData(), // you can consider this as 'data bag'
        url = 'upload.php';
    var ext=input.files[0].name.split('.').pop();
    files.append('fileName',input.files[0]); // append selected file to the bag named 'file'
    files.append('id',parent.idnvaprom);
    files.append('dir',ruta);
    files.append('ext',ext);
    $.ajax({
        type: 'post',
        url: url,
        processData: false,
        contentType: false,
        data: files,
        success: function (response) {
          if(response!='¡Posible ataque de subida de ficheros!')
          {
            srcfolder=ruta;
            indexarray=index;
            srcsource=response;
            if(idelement.includes(',')){
              var arrelem=idelement.split(',');
              arrelem.forEach(changeimg);
            }
            else {
              changeimg(idelement);
            }

          }

        },
        error: function (err) {

        }
    });
  }
}
function changeimg(idelement) {
  var ele=parent.frames['iframePlantilla'].contentDocument.getElementById(idelement);
  var componenteelement=parent.infopromoedit[indexarray];
  if(indexarray!==32)
  {
    var arrcompoele=componenteelement.split('?');
    arrcompoele[1]=srcsource;
    componenteelement=arrcompoele.join('?');
    parent.infopromoedit[indexarray]=componenteelement;
  }
  else {
    var arrayRS= parent.infopromoedit[indexarray].split('|');
    for(var irs=0;irs<arrayRS.length;irs++)
    {

      var rsClaveValor=arrayRS[irs].split('?');
      if(rsClaveValor.length>2)
      {
        var idel='ic'+rsClaveValor[0];
        if(idel==idelement)
        {
          rsClaveValor[2]=srcsource;
          arrayRS[irs]=rsClaveValor.join('?')
        }
      }

    }
    parent.infopromoedit[indexarray]=arrayRS.join('|')
  }
  if(idelement==='plantillaUno')
  {
    ele.style.backgroundImage='url("'+srcfolder+srcsource+'")';
  }
  else {
      ele.src=srcfolder+srcsource;
  }
}
