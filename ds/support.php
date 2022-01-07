<?php

require_once("g/sideBasket.php");

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <link rel="icon" href="/Imgs/FaviconDS.png">
    <title>Support Creator | Digital Store</title>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <link rel="stylesheet" type="text/css" href="/Code/CSS/Main.css">
    <link rel="stylesheet" type="text/css" href="g/ds-g.css">
    <style>
        .inputErr {
            font-size: calc(9px + .3vw);
            color: red;
            text-align: left;
            margin: 0;
            padding-left: .4vw;
            display: none;
        }

        #infoSpan {
            display: none;
        }

        .container {
            margin-bottom: 50px;
        }

        .productContent {
            display: flex;
            padding: 1vw;
            box-sizing: border-box;
            align-items: center;
        }

            .productContent p {
                font-size: calc(11px + .8vw);
                padding: 0 0 0 5px;
                width: 85%;
                text-align: left;
                display: inline-block;
                color: #5e5e5e;
                font-family: 'Lucida Sans', 'Lucida Sans Regular', 'Lucida Grande', 'Lucida Sans Unicode', Geneva, Verdana, sans-serif;
            }

        .imgDiv {
            width: 15%;
            display: inline-block;
        }

            .imgDiv img {
                border-radius: 9%;
                width: 100%;
            }

        .container {
            margin: 20px 0;
        }

        .suggestions {
            position: absolute;
            width: 30%;
            background-color: white;
            font-size: 1.2vw;
            padding: 4px;
            border-radius: 0 3px;
            border: 2px solid #f0c026;
            border-top: none;
        }

            .suggestions ul {
                list-style-type: none;
                padding: 0;
                margin: 0;
            }

                .suggestions ul li {
                    padding: 10px 0;
                    border-bottom: 1px solid #a8a8a8;
                }

                .suggestions ul :last-child {
                    border: none;
                }

                .suggestions ul li span {
                    color: black;
                }

                    .suggestions ul li span:hover {
                        color: #f0c026;
                        transition: .2s ease;
                    }
    </style>
</head>
<body>
    <div w3-include-html="/Code/CSS/GTopnav.html" style="position:sticky;top:0;"></div>

        <div class="flex-container">
            <div class='left-col'>
                <a href="home.php"><h1 class="menutitle">Digital Store</h1></a>
                <ul class="myMenu">
                    <li><a class="Bar" href="home.php">Browse</a></li>
                </ul>
                 <?php
                     doSideBasket();
                 ?>
                <img src="/Imgs/Bar2.png" alt="GreyBar" class='separator'>
                <ul class="myMenu bottomFAQ">
                    <li><a class="Bar" href="/docs/4/Partnerships" target="_blank">Many Isles Publishing</a></li>
                    <li><a class="Bar" href="/docs/15/Digital_Store" target="_blank">Digital Store documentation</a></li>
                </ul>
            </div>

            <div class='column'>

                <div class="contentblock">
                    <h2>Support Creator</h2>
                    <p>Choose an amount to send the creator you wish to support. 100% of your payment will be transferred to them.</p>
                    <div class="container">
                        <input type="text" id="productFinder" placeholder="Find product..." oninput="suggestNow();" onfocusout="killSugg();" onfocus="suggestNow();">
                        <div class="suggestions" id="suggestions" style="display:none;z-index:2"></div>
                    </div>
                    <div>
                        <img src="/Imgs/Bar2.png" alt="GreyBar" class='separator'>
                    </div>
                    <div class="productContent" id="myParent">
                    </div>
                    <div>
                        <img src="/Imgs/Bar2.png" alt="GreyBar" class='separator'>
                    </div>

                    <form action="basket.php" id="coolForm" method="POST" enctype="multipart/form-data">

                        <div class="container">
                            <label for="creditAmount"><i class="fas fa-coins"></i> Amount ($)</label>
                            <input type="text" id="creditAmount" name="nope" placeholder="10.50" pattern="[0-9]+\.[0-9]{2}$" oninput="inputGramm(this)" required>
                            <p id="InputErr" class="inputErr">Insufficient amount!</p>
                        </div>




                        <div>
                            <img src="/Imgs/Bar2.png" alt="GreyBar" class='separator'>
                        </div>
                        <input style="display:none" name="basketing" id="basketing" value="3" />
                        <input style="display:none" name="supportPair" id="supportPair" value="" />
                        <input style="display:none" name="goTo" id="goTo" value="nope" />
                        <div class="checkoutBox spec" style="z-index:0">
                            <button class="checkout" onclick="submitSpecial();">
                                <i class="fas fa-shopping-basket"></i>
                                <span>Basket</span>
                            </button>
                            <button class="checkout" onclick="submitNormal();">
                                <i class="fas fa-arrow-right"></i>
                                <span>Basket</span>
                            </button>
                        </div>
                    </form>

                </div>
        </div>
    </div>
    <div w3-include-html="g/GFooter.html" w3-create-newEl="true"></div>


</body>
</html>
<script class="jsbin" src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
<script src="/Code/CSS/global.js"></script>
<script>



    function inputGramm(x) {
        var input = x.value;
        let value = parseFloat(input);
        $("#InputErr").hide();
        if (isNaN(value)) {
            $("#InputErr").show();
            $("#InputErr").html("Incorrect Format!");
        }
    }

    function suggestNow() {
        let myValue = document.getElementById("productFinder").value;
        getFile = "/dl/findSuggestions.php?z=1&q=".concat(myValue);
        parent = document.getElementById("suggestions");
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                var resultJSON = JSON.parse(xhttp.responseText);
                  console.log(resultJSON);
                var unordered = document.createElement("UL");
                for (var block in resultJSON) {
                    var link = resultJSON[block]["link"];
                    var node = document.createElement("SPAN");
                    var textnode = document.createTextNode(resultJSON[block]["name"]);
                    var listElement = document.createElement("LI");
                    node.appendChild(textnode);
                    listElement.setAttribute("onclick", 'switchSupport("' +  resultJSON[block]["id"] + '");');
                    listElement.appendChild(node);
                    unordered.appendChild(listElement);
                }

                if (!unordered.hasChildNodes()) {
                    var node = document.createElement("SPAN");
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
        window.setTimeout(function () { document.getElementById('suggestions').style.display = 'none'; }, 300);
    }

    var currentPartner = "";
    function switchSupport(prodId) {
        getFile = "g/prodInfo.php?q=" + encodeURIComponent(prodId);
        parent = document.getElementById("myParent");
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                let resultJSON = JSON.parse(xhttp.responseText);
                console.log(resultJSON);
                currentPartner = resultJSON["partner"];
                parent.innerHTML = "";
                let imgDiv = document.createElement("DIV");
                imgDiv.setAttribute("class", "imgDiv");
                let img = document.createElement("IMG");
                img.setAttribute("src", resultJSON["image"]);
                imgDiv.appendChild(img);
                let p = document.createElement("P");
                p.innerHTML = resultJSON["name"] + ", by " + resultJSON["partner"];
                parent.appendChild(imgDiv);
                parent.appendChild(p);
            }
        };

        xhttp.open("GET", getFile, true);
        xhttp.send();
    }

    function submitSpecial() {
        document.getElementById("goTo").value = "support.php";
        submitNormal();
    }
    function submitNormal() {
        if (document.getElementById("creditAmount").value != ""){
        let sponsoring = parseFloat(document.getElementById("creditAmount").value);
            sponsoring = sponsoring * 100.0;
            sponsoring = sponsoring.toFixed(0);
            document.getElementById("supportPair").value = "("+currentPartner+"/"+sponsoring+")";
            document.getElementById("coolForm").submit();
        }
    }

    const urlParams = new URLSearchParams(window.location.search);
    const myParam = urlParams.get('which');
    if (myParam != null) {
        switchSupport(myParam);
    }
    else {
        switchSupport(0);
    }

</script>
