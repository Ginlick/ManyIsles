
/*function doResponsive() {
    var url = window.location.pathname.split('/');
    var filename = url[url.length - 2];
    if (filename != "f") {
        responsive("/wiki/wik-mobile.css", "smol");
        if (!large.matches) {
            $(".col-l").addClass("col-r");
            $(".col-l").removeClass("col-l");
        }
    }
    else if (url[url.length - 1].includes("f.php") || url[url.length - 1].includes("search.php")) {
        responsive("/wiki/wik-mobile-f.css", "smol");
    }
}
doResponsive();


function includeHTML() {
    var z, i, elmnt, file, xhttp;
    z = document.getElementsByTagName("*");
    for (i = 0; i < z.length; i++) {
        elmnt = z[i];
        file = elmnt.getAttribute("wiki-include-html");
        if (file) {
            xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function () {
                if (this.readyState == 4) {
                    if (this.status == 200) { elmnt.innerHTML = this.responseText; }
                    if (this.status == 404) { elmnt.innerHTML = "Page not found."; }
                    elmnt.removeAttribute("wiki-include-html");
                    if (typeof doOnIncludeLoad !== "undefined") {
                        doOnIncludeLoad(file);
                    }                   
                    includeHTML();
                }
            }
            xhttp.open("GET", file, true);
            xhttp.send();
            return;
        }
    }
    popLoads();
}
includeHTML();

function showBg(x) {
    if (document.getElementById(x).style.height != "100%") {
        var hidd = "#".concat(x);
        $(hidd).toggleClass("in");
        var icon = "#f".concat(x);
        $(icon).toggleClass("rotate");
        var hidcon = "#h".concat(x);
        $(hidcon).toggleClass("sho");
    }
    else {
        document.getElementById(x).style.height = "0";
        var icon = "#f".concat(x);
        $(icon).toggleClass("rotate");
    }
}



function popLoads() {
    if (document.getElementById("showSide") != null) {
        showBg(document.getElementById("showSide").innerHTML);
        var element = document.getElementById('showSide2');
        if (typeof (element) != 'undefined' && element != null) { showBg(document.getElementById("showSide2").innerHTML) }
        var urlParams = new URLSearchParams(window.location.search);
        var o = urlParams.get('o');
        if (o != null) { showBg(o); addHTML(o); }
    }
}

function doModal(x) {
    var modal = document.getElementById("mod".concat(x));
    modal.style.display = "block";
    var span = document.getElementById("close".concat(x));
    span.onclick = function () {
        var modal = document.getElementById("mod".concat(x));
        modal.style.display = "none";
    }
}

*/


if (window.location.pathname.match(/^\/wiki\//)) {
    createPopup("d:poet;b:1;dur:22200;txt:This wiki is deprecated.;bTxt:to docs;bHref:/docs/1/home");
}



