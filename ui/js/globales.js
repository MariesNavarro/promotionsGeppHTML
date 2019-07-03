//var idplantilla = document.getElementById("idplantilla").value;
//console.log('idplantilla: '+idplantilla);

function getPlantillaStr(idplantilla){
  var strplantilla = "";

  switch (idplantilla) {
    case 1:    strplantilla = "plantillaUno";    break;
    case 2:    strplantilla = "plantillaDos";    break;
    default:     strplantilla = "plantillaUno";
  }
  return  strplantilla;
}

//console.log(idplantilla+' '+getPlantillaStr(idplantilla));

var htmlEl = document.getElementById(getPlantillaStr(idplantilla)+'HTML'),
    marca = htmlEl.getAttribute("data-marca"),
    imgBack = marca + ".jpg",
    imgBottle = marca + ".png";
