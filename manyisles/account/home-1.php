<?php


?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <link rel="icon" href="/Imgs/Favicon.png">
    <title>Account</title>
    <link rel="stylesheet" type="text/css" href="/Code/CSS/Main.css">
    <link rel="stylesheet" type="text/css" href="/Code/CSS/pop.css">
    <link rel="stylesheet" type="text/css" href="g/acc.css">
    <style>


    </style>
</head>
<body>

  <div w3-include-html="/Code/CSS/GTopnav.html" w3-create-newel="true"></div>
    <div style="flex: 1 0 auto;">
        <div class="flex-container">

            <div class='left-col'>
                <ul id="myMenu">
                    <li onclick='clinnation("Log")'><p id='LogBar' class="Bar">Log In</p></li>
                    <li onclick='clinnation("Pol")'><p id='PolBar' class="Bar">Terms of Service</p></li>
                </ul>
            </div>

            <div id="Log" class="column">
                <h1 id="signTitle"> Sign In or Create Account</h1>
                <img src="/Imgs/Recruit.png" alt="WorkingMage" style='width:80%;display:block;margin:auto;padding: 2vw 0;' class='separator'>
                <p id="signExpl">Sign in to access the entirety of the Many Isles!</p>
                <div id="signCreateFormCont"></div>

            </div>

            <div id="Pol" class="column">
                <h1>Terms of Service</h1>
                <div>
                  <img src="/Imgs/Recruit.png" alt="WorkingMage" style='width:80%;display:block;margin:auto;padding: 2vw 0;' class='separator'>
                </div>
                <p>
                  The Terms of Service of the Many Isles website are outlined in this <a href="/docs/44/Terms_of_Service" target="_blank">article</a>.<br>
                  This site is protected by reCAPTCHA and the Google <a href=%double_quote%https://policies.google.com/privacy%double_quote%>Privacy Policy</a> and <a href=%double_quote%https://policies.google.com/terms%double_quote%>Terms of Service</a> apply.
                </p>
                <!-- <p>Find an outline of our account policy here; for more information, check out the <a href="https://docs.google.com/document/d/1ZxErZV-D1Otk0L4UbP3RZqUuR6ptSj8i3NNNmVxu9LU/edit?usp=sharing" target="_blank">Adventurer's Agreement</a>.<br />You agree to this policy, and the Adventurer's Agreement, when you create an account.</p>
                <p style="text-align:left">
                    <br>
                    &sect;0 Definitions<br>
                    &sect;0.1 Adventurer<br>
                    An adventurer is any person in the Many Isles. It is also the default title.<br>
                    &sect;0.2 Titles and Tiers<br>
                    In the Many Isles, each adventurer has a title and a tier. A title is always pronounced before the adventurer&rsquo;s username, for example &ldquo;Trader Hansfried&rdquo;. Each title is bound to a specific tier, going from 0 (Adventurer tier) to 4 (Pantheon tier). Among others, tiers grant greater access to the Many Isles digital library.<br>
                    &sect;0.3 Pantheon<br>
                    The Many Isles Pantheon can be seen as the moderators of the Many Isles. They uphold the varied platforms on which you can find the Many Isles, moderate those platforms, update and maintain Many Isles products, and in general make sure the community does well together. They&rsquo;re the absolute rulers and have all the rights; however, they also respect the rules written here, and will handle fairly any problems or disputes.<br>
                    &sect;1 Recognition<br>
                    &sect;1.1 Recognition of the Adventurer&rsquo;s Agreement<br>
                    The adventurer recognizes and accepts that all actions taken within the Many Isles are performed under the rules set by the Adventurer&rsquo;s Agreement. That document serves as the base rules for the entire Many Isles and regulates the community. They further recognize that the Pantheon may at any time alter the Adventurer&rsquo;s Agreement for the benefit and efficacy of all.<br>
                    &sect;1.5 Recognition of the Pantheon<br>
                    The adventurer recognizes that the Pantheon serves as final ruling authority over the Many Isles, and that it holds ultimate power over publishing and the trade institution.<br>
                    &sect;2 Data and Security<br>
                    &sect;2.1 Data Usage<br>
                    For the Many Isles services to work, they need to collect data from their users. However, this data is restricted to necessary usage within the Many Isles. It is not available in any form outside of the Many Isles proper. Additionally, no user profiling or advanced tracking is performed.<br>
                    &sect;2.2 Data Encryption and Security<br>
                    Any stored passwords are hashed and cannot be decrypted. The Many Isles database is secured and cannot be accessed externally, and no account has access to personal information beyond administrative needs to other accounts.<br>
                    &sect;2.3 Stored Data<br>
                    The Many Isles stores information in a number of tables. These include account information, partnership information, and others.<br>
                    &sect;2.4 Data Distribution<br>
                    The Many Isles takes care not to spread your data. We do not have ads, so no browser or personal information is sent to unknown buyers. All data collected in Many Isles databases never come in the hands of outside services except for Stripe payouts.<br>
                    <br>
                    <br>
                </p> -->

            </div>
        </div>
        <div class="icon" onclick="shoBar()">
            <i class="fas fa-bars rotate"></i>
        </div>
    </div>

    <div id="modal" class="modal" onclick="pop('ded')">
    </div>

    <div w3-include-html="/Code/CSS/genericFooter.html" w3-create-newEl="true"></div>

</body>
</html>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="/Code/CSS/global.js"></script>
<script>
    responsive("g/acc-m.css", "smol");

    function pop(x) {
        if (x == "ded") {
            document.getElementById("modal").style.display = "none";
            $(".modCol").hide();
        }
        else {
            document.getElementById("modal").style.display = "block";
            document.getElementById(x).style.display = "block";
        }
    }

    function shoBar() {
        if ($(".left-col").is(":hidden")) {
            $(".left-col").show();
            $(".fas").toggleClass("rotate");
            $("#".concat(tab)).hide();

        }
        else {
            $(".left-col").hide();
            $(".fas").toggleClass("rotate");
            $(".column").hide();
            $("#".concat(tab)).show();
        }
    }

    var tab = "Log";
    function clinnation(clicked) {
        if (document.getElementById(clicked) == null) { clicked = "Log"; }
        tab = clicked;
        $(".Bar").removeAttr("style");
        document.getElementById(clicked.concat("Bar")).style.color = "#9f9f9f";
        $(".column").hide();
        if (format == "mobile") {
            $(".left-col").hide();
            $(".fas").toggleClass("rotate");
            $("#".concat(clicked)).show();
        }
        else { document.getElementById(clicked).style.display = "block"; }
    }
    clinnation("Log");

    var urlParams = new URLSearchParams(window.location.search);
    var error = urlParams.get('error');
    var display = urlParams.get('display');
    var add = urlParams.get('add');
    if (display != null) {
        clinnation(display);
    }
    if (error == "notSignedIn") {
        document.getElementById("signTitle").innerHTML = "Sign In First";
        document.getElementById("signExpl").innerHTML = "You need to sign in to use this feature.";
    }
    else if (error == "wannaPublish") {
        document.getElementById("SignUp-title").innerHTML = "Make an Account to start Publishing!";
    }
    else if (error == "deleted") {
      createPopup("d:acc;txt:Account deleted.");
    }
    else if (error == "loginError") {
      createPopup("d:acc;txt:Failed to log in.");
    }
    else if (error == "signedOut") {
      createPopup("d:acc;txt:Successfully logged out.");
    }
    /*acp*/
    returnFin = function (resultObject) {
      if (getCookie("acceptCookies") && !getCookie("hasAccount")){
        document.cookie = "hasAccount=1; path=/;";
      }
      location.reload();
    }
    returnFcreate = function (resultObject) {
      var urlParams = new URLSearchParams(window.location.search);
      var error = urlParams.get('error');
      if (error == "wannaPublish"){
        document.getElementById("signCreateBigCont").replaceChildren(this.giveHTMLel("successCreateWanttopublishHTML"));
      }
      else {
        document.getElementById("signCreateBigCont").replaceChildren(this.giveHTMLel("successCreateHTML"));
      }
      if (getCookie("acceptCookies")){
        document.cookie = "hasAccount=1; path=/;";
      }
    }
    function acp_launcher() {
      acpBuilder2 = new acp_builder(returnFcreate);
      acpBuilder2.createPortal(document.getElementById("signCreateFormCont"), "signInBasic");
    };
  </script>
