addCss("/Code/CSS/pop.css", "css");

var modList = ["charop", "race", "rule", "adventure", "lore", "dms"];
var tulList = ["hmbrw", "genr", "indx"];
var artList = ["vis", "cart", "dun"];
var fulList = modList.concat(tulList).concat(artList);
var type = "module";
var sysNum = 0;
var gertFile = "";

let urlaa = window.location.href;
var filename = urlaa.split('/').pop().split('#')[0].split('?')[0];

const queryString = window.location.search;
const urlParams = new URLSearchParams(queryString);

function typeValue(wassup) {
    document.getElementById("Categories").value = "g";
    var dropSays = "";
    if (wassup == "module") { dropSays = "Modules" }
    else if (wassup == "diggie") { dropSays = "Tools" }
    else { dropSays = "Art" }
    document.getElementById("showType").innerHTML = dropSays;
    document.getElementById("type").value = wassup;
    document.getElementById("returnType").value = wassup;
    document.getElementById("returnCategory").value = wassup;
    resetAll();
    if (wassup == "module") {
        type = "module";
        document.getElementById("moduleMenu").style.display = "block";
        document.getElementById("diggieMenu").style.display = "none";
        document.getElementById("artMenu").style.display = "none";
        document.getElementById("phoneTitle").innerHTML = "Browse Modules";
    }
    else if (wassup == "diggie") {
        type = "diggie";
        document.getElementById("moduleMenu").style.display = "none";
        document.getElementById("diggieMenu").style.display = "block";
        document.getElementById("artMenu").style.display = "none";
        document.getElementById("phoneTitle").innerHTML = "Browse Tools";
   }
    else {
        type = "art";
        document.getElementById("moduleMenu").style.display = "none";
        document.getElementById("diggieMenu").style.display = "none";
        document.getElementById("artMenu").style.display = "block";
        document.getElementById("phoneTitle").innerHTML = "Browse Art";
    }
}

function newSys(value) {
    sysNum = value;
    document.getElementById("gsystem").value = value;
}


function killAll() {
    document.getElementById("SignUp").style.display = "none";
    document.getElementById("modContent").style.display = "none";
}

function showSignIn(showwhat) {
    setReturnValue();
    document.getElementById("SignUp").style.display = "block";
    document.getElementById("modContent").style.display = "block";
    if (showwhat == "fail") { document.getElementById("youFailedMaggot").style.display = "block"; }
    if (document.cookie.indexOf('loggedIn') != -1) {
        document.getElementById("poptitle").innerHTML = "You're signed in!";
        document.getElementById("popCon").style.display = "none";
    }
    if (showwhat == "success") {
        document.getElementById("youFailedMaggot").innerHTML = "You're signed in!";
        document.getElementById("youFailedMaggot").style.color = "green";
        document.getElementById("youFailedMaggot").style.display = "block";
    }
}
function SignOut(){
    document.cookie = "loggedIn=John Smith; expires=Thu, 18 Dec 2013 12:00:00 UTC; path=/";
    location.reload();
}
function checkParams() {
    if (document.cookie.indexOf('loggedIn') != -1) {
        document.getElementById("signProposition").style.display = "none";
        document.getElementById("offProposition").style.display = "inline";
    }
    var why = urlParams.get("why");
    if (why == "signingIn") {
        showSignIn("fail");
    }
    if (why == "success") {
        showSignIn("success");
    }
}

const urla = window.location.href;
function setReturnValue() {
    if (urla.includes("View")) {
        document.getElementById("returnTo").value = "View.php";
        document.getElementById("returnId").value = document.getElementById("toReturnId").value;
    }
    else if (urla.includes("Tool")) {
        document.getElementById("returnTo").value = "Tool.php";
        document.getElementById("returnId").value = document.getElementById("toReturnId").value;
    }
    else if (urla.includes("Search")) {
        document.getElementById("returnTo").value = "Search.php"
        document.getElementById("returnCategory").value = document.getElementById("catInfo").innerHTML;
        document.getElementById("returnQuery").value = document.getElementById("querInfo").innerHTML;
    }
}

   function dropdown() {
        document.getElementById("myDropdown").classList.toggle("show");
  }

window.onclick = function (event) {
  if (!event.target.matches('.dropbtn')) {
    var dropdowns = document.getElementsByClassName("dropdown-content");
    var i;
    for (i = 0; i < dropdowns.length; i++) {
      var openDropdown = dropdowns[i];
      if (openDropdown.classList.contains('show')) {
        openDropdown.classList.remove('show');
  }
}
}
}


function clinnation(clicked) {
    catValue = document.getElementById('Categories').value;
    input = "";
    let gen = "genTri";
    if (type == "module") {
        gen = "gen";
        if (clicked == "charop") { input = "c" }
        else if (clicked == "race") { input = "r" }
        else if (clicked == "rule") { input = "u" }
        else if (clicked == "adventure") { input = "a" }
        else if (clicked == "lore") { input = "l" }
        else if (clicked == "dms") { input = "d" }
    }
    else if (type == "diggie") {
        gen = "genTu";
        if (clicked == "hmbrw") { input = "h" }
        else if (clicked == "genr") { input = "r" }
        else if (clicked == "indx") { input = "i" }
    }
    else {
        if (clicked == "vis") { input = "v" }
        else if (clicked == "cart") { input = "m" }
        else if (clicked == "dun") { input = "n" }
    }
    if (document.getElementById(clicked).style.color == "white") {
        document.getElementById(clicked).style.color = "black";
        document.getElementById(clicked.concat("t")).style.display = "none";
        checkGenWhite();
        newValue = document.getElementById('Categories').value.replace(input, "");
        document.getElementById('Categories').value = newValue;
    }
    else {
        document.getElementById(clicked.concat("t")).style.display = "inline-block";
        document.getElementById(clicked).style.color = "white";
        document.getElementById(gen).style.color = "black";
        catValue = catValue.concat(input);
        document.getElementById('Categories').value = catValue.replace("g", "");
    }
}

function checkGenWhite() {
    if (type == "module") {
        if (
            document.getElementById("charop").style.color == "black" &&
            document.getElementById("race").style.color == "black" &&
            document.getElementById("rule").style.color == "black" &&
            document.getElementById("adventure").style.color == "black" &&
            document.getElementById("lore").style.color == "black" &&
            document.getElementById("dms").style.color == "black") {
            document.getElementById("gen").style.color = "white";
            document.getElementById('Categories').value = "g";
        }
    }
    else if (type == "diggie") {
        if (document.getElementById("hmbrw").style.color == "black" &&
            document.getElementById("genr").style.color == "black" &&
            document.getElementById("indx").style.color == "black") {
            document.getElementById("genTu").style.color = "white";
            document.getElementById('Categories').value = "g";
        }
    }
    else {
        if (document.getElementById("vis").style.color == "black" &&
            document.getElementById("cart").style.color == "black" &&
            document.getElementById("dun").style.color == "black") {
            document.getElementById("genTri").style.color = "white";
            document.getElementById('Categories').value = "g";
        }
    }
}
function resetAll() {
    document.getElementById('Categories').value = "";
    for (x of fulList){
    document.getElementById(x).style.color = "black";
    document.getElementById(x.concat("t")).style.display = "none";
    }
    if (type == "module") { document.getElementById("gen").style.color = "white"; }
    else if (type == "diggie") { document.getElementById("genTu").style.color = "white"; }
    else { document.getElementById("genTri").style.color = "white"; }
}

function whiteStart() {
    if (!filename.includes("Goods") && !filename.includes("Partner")) {
        if ($("#sysInfo").html() != null) { newSys($("#sysInfo").html()); document.getElementById("sysDropdown").value = parseInt(sysNum);}
        else if (urlParams.get("gsystem") != null) {
             newSys(urlParams.get("gsystem")); document.getElementById("sysDropdown").value = parseInt(sysNum);
        }
        var categz = document.getElementById("catInfo").innerHTML;
        if (document.getElementById('typeInfo').innerHTML == "module" || document.getElementById('typeInfo').innerHTML == "m") {
            if (categz.search("c") != -1) { clinnation("charop"); $('#charops').css('display', 'inline-block'); }
            if (categz.search("r") != -1) { clinnation("race"); $('#races').css('display', 'inline-block'); }
            if (categz.search("u") != -1) { clinnation("rule"); $('#rules').css('display', 'inline-block'); }
            if (categz.search("a") != -1) { clinnation("adventure"); $('#adventures').css('display', 'inline-block'); }
            if (categz.search("l") != -1) { clinnation("lore"); $('#lores').css('display', 'inline-block'); }
            if (categz.search("d") != -1) { clinnation("dms"); $('#dmss').css('display', 'inline-block'); }
        }
        else if (document.getElementById('typeInfo').innerHTML == "diggie" || document.getElementById('typeInfo').innerHTML == "d") {
            typeValue("diggie");
            if (categz.search("h") != -1) { clinnation("hmbrw"); $('#hmbrws').css('display', 'inline-block'); }
            if (categz.search("r") != -1) { clinnation("genr"); $('#genrs').css('display', 'inline-block'); }
            if (categz.search("i") != -1) { clinnation("indx"); $('#indxs').css('display', 'inline-block'); }
            $('.dtab').show();
        }
        else {
            typeValue("art");
            if (categz.search("v") != -1) { clinnation("vis"); $('#viss').css('display', 'inline-block'); }
            if (categz.search("m") != -1) { clinnation("cart"); $('#carts').css('display', 'inline-block'); }
            if (categz.search("n") != -1) { clinnation("dun"); $('#duns').css('display', 'inline-block'); }
            $('.atab').show();
        }
    }
}

function nullify() {
    if (filename == "Partner.php") {
        document.getElementById("showType").innerHTML = "Partnership";
        document.getElementById("phoneTitle").innerHTML = "Partnership";
        document.getElementById("type").value = "module";
        type = "module";
        document.getElementById("moduleMenu").style.display = "none";
        document.getElementById("diggieMenu").style.display = "none";
        document.getElementById("artMenu").style.display = "none";
        document.getElementById("returnTo").value = "Partner.php"
        document.getElementById("returnId").value = document.getElementById("partId").innerHTML;
    }

}

function shoBar() {
    if ($(".sideMenu").is(":hidden")) {
        $(".sideMenu").show();
        $(".fas").toggleClass("rotate");
    }
    else {
        $(".sideMenu").hide();
        $(".fas").toggleClass("rotate");
    }
}

function suggestNow() {
    let myValue = document.getElementById("mySearch").value;
    getFile = "findSuggestions.php?q=".concat(myValue);
    getFile = getFile.concat("&t=").concat(type);
    getFile = getFile.concat("&c=").concat(document.getElementById('Categories').value);
    if (type == "module") { getFile = getFile.concat("&s=").concat(document.getElementById('sysDropdown').value); }
    terpFile = getFile;
    parent = document.getElementById("suggestions");
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            var resultJSON = JSON.parse(xhttp.responseText);

            var unordered = document.createElement("UL");
            for (var name in resultJSON) {
                var link = resultJSON[name];
                var node = document.createElement("A");
                var textnode = document.createTextNode(name);
                var listElement = document.createElement("LI");
                node.setAttribute("href", link);
                node.appendChild(textnode);
                listElement.appendChild(node);
                unordered.appendChild(listElement);
            }

            if (!unordered.hasChildNodes()) {
                var node = document.createElement("A");
                var textnode = document.createTextNode("No fitting titles");
                var listElement = document.createElement("LI");
                node.appendChild(textnode);
                listElement.appendChild(node);
                unordered.appendChild(listElement);
            }
            while (parent.firstChild) {
                parent.removeChild(parent.firstChild);
            }
            parent.appendChild(unordered);
            parent.style.display = "block";
        }
    };

    xhttp.open("GET", getFile, true);
    xhttp.send();
}

function killSugg() {
    window.setTimeout(function () { document.getElementById('suggestions').style.display = 'none'; }, 300);
}

responsive('global/dl2-large.css', "big");

function hideSome() {
    if (format == "desktop") {
        var huge = window.matchMedia("(min-width: 1800px), (min-aspect-ratio:3/2)");
        if (huge.matches) {
            $('.4').show();
            $('.5').show();
            $('.6').show();
            $('.7').show();
            $('.8').show();
            if (document.getElementById("e") != null) { document.getElementById("e").style.display = "inline-block" }
            if (document.getElementById("d") != null) { document.getElementById("d").style.display = "inline-block" }
            if (document.getElementById("c") != null) { document.getElementById("c").style.display = "inline-block" }
            if (document.getElementById("a") != null) { document.getElementById("a").style.display = "inline-block" }
            if (document.getElementById("b") != null) { document.getElementById("b").style.display = "inline-block" }
        }
        else {
            $('.4').show();
            $('.5').show();
            $('.6').hide();
            $('.7').hide();
            $('.8').hide();
            if (document.getElementById("e") != null) { document.getElementById("e").style.display = "none" }
            if (document.getElementById("d") != null) { document.getElementById("d").style.display = "none" }
            if (document.getElementById("c") != null) { document.getElementById("c").style.display = "none" }
            if (document.getElementById("a") != null) { document.getElementById("a").style.display = "inline-block" }
            if (document.getElementById("b") != null) { document.getElementById("b").style.display = "inline-block" }
        }
    }
    else {
        $('.4').hide();
        $('.5').hide();
        $('.6').hide();
        $('.7').hide();
        $('.8').hide();
        if (document.getElementById("e") != null) { document.getElementById("e").style.display = "none" }
        if (document.getElementById("d") != null) { document.getElementById("d").style.display = "none" }
        if (document.getElementById("c") != null) { document.getElementById("c").style.display = "none" }
        if (document.getElementById("a") != null) { document.getElementById("a").style.display = "none" }
        if (document.getElementById("b") != null) { document.getElementById("b").style.display = "none" }
    }
}
hideSome();

function includeHTMLdl() {
    var z, i, elmnt, file, xhttp;
    z = document.getElementsByTagName("*");
    for (i = 0; i < z.length; i++) {
        elmnt = z[i];
        file = elmnt.getAttribute("include-html");
        if (file) {
            xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function () {
                if (this.readyState == 4) {
                    if (this.status == 200) { elmnt.innerHTML = this.responseText; }
                    if (this.status == 404) { elmnt.innerHTML = "Page not found."; }
                    let urlaa = window.location.href;
                    var filename = urlaa.split('/').pop().split('#')[0].split('?')[0];
                    if (elmnt.getAttribute("include-html") == "global/gmenu.html" && filename != "Goods.php" && filename != "Partner.php") { whiteStart(); }
                    else if (elmnt.getAttribute("include-html") == "global/gmenu.html" && filename == "Partner.php") { nullify(); }
                    if (elmnt.getAttribute("include-html") == "global/gprods.html") { checkParams(); }
                    elmnt.removeAttribute("include-html");
                    includeHTMLdl();
                }
            }
            xhttp.open("GET", file, true);
            xhttp.send();
            return;
        }
    }
}
includeHTMLdl();
