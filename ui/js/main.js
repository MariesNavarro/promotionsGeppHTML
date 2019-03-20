"use strict";
function _(el){return document.querySelector(el); }
function __(el){return document.querySelectorAll(el); }

function promoTabs(p,t){
  var w = _("#promoTabs"),
      ch = __(".tabButtons");
  w.style.left = p;
  for (var i = 0; i < ch.length; i++) {
    ch[i].setAttribute("class", "trans5 tabButtons");
  }
  t.setAttribute("class", "trans5 tabButtons selectTab");
}


function menuMobile(e, t){
  var w = _("#menuMobile"),
      b = _('login body');
  if(e === "open"){
    b.style.position = "fixed";
    w.setAttribute("class", "displayFlex trans5");
    t.setAttribute("onclick", "menuMobile('close', this)");
    t.setAttribute("class", "hamburgerMenu menuCross displayFlex");
    setTimeout(function(){
      w.style.opacity = "1";
      t.setAttribute("class", "hamburgerMenu hoverCross menuCross displayFlex");
    },700);
  } else {
    b.style.position = "absolute";
    w.style.opacity = "0";
    t.setAttribute("onclick", "menuMobile('open', this)");
    t.setAttribute("class", "hamburgerMenu hamburgerHover displayFlex");
    setTimeout(function(){
      w.setAttribute("class", "displayNone trans5");
    },700);
  }
}
