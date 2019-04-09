!function(e,t,n){typeof module!="undefined"&&module.exports?module.exports=n():typeof define=="function"&&define.amd?define(t,n):e[t]=n()}(this,"bowser",function(){function t(t){function n(e){var n=t.match(e);return n&&n.length>1&&n[1]||""}function r(e){var n=t.match(e);return n&&n.length>1&&n[2]||""}var i=n(/(ipod|iphone|ipad)/i).toLowerCase(),s=/like android/i.test(t),o=!s&&/android/i.test(t),u=/nexus\s*[0-6]\s*/i.test(t),a=!u&&/nexus\s*[0-9]+/i.test(t),f=/CrOS/.test(t),l=/silk/i.test(t),c=/sailfish/i.test(t),h=/tizen/i.test(t),p=/(web|hpw)os/i.test(t),d=/windows phone/i.test(t),v=/SamsungBrowser/i.test(t),m=!d&&/windows/i.test(t),g=!i&&!l&&/macintosh/i.test(t),y=!o&&!c&&!h&&!p&&/linux/i.test(t),b=n(/edge\/(\d+(\.\d+)?)/i),w=n(/version\/(\d+(\.\d+)?)/i),E=/tablet/i.test(t),S=!E&&/[^-]mobi/i.test(t),x=/xbox/i.test(t),T;/opera/i.test(t)?T={name:"Opera",opera:e,version:w||n(/(?:opera|opr|opios)[\s\/](\d+(\.\d+)?)/i)}:/opr|opios/i.test(t)?T={name:"Opera",opera:e,version:n(/(?:opr|opios)[\s\/](\d+(\.\d+)?)/i)||w}:/SamsungBrowser/i.test(t)?T={name:"Samsung Internet for Android",samsungBrowser:e,version:w||n(/(?:SamsungBrowser)[\s\/](\d+(\.\d+)?)/i)}:/coast/i.test(t)?T={name:"Opera Coast",coast:e,version:w||n(/(?:coast)[\s\/](\d+(\.\d+)?)/i)}:/yabrowser/i.test(t)?T={name:"Yandex Browser",yandexbrowser:e,version:w||n(/(?:yabrowser)[\s\/](\d+(\.\d+)?)/i)}:/ucbrowser/i.test(t)?T={name:"UC Browser",ucbrowser:e,version:n(/(?:ucbrowser)[\s\/](\d+(?:\.\d+)+)/i)}:/mxios/i.test(t)?T={name:"Maxthon",maxthon:e,version:n(/(?:mxios)[\s\/](\d+(?:\.\d+)+)/i)}:/epiphany/i.test(t)?T={name:"Epiphany",epiphany:e,version:n(/(?:epiphany)[\s\/](\d+(?:\.\d+)+)/i)}:/puffin/i.test(t)?T={name:"Puffin",puffin:e,version:n(/(?:puffin)[\s\/](\d+(?:\.\d+)?)/i)}:/sleipnir/i.test(t)?T={name:"Sleipnir",sleipnir:e,version:n(/(?:sleipnir)[\s\/](\d+(?:\.\d+)+)/i)}:/k-meleon/i.test(t)?T={name:"K-Meleon",kMeleon:e,version:n(/(?:k-meleon)[\s\/](\d+(?:\.\d+)+)/i)}:d?(T={name:"Windows Phone",windowsphone:e},b?(T.msedge=e,T.version=b):(T.msie=e,T.version=n(/iemobile\/(\d+(\.\d+)?)/i))):/msie|trident/i.test(t)?T={name:"Internet Explorer",msie:e,version:n(/(?:msie |rv:)(\d+(\.\d+)?)/i)}:f?T={name:"Chrome",chromeos:e,chromeBook:e,chrome:e,version:n(/(?:chrome|crios|crmo)\/(\d+(\.\d+)?)/i)}:/chrome.+? edge/i.test(t)?T={name:"Microsoft Edge",msedge:e,version:b}:/vivaldi/i.test(t)?T={name:"Vivaldi",vivaldi:e,version:n(/vivaldi\/(\d+(\.\d+)?)/i)||w}:c?T={name:"Sailfish",sailfish:e,version:n(/sailfish\s?browser\/(\d+(\.\d+)?)/i)}:/seamonkey\//i.test(t)?T={name:"SeaMonkey",seamonkey:e,version:n(/seamonkey\/(\d+(\.\d+)?)/i)}:/firefox|iceweasel|fxios/i.test(t)?(T={name:"Firefox",firefox:e,version:n(/(?:firefox|iceweasel|fxios)[ \/](\d+(\.\d+)?)/i)},/\((mobile|tablet);[^\)]*rv:[\d\.]+\)/i.test(t)&&(T.firefoxos=e)):l?T={name:"Amazon Silk",silk:e,version:n(/silk\/(\d+(\.\d+)?)/i)}:/phantom/i.test(t)?T={name:"PhantomJS",phantom:e,version:n(/phantomjs\/(\d+(\.\d+)?)/i)}:/slimerjs/i.test(t)?T={name:"SlimerJS",slimer:e,version:n(/slimerjs\/(\d+(\.\d+)?)/i)}:/blackberry|\bbb\d+/i.test(t)||/rim\stablet/i.test(t)?T={name:"BlackBerry",blackberry:e,version:w||n(/blackberry[\d]+\/(\d+(\.\d+)?)/i)}:p?(T={name:"WebOS",webos:e,version:w||n(/w(?:eb)?osbrowser\/(\d+(\.\d+)?)/i)},/touchpad\//i.test(t)&&(T.touchpad=e)):/bada/i.test(t)?T={name:"Bada",bada:e,version:n(/dolfin\/(\d+(\.\d+)?)/i)}:h?T={name:"Tizen",tizen:e,version:n(/(?:tizen\s?)?browser\/(\d+(\.\d+)?)/i)||w}:/qupzilla/i.test(t)?T={name:"QupZilla",qupzilla:e,version:n(/(?:qupzilla)[\s\/](\d+(?:\.\d+)+)/i)||w}:/chromium/i.test(t)?T={name:"Chromium",chromium:e,version:n(/(?:chromium)[\s\/](\d+(?:\.\d+)?)/i)||w}:/chrome|crios|crmo/i.test(t)?T={name:"Chrome",chrome:e,version:n(/(?:chrome|crios|crmo)\/(\d+(\.\d+)?)/i)}:o?T={name:"Android",version:w}:/safari|applewebkit/i.test(t)?(T={name:"Safari",safari:e},w&&(T.version=w)):i?(T={name:i=="iphone"?"iPhone":i=="ipad"?"iPad":"iPod"},w&&(T.version=w)):/googlebot/i.test(t)?T={name:"Googlebot",googlebot:e,version:n(/googlebot\/(\d+(\.\d+))/i)||w}:T={name:n(/^(.*)\/(.*) /),version:r(/^(.*)\/(.*) /)},!T.msedge&&/(apple)?webkit/i.test(t)?(/(apple)?webkit\/537\.36/i.test(t)?(T.name=T.name||"Blink",T.blink=e):(T.name=T.name||"Webkit",T.webkit=e),!T.version&&w&&(T.version=w)):!T.opera&&/gecko\//i.test(t)&&(T.name=T.name||"Gecko",T.gecko=e,T.version=T.version||n(/gecko\/(\d+(\.\d+)?)/i)),!T.windowsphone&&!T.msedge&&(o||T.silk)?T.android=e:!T.windowsphone&&!T.msedge&&i?(T[i]=e,T.ios=e):g?T.mac=e:x?T.xbox=e:m?T.windows=e:y&&(T.linux=e);var N="";T.windowsphone?N=n(/windows phone (?:os)?\s?(\d+(\.\d+)*)/i):i?(N=n(/os (\d+([_\s]\d+)*) like mac os x/i),N=N.replace(/[_\s]/g,".")):o?N=n(/android[ \/-](\d+(\.\d+)*)/i):T.webos?N=n(/(?:web|hpw)os\/(\d+(\.\d+)*)/i):T.blackberry?N=n(/rim\stablet\sos\s(\d+(\.\d+)*)/i):T.bada?N=n(/bada\/(\d+(\.\d+)*)/i):T.tizen&&(N=n(/tizen[\/\s](\d+(\.\d+)*)/i)),N&&(T.osversion=N);var C=N.split(".")[0];if(E||a||i=="ipad"||o&&(C==3||C>=4&&!S)||T.silk)T.tablet=e;else if(S||i=="iphone"||i=="ipod"||o||u||T.blackberry||T.webos||T.bada)T.mobile=e;return T.msedge||T.msie&&T.version>=10||T.yandexbrowser&&T.version>=15||T.vivaldi&&T.version>=1||T.chrome&&T.version>=20||T.samsungBrowser&&T.version>=4||T.firefox&&T.version>=20||T.safari&&T.version>=6||T.opera&&T.version>=10||T.ios&&T.osversion&&T.osversion.split(".")[0]>=6||T.blackberry&&T.version>=10.1||T.chromium&&T.version>=20?T.a=e:T.msie&&T.version<10||T.chrome&&T.version<20||T.firefox&&T.version<20||T.safari&&T.version<6||T.opera&&T.version<10||T.ios&&T.osversion&&T.osversion.split(".")[0]<6||T.chromium&&T.version<20?T.c=e:T.x=e,T}function r(e){return e.split(".").length}function i(e,t){var n=[],r;if(Array.prototype.map)return Array.prototype.map.call(e,t);for(r=0;r<e.length;r++)n.push(t(e[r]));return n}function s(e){var t=Math.max(r(e[0]),r(e[1])),n=i(e,function(e){var n=t-r(e);return e+=(new Array(n+1)).join(".0"),i(e.split("."),function(e){return(new Array(20-e.length)).join("0")+e}).reverse()});while(--t>=0){if(n[0][t]>n[1][t])return 1;if(n[0][t]!==n[1][t])return-1;if(t===0)return 0}}function o(e,r,i){var o=n;typeof r=="string"&&(i=r,r=void 0),r===void 0&&(r=!1),i&&(o=t(i));var u=""+o.version;for(var a in e)if(e.hasOwnProperty(a)&&o[a]){if(typeof e[a]!="string")throw new Error("Browser version in the minVersion map should be a string: "+a+": "+String(e));return s([u,e[a]])<0}return r}function u(e,t,n){return!o(e,t,n)}var e=!0,n=t(typeof navigator!="undefined"?navigator.userAgent||"":"");return n.test=function(e){for(var t=0;t<e.length;++t){var r=e[t];if(typeof r=="string"&&r in n)return!0}return!1},n.isUnsupportedBrowser=o,n.compareVersions=s,n.check=u,n._detect=t,n})
"use strict";
function _(el){return document.querySelector(el); }
function __(el){return document.querySelectorAll(el); }
var wLoad = _("#loading"),
    wHome = _("#homeUno"),
    wLoadCoupon = _("#loading"),
    textoEdo = _("#textoEdo"),
    cuponUno = _("#cuponUno"),
    mensajeUno = _("#mensajeUno"),
    proveedorUno = _("#proveedorUno"),
    preventW = _("#prevent"),
    preventTx = preventW.children[0];

var checkMobile = false;
if(bowser.mobile || bowser.tablet || /SymbianOS/.test(window.navigator.userAgent)) checkMobile = true;

function preventHeight(){
  if(!checkMobile){
  	var h = window.innerHeight;
  	if(h <= 350){
      preventTx.innerHTML = "Porfavor haz más grande tu ventana";
  		preventW.setAttribute("class", "displayFlex");
  	} else {
      preventTx.innerHTML = " ";
  		preventW.setAttribute("class", "displayNone");
  	}
  }
}

function preventRot(){
  if(checkMobile){
    if(window.orientation == 90 || window.orientation == -90){
      preventTx.innerHTML = "Porfavor voltea tu celular";
      preventW.setAttribute("class", "displayFlex");
    } else {
      preventTx.innerHTML = " ";
      preventW.setAttribute("class", "displayNone");
    }
  }
}

function loadingImages(){
  var back = false, bottle = false;
  loadEl("ui/img/back/", imgBack);
  loadEl("ui/img/producto/", imgBottle);
  function loadEl(url, name){
    var el = new XMLHttpRequest();
    el.open("GET", url+name, true);
    el.responseType = "blob";
    el.onload = function(e){
      if(this.readyState == 4){
        if(url === "ui/img/back/")back = true;
        if(url === "ui/img/back/")bottle = true;
        if(back && bottle){
          imgReady();
        }
      }
    }
    el.send();
  }
  function imgReady(){
    setTimeout(function(){
      wLoad.style.opacity = "0";
      wHome.setAttribute("class", "trans5");
      setTimeout(function(){
        wLoad.style.display = "none";
        wHome.style.opacity = "1";
      },700);
    },2000);
  }
}

function showLoadCoupon(){
  textoEdo.innerHTML = "Carga de cupón";
  wHome.style.opacity = "0";
  setTimeout(function(){
    textoEdo.setAttribute("style", " ");
    wLoadCoupon.setAttribute("style", "background:none");
    wHome.style.display = "none";
  },700);
}

function showCoupon(){
  textoEdo.style.opacity = "0";
  wLoadCoupon.style.opacity = "0";
  cuponUno.style.display = "block";
  setTimeout(function(){
    wLoadCoupon.setAttribute("style", "display:none");
    cuponUno.style.opacity = "1";
    textoEdo.innerHTML = "Cupón Listo";
    textoEdo.style.opacity = "1";
    proveedorUno.style.opacity = "1";
  },700);
}

function hideCoupon(){
  cuponUno.style.opacity = "0";
  setTimeout(function(){
    cuponUno.style.display = "none";
  },700)
}

function showMsg(n){
  var msg = mensajeUno.children[n];
  msg.setAttribute("style", " ");
  mensajeUno.setAttribute("style", " ");
  setTimeout(function(){
    mensajeUno.style.opacity = "1";
  },100);
}

function hideMsg(n){
  var msg = mensajeUno.children[n];
  msg.style.display = "none";
  mensajeUno.style.opacity = "0";
}

function ctaCoupon(){
  showLoadCoupon();
  setTimeout(function(){
    showCoupon();
  },3000);
}

function ctaDownloadImg(){
  hideCoupon();
  //Si 0 = Mensaje de éxito
  //Si 1 = Mensaje de error
  showMsg(0);
}

function screensOnConf(n){
  switch (n) {
    case 0:
      clearAllScreens();
      wLoad.setAttribute("style", " ");
    break;
    case 1:
      clearAllScreens();
      wHome.setAttribute("class", "trans5");
      wHome.style.opacity = "1";
    break;
    case 2:
      clearAllScreens();
      cuponUno.style.display = "block";
      cuponUno.style.opacity = "1";
      textoEdo.innerHTML = "Cupón Listo";
      textoEdo.style.opacity = "1";
      proveedorUno.style.opacity = "1";
    break;
    case 3:
    clearAllScreens();
    showMsg(0);
    textoEdo.innerHTML = "Cupón Listo";
    textoEdo.style.opacity = "1";
    proveedorUno.style.opacity = "1";
    break;
    case 4:
    clearAllScreens();
    showMsg(1);
    textoEdo.innerHTML = "Cupón Listo";
    textoEdo.style.opacity = "1";
    proveedorUno.style.opacity = "1";
    break;
  }
  function clearAllScreens(){
    wLoad.style.opacity = "0";
    wLoad.style.display = "none";
    wHome.setAttribute("class", "trans5 displayNone");
    wHome.style.opacity = "0";
    cuponUno.style.display = "none";
    cuponUno.style.opacity = "0";
    hideMsg(0);
    hideMsg(1);
    textoEdo.innerHTML = " ";
    textoEdo.style.opacity = "0";
    proveedorUno.style.opacity = "0";
  }
}
