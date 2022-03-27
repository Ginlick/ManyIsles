//wants: parentWiki, articleCategArray, newWiki

function addSome(how) {
    let currentnum = document.getElementById("gimmeBabesTbody").childElementCount + 1;
    if (how == 1) {
        var fullrow = document.createElement("tr");
        var cell1 = document.createElement("td");
        var cell1Text = document.createElement("text");
        cell1Text.innerHTML = currentnum;
        cell1.appendChild(cell1Text);

        var cell2 = document.createElement("td");
        var cell2Input = document.createElement("input");
        cell2Input.setAttribute("type", "text");
        cell2Input.setAttribute("placeholder", "Author. *Source Name*. (Publisher, Year)");
        cell2Input.setAttribute("onchange", "newSource(" + currentnum + ")");
        cell2.appendChild(cell2Input);

        fullrow.appendChild(cell1);
        fullrow.appendChild(cell2);
        document.getElementById("gimmeBabesTbody").appendChild(fullrow);
    }
    else {
        let babes = document.getElementById("gimmeBabesTbody");
        delete sourceJSON[currentnum - 1];
        document.getElementById("sources").value = JSON.stringify(sourceJSON);
        babes.removeChild(babes.lastChild);
    }
}
function newSource(which) {
    let sourceName = document.getElementById("gimmeBabesTbody").children[which - 1].children[1].children[0].value;
    sourceName = sourceName.replace(/"/g, '“');
    sourceJSON[which] = sourceName;
    document.getElementById("sources").value = JSON.stringify(sourceJSON);
}


var textareaToFill = document.getElementById("bodyFieldarea");


function switchSupport(parentname) {
    if (parentname == 0) {
        $("#currentRoot").innerHTML = "<a href='/fandom/home' >Fandom</a > - <a>Wiki</a>";
        $("#root").value = 0;
        $("#rootChanger").hide();
        $("#writeInfo").innerHTML = 1;
        if (newWiki) {
            parentWiki = 0;
            $("#addCateColl").hide();
            $("#cateChanger").hide();
            document.getElementById("topLInfo").innerHTML = "<p>You are creating the homepage of a new wiki.<br> <a href='/wiki/h/fandom.html' target='_blank'>more info</a></p>";
            document.getElementById("coolInfoH1").innerHTML = "Create Homepage";
        }
    }
    else {
      if (domainType != "spells"){
        getFile = "/fandom/getRoot.php?q=" + encodeURIComponent(parentname) + "&dom=" + domain;
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                let result = xhttp.responseText;
                document.getElementById("currentRoot").innerHTML = result;
                document.getElementById("root").value = parentname;
            }
        };
        xhttp.open("GET", getFile, true);
        xhttp.send();
      }
    }
}

function addChar(myText) {
  var myField = document.activeElement;

    //IE support
    if (document.selection) {
      document.myField.focus();
      sel = document.selection.createRange();
      sel.text = myText;
    }
    //MOZILLA and others
    else if (myField.selectionStart || myField.selectionStart == "0") {
        var startPos = myField.selectionStart;
        var endPos = myField.selectionEnd;
        myField.value = myField.value.substring(0, startPos)
            + myText
            + myField.value.substring(endPos, myField.value.length);
    } else {
        myField.value += myText;
    }
}
function insertImage(link, what) {
    var myField = textareaToFill;
    let imgClass = document.getElementById("insImgClass").options[document.getElementById("insImgClass").selectedIndex].value;
    let imgSrc = document.getElementById("insImgSrc").value;
    let imgCap = document.getElementById("insImgCap").value;
    let imgStyle = document.getElementById("insImgStyle").value;

    let toInsertText = "{";
    if (imgClass != "") { toInsertText += "class[" + imgClass + "]" }
    if (imgSrc != "") { toInsertText += "src[" + imgSrc + "]" }
    if (imgCap != "") { toInsertText += "caption[" + imgCap + "]" }
    if (imgStyle != "") { toInsertText += "style[" + imgStyle + "]" }
    toInsertText += "}";

    //IE support
    if (document.selection) {
        document.myField.focus();
        sel = document.selection.createRange();
        sel.text = toInsertText;
    }
    else if (myField.selectionStart || myField.selectionStart == '0') {
        insertText(myField, toInsertText);
    } else {
        myField.value += toInsertText;
    }
    removePops();
}
function addCategory(link, name) {
  if (!articleCategArray.includes(link)) {
      let cateRow = document.getElementById("currentCategs");
      if (cateRow.children.length != 0) {
          var toAddCate = "<span onclick='removeCateg(" + link + ")' id='removableCateg"+link+"'>, " + name + "</span>";
      }
      else {
          var toAddCate = "<span onclick='removeCateg(" + link + ")' id='removableCateg" + link +"'>" + name + "</span>";
      }
      cateRow.innerHTML = cateRow.innerHTML + toAddCate;
      articleCategArray.push(link);
  }
  document.getElementById("categs").value = articleCategArray.join();
}
function removeCateg(link) {
    for (let i = 0; i < articleCategArray.length; i++) {
        if (articleCategArray[i] == link) {
            articleCategArray.splice(i, 1);
        }
    }
    document.getElementById("removableCateg" + link).remove();
    document.getElementById("categs").value = articleCategArray.join();
    if (document.getElementById("currentCategs").children.length > 0) {document.getElementById("currentCategs").children[0].innerHTML = document.getElementById("currentCategs").children[0].innerHTML.replace(", ", "");}
}
function createLink(link, what, pdomain = domain) {
    var myField = textareaToFill;
    if (what == "href") {
      if (/^\d+$/.test(link)) {
        console.log(link);
          link = domInfos[pdomain]["baseURL"] + link + "/";
          console.log(link);
      }
      let linkName = document.getElementById("linkNameEr").value;
      linkName = linkName.replace(/"/g, '“');
      var myText = "[" + linkName + "](" + link + ")";
      if (document.getElementById("autoLinkChecker") != null) {
          if (document.getElementById("autoLinkChecker").checked) {
              newAutoLink(document.getElementById("linkNameEr").value, link, parentWiki);
          }
      }
    }
    else if (what == "wikthumb") {
        var myText = "[wiki:art" + link + "]";
        myField = document.getElementById("bodyFieldarea");
    }
    //IE support
    if (document.selection) {
        document.myField.focus();
        sel = document.selection.createRange();
        sel.text = myText;
    }
    else if (myField.selectionStart || myField.selectionStart == '0') {
        insertText(myField, myText);
    } else {
        myField.value += myText;
    }
    myField.focus();
    removePops();
}
function suggestLinks() {
    var selected = document.getElementById("linkType").value;
    if (selected != "false") {
        let ig = + !document.getElementById("linkFinderLocal").checked;
        if (selected != domain) { ig = 1;}
        offerSuggestions(document.getElementById("viewRoot2"), 'findSuggestions', 0, 'createLink', ig, selected);
    }
}
function insFootnote() {
    var myField = textareaToFill;
    var startPos = myField.selectionStart;
    myField.value = myField.value.substring(0, startPos) + "[footnote:]" + myField.value.substring(myField.selectionEnd, myField.value.length);
    myField.setSelectionRange(startPos + 10, startPos + 10);
}
function insLink() {
    let selectedText = getSelectionText();
    document.getElementById("linkNameEr").value = selectedText;
    document.getElementById("viewRoot2").value = "";
    document.getElementById("modal").style.display = "block";
    document.getElementById("mod1").style.display = "block";
    if (selectedText == "") {
        document.getElementById("linkNameEr").focus();
    }
    else {
        document.getElementById("viewRoot2").focus();
    }
    return false;
}

Mousetrap.bind("ctrl+shift+k", function (e) {
    return insLink();
});
Mousetrap.bind("ctrl+shift+j", function (e) {
    e.preventDefault();
    startCategging();
    return false;
});
Mousetrap.bind("ctrl+shift+o", function (e) {
    e.preventDefault();
    insFootnote();
    return false;
});
function insThumb() {
    document.getElementById("modal").style.display = "block";
    document.getElementById("mod3").style.display = "block";
    document.getElementById("viewRoot4").focus();
    return false;
}
Mousetrap.bind("ctrl+shift+l", function (e) {
    e.preventDefault();
    return insThumb();
});
function insImg() {
    document.getElementById("modal").style.display = "block";
    document.getElementById("mod6").style.display = "block";
    document.getElementById("insImgSrc").value = "";
    document.getElementById("insImgCap").value = "";
    document.getElementById("insImgStyle").value = "";
    document.getElementById("insImgSrc").focus();
}
Mousetrap.bind("ctrl+shift+i", function (e) {
    e.preventDefault();
    return insImg();
});
Mousetrap.bind("esc", function () {
    removePops();
    return false;
});

function startCategging() {
    if (!newWiki) {
        document.getElementById("modal").style.display = "block";
        document.getElementById("mod2").style.display = "block";
        document.getElementById("madder").style.display = "none";
        document.getElementById("categInput").focus();
    }
}
function createCategory(newcate) {
    var patt = new RegExp("[^A-Za-z0-9',()\-: ]");
    if (!patt.test(newcate)) {
        getFile = "/fandom/addCateg.php?q=" + encodeURIComponent(newcate) + "&w=" + parentWiki + "&dom=" + domain;
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                let result = xhttp.responseText;
                if (result.includes("success")) {
                    document.getElementById("madder").style.display = "inline-block";
                    document.getElementById("categInput").style.value = "";
                }
                else {
                    console.log(result);
                    document.getElementById("categInput").style.value = "";
                    madder = document.getElementById("madder");
                    madder.style.display = "inline-block";
                    madder.style.color = "red";
                    madder.innerHTML = "Failed";
                }
                document.getElementById("categInput").value = "";
            }
        };

        xhttp.open("GET", getFile, true);
        xhttp.send();
    }
    else {
        madder = document.getElementById("madder");
        madder.style.display = "inline-block";
        madder.style.color = "red";
        madder.innerHTML = "Incorrect Format";
    }
}

function submitOutstander() {
    let articleName = document.getElementById("articleName").value;
    let requURL = "/fandom/createOutstander.php?name=" + encodeURI(articleName) + "&wiki=" + parentWiki + "&dom=" + domain;
    console.log(requURL);
    var myRequest = new XMLHttpRequest();
    myRequest.onreadystatechange = function () {
        if (myRequest.readyState == 4 && myRequest.status == 200) {
            let result = myRequest.responseText.charAt(myRequest.responseText.length-1);
            if (result.includes("currentSlot:i")) {
                document.getElementById("outstanderAlert").innerHTML = "Warning: Only 1 Slot Left";
                document.getElementById("outstanderAlert").style.color = "#A93226";
                document.getElementById("mod5").style.display = "block";
                document.getElementById("modal").style.display = "block";
            }
            else if (result.includes("currentSlot:j")) {
                document.getElementById("outstanderAlert").innerHTML = "No Slots Left";
                document.getElementById("outstanderAlert").style.color = "#A93226";
                document.getElementById("mod5").innerHTML = '<div class="modContent"><img src = "/Imgs/PopPoet.png" alt = "Hello There!" style = "width: 100%; margin: 0; padding: 0; display: inline-block " /><h1>Warning: Out of Slots</h1><p>You have no slots left. If you submit your work now, it will go lost. Please save any important edits on an external file and wait until your slots are cleared by the admins.</p></div>';
                document.getElementById("mod5").style.display = "block";
                document.getElementById("modal").style.display = "block";
                document.getElementById("submitButton").removeAttribute("type");
                document.getElementById("submitButton").setAttribute("onclick", 'document.getElementById("mod5").style.display = "block"; document.getElementById("modal").style.display = "block";');            }
            console.log(result);

            document.getElementById("articleName").value = "";
            document.getElementById("outstanderAlert").style.display = "block";
            if (!result.includes("currentSlot:i")) {
                setTimeout(function () {
                    document.getElementById("outstanderAlert").style.display = "none";
                }, 5000);
            }
        }
    }
    myRequest.open("GET", requURL, true);
    myRequest.send(null);
}

function rEmove(elmnt) {
    elmnt.value = elmnt.value.replace("[", "%sqbrak_left%");
    elmnt.value = elmnt.value.replace("]", "%sqbrak_right%");
    elmnt.value = elmnt.value.replace("{", "%qbrak_left%");
    elmnt.value = elmnt.value.replace("}", "%qbrak_right%");
}
Mousetrap.bind("ctrl+shift+e", function (e) {
    addChar("%sqbrak_left%");
});
Mousetrap.bind("ctrl+shift+r", function (e) {
    addChar("%sqbrak_right%");
});

var allCompletes = document.getElementsByClassName("complete");
var allFlipCompletes = document.getElementsByClassName("flipcomplete");
function differComplic(check, target = "preferFullEdit") {
    if (check != null) {
        if (check.checked) {
            document.cookie = target + "=1; Max-Age: 15770000; path=/;";
            if (target == "doLinkage") { doLinkage = true; }
        }
        else {
            document.cookie = target + "=0; Max-Age: 15770000; path=/;";
            if (target == "doLinkage") { doLinkage = false; }
        }
        if (target == "preferFullEdit") {
            for (let element of allCompletes) {
                if (check.checked) {
                    element.style.display = "inherit";
                }
                else {
                    element.style.display = "none";
                }
            }
            for (let element of allFlipCompletes) {
                if (check.checked) {
                    element.style.display = "none";
                }
                else {
                    element.style.display = "inherit";
                }
            }
        }
    }
}
differComplic(document.getElementById("neatChecker"));

var doLinkage = true;
var allDoneLinks = [];
if (getCookie("doLinkage") == 0) { doLinkage = false;}
function autoLinkage() {
    if (!doLinkage) { return;}
    let checkPos = textareaToFill.selectionStart;
    var checkValue = textareaToFill.value.substr(checkPos - 1, 1);
    var firstHalf = textareaToFill.value.substr(0, checkPos);
    var lastHalf = textareaToFill.value.substr(checkPos);
    if (checkValue == " ") {
        var word = firstHalf.match(/[^\r\n\v ]* $/)[0];
        word = word.slice(0, - 1);
        doStuff(word, firstHalf, lastHalf);
        var word2 = firstHalf.match(/[^\r\n\v ]* [^\r\n\v ]* $/)
        if (word2 != null) {
            word2 = word2[0];
            word2 = word2.slice(0, - 1);
            doStuff(word2, firstHalf, lastHalf);
        }
    }
}

function doStuff(word, firstHalf, lastHalf) {
  usableLinks = autoLinks[0];
  if (autoLinks[parentWiki]!=undefined){usableLinks = [...usableLinks, ...autoLinks[parentWiki]];}

  if (allDoneLinks[textareaToFill.id] == undefined) { allDoneLinks[textareaToFill.id] = [];}

  if (word.match(/.*s/)) { word.slice(0, -1); }
  if (allDoneLinks[textareaToFill.id].includes(word)) { return; }
  for (let autolink of usableLinks){
    if (autolink["name"] == word.toLowerCase()){
      console.log("triggered");
      let coolLink = "[" + word + "](" + autolink["href"] + ") ";
      let firstPos = firstHalf.length - word.length - 1;
      textareaToFill.value = firstHalf.substr(0, firstPos) + coolLink + lastHalf;
      textareaToFill.setSelectionRange(firstPos + coolLink.length, firstPos + coolLink.length);
      allDoneLinks[textareaToFill.id].push(word);
      break;
    }
  }
}
