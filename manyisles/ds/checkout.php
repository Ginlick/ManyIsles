<?php
require_once("g/dsEngine.php");
$ds = new dsEngine;
$ds->killCache();
if ($ds->user->signedIn){$ds->go("checkout1");}

 ?>
<!DOCTYPE html>
<html>
<head>
    <title>Checkout | Digital Store</title>
    <?php
      echo $ds->giveHead();
     ?>
    <style>
    </style>
</head>
<body>
    <div w3-include-html="/Code/CSS/GTopnav.html" style="position:sticky;top:0;"></div>
        <div class="flex-container">
            <div class='left-col'>
                <a href="home.php"><h1 class="menutitle">Digital Store</h1></a>
                <ul class="myMenu">
                    <li><p class="Bar" style="color:black">Checking out</p></li>
                </ul>
                <?php
                    echo $ds->sideBasket();
                ?>
                <img src="/Imgs/Bar2.png" alt="GreyBar" class='separator'>
                <ul class="myMenu bottomFAQ">
                    <li><a class="Bar" href="/docs/6/Accounts" target="_blank">Many Isles accounts</a></li>
                    <li><a class="Bar" href="/account/home?display=Pol" target="_blank">Many Isles account policy</a></li>
                </ul>
            </div>

            <div id='content' class='column'>

                <h1>Step 1</h1>

                <div class="contentblock" id="makeIt">
                </div>

                <button class="checkout grey" id="checkoutButton">
                    <i class="fas fa-arrow-right"></i>
                    <span>Next</span>
                </button>

            </div>
        </div>
    <div w3-include-html="g/GFooter.html" w3-create-newEl="true"></div>


</body>
</html>
<script src="/Code/CSS/global.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
    var whichBlock = "makeIt";
    function switchBlock(x) {
        document.getElementById(x).style.display = "block";
        document.getElementById(whichBlock).style.display = "none";
        if (x == "makeIt") {
          document.getElementById("checkoutBox").setAttribute("onclick", "document.getElementById('SignUpForm').submit();");
        }
        else {
          document.getElementById("checkoutBox").setAttribute("onclick", "document.getElementById('signInForm').submit();");
        }
        whichBlock = x;
    }

    function pop(x) {
        if (x == "ded") {
            document.getElementById("modal").style.display = "none";
            for (let el of document.querySelectorAll('.modCol')) el.style.display = 'none';
        }
        else {
            document.getElementById("modal").style.display = "block";
            document.getElementById(x).style.display = "block";
        }
    }

    function inputGramm(x, y) {
        var input = x.value;
        var patt = new RegExp("[^A-Za-z0-9 ]");
        var target = "uname";
        if (y == "e") { patt = new RegExp("[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]$"); target = "email"; }
        else if (y == "p") { patt = new RegExp("[^A-Za-z0-9 ]"); target = "psw"; }
        target = "#".concat(target).concat("InputErr");
        $("input").removeAttr("style");
        if (y != "e") {
            if (patt.test(input) && input.length != 0) { $(target).show(); }
            else { $(target).hide(); }
        }
        else {
            if (!patt.test(input) && input.length != 0) { $(target).show(); }
            else { $(target).hide(); }
        }
    }

    var urlParams = new URLSearchParams(window.location.search);
    var error = urlParams.get('error');
    if (urlParams.get('uname') != null) { document.getElementById("uname").value = urlParams.get('uname'); }
    if (urlParams.get('email') != null) { document.getElementById("email").value = urlParams.get('email'); }
    if (error == "EmailTaken") {
        document.getElementById("email").placeholder = "Email Already Used";
        document.getElementById("email").value = null;
        document.getElementById("email").style.backgroundColor = "#ff8f8f";
    }
    else if (error == "UnameTaken") {
        document.getElementById("fname").placeholder = "username already in use";
        document.getElementById("fname").value = null;
        document.getElementById("fname").style.backgroundColor = "#ff8f8f";
    }
    else if (error == "signingIn") {
        document.getElementById("youFailedMaggot").style.display = "block";
        switchBlock("getIt");
    }
    else if (error == "spamblock") {
        document.getElementById("createBlock").style.display = "block";
    }

    var returnF = function(returned) {
      var html = `
        <div class="acp-portal-cont">
          <h2>Successfully signed in</h2>
          <i class="acp-successor fa-regular fa-circle-check"></i>
          <p>Signed in as  Adventurer Hansfried<br>
          <a href="/account/home" target="_blank">Visit account</a>
          </p>
        </div>
      `;
      html = html.replace("Adventurer Hansfried", returned["fullname"]);
      document.getElementById("makeIt").innerHTML = html;
      //activate button (also consider confirm email)
      let checkouter = document.getElementById("checkoutButton");
      if (returned["emailConfirmed"]){
        checkouter.addEventListener("click", function() {window.location.href = "checkout1";});
      }
      else {
        checkouter.addEventListener("click", function() {window.location.href = "checkoutw?w=1";});
      }
      checkouter.classList.remove("grey");
    }
    function acp_launcher() {
      let acpBuilder = new acp_builder(returnF);
      acpBuilder.createPortal(document.getElementById("makeIt"), "signCreate");
    }

</script>
