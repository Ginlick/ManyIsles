﻿//requires: domain

responsive('/wiki/mobile.css', "small");

function showBg(x) {
    if (document.getElementById(x) !== null) {
        if (!document.getElementById(x).classList.contains("sho")) {
            document.getElementById(x).children[0].classList.add("in");
            let icon = document.getElementById("f" + x);
            icon.classList.add("rotate");
            document.getElementById(x).classList.add("sho");
        }
        else {
            document.getElementById(x).children[0].classList.remove("in");
            document.getElementById("f" + x).classList.remove("rotate");
            document.getElementById(x).classList.remove("sho");
        }
    }
}

function showMenu(x) {
    if (!x.classList.contains("sho")) {
        x.children[0].classList.add("fa-rotate-90");
        x.classList.add("sho");
        for (let fandomcoll of document.getElementsByClassName("coll")) {
            fandomcoll.classList.add("visible");
        }
        for (let fandomcoll of document.getElementsByClassName("fandomcoll")) {
            fandomcoll.classList.add("visible");
        }
        if (domain == 0 || domain == 3) {
            for (let modal of document.getElementsByClassName("modal")) {
                modal.style.display = "block";
            }
        }
    }
    else {
        x.children[0].classList.remove("fa-rotate-90");
        x.classList.remove("sho");
        for (let fandomcoll of document.getElementsByClassName("coll")) {
            fandomcoll.classList.remove("visible");
        }
        for (let fandomcoll of document.getElementsByClassName("fandomcoll")) {
            fandomcoll.classList.remove("visible");
        }
        setTimeout(function () {
            for (let modal of document.getElementsByClassName("modal")) {
                modal.style.display = "none";
            }
        }, 300);
    }
}

function addHTML(x) {
    var elmnt, file, xhttp;
    elmnt = document.getElementById(x);
    file = elmnt.getAttribute("wiki-load-html");
    if (file) {
        xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function () {
            if (this.readyState == 4) {
                if (this.status == 200) { elmnt.innerHTML = this.responseText; }
                if (this.status == 404) { elmnt.innerHTML = "Page not found."; }
                elmnt.removeAttribute("wiki-load-html");
            }
        }
        xhttp.open("GET", file, true);
        xhttp.send();
        return;
    }
}


function offerSuggestions(searcher, target = "findSuggestions", extent = 0, action = "link", ignore = 0, pdomain = null) {
    if (pdomain == null) { pdomain = domain;}
    var query = searcher.value;
    var getFile = "";
    if (target == "findCategSugg") {
        getFile = "/fandom/findCategSugg.php?q=" + query + "&w=" + parentWiki + "&domain=" + pdomain + "&u=" + user;
    }
    else {
        getFile = "/fandom/findSuggestions.php?q=" + query + "&w=" + parentWiki + "&ig=" + ignore + "&domain=" + pdomain + "&u=" + user;
        if (action == "addSrc"){getFile += "&src=1&mode=1";}
    }
    console.log(getFile);
    parent = searcher.nextElementSibling;
    let xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            var resultJSON = JSON.parse(xhttp.responseText);
            var unordered = document.createElement("UL");
            for (var object of resultJSON) {
                if (!("name" in object)) { continue; }
                if (!("id" in object)) { object["id"] = 0; }
                if (!("genre" in object)) { object["genre"] = ""; }
                if (!("thumbnail" in object)) { object["thumbnail"] = ""; }
                if (!("NSFW" in object)) { object["NSFW"] = 0; }
                if (!("date" in object)) { object["date"] = ""; }
                if (action == "switchSupport"){
                  if (object["genre"] == "Source"){continue;}
                }

                var listElement = document.createElement("LI");
                if (action == "link") {
                    var linkingCont = document.createElement("A");
                    linkingCont.setAttribute("href", baseURL + object["id"] + "/" + object["name"]);
                }
                else {
                    var linkingCont = document.createElement("DIV");
                    if (action == "createLink") { linkingCont.setAttribute("onclick", 'createLink("' + object["id"] + '", "href", ' + pdomain + ');'); }
                    else if (action == "wikthumb") { linkingCont.setAttribute("onclick", 'createLink("' + object["id"] + '", "wikthumb", ' + pdomain + ');'); }
                    else if (action == "switchSupport") {linkingCont.setAttribute("onclick", 'switchSupport("' + object["id"] + '")'); }
                    else if (action == "addCategory") { linkingCont.setAttribute("onclick", 'addCategory("' + object["id"] + '", "' + object["name"] + '")'); }
                    else if (action == "addSrc"){
                        let href = "/fandom/" + object["wiki"]["name"]+"/"+object["id"]+"/"+object["name"];
                        let name = object["wiki"]["name"] + " Wiki. *[Source: "+object["name"]+"]("+href+").* (Many Isles Fandom, "+object["year"]+")";
                        linkingCont.sourceText = name;
                        linkingCont.addEventListener("click", writeSrc);
                    }
                }
                linkingCont.classList.add("linkingCont");
                if (extent > 0) {
                    var thumbnail = document.createElement("DIV");
                    thumbnail.setAttribute("class", "thumbnail imgCont");
                    thumbnail.setAttribute("load-image", object["thumbnail"]);
                    linkingCont.appendChild(thumbnail);
                }
                else {
                    listElement.classList.add("smol");
                    if (object["name"] == "I'm Feeling Lucky") { continue;}
                }
                var textnail = document.createElement("DIV");
                textnail.setAttribute("class", "textnail");
                var pp = document.createElement("P");
                pp.setAttribute("class", "title");
                var roundGenre = document.createElement("SPAN");
                if (object["genre"] != "") {
                    roundGenre.setAttribute("class", "roundInfo pink");
                    roundGenre.innerHTML = object["genre"];
                }
                var roundN = document.createElement("SPAN");
                if (extent > 0 && object["NSFW"] != 0) {
                    roundN.setAttribute("class", "roundInfo");
                    if (object["NSFW"] == 1) {
                        roundN.classList.add("orange");
                    }
                    else {
                        roundN.classList.add("red");
                    }
                    roundN.innerHTML = "NSFW";
                }
                pp.innerHTML = object["name"] + roundGenre.outerHTML + roundN.outerHTML;
                if (extent > 0) { pp.innerHTML = pp.innerHTML + "<br><i>" + object["date"] + "</i>"; }
                textnail.appendChild(pp);
                linkingCont.appendChild(textnail);
                listElement.appendChild(linkingCont);
                unordered.appendChild(listElement);
            }
            if (!unordered.hasChildNodes()) {
                var node = document.createElement("SPAN");
                var textnode = document.createTextNode("No fitting suggestions");
                var listElement = document.createElement("LI");
                listElement.classList.add("smol");
                node.appendChild(textnode);
                listElement.appendChild(node);
                unordered.appendChild(listElement);
            }
            while (parent.firstChild) {
                parent.removeChild(parent.firstChild);
            }
            parent.appendChild(unordered);
            parent.style.display = "block";
            if (extent > 0) {
                getIndexImgs();
            }
            searcher.addEventListener("focusout", (event) => {
                let trie = parent;
                window.setTimeout(function () { trie.style.display = 'none'; }, 300);
            });

        }
    };
    xhttp.open("GET", getFile, true);
    xhttp.send();
}

function newAutoLink(name, reference, wiki = 0) {
    if (name.match(/^[^ ]+ [^ ]+$/) !== null || name.match(/^[^ ]+$/) !== null) {
        if (name.match(/.*s/)) { name.slice(0, -1); }

        if (typeof autoLinks !== 'undefined') {
            autoLinks[name.toLowerCase()] = { "href": reference };
        }
        file = "/mystral/newAutoLink.php?name=" + name + "&reference=" + reference + "&wiki=" + wiki;
        console.log(file);
        xhttp.open("GET", file, true);
        xhttp.send();
    }
    return;
}

function removePops() {
    barsBoi = document.getElementById("barsBoi");
    if (barsBoi.classList.contains("sho")) {
        showMenu(barsBoi);
    }
    else {
        document.getElementById("modal").style.display = "none";
        for (let pop of document.getElementsByClassName("modCol")) {
            pop.style.display = "none";
        }
    }
    if (typeof textareaToFill !== "undefined") {
        textareaToFill.focus();
    }
}

whenAvailable("Mousetrap", function () {
    Mousetrap.bind("esc", function () {
        removePops();
    });
});

//doc-like tabbing
var switched = false;
function switchDis(which) {
    switched = true;
    for (let cont of document.getElementsByClassName("colrTab")) {
        cont.style.display = "none";
    }
    for (let cont of document.getElementsByClassName("navLink")) {
        cont.classList.remove("selected");
    }
    var tab = document.getElementById(which);
    var naver = document.getElementById("sid" + which);
    if (tab != null){tab.style.display = "block";}
    if (naver != null){naver.classList.add("selected");}
}

var urlParams2 = new URLSearchParams(window.location.search);
var view = urlParams2.get("view");
if (view != null){
    switchDis(view);
}

//sortable tables
function callSortTable(e){
  let targ = e.currentTarget;
  if (targ.sortDirection == "none" || targ.sortDirection == "up"){
    targ.sortDirection = "down";
    targ.firstElementChild.classList.remove("up");
    targ.firstElementChild.classList.add("down");
  }
  else if (targ.sortDirection == "down") {
    targ.sortDirection = "up";
    targ.firstElementChild.classList.add("up");
    targ.firstElementChild.classList.remove("down");
  }

  for (let element of document.getElementsByClassName("sortable")){
    if (element == targ){continue;}
    element.sortDirection = "none";
    element.firstElementChild.classList.remove("up");
  }
  sortTable(e.currentTarget.parentTable, e.currentTarget.columnIndex);
}
function sortTable(table, col = 1){
    var rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
    switching = true;
    dir = "asc";
    while (switching) {
        switching = false;
        rows = table.rows;
        for (i = 1; i < (rows.length - 1); i++) {
            shouldSwitch = false;
            x = rows[i].getElementsByTagName("TD")[col];
            y = rows[i + 1].getElementsByTagName("TD")[col];
            //if (x === undefined || y === undefined){continue;}
            valuex = x.innerText.replace(/[^a-zA-Z0-9]+/g, '').toLowerCase();
            valuey = y.innerText.replace(/[^a-zA-Z0-9]+/g, '').toLowerCase();
            console.log(x.innerText);
            if (dir == "asc") {
                if (valuex > valuey) {
                    shouldSwitch = true;
                    break;
                }
            } else if (dir == "desc") {
                if (valuex < valuey) {
                    shouldSwitch = true;
                    break;
                }
            }
        }
        if (shouldSwitch) {
            rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
            switching = true;
            switchcount++;
        } else {
            if (switchcount == 0 && dir == "asc") {
                dir = "desc";
                switching = true;
            }
        }
    }
}
for (let element of document.getElementsByTagName("TABLE")){
    for (let i = 0; i < element.rows[0].cells.length; i++){
      cell = element.rows[0].cells[i];
      if (cell.classList.contains("sortable")){
        cell.addEventListener("click", callSortTable);
        cell.parentTable = element;
        cell.columnIndex = i;
        cell.innerHTML += "<i class='arrow'></i>";
        cell.sortDirection = "none";
      }
    }
  }
