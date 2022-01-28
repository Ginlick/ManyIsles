table = document.getElementById("theTable");

function doPops(type) {
  if (type == "hide"){
    $(".modal").hide();
    $(".modCol").hide();
  }
  else {
    $(".modCol").hide();
    $(".modal").show();
    $("#"+type).show();
    $("#spellToAdd").val("");
    if (type == "modContent"){
      $("#spellToAdd").focus();
    }
  }
}
function fulfillQuery(url, cb = null){
  getFile = "/spells/"+ url + "&id=" + currSpell + "&list=" + list;
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function () {
      if (this.readyState == 4 && this.status == 200) {
          let result = xhttp.responseText;
          if (!result.includes("Error.")){
            if (typeof cb === 'function') cb(result);
          }
          else {
            console.log(getFile);
            createPopup("d:poet;txt:Error.");
            return false;
          }
      }
      else if (this.readyState == 4) {
          createPopup("d:poet;txt:Error.");
          return true;
      }
  };
  xhttp.open("GET", getFile, false);
  xhttp.send();
}
function removeSpell() {
  if (fulfillQuery("savedAlterSpell.php?dir=0")!==false) {
    for (let row of table.rows) {
        if (row.cells[0].innerHTML == currSpell) {
            table.deleteRow(row.rowIndex);
        }
    }
    genSide(table.rows[1].cells[0].innerHTML);
    let name = "Spell";
    for (let spell of spells) {
      if (spell["id"] == currSpell){name = spell["Name"];break;}
    }
    indexList[currSpell] = name;
    generateDatalist();
    countAvailable();
  }
}
function addSpell() {
    let spellToAdd = document.getElementById("spellToAdd").value.toLowerCase();
    let pizza = true;
    for (var i = 0, row; row = table.rows[i]; i++) {
        if (row.cells[0].innerHTML.toLowerCase() == spellToAdd) {
            createPopup("d:poet;txt:Spell already present!");
            pizza = false;
        }
    }
    if (pizza){
      for (let spell in indexList) {
        if (indexList[spell].toLowerCase() == spellToAdd){currSpell = spell;break;}
      }
      fulfillQuery("savedAlterSpell.php?dir=1", function(response) {
        response = JSON.parse(response);
        let newFirstElement = "<td>"+response["id"]+"</td><td>"+response["Name"]+"</td><td>"+response["Level"]+"</td><td>"+response["School"]+"</td><td>"+response["Class"]+"</td>";
        let row = table.insertRow();
        row.innerHTML = newFirstElement;
        genSide(currSpell);
        doPops("hide");
        generateDatalist();
        countAvailable();
      });

    }
}
/*function addToDatalist(name) {
    datalist = document.getElementById("spellSugg");
    datalist.innerHTML = datalist.innerHTML.concat('<option value="').concat(name).concat('" id="ss').concat(name).concat('" />');
}
function removeFromDatalist(name) {
  document.getElementById("ss".concat(name)).remove();
}*/
function generateDatalist() {
  datalist = document.getElementById("spellSugg");
  let full = "";
  for (let spell in indexList) {
    full += '<option value="'+indexList[spell]+'" id="ss'+indexList[spell]+'" />';
  }
  datalist.innerHTML=full;
}
generateDatalist();
