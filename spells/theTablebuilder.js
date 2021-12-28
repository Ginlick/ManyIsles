function shoBar() {
    var x = document.getElementById("topnav");
    if (x.className === "topnav") {
        x.className += " responsive";
    } else {
        x.className = "topnav";
    }
}

function genSide(dic, huh) {
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
    if (huh == "yay") { document.getElementById("exclusiveNote").style.display = "block" } else { document.getElementById("exclusiveNote").style.display = "none" };

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
    else return false;
}
function kickRows(ya) {
    if (ya.includes("Ritual")) { return true } else { return false };
}
function specExcls(ya) {
    if (ya.includes("exclusive")) {
        return true
    } else { return false }
    ;
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
            console.log(row);
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
function generateTable(table, list) {
    for (let element of list) {
        if (kickRows(element.Type)) continue;
        let row = table.insertRow();
        if (!specExcls(element.Note)) { row.onclick = function () { genSide(element, "nay") }; } else {
            row.onclick = function () { genSide(element, "yay") };
        }
        for (key in element) {
            if (!kickColumns(key)) {
                let cell = row.insertCell();
                let text = document.createTextNode(element[key]);
                cell.appendChild(text);
            }
        }
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
        for (var i = 1, row; row = table.rows[i]; i++) {
            if (!row.cells[1].innerHTML.toLowerCase().includes(value)) { row.style.display = "none" }
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
function sortTable(n, shouldNumber) {
    var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
    table = document.getElementById("theTable");
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

let tableH = document.getElementById("theTable");

generateTable(tableH, spells);

let data = Object.keys(spells[0]);
generateTableHead(tableH, data);

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
                        if (this.status == 200) {elmnt.innerHTML = this.responseText; }
                        if (this.status == 404) {elmnt.innerHTML = "Page not found."; }
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