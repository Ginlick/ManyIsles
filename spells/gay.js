var loaded = false;


function getCookie(name) {
    var cookieArr = document.cookie.split(";");
    for (var i = 0; i < cookieArr.length; i++) {
        var cookiePair = cookieArr[i].split("=");
        if (name == cookiePair[0].trim()) {
            return decodeURIComponent(cookiePair[1]);
        }
    }
    return null;
}

const table = document.getElementById("theTable");

function insertSpellNum() {
    tbodyRowCount = table.tBodies[0].rows.length;
    document.getElementById("spellNumber").innerHTML = tbodyRowCount;
}

function genSide(dic) {
    document.getElementById("listTitle").style.display = "none";
    document.getElementById("titleSpells").style.display = "block";
    let parent = document.getElementById("AddTagsHerePlease");
    while (parent.firstChild) { parent.removeChild(parent.firstChild); }
    document.getElementById("sName").innerHTML = dic.Name;
    document.getElementById("sLevel").innerHTML = "Level " + dic.Level + " spell";
    document.getElementById("sSchool").innerHTML = dic.School;
    document.getElementById("sElement").innerHTML = dic.Element;
    document.getElementById("sCastingTime").innerHTML = "Casting Time: " + dic.CastingTime;
    document.getElementById("sRange").innerHTML = "Range: " + dic.Range;
    document.getElementById("sComponents").innerHTML = "Components: " + dic.Components;
    document.getElementById("sDuration").innerHTML = "Duration: " + dic.Duration;
    document.getElementById("sFullDesc").innerHTML = dic.FullDesc;
    document.getElementById("sClass").innerHTML = dic.Class;
    if (document.getElementById("Seas").style.display == "inline-block" && dic.Source == "Seas") { document.getElementById("AddTagsHerePlease").appendChild(document.getElementById("Seas").cloneNode(true)); }
    else if (document.getElementById("DarkSecrets").style.display == "inline-block" && dic.Source == "DarkSecrets") { document.getElementById("AddTagsHerePlease").appendChild(document.getElementById("DarkSecrets").cloneNode(true)); }
}

function kickColumns(zzz) {
    if (zzz == "Element") return true;
    if (zzz == "CastingTime") return true;
    if (zzz == "Range") return true;
    if (zzz == "Components") return true;
    if (zzz == "Duration") return true;
    if (zzz == "Type") return true;
    if (zzz == "CasterNumer") return true;
    if (zzz == "Note") return true;
    if (zzz == "FullDesc") return true;
    if (zzz == "Source") return true;
    else return false;
}
function kickRows(ya) {
    let selectedClass = getClassVal(getClassField());
    let selectedRace = getClassVal(getRaceField());
    let selectedDeity = getClassVal(getDeityField());
    changeTitle(selectedClass, selectedRace, selectedDeity);
    if (selectedClass.includes("Poultrymancer") && ya.includes("Wizard")) return false;
    if (ya.includes("Creature")) return false;
    else if (ya.includes(selectedRace) || ya.includes(selectedClass) || ya.includes(selectedDeity)) return false;
    else return true;
}
function kickRits(ya) {
    if (ya.includes("Ritual")) return true;
    else return false;
}

function generateTableHead(table, headers) {
    let thead = table.createTHead();
    let row = thead.insertRow();
    for (let header of headers) {
        if (!kickColumns(header)) {
            let th = document.createElement("th");
            let text = document.createTextNode(header);
            th.appendChild(text);
            row.appendChild(th);
            if (header == "Name") {
                th.onclick = function () { sortTable(0, false) }
            }
            if (header == "Level") {
                th.onclick = function () { sortTable(1, true) }
            }
            if (header == "School") {
                th.onclick = function () { sortTable(2, false) }
            }
            if (header == "Type") {
                th.onclick = function () { sortTable(4, false) }
            }
        }
    }
}

var started = false;
var firstDic = "";
function generateTable(table, list) {
    for (let dic of list) {
        if (dic.Note == "exclusive") { continue; };
        if (kickRows(dic.Class)) { addToDatalist(dic.Name); continue; }
        if (kickRits(dic.Type)) { continue; };
        if (kickEm() && kickMythicalRows(dic.Level)) { addToDatalist(dic.Name); continue; }
        let row = table.insertRow();
        if (started == false) { firstDic = dic; started = true }
        row.onclick = function () { genSide(dic) };
        for (kv in dic) {
            if (!kickColumns(kv)) {
                let cell = row.insertCell();
                let text = document.createTextNode(dic[kv]);
                cell.appendChild(text);
            }
        }
    }
}
function addToDatalist(name) {
    datalist = document.getElementById("spellSugg");
    datalist.innerHTML = datalist.innerHTML.concat('<option value="').concat(name).concat('" id="ss').concat(name).concat('" />');
}

function sortTable(n, shouldNumber) {
    var rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
    switching = true;
    dir = "asc";
    while (switching) {
        switching = false;
        rows = table.rows;
        for (i = 1; i < (rows.length - 1); i++) {
            shouldSwitch = false;
            x = rows[i].getElementsByTagName("TD")[n];
            y = rows[i + 1].getElementsByTagName("TD")[n];
            if (shouldNumber) { valuex = parseInt(x.innerHTML); valuey = parseInt(y.innerHTML) } else { valuex = x.innerHTML.toLowerCase(); valuey = y.innerHTML.toLowerCase(); }
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

function clickedTitle() {
    document.getElementById("titleSpells").style.display = "none";
    document.getElementById("listTitle").style.display = "block";
}


function submitTitle() {
    if (document.getElementById("toBeNewTitle").value) {
        let newTitle = document.getElementById("toBeNewTitle").value;
        document.getElementById("titleSpells").innerHTML = newTitle;
        document.getElementById("titleSpells").style.display = "block";
        document.getElementById("listTitle").style.display = "none";
        unsave();
    }
}

function shoBar() {
    var x = document.getElementById("topnav");
    if (x.className === "topnav") {
        x.className += " responsive";
    } else {
        x.className = "topnav";
    }
}

function searchSpells() {
    let value = document.getElementById("spellSearch").value.toLowerCase();
    let table = document.getElementById("theTable");
    if (value == "") {
        let odd = true;
        for (var i = 0, row; row = table.rows[i]; i++) {
            row.style.display = "table-row";
            if (odd == true) {
                row.style.backgroundColor = "#e4e4e3";
                odd = false;
            }
            else { row.style.backgroundColor = "#f2f2f2"; odd = true; }
        }
    }
    else {
        let isNone = true;
        table.rows[0].style.display = "table-row";
        let odd = false;
        for (let row of table.rows) {
            if (!row.cells[1].innerHTML.toLowerCase().includes(value) && row.cells[1].innerHTML != "Name") { row.style.display = "none"; console.log }
            else {
                row.style.display = "table-row";
                isNone = false;
                if (odd == true) {
                    row.style.backgroundColor = "#e4e4e3";
                    odd = false;
                }
                else { row.style.backgroundColor = "#f2f2f2"; odd = true; }
            };
        }
        if (isNone == true) { table.rows[0].style.display = "none"; }
    }
}

let mods = { Seas: "f", DarkSecrets: "f" }

function addSpell(list) {
    let spellToAdd = document.getElementById("spellToAdd").value.toLowerCase();
    let pizza = false;
    for (var i = 0, row; row = table.rows[i]; i++) {
        if (row.cells[0].innerHTML.toLowerCase() == spellToAdd.toLowerCase()) {
            document.getElementById("warner").style.display = "block";
            document.getElementById("adder").style.display = "none";
            document.getElementById("warner").innerHTML = "Spell Already Present!";
            throw "Hehe";
        }
    }
    for (let dic of list) {
        if (dic.Note == "exclusive") {
            if (mods[dic.Source] == "f") { continue; }
        }
        if (dic.Name.toLowerCase() == spellToAdd) {
            pizza = true;
            let row = table.insertRow();
            row.onclick = function () { genSide(dic) };
            for (kv in dic) {
                if (!kickColumns(kv)) {
                    let cell = row.insertCell();
                    let text = document.createTextNode(dic[kv]);
                    cell.appendChild(text);
                }
            }
            insertSpellNum();
            genSide(dic);
            document.getElementById("spellToAdd").value = "";
            unsave();
        }
    }
    if (pizza == false) {
        document.getElementById("warner").style.display = "block";
        document.getElementById("warner").innerHTML = "Unknown Spell";
        document.getElementById("adder").style.display = "none";
    } else {
        document.getElementById("warner").style.display = "none";
        document.getElementById("adder").style.display = "block";
    }
}

function addModule(list) {
    let modToAdd = document.getElementById("moduleToAdd").value;
    pizza = false;
    if (modToAdd == 4251) { pizza = true; modToAdd = "Seas"; }
    if (modToAdd == 6660) { pizza = true; modToAdd = "DarkSecrets"; }
    if (pizza == false) {
        document.getElementById("mwarner").style.display = "block";
        document.getElementById("madder").style.display = "none";
        document.getElementById("mwarner").innerHTML = "False Code!";
    }
    else if (document.getElementById(modToAdd).style.display == "inline-block") {
        document.getElementById("mwarner").style.display = "block";
        document.getElementById("madder").style.display = "none";
        document.getElementById("mwarner").innerHTML = "Module already added";
    }
    else {
        document.getElementById(modToAdd).style.display = "inline-block";
        document.getElementById("modtabCont").style.display = "block";
        document.getElementById("mwarner").style.display = "none";
        document.getElementById("madder").style.display = "block";
        document.getElementById("moduleToAdd").value = "";
        mods[modToAdd] = "t";
        for (let dic of list) {
            if (dic.Source != modToAdd) { continue; }
            if (kickRows(dic.Class)) { addToDatalist(dic.Name); continue; }
            if (kickRits(dic.Type)) { continue; };
            if (kickEm() && kickMythicalRows(dic.Level)) { addToDatalist(dic.Name); continue; }
            let row = table.insertRow();
            row.onclick = function () { genSide(dic) };
            for (kv in dic) {
                if (!kickColumns(kv)) {
                    let cell = row.insertCell();
                    let text = document.createTextNode(dic[kv]);
                    cell.appendChild(text);
                }
            }
        }
        insertSpellNum();
        unsave();
    }
}


function removeSpell() {
    spellName = document.getElementById("sName").innerHTML;
    for (let row of table.rows) {
        if (row.cells[1].innerHTML == spellName) {
            table.deleteRow(row.rowIndex);
        }
    }
    document.getElementById("theTable").childNodes[2].firstChild.click();
    addToDatalist(spellName);
    insertSpellNum();
    unsave();
}

function downloadSpellList() {
    var doc = new jsPDF();
    doc.text(20, 15, document.getElementById("titleSpells").innerHTML);
    function giveLeftBorder(rowsIndex) {
        if (rowsIndex < 88) { return 20 } else if (rowsIndex < 176) { return 50 } else { return 80 };
    }
    function giveHeight(rowsIndex) {
        if (rowsIndex < 88) { return 20 + rowsIndex * 3 } else { return 20 + (rowsIndex - 88) * 3 }
    }
    for (let row of table.rows) {
        doc.setFontSize(5);
        doc.text(giveLeftBorder(row.rowIndex), giveHeight(row.rowIndex), row.cells[2].innerHTML + "   " + row.cells[1].innerHTML);
    }
    doc.save(document.getElementById("titleSpells").innerHTML + '.pdf');
}

let urlaa = window.location.href;
var filename = urlaa.split('/').pop().split('#')[0].split('?')[0];
var urlParams = new URLSearchParams(window.location.search);
var actualList = urlParams.get('sl');
function saveSpellList() {
    var spellList = new Array(document.getElementById("titleSpells").innerHTML);
    if (document.cookie.indexOf('loggedIn') == -1) {
        window.open("/account/Account.html?error=notSignedIn", '_blank');
        //sl = "none";
    }
    else {
        let tableS = document.getElementById("theTable");
        for (var i = 0, row; row = tableS.rows[i]; i++) {
            spellList.push(row.cells[0].innerHTML);
        }
        if (document.getElementById("Seas").style.display == "inline-block") { spellList.push("Seas"); }
        if (document.getElementById("DarkSecrets").style.display == "inline-block") { spellList.push("DarkSecrets"); }

        if (filename == "SavedList.html") {
            window.location.href = "AddList.php?body=".concat(spellList).concat("&sl=").concat(actualList);
        }
        else if (document.cookie.indexOf('spellLists') == -1 && actualList == "none") {
            window.location.href = "NewSpellList.php?body=".concat(spellList);
        }
        else if (document.cookie.indexOf('spellLists') == -1 && actualList != "none") {
            window.location.href = "SetSLCook.php";
        }
        else if (document.cookie.indexOf('spellLists') != -1) {
            window.location.href = "AddList.php?body=".concat(spellList);
        }
        else {
            throw "what?";
        }
    }
}
function unsave() {
    document.getElementById("saveInfo").innerHTML = "Not Saved - Save Now"
}

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
                    if (this.status == 200) { elmnt.innerHTML = this.responseText; }
                    if (this.status == 404) { elmnt.innerHTML = "Page not found."; }
                    elmnt.removeAttribute("w3-include-html");
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

function doPops(what) {
    if (what == "show") {
        document.getElementById("SignUp").style.display = "block";
        document.getElementById("modContent").style.display = "block";
    }
    else if (what == "show2") {
        document.getElementById("SignUp").style.display = "block";
        document.getElementById("moduleContent").style.display = "block";
    }
    else {
        document.getElementById("SignUp").style.display = "none";
        document.getElementById("modContent").style.display = "none";
        document.getElementById("moduleContent").style.display = "none";
        if (document.getElementById("delContent") != null) { document.getElementById("delContent").style.display = "none"; }
        if (document.getElementById("fullContent") != null) { document.getElementById("fullContent").style.display = "none"; }
    }
}
