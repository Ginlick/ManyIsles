var modList = ["charop", "race", "rule", "adventure", "lore", "dms"];
var tulList = ["hmbrw", "genr", "indx"];
var artList = ["vis", "cart", "dun"];
var fulList = modList.concat(tulList).concat(artList);

let urlaa = window.location.href;
var filename = urlaa.split('/').pop().split('#')[0].split('?')[0];

const queryString = window.location.search;
const urlParams = new URLSearchParams(queryString);

var menuList = [];
let menu = document.getElementsByClassName("menuList");
for (let menul of menu) {
  menuList[menul.getAttribute("type")] = menul;
  for (let liss of menul.children){
    liss.addEventListener("click", function() {modSelect(this);});
  }
}
var mySearch = document.getElementById("mySearch");

function newSys(value) {
    sysNum = value;
}
function cateStr() {
  var fullLine = "";
  for (let cat of categs ){
      fullLine += cat;
  }
  return fullLine;
}

//suggs
function suggestNow() {
    let myValue = document.getElementById("mySearch").value;
    getFile = "findSuggestions.php?q=".concat(myValue);
    getFile = getFile.concat("&t=").concat(type);
    getFile = getFile.concat("&c=").concat(cateStr());
    console.log(getFile);
    parent = document.getElementById("suggestions");
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            var resultJSON = JSON.parse(xhttp.responseText);

            var unordered = document.createElement("UL");
            for (let arr of resultJSON) {
              var name = arr["name"];
              var link = arr["link"];
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
  window.setTimeout(function () {
    document.getElementById("suggestions").style.display = "none";
  }, 500);
}

//detail search

function dropdown() {
     document.getElementById("myDropdown").classList.toggle("show");
}
window.onclick = function (event) {
  if (!event.target.matches('.dropbtn')) {
    var dropdowns = document.getElementsByClassName("dropdown-content");
    for (let dropbtn of dropdowns) {
        dropbtn.classList.remove('show');
    }
  }
}
function upSels() {
  for (let liss of menuList[type].children) {
    if (categs.includes(liss.getAttribute("subgenre"))) {
      liss.classList.add("active");
    }
    else {
      liss.classList.remove("active");
    }
  }

  if ((categs.length == 1 && categs[0] == "") || categs.length < 1) {
    menuList[type].firstElementChild.classList.add("active");
  }
  else {
    menuList[type].firstElementChild.classList.remove("active");
  }
}
function modSelect(element) {
  var newSubg = element.getAttribute("subgenre");
  console.log(newSubg);
  if (newSubg == ""){
    categs = [];
  }
  else if (newSubg == null) {return;}
  else {
    if (categs.includes(newSubg)){
      var location = categs.indexOf(newSubg);
      categs.splice(location, 1);
    }
    else {
      categs.push(newSubg);
    }
  }
  upSels();
}

function typeValue(num) {
  if (num != type){  categs = '';}
  type = num;
  for (let key in menuList) {
    if (key == num){
      menuList[key].style.display = "block";
    }
    else {
      menuList[key].style.display = "none";
    }
  }
  document.getElementById("showType").innerHTML = typeNames[type];
  if (mySearch != null){ mySearch.placeholder = "Search " + typeNames[type] + "...";}
  upSels();
}
typeValue(type);

function goSearch(oldform) {
  var form = document.createElement("form");
  form.method = "POST";
  form.action = "/dl/search";

  var element1 = document.createElement("input");
  element1.name = "query";
  element1.value = oldform.elements["query"].value;
  form.appendChild(element1);
  var element2 = document.createElement("input");
  element2.name = "genre";
  element2.value = type;
  form.appendChild(element2);
  var element3 = document.createElement("input");
  element3.name = "subgenre";
  element3.value = cateStr();
  form.appendChild(element3);
  if (type == 1){
    var element4 = document.createElement("input");
    element4.name = "gsystem";
    element4.value = document.getElementById("sysDropdown").value;
    form.appendChild(element4);
  }

  document.body.appendChild(form);
  form.submit();
  return false;
}
var dropper = document.getElementById("sysDropdown");
for (let option of dropper.options){
  if (option.value == sysNum){
    option.selected="selected";
  }
}

responsive('/dl/global/dl3-mobile.css', "small");
