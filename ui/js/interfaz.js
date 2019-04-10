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
