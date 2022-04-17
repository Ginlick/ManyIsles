//requires language

function suggestNow(element, mode = 0) {
  //modes: 0=links to word page, 1=translate's options
    var value = element.value;
    var getFile = "/dic/load/searchSugg.php?dics="+value+"&dicl="+language;
    var parent = document.getElementById("suggestions");

    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
      if (this.readyState == 4 && this.status == 200) {
        var returnText = JSON.parse(xhttp.responseText);
        var unordered = document.createElement("UL");
        if (typeof returnText == "object" && returnText.length > 0){
          for (let arr of returnText) {
            var listElement = document.createElement("LI");
            if (mode == 1){
              var node = document.createElement("A");
              node.innerHTML = arr["word"];
              node.addEventListener("click", newWord);
              listElement.appendChild(node);
            }
            else {
              listElement.innerHTML = arr["link"];
            }
            unordered.appendChild(listElement);
          }
        }
        else {
          var node = document.createElement("A");
          var textnode = document.createTextNode("No similar words");
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

function gQuery(getFile) {
    console.log("gSuggestNow:" + getFile);

}
