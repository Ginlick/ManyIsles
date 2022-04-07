var format = "desktop";
var large = window.matchMedia("(min-width: 1000px), (min-aspect-ratio:3/4)");
var parser = new DOMParser();

function includeHTML() {
    var z, i, elmnt, file, xhttp;
    z = document.getElementsByTagName("*");
    for (i = 0; i < z.length; i++) {
        elmnt = z[i];
        file = elmnt.getAttribute("w3-include-html");
        if (file) {
            xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function () {
                if (this.readyState == 4) {
                    if (this.status == 200) {
                        if (elmnt.getAttribute("w3-create-newEl") == "true") {
                            elmnt.parentElement.insertBefore(parser.parseFromString(this.responseText, "text/html").firstChild.children[1].firstChild, elmnt);
                            elmnt.remove();
                        }
                        else { elmnt.innerHTML = this.responseText; elmnt.removeAttribute("w3-include-html"); }
                    }
                    if (this.status == 404) { elmnt.innerHTML = "Page not found."; }
                    includeHTML();
                }
            }
            xhttp.open("GET", file, true);
            xhttp.send();
            return;
        }
    }
}
includeHTML();

function getIndexImgs() {
    var z, i, file, elmnt, xhttp;
    z = document.getElementsByTagName("*");
    for (i = 0; i < z.length; i++) {
        elmnt = z[i];
        file = elmnt.getAttribute("load-image");
        if (file) {
            var thumbnail = document.createElement("IMG");
            thumbnail.setAttribute("style", "display:none;");
            thumbnail.setAttribute("onload", 'this.setAttribute("style", "display:block;");this.parentElement.classList.add("loaded");');
            thumbnail.src = file;
            thumbnail.setAttribute("alt", "Failed to load");
            thumbnail.setAttribute("class", "linkim");
            elmnt.appendChild(thumbnail);
            elmnt.removeAttribute("load-image");
            if (this.status == 404) { thumbnail.setAttribute("style", "display:block;");  continue; }
        }
    }
}
getIndexImgs();

function addCss(fileName, fileType) {
    var head = document.head;
    if (fileType == "css") {
        var link = document.createElement("link");
        link.type = "text/css";
        link.rel = "stylesheet";
        link.href = fileName;
    }
    else if (fileType == "js") {
        var link = document.createElement("script");
        link.src = fileName;
    }
    else {
        var link = document.createElement("link");
        link.rel = fileType;
    }
    head.appendChild(link);
}

function whenAvailable(name, callback) {
    var interval = 10; // ms
    window.setTimeout(function () {
        if (window[name]) {
            callback(window[name]);
        } else {
            whenAvailable(name, callback);
        }
    }, interval);
}

function formatBytes(bytes, decimals = 2) {
    if (bytes === 0) return '0 Bytes';

    const k = 1024;
    const dm = decimals < 0 ? 0 : decimals;
    const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];

    const i = Math.floor(Math.log(bytes) / Math.log(k));

    return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
}

if (window.location.href.includes("/ds/")) {
    addCss("https://fonts.googleapis.com/css2?family=Montserrat:wght@100&display=swap", "css");
    addCss("https://fonts.googleapis.com/css2?family=Open+Sans:wght@300&display=swap", "css");
}
else if (window.location.href.includes("/SignedIn.php")) {
    addCss("https://fonts.googleapis.com", "preconnect");
    addCss("https://fonts.gstatic.com", "preconnect");
    addCss("https://fonts.googleapis.com/css2?family=Montserrat:wght@100&display=swap", "css");
}
addCss("/Code/CSS/pop.2.css", "css");

addCss("https://kit.fontawesome.com/1f4b1e9440.js", "js");
addCss("https://cdnjs.cloudflare.com/ajax/libs/mousetrap/1.4.6/mousetrap.min.js", "js");

addCss("https://fonts.googleapis.com", "preconnect");
addCss("https://fonts.gstatic.com", "preconnect");
addCss("https://fonts.googleapis.com/css2?family=Roboto&display=swap", "css");

function responsive(fileName, ifWhich) {
    if (ifWhich == "big") {
        if (large.matches) { addCss(fileName, "css"); format = "desktop"; } else { format = "mobile"; }
    }
    else {
        if (!large.matches) { addCss(fileName, "css"); format = "mobile"; } else { format = "desktop"; }
    }
}

//remove parameters
function removeParam(key, sourceURL) {
    var rtn = sourceURL.split("?")[0],
        param,
        params_arr = [],
        queryString = (sourceURL.indexOf("?") !== -1) ? sourceURL.split("?")[1] : "";
    if (queryString !== "") {
        params_arr = queryString.split("&");
        for (var i = params_arr.length - 1; i >= 0; i -= 1) {
            param = params_arr[i].split("=")[0];
            if (param === key) {
                params_arr.splice(i, 1);
            }
        }
        if (params_arr.length) rtn = rtn + "?" + params_arr.join("&");
    }
    return rtn;
}

//pBars

var txtOArray = {
    "0": "Changes effectuated",
    "1": "Changes failed"
};
var imgOArray = {
    "gen": "/Imgs/doms/gen.png",
    "pub": "/Imgs/doms/pub.png",
    "dsp": "/Imgs/doms/pub.png",
    "poet": "/Imgs/doms/pen.png",
    "acc": "/Imgs/doms/acc.png"
};
var img2Array = {
    "gen": "/Imgs/PopupGeneral.png",
    "pub": "/Imgs/PopTrade.png",
    "poet": "/Imgs/PopPoet.png",
    "acc": "/Imgs/PopupBar.png",
    "spells": "/Imgs/PopupSpells.png"
}
function createPopup(popup) {
    let bigArr = popup.match(/([^\\\][^;]|\\;)+/g);
    let popupArray = {};
    for (let pair in bigArr) {
        let currArr = bigArr[pair].match(/([^\\\][^:]|\\:)+/g);
        popupArray[currArr[0]] = currArr[1];
    }
    if (popupArray["d"] == null) {
        idom = "gen";
    }
    else { idom = popupArray["d"]; }
    if (popupArray["b"] == null) {
        popupArray["b"] = "0";
    }
    if (popupArray["dur"] == null) {
        var dur = 4000;
    } else { var dur = parseInt(popupArray["dur"]); }
    let txt = "Done";
    if (popupArray["txt"] != null) {
        txt = popupArray["txt"]
    }
    else if (popupArray["txtO"] != null) {
        txt = txtOArray[popupArray["txtO"]];
    }
    if (popupArray["bTxt"] == null) {
        popupArray["bTxt"] = "Dismiss";
    }
    let popupBar = document.createElement("div");
    popupBar.setAttribute("class", "popupBar");
    let popupImage = document.createElement("img");
    popupImage.setAttribute("src", imgOArray[idom]);
    popupImage.setAttribute("alt", idom);
    let popupP = document.createElement("p");
    popupP.innerHTML = txt;
    if (popupArray["b"] == 1) {
        let popupButton = document.createElement("span");
        popupButton.setAttribute("class", idom);
        if (popupArray["bHref"] != null) {
          let pButtA = document.createElement("a");
          pButtA.setAttribute("href", popupArray["bHref"]);
          pButtA.innerHTML = popupArray["bTxt"];
          popupButton.appendChild(pButtA);
          if (popupArray["dur"] == null) { dur = 8000; }
        }
        else if (popupArray["bAct"] != null) {
          popupButton.setAttribute("onclick", "hidePopup(this.parentElement.parentElement);"+popupArray["bAct"]);
          popupButton.innerHTML = popupArray["bTxt"];
        }
        else {
          popupButton.setAttribute("onclick", "hidePopup(this.parentElement.parentElement);");
          if (popupArray["dur"] == null) { dur = 22000; }
          popupButton.innerHTML = popupArray["bTxt"];
        }
        popupP.appendChild(popupButton);
    }
    popupBar.appendChild(popupImage);
    popupBar.appendChild(popupP);
    document.getElementById("popupBar-container").appendChild(popupBar);
    setTimeout(function () {
        popupBar.classList.toggle('show');
        setTimeout(function () {
           hidePopup(popupBar);
        }, dur);
    }, 30);
}
function hidePopup(element) {
    element.classList.toggle('hide');
    setTimeout(function () {
        element.style.display = "none";
    }, 500);
}
let neatContContCont = document.createElement("div");
let neatContCont = document.createElement("div");
neatContCont.setAttribute("class", "popupBar-container");
neatContCont.setAttribute("id", "popupBar-container");
neatContContCont.setAttribute("class", "popupBar-contCont");
neatContContCont.appendChild(neatContCont);
document.body.appendChild(neatContContCont);

//newPop

function newpop(x = "ded") {
  if (x == "ded") {
      for (let modal of document.getElementsByClassName("modal")) {
          modal.style.display = "none";
      }
      for (let pop of document.getElementsByClassName("modCol")) {
        if (pop.classList.contains("killable")){
          pop.remove();
        }
        else {
          pop.style.display = "none";
        }
      }
      if (typeof myField !== "undefined" && myField != null) {
        myField.focus();
      }
  }
  else {
    for (let modal of document.getElementsByClassName("modal")) {
        modal.style.display = "block";
    }
    if (typeof (x) == "object") { x.style.display = "block"; }
    else {
        document.getElementById(x).style.display = "block";
    }
  }
}
function killpop() {
  newpop("ded");
}
whenAvailable("Mousetrap", function () {
  Mousetrap.bind("esc", function () {
      newpop("ded");
  });
  Mousetrap.stopCallback = function () {
  }
});

let moddal = document.createElement("DIV");
moddal.classList.add("modal");
moddal.addEventListener("click", killpop);
document.body.appendChild(moddal);

function createModal(popupArray) {
    if (popupArray["cont"] == null) {return false;}
    if (popupArray["d"] == null) {popupArray["d"] = "gen";}
    if (popupArray["type"] == null) {popupArray["type"] = "standard";}
    var nmodCol = document.createElement("DIV");
    nmodCol.classList.add("modCol");
    var nmodCont = document.createElement("DIV");
    nmodCont.classList.add("modContent");
    nmodCont.classList.add(popupArray["type"]);
    nmodCont.classList.add("new");
    if (popupArray["id"]==""){nmodCol.classList.add("killable");}
    else {nmodCont.setAttribute("id", popupArray["id"]);}
    if (popupArray["type"]=="standard"){
      nmodCont.innerHTML = ' <img src="' + img2Array[popupArray["d"]] + '" alt="image" class="nmodImg" /> ';
    }
    var nmodBody = document.createElement("DIV");
    nmodBody.classList.add("nmodBody");
    nmodBody.innerHTML = popupArray["cont"];
    nmodCont.appendChild(nmodBody);
    var cross = document.createElement("DIV");
    cross.classList.add("closer");
    cross.addEventListener("click", killpop);
    cross.innerHTML = "<i class='fas fa-times'></i>";
    nmodCont.appendChild(cross);
    nmodCol.appendChild(nmodCont);
    document.body.appendChild(nmodCol);
    newpop(nmodCol);
}
/*function contact() {
    let content = { "cont": "<p>Give us a heads up, feedback, or suggestions to make the Many Isles greater!</p>" };
    createModal(content);
}*/

function getSelectionText() {
    var text = "";
    if (window.getSelection) {
        text = window.getSelection().toString();
    } else if (document.selection && document.selection.type != "Control") {
        text = document.selection.createRange().text;
    }
    return text;
}
function insertText(myField, myText) {
    var startPos = myField.selectionStart;
    var endPos = myField.selectionEnd;
    myField.value = myField.value.substring(0, startPos)
        + myText
        + myField.value.substring(endPos, myField.value.length);
    endPos += startPos - endPos;
    myField.setSelectionRange(endPos + myText.length, endPos + myText.length);
}

//cookies
function getCookie(cname) {
    let name = cname + "=";
    let decodedCookie = decodeURIComponent(document.cookie);
    let ca = decodedCookie.split(';');
    for (let i = 0; i < ca.length; i++) {
        let c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return false;
}

//account
function signOut(yeah = null) {
    document.cookie = "loggedIn=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
    document.cookie = "loggedCode=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
    let newpath = removeParam("i", window.location.href);

    if (yeah == "friendly") { window.location.href = "/account/Account?error=signIn"; }
    else if (yeah == "baddie") { window.location.href = "/account/Account?error=notSignedIn";}
    else {
      if (window.location.href != newpath){
        window.location.href = newpath;
      }
      else {
        window.location.reload();
      }
    }

}
function seekMaker(returner) {
  if (returner == "dl"){returner = "/dl/home";}
  else if (returner == "ds"){returner = "/ds/store";}
  else if (returner == "publish"){returner = "/ds/Publish";}
  document.cookie='seeker='+returner;
}


//cookie accepted checker
if (localStorage["alertdisplayed"]=='true' && !getCookie("acceptCookies")){
  createPopup("txt:We use cookies to recognize users and their preferences.;b:1;bTxt:accept;dur:55000;bAct:acceptCookies();'")
}
function acceptCookies() {
  document.cookie = "acceptCookies=1; path=/;";
}
