<?php
if (isset($_POST["input"])) {
    $input = $_POST["input"];
    if ($input == "" OR $input == "Markup goes here") {$input = "false";}
}
else {
    $input = "false";
}



?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <link rel="icon" href="/Imgs/Favicon.png">
    <title>Many Isles Markdown Editor</title>
    <link rel="stylesheet" type="text/css" href="/Code/CSS/Main.css">
    <link rel="stylesheet" type="text/css" href="/wiki/wik.css">
    <script src="https://kit.fontawesome.com/1f4b1e9440.js" crossorigin="anonymous"></script>
    <style>
        .col-1 {
            box-sizing: border-box;
            background-color: #fffefd;
            width: 35%;
            height: 100%;
            margin: 0;
            display: inline-block;
            padding: 8px;
            border-right: 1px solid #ddd;
            resize: horizontal;
            overflow: hidden;
            float: left;
        }
        .col-r {
            width: 64%;
            float:left;
            box-sizing: border-box;
            min-height: 200px;
            margin: 10px 0 10px .5%;
            background-color: #fffefd;
            padding: 8px;
            box-shadow: 0 6px 120px 0 rgba(0, 0, 0, 0.22);
            border-radius: 4px;
            overflow: hidden;
        }
        .topBanner {
            margin: 0;
            width: calc(100% + 16px);
            transform: translate(-8px, -8px);
        }
        .bigText {
            width: 100%;
            padding: 5px;
            font-family: Arial, Helvetica, sans-serif;
            height: calc(100% - 75px - 12vw);
            box-sizing: border-box;
            resize:none;
        }
    </style>
</head>
<body onresize="resetWidth()" onload="resetWidth()">

    <div class="content">
        <div class="col-1" id="left">
            <img src="/Imgs/PopPoet.png" alt="Hello There!" style="width:calc(100% + 16px);transform: translate(-8px, -8px);display: inline-block " />
            <form action="markedit.php" method="POST" style="height:100%;" onsubmit="setFormSubmitting()">
                <textarea class="bigText" id="input" name="input"><?php if ($input != "false"){echo $input;} else {echo "Markup goes here";} ?></textarea>
                <div class="bottButtCon" style="display: table">
                    <button class="wikiButton">View</button>
                </div>
            </form>
        </div>
        <div class="col-r" id="right">
            <img src="/wikimgs/banners/fandom.png" alt="oops" class="topBanner" />
            <p class="topinfo"><a href="/home.html">Many Isles</a> - <a href="tools.html">Tools</a> - <a href="#">Markdown Editor</a> </p>
            <h1>Many Isles Markdown Editor</h1>
<?php
if ($input != "false") {
    require "Parsedown.php";
     $Parsedown = new Parsedown();
     echo $Parsedown->text($input);
}
else {
    echo <<<Megatext
                Write some Many Isles Markdown in the edit column left, press on "view", and see how your page will look here! Once you've got it fleshed out, feel free to start writing in the <a href="/wiki/f/write.php">Many Isles Fandom Wiki</a>!<br />
                For further information, check out the <a href="/wiki/h/fandom.html">wiki article</a>.
                <h2>Many Isles Markdown</h2>
            <p>This is a quick overview of the pivotal elements; see the <a href="/wiki/h/fandom/markdown.html" target="_blank">article</a> for a comprehensive list.</p>
            <p>
                <i>#header</i> You can write headers using hashtags. A single one shouldn't be used as it designates a title, while ## gives header 2 and ### header 3.<br />
                <i>*italics*</i> A single pair of asterisks displays text in italics.<br />
                <i>**bold**</i> A double pair of asterisks displays text in bold.<br />
                <i>***bold italics***</i> A triple pair of asterisks displays text in bold and italics.<br />
                <i>[link](url)</i> You can embed links like this, with the square brackets being the displayed text, while the standard ones are the actual url.
            </p>
Megatext;
}

?>

            
        </div>
    </div>
</body>
</html>
<script class="jsbin" src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
<script src="ResizeSensor.js"></script>
<script>new ResizeSensor(document.getElementById("left"), function resetWidth() {
        let leftPercent = document.getElementById("left").offsetWidth / window.innerWidth;
        leftPercent = leftPercent * 100;
        let rightPercent = 100 - leftPercent - 1;
        rightPercent = rightPercent.toString();
        document.getElementById("right").style.width = rightPercent.concat("%");
    });
    function resetWidth() {
        let leftPercent = document.getElementById("left").offsetWidth / window.innerWidth;
        leftPercent = leftPercent * 100;
        let rightPercent = 100 - leftPercent - 1;
        rightPercent = rightPercent.toString();
        document.getElementById("right").style.width = rightPercent.concat("%");
    };

    var formSubmitting = false;
    var setFormSubmitting = function () { formSubmitting = true; };
    window.onload = function () {
        window.addEventListener("beforeunload", function (e) {
            if (formSubmitting) {
                return undefined;
            }
            else if (document.getElementById("input").value.length < 100) {
                return undefined;
            }

            var confirmationMessage = 'Are you sure you want to lose any unsaved changes?';

            (e || window.event).returnValue = confirmationMessage; 
            return confirmationMessage; 
        });
    };
    </script>
