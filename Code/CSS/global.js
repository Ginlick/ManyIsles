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

if (window.location.href.includes("/ds/")) {
    addCss("https://fonts.googleapis.com/css2?family=Montserrat:wght@100&display=swap", "css");
    addCss("https://fonts.googleapis.com/css2?family=Open+Sans:wght@300&display=swap", "css");
}
else if (window.location.href.includes("/SignedIn.php")) {
    addCss("https://fonts.googleapis.com", "preconnect");
    addCss("https://fonts.gstatic.com", "preconnect");
    addCss("https://fonts.googleapis.com/css2?family=Montserrat:wght@100&display=swap", "css");
}


addCss("https://kit.fontawesome.com/1f4b1e9440.js", "js");
addCss("https://cdnjs.cloudflare.com/ajax/libs/mousetrap/1.4.6/mousetrap.min.js", "js");

addCss("https://fonts.googleapis.com", "preconnect");
addCss("https://fonts.gstatic.com", "preconnect");
addCss("https://fonts.googleapis.com/css2?family=Roboto&display=swap", "css");
addCss("//cdnjs.cloudflare.com/ajax/libs/mousetrap/1.4.6/mousetrap.min.js", "js");

function responsive(fileName, ifWhich) {
    if (ifWhich == "big") {
        if (large.matches) { addCss(fileName, "css"); format = "desktop"; } else { format = "mobile"; }
    }
    else {
        if (!large.matches) { addCss(fileName, "css"); format = "mobile"; } else { format = "desktop"; }
    }
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
    let bigArr = popup.split(";");
    let popupArray = {};
    for (let pair in bigArr) {
        let currArr = bigArr[pair].split(":");
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
    popupImage.setAttribute("alt", "domain");
    let popupP = document.createElement("p");
    popupP.innerHTML = txt;
    if (popupArray["b"] == 1) {
        let popupButton = document.createElement("span");
        popupButton.setAttribute("class", idom);
        if (popupArray["bHref"] == null) {
            popupButton.setAttribute("onclick", "hidePopup(this.parentElement.parentElement);");
            if (popupArray["dur"] == null) { dur = 22000; }
            popupButton.innerHTML = popupArray["bTxt"];
        }
        else {
            let pButtA = document.createElement("a");
            pButtA.setAttribute("href", popupArray["bHref"]);
            pButtA.innerHTML = popupArray["bTxt"];
            popupButton.appendChild(pButtA);
            if (popupArray["dur"] == null) { dur = 8000; }
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

function newpop(x) {
    if (x == "ded") {
        for (let modal of document.getElementsByClassName("modal")) {
            modal.style.display = "none";
        }
        for (let pop of document.getElementsByClassName("modCol")) {
            pop.style.display = "none";
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
whenAvailable("Mousetrap", function () {
    Mousetrap.bind("esc", function () {
        newpop("ded");
    });
});

/*let moddal = document.createElement("DIV");
moddal.classList.add("modal");
document.body.appendChild(moddal);
moddal.addEventListener("click",newpop("ded"), false);

function createModal(popupArray) {
    if (popupArray["cont"] == null) {
        return false;
    }
    if (popupArray["d"] == null) {
        popupArray["d"] = "gen";
    }
    var nmodCont = document.createElement("DIV");
    nmodCont.classList.add("modCol");
    nmodCont.classList.add("modContent");
    nmodCont.classList.add("new");
    nmodCont.innerHTML = ' <img src="' + img2Array[popupArray["d"]] + '" alt="image" class="nmodImg" /> ';
    var nmodBody = document.createElement("DIV");
    nmodBody.classList.add("nmodBody");
    nmodBody.innerHTML = popupArray["cont"];
    nmodCont.appendChild(nmodBody);
    var cross = "<div class='closer' onclick='newpop(\"ded\")'><i class='fas fa-times'></i></div>";
    var parser = new DOMParser();
    var cross = parser.parseFromString(cross, 'text/html');
    nmodCont.appendChild(cross.firstChild);
    document.body.appendChild(nmodCont);
    newpop(nmodCont);
}
function contact() {
    let content = { "cont": "<p>Give us a heads up, feedback, or suggestions to make the Many Isles greater!</p>" };
    createModal(content);
}*/

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
    return "";
}

//account
function signOut(yeah = null) {
    document.cookie = "loggedIn=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
    document.cookie = "loggedP=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
    document.cookie = "spellLists=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
    document.cookie = "spellb=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
    document.cookie = "spellc=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
    document.cookie = "spelld=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
    document.cookie = "spelle=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
    if (yeah == "friendly") { window.location.href = "/account/Account?error=signIn"; }
    else if (yeah == "baddie") { window.location.href = "/account/Account?error=notSignedIn";}
    else {window.location.reload(true);}
}
function seekMaker(returner) {
  if (returner == "dl"){returner = "/dl/home";}
  else if (returner == "ds"){returner = "/ds/store";}
  else if (returner == "publish"){returner = "/ds/Publish";}
  document.cookie='seeker='+returner;
}
