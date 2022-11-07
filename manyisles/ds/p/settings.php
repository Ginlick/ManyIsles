<?php
require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/db_accounts.php");
if(!isset($_COOKIE["loggedIn"])){header("Location: /account/Account?error=notSignedIn");exit();}

if (isset($_COOKIE["admin"])){$adminClearance = true;$admin = true;}else {$admin = false;}
$redirect = "../home.php";
$checkDSpresence = true;
require_once("security.php");

require_once("../g/makeHuman.php");
require_once("../g/alertStock.php");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <link rel="icon" href="/Imgs/FaviconDS.png">
    <title>Partnership Settings | Digital Store</title>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <link rel="stylesheet" type="text/css" href="/Code/CSS/Main.css">
    <link rel="stylesheet" type="text/css" href="../g/ds-g.css">
    <link rel="stylesheet" type="text/css" href="../g/ds-tables.css">
    <link rel="stylesheet" type="text/css" href="form.css">
    <style>
        .inputCont.checker {
            text-align: center;
            padding: 5px 0 10px;
        }
        .inputCont.checker input {
            width: auto;
        }
        .credTable tbody > tr > td > img {
            max-height: 100px;
            object-fit: cover;
              object-position: 50% 20%;
        }
    .credTable.prods.two tbody > tr > :nth-child(1) {
        width: 22%;
    }
    .credTable.prods.two tbody > tr > :nth-child(2) {
        width: 30%;
    }
    .credTable.prods.two tbody > tr > :nth-child(3) {
        width: 10%;
    }
    .credTable.prods.two tbody > tr > :nth-child(4) {
        width: 15%;
    }
    .credTable.prods.two tbody > tr > :nth-child(5) {
        width: 9%;
    }
    .credTable.prods.two tbody > tr > :nth-child(6) {
        width: 20%;
    }
    </style>
</head>
<body>
    <div w3-include-html="/Code/CSS/GTopnav.html" style="position:sticky;top:0;z-index:5;"></div>
        <div class="flex-container">
            <div class='left-col'>
                <a href="../home.php"><h1 class="menutitle">Partnership</h1></a>
                <ul class="myMenu">
                    <li><a class="Bar" href="hub.php"><i class="fas fa-arrow-left"></i> Hub</a></li>
                </ul>
                <img src="/Imgs/Bar2.png" alt="GreyBar" class='separator'>
                <ul class="myMenu bottomFAQ">
                    <li><a class="Bar" href="/docs/18/Digital_Store_Extension" target="_blank">DS Publishing</a></li>
                    <li><a class="Bar" href="/docs/19/Publishing_Obligations">DS Publishing Conditions</a></li>
                    <li><a class="Bar" href="/docs/15/Digital_Store" target="_blank">Digital Store FAQ</a></li>
                </ul>
            </div>

            <div id='content' class='column'>
                <h1>DS Extension Settings</h1>
                <div class='dsBanner'><img src='/Imgs/Ranks/HighMerchant.png' alt:'Oopsie!'></div>
                <p><?php echo "p#$pId<br>"; ?>Set up your Digital Store extension.</p>
                <?php if ($admin) {echo "<p>Viewing as admin</p>";} ?>
                <div class="checkoutBox" style="margin-bottom:0;">
                    <button class="checkout" type="submit" onclick="window.location='settings.php'">
                        <i class="fas fa-redo"></i>
                        <span>Reload</span>
                    </button>
                </div>
                <h2>Publications Status</h2>


                    <?php
                        $query = 'SELECT * FROM dsprods WHERE sellerId = "'.$pId.'"';
                        if ($admin){$query = 'SELECT * FROM dsprods';}
                        if ($toprow = $conn->query($query)) {
                            if (mysqli_num_rows($toprow) != 0) {
                                echo '                <p>Change the status of your items. Paused items cannot be ordered.</p>
                                        <form action="changeStatus.php" method="POST">
                                        <table class="credTable prods" style="width: 90%;margin:auto">
                                            <thead><tr><td></td><td></td><td>Views</td><td>Status</td><td onclick="toggleSelect()" class="fakelink">Select</td></tr></thead>

                                            <tbody>';

                                while ($row = $toprow->fetch_assoc()) {
                                    $datetime1 = date_create();
                                    $datetime2 = date_create( $row["reg_date"]);
                                    $interval = date_diff($datetime1, $datetime2);
                                    if ($interval->format('%d') > 1 AND $row["status"] == "deleted") {continue;}

                                    $articleName = $row["name"];
                                    $articleId = $row["id"];

                                    echo "<tr>";
                                    echo '<td><img src="'.clearImgUrl($row["thumbnail"]).'" alt="thumbnail" /></td>';
                                    echo '<td><a href="item.php?id='.$articleId.'" target="_blank">'.$articleName.'</a></td>';
                                    echo '<td>'.$row["popularity"].'</td>';
                                    echo '<td>'.prodStatSpan($row["status"]).'</td>';
                                    echo '<td><input name="'.$articleId.'" type="checkbox" class="pubVisSel" /> </td>';
                                    echo "</tr>";


                                }
                                echo '                    </tbody>
                                            </table>
                                            <div class="checkoutCont" style="margin: 30px 0">
                                                <button type="submit" class="checkout">
                                                    <i class="fas fa-arrow-right"></i>
                                                    <span>Toggle Selected</span>
                                                </button>
                                            </div>
                                            </form>';
                            }
                            else {
                                echo "<p>No published items yet.</p>";
                            }

                        }

                    ?>
                <h2 id="hRedCodes">Discount Codes</h2>
                    <div class="inputCont checker">
                        <input type="checkbox" name="acceptCodes" onchange="toggleAcception(this)" checked = "<?php echo $pAcceptCodes; ?>" required />
                        <label for="name">Accept All Discount Codes</label>
                    </div>

<?php

$query = 'SELECT * FROM dscodes WHERE affect = "1,'.$pId.'"';
if ($admin) {$query = 'SELECT * FROM dscodes';}
if ($toprow = $conn->query($query)) {
    if (mysqli_num_rows($toprow) != 0) {
        echo '                <table class="credTable prods two">
                    <thead><tr><td>Code</td><td>Amount</td><td>Uses</td><td>Max</td><td></td><td></td></tr></thead>
                    <tbody>';
        while ($row = $toprow->fetch_assoc()) {
            if ($row["status"]!=1){continue;}

            $codeAlterMode = $row["alterMode"];
            if ($codeAlterMode == "linear"){
                $codeAmount = "-".makeHuman($row["amount"]);
            }
            else {
                $codeAmount = "-".($row["amount"]/10)."%";
            }
            $cCode = $row["code"];

            echo "<tr>";
            echo '<td>'.$cCode.'</td>';
            echo '<td>'.$codeAmount.'</td>';
            echo '<td>'.$row["uses"].'</td>';
            echo '<td>'.$row["maxUses"].'</td>';
            echo "<td onclick='navigator.clipboard.writeText(\"$cCode\");createPopup(\"d:dsp;txt:Code copied to clipboard\");' class=' fakelink '><i class='far fa-clipboard'></i></td>";
            echo '<td><a href="dCode.php?c='.$row["id"].'"><button class="checkout homescreen"><i class="fas fa-trash"></i> Delete</button></a></td>';
            echo "</tr>";
        }
        echo '                        </tbody>
                    </table>';
    }
}

?>

                    <h3>New Code</h3>
                    <form  action="newCode.php" method="POST">
                    <div class="formContentBlock">
                        <div class="inputCont">
                            <label for="code">Name <span>*</span></label>
                            <input type="text" name="code" placeholder="22-22-22" oninput="checkSyntaxR(this, '^[0-9-]+$', 0)" onchange="checkSyntaxR(this, '^[0-9]{2}-[0-9]{2}-[0-9]{2}$', 1)" autocomplete="off" required />
                            <p class="inputErr info" default="Your unique code."></p>
                        </div>
                        <div class="inputCont">
                            <label for="alterMode">Alter Mode <span>*</span></label>
                            <select name="alterMode"><option value="linear">linear</option><option value="geometric">geometric</option></select>
                            <p class="inputErr info" default="See the <a href='/wiki/h/digsto/codes' target='_blank'>codes</a> documentation for more."></p>
                        </div>
                        <div class="inputCont">
                            <label for="amount">Amount <span>*</span></label>
                            <input type="number" name="amount" placeholder="2200" oninput="checkSyntaxR(this, '^[0-9]+$', 0)" onchange="checkSyntaxR(this, '^[0-9]+$', 1)" required />
                            <p class="inputErr info" default="The influencing factor. Linear: discount in cents. Geometric: tenths of a percent (1000 = 100% discount)."></p>
                        </div>
                        <div class="inputCont">
                            <label for="maxUses">Maximal Uses <span>*</span></label>
                            <input type="number" name="maxUses" placeholder="2200" min="1" max="50" oninput="checkSyntax(this, '^[0-9]+$', 0)" onchange="checkSyntax(this, '^[0-9]+$', 1)" required />
                            <p class="inputErr info" default="How many times the code can be used before it goes bad."></p>
                        </div>
                    </div>
                    <div class="checkoutCont" style="margin: 30px 0">
                        <button type="submit" class="checkout">
                            <i class="fas fa-arrow-right"></i>
                            <span>Create</span>
                        </button>
                    </div>
                    </form>

            </div>
        </div>
    </div>


    <div w3-include-html="../g/GFooter.html" w3-create-newEl="true"></div>


</body>
</html>
<script src="/Code/CSS/global.js"></script>
<script>
var urlParams = new URLSearchParams(window.location.search);
var why = urlParams.get('why');
if (why =="fStatus"){
    createPopup("d:dsp;txt:Failed to update statuses.");
}
else if (why =="sStatus"){
    createPopup("d:dsp;txt:Updated statuses.");
}
else if (why =="newCode"){
    createPopup("d:dsp;txt:Code created!");
}
else if (why =="doubleCode"){
    createPopup("d:dsp;txt:Code already exists - use a different name");
}
else if (why =="delCode"){
    createPopup("d:dsp;txt:Code deleted");
}
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
            target.innerHTML = "";
        }
    }
    else {
        target.style.opacity = "0";
    }
}
function toggleSelect() {
    for (let checker of document.getElementsByClassName("pubVisSel")) {
        checker.checked = !checker.checked;
    }
}
function toggleAcception(dir) {
    getFile = "switchCodeAccepp.php?dir=" + dir.checked;
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            let result = xhttp.responseText;
            if (result.includes("success")) {
                createPopup("d:dsp;txt:Successfully updated setting.");
            }
            else {
                createPopup("d:dsp;txt:Error. Failed to update setting.");
                dir.checked = !dir.checked;
            }
        }
    };

    xhttp.open("GET", getFile, true);
    xhttp.send();
}
</script>
