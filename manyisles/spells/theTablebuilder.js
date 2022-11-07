var currSpell = 0;
function genSide(id) {
  getFile = "/spells/returnSpell.php" + "?id=" + id + "&wiki=" + parentWiki;
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function () {
      if (this.readyState == 4 && this.status == 200) {
          let result = xhttp.responseText;
          document.getElementById("sInfo").innerHTML = result;
          currSpell = id;
          getIndexImgs();
      }
  };
  xhttp.open("GET", getFile, true);
  xhttp.send();
}
startspell = 0; if (spells[0]!=undefined){startspell =spells[0]["id"];}
genSide(startspell);

function kickColumns(zzz) {
  if (zzz == "id") return false;
  if (zzz == "Name") return false;
  if (zzz == "Level") return false;
  if (zzz == "School") return false;
  if (zzz == "Class") return false;
  else return true;
}
function kickRows(ya) {
  if (ya != undefined){
    if (ya.includes("Ritual")) { return true } else { return false };
  }
}
function specExcls(ya) {
  if (ya != undefined){
    if (ya.includes("exclusive")) {
        return true
    } else { return false }
  }
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
                th.onclick = function () { sortTable(1, false) }
            }
            if (header == "Level") {
                th.onclick = function () { sortTable(2, true) }
            }
            if (header == "School") {
                th.onclick = function () { sortTable(3, false) }
            }
            if (header == "Class") {
                th.onclick = function () { sortTable(4, false) }
            }
        }
    }
}
function generateTable(table, list) {
    for (let element of list) {
        if (kickRows(element.Type)) continue;
        let row = table.insertRow();
        if (!specExcls(element.Note)) { row.onclick = function () { genSide(element.id) }; } else {
            row.onclick = function () { genSide(element.id) };
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
                row.style.backgroundColor = "var(--spell-accent)";
                odd = false;
            }
            else { row.style.backgroundColor = "transparent"; odd = true; }
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
                    row.style.backgroundColor = "var(--spell-accent)";
                    odd = false;
                }
                else { row.style.backgroundColor = "transparent"; odd = true; }
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
//sortTable(2, true);
