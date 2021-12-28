//wants: sourceJSON, power, parentWiki

var urlParams = new URLSearchParams(window.location.search);
var why = urlParams.get("i");
var show = urlParams.get("show");
if (why == "noslot") {
    nospace();
}
else if (why == "reported") {
    document.getElementById("modal").style.display = "block";
    document.getElementById("mod2").style.display = "block";
}
else if (why == "cant") {
    createPopup("d:poet;txt:Cannot revert - no older versions.");
}
else if (why == "susp") {
    createPopup("d:poet;txt:The article you tried to visit is suspended.");
}
else if (why == "deleted") {
    createPopup("d:poet;txt:Article deleted.");
}
else if (why == "1") {
    createPopup("d:poet;txt0:1");
}
else if (why == "reqCur") {
    createPopup("d:poet;txt:Sucessfully sent curation request");
}
if (urlParams.has('del')) {
    createPopup("d:poet;txt:Deleted " + urlParams.get('del') + " older versions. Happy filicide!");
};
if (show == "updated") {
    createPopup("d:poet;txtO:0");
}
else if (show == "aged") {
    createPopup("d:poet;txt:All older articles were deleted.");
}
else if (show == "fail") {
    createPopup("d:poet;txtO:1");
}

function fancyLinkage() {
    let allLinks = document.getElementsByTagName("A");
    for (let coollink of allLinks) {
        var idStr = "";
        let chref = coollink.getAttribute("href");
        if (/\/fandom\/.*\/[0-9]+\//.test(chref)) {
            idStr = chref.replace(/\/fandom\/[^/]*\//, "");
            idStr = idStr.replace(/\/.*/, "");
        }
        else if (/f.php\?id=[0-9]+/.test(chref)) {
            idStr = chref.substr(chref.indexOf("?id=") + 4);
        }
        else if (/\/mystral\/[0-9]+\//.test(chref)) {
            idStr = chref.replace(/\/mystral\//, "");
            idStr = idStr.replace(/\/.*/, "");
        }
        if (idStr != "") {
            let targetPage = "/fandom/getArticleInfo.php?id=" + idStr + "&dom=" + domain;
            console.log(targetPage);
            xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function () {
                if (this.readyState == 4) {
                    if (this.status == 200) {
                        var object = JSON.parse(this.responseText);
                        if (object["status"] == "outstanding" || object["status"] == "suspended" || object["status"] == "incomplete") {
                            coollink.classList.add(object["status"]);
                        }
                        if (("name" in object)) {
                            if (!("id" in object)) { object["id"] = 0; }
                            if (!("genre" in object)) { object["genre"] = ""; }
                            if (!("thumbnail" in object)) { object["thumbnail"] = ""; }
                            if (!("NSFW" in object)) { object["NSFW"] = 0; }
                            if (!("date" in object)) { object["date"] = ""; }
                            if (!("jacob" in object)) { object["jacob"] = ""; }
                            if (!("status" in object)) { object["status"] = "active"; }

                            var aHoverDiv = document.createElement("DIV");
                            aHoverDiv.classList.add("aHoverDiv");
                            var linkingCont = document.createElement("A");
                            linkingCont.setAttribute("href", baseURL + object["id"] + "/" + object["name"]);
                            linkingCont.classList.add("linkingCont");
                            var thumbnail = document.createElement("DIV");
                            thumbnail.setAttribute("class", "thumbnail imgCont");
                            thumbnail.setAttribute("load-image", object["thumbnail"]);
                            linkingCont.appendChild(thumbnail);
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
                            if (object["NSFW"] != 0) {
                                roundN.setAttribute("class", "roundInfo");
                                if (object["NSFW"] == 1) {
                                    roundN.classList.add("orange");
                                }
                                else {
                                    roundN.classList.add("red");
                                }
                                roundN.innerHTML = "NSFW";
                            }
                            var roundS = document.createElement("SPAN");
                            if (object["status"] != "active") {
                                roundS.setAttribute("class", "roundInfo red");
                                roundS.innerHTML = object["status"];
                            }
                            pp.innerHTML = object["name"] + roundGenre.outerHTML + roundN.outerHTML + roundS.outerHTML;
                            if (object["date"] != "") { pp.innerHTML += "<br><i>" + object["date"] + "</i>" + "<br>"; }
                            textnail.appendChild(pp);
                            let jac = document.createElement("DIV");
                            jac.setAttribute("class", "jac");
                            jac.innerHTML = object["jacob"];
                            textnail.appendChild(jac);
                            linkingCont.appendChild(textnail);
                            aHoverDiv.appendChild(linkingCont);
                            coollink.appendChild(aHoverDiv);
                            getIndexImgs();
                        }
                    }
                }
            }
            xhttp.open("GET", targetPage, true);
            xhttp.send();
        }
    }
}

function showAuthors() {
    if (document.getElementById("authCont") != null) {
        let authors = document.getElementById("authCont").children;
        for (let author of authors) {
            var authName = author.innerHTML;
            let targetPage = "/fandom/getAuthInfo.php?name=" + authName + "&w=" + parentWiki;
            xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function () {
                if (this.readyState == 4) {
                    if (this.status == 200) {
                        var object = JSON.parse(this.responseText);
                        if (("name" in object)) {
                            if (!("status" in object)) { object["status"] = 0; }
                            if (!("edits" in object)) { object["edits"] = 0; }
                            if (!("id" in object)) { object["id"] = 0; }

                            var aHoverDiv = document.createElement("DIV");
                            aHoverDiv.classList.add("aHoverDiv");
                            aHoverDiv.classList.add("auth");
                            var title = document.createElement("h3");
                            title.innerHTML = "User " + object["name"];
                            var statusRound = document.createElement("SPAN");
                            statusRound.setAttribute("class", "roundInfo");
                            if (object["status"] == "banned") { statusRound.classList.add("red"); }
                            statusRound.innerHTML = object["status"];
                            title.appendChild(statusRound);
                            aHoverDiv.appendChild(title);
                            var info = document.createElement("P");
                            info.innerHTML = "Edits: " + object["edits"];
                            aHoverDiv.appendChild(info);
                            var biggerInfo = document.createElement("DIV");
                            if (power > 2) {
                                var adminInfo = "<img src='/Imgs/Bar2.png' class='separator'></img><h4>Promote User</h4><div class='bottButtCon'><a class='wikiButton' href ='/fandom/promote.php?dir=1&d=0&id=" + parentWiki + "&who=" + object["id"] + "'>Ban</a><a class='wikiButton' href ='/fandom/promote.php?dir=1&d=2&id=" + parentWiki + "&who=" + object["id"] + "'>Curate</a><a class='wikiButton' href ='/fandom/promote.php?dir=1&d=3&id=" + parentWiki + "&who=" + object["id"] + "'>Mod</a></div>";
                                biggerInfo.innerHTML = adminInfo;
                            }
                            else if (typeof getCookie === "function" && getCookie("loggedIn") == object["id"] && object["status"] == "poet") {
                                var userInfo = "<img src='/Imgs/Bar2.png' class='separator'></img><h4>Request Curation</h4><p>Unlimited editing. <a href='/docs/37/curation' target='_blank'>more info</a></p><div class='bottButtCon'><a class='wikiButton' href ='/fandom/rcur.php?wiki=" + parentWiki + "&who=" + object["id"] + "&w=prom'>Request</a></div>";
                                biggerInfo.innerHTML = userInfo;
                            }
                            aHoverDiv.appendChild(biggerInfo);
                            author.appendChild(aHoverDiv);
                        }
                    }
                }
            }
            xhttp.open("GET", targetPage, true);
            xhttp.send();
        }
    }
}

function addJSON(sourceJSON) {
    if (sourceJSON != "") {
        sourceJSON = JSON.parse(sourceJSON);
        document.getElementById("footnotes").style.display = "block";
        for (let key in sourceJSON) {
            let line = document.createElement("p");
            getFile = encodeURI("/fandom/giveParse.php?q=".concat(sourceJSON[key]));
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = async function () {
                if (this.readyState == 4 && this.status == 200) {
                    let result = xhttp.responseText;
                    let tokillement = document.createElement("div");
                    tokillement.innerHTML = result;
                    let parsedText = tokillement.children[0].innerHTML;
                    line.innerHTML = "<sup>" + key + "</sup> " + parsedText;
                    line.setAttribute("id", "footnote" + key);
                    document.getElementById("gimmeSources").appendChild(line);
                }
            };
            xhttp.open("GET", getFile, false);
            xhttp.send();
        }
    }
}

function viewNSFW(x) {
    document.getElementById("NSFWoverlayCont").style.display = "none";
    document.getElementById("NSFWheightPadd").style.display = "none";
    document.getElementById("actualNeatCont").style.visibility = "visible";
    if (x == 1) {
        document.cookie = "clearNSFW=1;path=/;";
    }
}

function showFoot(key) {
    let footnote = document.getElementById("footnote" + key);
    footnote.classList.remove("highlighted");
    footnote.scrollIntoView({ behavior: "smooth", block: "center" });
    footnote.classList.add("highlighted");
}
