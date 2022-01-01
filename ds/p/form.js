for (let inputCont of document.getElementsByClassName("inputCont")) {
    let input = inputCont.children[1];
    input.addEventListener("focus", showInfo);
    input.addEventListener("focusout", hideInfo);
}
function showInfo(evt) {
    evt.currentTarget.parentElement.children[2].classList.add("info");
    evt.currentTarget.parentElement.children[2].innerHTML = evt.currentTarget.parentElement.children[2].getAttribute("default");
    evt.currentTarget.parentElement.children[2].style.opacity = "1";
}

for (let inputErr of document.getElementsByClassName("inputErr")) {
    if (inputErr.getAttribute("default") !== null){
         inputErr.innerHTML = inputErr.getAttribute("default");
    }
}
function hideInfo(evt) {
    evt.currentTarget.parentElement.children[2].style.opacity = "0";
}
function newShortie(element) {
    document.getElementById("shortname").value = element.value;
}

function checkSyntax(element, regex, brutal) {
    var input = element.value;
    var patt = new RegExp(regex, "g");
    target = element.parentElement.children[2];
    if (patt.test(input)) {
        if (brutal == 0) {
            target.style.opacity = "1";
            target.innerHTML = "Incorrect Input!";
            target.classList.remove("info");
        }
        else {
            element.value = input.replace(patt, "");
        }
    }
    else {
        target.style.opacity = "0";
    }
}
function checkSyntaxR(element, regex, brutal) {
    var input = element.value;
    var patt = new RegExp(regex, "g");
    target = element.parentElement.children[2];
    if (!patt.test(input)) {
        if (brutal == 0) {
            target.style.opacity = "1";
            target.innerHTML = "Incorrect Input!";
            target.classList.remove("info");
        }
        else {
            element.value = "";
        }
    }
    else {
        target.style.opacity = "0";
    }
}
