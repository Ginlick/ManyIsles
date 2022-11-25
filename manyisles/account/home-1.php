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
        #wanttoPublish {
            display: none;
        }
    </style>
</head>
<body>

  <div w3-include-html="/Code/CSS/GTopnav.html" w3-create-newel="true"></div>
    <div style="flex: 1 0 auto;">
        <div class="flex-container">

            <div class='left-col'>
                <ul id="myMenu">
                    <li onclick='clinnation("Sign")'><p id='SignBar' class="Bar">Sign Up</p></li>
                    <li onclick='clinnation("Log")'><p id='LogBar' class="Bar">Log In</p></li>
                    <li onclick='clinnation("Pol")'><p id='PolBar' class="Bar">Account Policy</p></li>
                </ul>
            </div>

            <div id='Sign' class='column'>

                <h1 id="SignUp-title"> Join the Many Isles! </h1>
                <img src="/Imgs/Recruit.png" alt="WorkingMage" style='width:80%;display:block;margin:auto;padding: 2vw 0;' class='separator'>
                <p id="createAccountP">Only normal letters are allowed throughout the form. You may use numbers in the password, and special characters in the email.</p>

                <div id="signCreateFormCont"></div>

                <p>Already have an account? <span onclick="clinnation('Log')" class="fakelink">Log in</span></p>
                <div style="margin-top:7vw;">
                    <img src="/Imgs/Bar2.png" alt="GreyBar" class='separator'>
                </div>
                <h2>Why an Account?</h2>
                <p>
                    With an account, you unlock many awesome features in the Many Isles website and beyond.<br />
                    You can create custom spell lists, help in the creation of new products, join our awesome discord community, and download products directly from the <a href="/Code/Goods.php">digital library</a>, with tons more options to come!<br />
                    Additionally, you encourage us to go beyond and continue working to make this into something great. Show your support now!<br />
                    For more information, check out the <a href="/docs/7/Create_Account" target="_blank">documentation</a>.
                </p>
                <h2>Your Data and Privacy</h2>
                <p>
                    You might voice concerns as to the security of your data. After all, we might be evil phishers hailing from the darkest of realms where only aboleths and illithids venture.<br />
                    For that reason, be as safe as possible. Use a truly unique password, even though we encrypt them, because you can never know. Use the name of your latest D&D character. However, please use an email you check on at least occasionally, so we can contact you in case of need.<br />
                    For more information on our security policy, see the <a href="/docs/6/Accounts" target="_blank">account doc</a>.
                </p>

            </div>

            <div id="Log" class="column">

                <h1 id="signTitle"> Sign In </h1>
                <img src="/Imgs/Recruit.png" alt="WorkingMage" style='width:80%;display:block;margin:auto;padding: 2vw 0;' class='separator'>
                <p id="signExpl">Sign in to access the entirety of the Many Isles!</p>
                <div id="signFormCont"></div>

            </div>

            <div id="Pol" class="column">
                <h1>Account Policy</h1>
                <p>Find an outline of our account policy here; for more information, check out the <a href="https://docs.google.com/document/d/1ZxErZV-D1Otk0L4UbP3RZqUuR6ptSj8i3NNNmVxu9LU/edit?usp=sharing" target="_blank">Adventurer's Agreement</a>.<br />You agree to this policy, and the Adventurer's Agreement, when you create an account.</p>
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
                </p>

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
<script src="/account/portal/acp-builder.js"></script>
<script>
    returnF = function (resultObject) {
      location.reload(); //also support seeker cookie (log in), wanttoPublish (create)
    }
    element = document.getElementById("signFormCont");
    acpBuilder = new acp_builder(returnF);
    acpBuilder.createPortal(element); //options: have these be just nude inputform, don't allow changing

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

    var tab = "Sign";
    function clinnation(clicked) {
        if (document.getElementById(clicked) == null) { clicked = "Sign"; }
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
    clinnation("Sign");

    var urlParams = new URLSearchParams(window.location.search);
    var error = urlParams.get('error');
    var display = urlParams.get('display');
    var add = urlParams.get('add');
    if (display != null) {
        clinnation(display);
    }
    if (urlParams.get('uname') != null) { document.getElementById("uname").value = urlParams.get('uname'); }
    if (urlParams.get('email') != null) { document.getElementById("email").value = urlParams.get('email'); }
    if (error == "EmailTaken") {
        document.getElementById("email").placeholder = "Email Already Used";
        document.getElementById("email").value = null;
        document.getElementById("email").style.backgroundColor = "#ff8f8f";
    }
    else if (error == "UnameTaken") {
        document.getElementById("uname").placeholder = "username already in use";
        document.getElementById("uname").value = null;
        document.getElementById("uname").style.backgroundColor = "#ff8f8f";
    }
    else if (error == "signingIn") {
        clinnation('Log');
        document.getElementById("youFailedMaggot").style.display = "block";
    }
    else if (error == "notSignedIn") {
        clinnation('Log');
        document.getElementById("signTitle").innerHTML = "Sign In First";
        document.getElementById("signExpl").innerHTML = "You need to sign in to use this feature.";
    }
    else if (error == "signIn") {
        clinnation('Log');
    }
    else if (error == "wannaPublish") {
        document.getElementById("SignUp-title").innerHTML = "Make an Account to start Publishing!";
        document.getElementById("wanttoPublish").value = "1";
    }
    else if (error == "spamblock") {
        document.getElementById("createAccountP").innerHTML = "Our spam block interrupted your account creation. Please try again tomorrow.";
    }
    else if (error == "creatingAcc") {
      createPopup("d:acc;txt:Sorry, there was an error creating your account.");
    }
    else if (error == "deleted") {
      createPopup("d:acc;txt:Account deleted.");
    }

    if (add != null) {
      seekMaker(add);
    }

    function inputGramm(x, y) {
        var input = x.value;
        var patt = new RegExp("[^A-Za-z0-9 ]");
        var target = "uname";
        if (y == "e") { patt = new RegExp("[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]$"); target = "email"; }
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
    }</script>
