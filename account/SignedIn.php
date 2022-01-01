<?php
require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/db_money.php");
require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/transactions.php");
require_once($_SERVER['DOCUMENT_ROOT']."/ds/g/makeHuman.php");
require_once($_SERVER['DOCUMENT_ROOT']."/dl/global/engine.php");
$dl = new dlengine();

$partner = false;
if ($dl->partner(false)) {
  $partner = true;
  $partname = $dl->partName;
  $partstatus = $dl->partStat;
  $partId = $dl->partId;
}
$conn = $dl->conn;

$id = $dl->user->user;
if (!$dl->user->signedIn){$dl->go("/account/Account", "");}

$query = "SELECT * FROM accountsTable WHERE id = ".$id;
$result = $conn->query($query);
while ($row = $result -> fetch_assoc()) {
    $name = $row["uname"];
    $title = $row["title"];
    $email = $row["email"];
    $checkpsw = $row["password"];
    $emailConfirmed = $row["emailConfirmed"];
    $discname = $row["discname"];
    $region = $row["region"];
}




if(isset($_GET["show"])){$goTo = "Location: /spells/SetSLCook.php?where=SignedIn&show=".$_GET["show"];} else {$goTo = "Location: /spells/SetSLCook.php?where=SignedIn";}
if (!isset($_COOKIE["spellLists"]) && $_GET["sl"]!="no"){header($goTo);}

$disctitle = "Connect Discord";
if ($discname != null){$disctitle = "Discord";}

$tradeadmin = false;
$poetadmin = false;
if ($id == 11 or $id == 14 or $id == 26 or $id == 36) {
    $tradeadmin = true;
}
$query="SELECT * FROM poets WHERE id = ".$id;
$result =  $conn->query($query);
if ($result != false){
    while ($row = $result->fetch_assoc()) {
        if ($row["admin"] == 1) {
            $poetadmin = true;
        }
    }
}

//bodies

$discConMailBody = <<<MESSAGE
                <div style="margin:auto;">
                    <img src="/Imgs/disc.png" alt="discord" style='width:20%;display:block;margin:auto;padding: 2vw 0;' class='separator'>
                </div>
                <p>
                    Please <span class="fakelink" onclick="pop('Conf');">confirm your email</span> first.
                </p>

MESSAGE;

$discConnBody = <<<MESSAGE
                <div style="margin:auto;">
                    <img src="/Imgs/disc.png" alt="discord" style='width:20%;display:block;margin:auto;padding: 2vw 0;' class='separator'>
                </div>
                <p>
                    The <a href="https://discord.gg/XTQnR7mS3D" target="_blank">Many Isles discord server</a> is a great place for the community to come together and share their brews.<br />
                    If you've already joined the server, please submit your discord username, which will connect your accounts. Your discord nickname will be updated with your Many Isles title, tier and username in a second!
                </p>
                <form action="DiscConn.php" method="post">
                    <div class="container">
                        <input class="mainInput" type="text" name="discSubmit" id="discSubmit" placeholder="Ginlic#6643" autocomplete="off" oninput="discGramm(this)" required />
                        <p id="discInputErr" class="inputErr">Incorrect input!</p>
                        <p id="discDuplicateErr" style="display:none;color:red">Username already in use.</p>
                    </div>
                    <button class="popupButton" type="submit" style="margin-top:3vw;">Submit</button>
                </form>
MESSAGE;

$discBody = <<<MESSAGE
                <div style="margin:auto;">
                    <img src="/Imgs/disc.png" alt="discord" style='width:20%;display:block;margin:auto;padding: 2vw 0;' class='separator'>
                </div>
                <p>
                    The <a href="https://discord.gg/XTQnR7mS3D" target="_blank">Many Isles discord server</a> is a great place for the community to come together and share their brews.<br />
                    Your discord username is <b>aSDADA</b>.
                    To get your discord nickname and tier updated, please dm an admin until we set up a bot to do the work.
                </p>
                <form action="DiscConn.php" method="post">
                    <div class="container">
                        <input class="mainInput" type="text" name="discSubmit" id="discSubmit" placeholder="New Username" autocomplete="off" oninput="discGramm(this)" required />
                        <p id="discInputErr" class="inputErr">Incorrect input!</p>
                        <p id="discDuplicateErr" style="display:none;color:red">Username already in use.</p>
                    </div>
                    <button class="popupButton" type="submit" style="margin-top:3vw;">Submit</button>
                </form>

MESSAGE;
$discBody = str_replace("aSDADA", $discname, $discBody);


$confMailBar = <<<message
<li onclick='clinnation("Conf")'> <p id='ConfBar' class="Bar">Confirm Email</p></li>
message;

$confMailBody = <<<MESSAGE
            <div id='Conf' class='column'>
                <h1>Confirm Email</h1>
                <div style="margin:auto;">
                    <img src="/Imgs/Recruit.png" alt="WorkingMage" style='width:80%;display:block;margin:auto;padding: 2vw 0;' class='separator'>
                </div>
                <p>
                    Make sure you confirm your email as soon as possible to profit fully of your Many Isles account.
                </p>
                 <button class="popupButton" type="submit" style="width:auto;margin-top:4vw"><a href="resendConfirm.php?mail=
MESSAGE;
    $confMailBody = $confMailBody.$email."&id=".$id.'" style="color:white;text-decoration:none">Resend Email</a></button></div>';

if ($partner == false) {
$partBar = <<<message
<li> <a class="Bar line" href="BePartner.php" target="_blank">Become Partner</a></li>
message;
}
else if ($partstatus == "pending") {
$partBar = <<<message
<li onclick='clinnation("Part")'> <p id='PartBar' class="Bar line">Partnership</p></li>
message;
}
else if ($partstatus == "active") {
$partBar = <<<message
<li onclick='clinnation("Part")'> <p id='PartBar' class="Bar">Partnership Overview</p></li>
<li> <a class="Bar line" href="Publish.php">Edit Partnership</a></li>COOLDS
message;

    $query = "SELECT acceptCodes FROM partners_ds WHERE id = $partId";
    $result = $conn->query($query);
    if (mysqli_num_rows($result) != 0){
      $partBar = str_replace("Bar line", 'Bar', $partBar); ;
      $partBar = str_replace("COOLDS", '<li><a class="Bar line" href="/ds/p/hub.php">Digital Store Hub</a></li>', $partBar); ;
    }
    else {
        $partBar = str_replace("COOLDS", '', $partBar); ;
    }
}
else if ($partstatus == "suspended") {
$partBar = <<<message
<li onclick='clinnation("Part")'> <p id='PartBar' class="Bar line">Partnership</p></li>
message;
}

if ($partner == false ){
    $partBody = null;
}
else if ($partstatus == "pending") {
$partBody =<<<MESSAGE
                <div id='Part' class='column'>
                <h1>Partnership Pending</h1>
                <div style="margin:auto;">
                    <img src="/Imgs/Ranks/Trader.png" alt="trade" style='width:80%;display:block;margin:auto;padding: 2vw 0;' class='separator'>
                </div>
                <p>
                    Your partnership is currently pending; check back soon to get going!<br>
                    In the meantime, feel free to explore our partnership program <a href="/wiki/h/publishing.html" target="_blank">publishing articles</a>.
                </p>
            </div>
MESSAGE;
}
else if ($partstatus == "active") {
$partBody =<<<MESSAGE
                <div id='Part' class='column'>
                <h1>partnership</h1>
                <div style="margin:auto;">
                    <img src="/Imgs/Ranks/Trader.png" alt="trade" style='width:80%;display:block;margin:auto;padding: 2vw 0;' class='separator'>
                </div>
                <p>
                    Your great participation to the Many Isles!
                </p>
                <ul class="coolInfo">
                    <li><b>XADA1</b> products</li>
                    <li><b>XADA4</b> tiered products</li>
                </ul>
                <div style="width:30%;margin:auto;">
                    <a class="popupButton" style="margin-top:3vw;" href="Publish.php">Edit</a>
                </div>
            </div>
MESSAGE;/*'*/
    $partBody = str_replace("partnership", $partname, $partBody);
    if ($dl->ppower > 0){$partBody = str_replace("Trader.png", "HighMerchant.png", $partBody);}
    $partBody = str_replace("partnership", $partname, $partBody);
    $partBody = str_replace("XADA1", $dl->totalPub, $partBody);
    $partBody = str_replace("XADA4", $dl->totalPrems, $partBody);
}
else if ($partstatus == "suspended") {
$partBody =<<<MESSAGE
                <div id='Part' class='column'>
                <h1>Partnership Suspended</h1>
                <div style="margin:auto;">
                    <img src="/Imgs/Ranks/Trader.png" alt="trade" style='width:80%;display:block;margin:auto;padding: 2vw 0;' class='separator'>
                </div>
                <p>
                    Your partnership gay is currently suspended, and none of its content is visible on the digital library. Feel free to contact <a href="mailto:pantheon@manyisles.ch" target="_blank">pantheon@manyisles.ch</a> for more information.<br>
                    Please make sure you understand the circumstances given in our <span class="fakelink" onclick="clinnation('Pol')">Trade Policy</span> and in the <a href="https://docs.google.com/document/d/1Q1CqPuaHVOM2Bz9GsZQ9S9QvrRZmyMFVo6_Iu7fq2K8/edit?usp=sharing" target="_blank">Trader's Agreement</a>.<br>
                    You may lose possession of some of your partnership if the suspension is not resolved, in accordance with the Trader's Agreement.
                </p>
            </div>
MESSAGE;
    $partBody = str_replace("gay", $partname, $partBody);
}

if ($poetadmin == false AND $tradeadmin == false) {
    $adminBar = "";
}
else if ($poetadmin == true AND $tradeadmin == true){
$adminBar = <<<message
                <li> <a class="Bar" href="admin.php">Trade Admin</a></li>
                <li> <a class="Bar line" href="/fandom/admin.php">Poetry Admin</a></li>
message;
}
else if ($poetadmin == true){
$adminBar = <<<message
                <li> <a class="Bar line" href="/fandom/admin.php">Poetry Admin</a></li>
message;
}
else if ($tradeadmin == true){
$adminBar = <<<message
                <li> <a class="Bar line" href="admin.php">Trade Admin</a></li>
message;
}




$ordersBody = <<<NICENICE

            <div id="orders" class="column">
                <h1>Orders</h1>
                <img src="/IndexImgs/coins.png" alt="coins" style='width:30%;display:block;margin:auto;padding: 1vw 0;' class='separator'>
                <p>Your entire order history in the <a href="/ds/home.php" target="_blank">digital store</a>.</p>
                COOLTABLEHERE
            </div>

NICENICE;

$ordersBar = <<<MEGA
    <li onclick='clinnation("orders")'> <p id='ordersBar' class="Bar line">Orders</p></li>
MEGA;

$query = "SELECT * FROM dsorders WHERE buyer = $id ORDER BY ud DESC";
$ordersExist = false;
if ($toprow = $conn->query($query)) {
    if (mysqli_num_rows($toprow) != 0) {
        include_once("../ds/g/ordStatus.php");

        $ordersExist = true;
        $coolTable = "<table class='credTable orders'><thead><tr><td>Order Id</td><td>Items</td><td>Seller</td><td>Amount</td><td>Date</td><td>Status</td></tr></thead><tbody>";
        include_once("../ds/g/loopBasket.php");

        while ($row = $toprow->fetch_assoc()) {
            $ordUd = $row["ud"];
            $ordClid = $row["orderId"];
            $ordSeller = $row["seller"];
            $ordAmount = $row["amount"];
            $ordItems = $row["items"];
            $ordXStatus = $row["status"];
            $ordRegdate = $row["reg_date"];

            $inbasket = explode(",", $ordItems);
            $fullUL = detailsUL($inbasket);


            $date_array = date_parse($ordRegdate);
            $ordPubdate = $date_array["day"].".".$date_array["month"].".".$date_array["year"]." ".$date_array["hour"].":".$date_array["minute"];

            $coolTable .= "<tr>";
            $coolTable .= '<td>#'.$ordClid."-".$ordUd.'</td>';
            $coolTable .= '<td>'.$fullUL.'</td>';
            $coolTable .= '<td><a href="/ds/p/partner.php?id='.$ordSeller.'" target="_blank">p#'.$ordSeller.'</a></td>';
            $coolTable .= '<td>'.makeHuman($ordAmount).'</td>';
            $coolTable .= '<td>'.$ordPubdate.'</td>';
            $coolTable .= '<td>'.ordStatus($ordXStatus, 1).'</td>';
            $coolTable .= "</tr>";

        }
        $coolTable .= "</tbody></table>";
        $ordersBody = str_replace("COOLTABLEHERE", $coolTable, $ordersBody);
    }
}

$creditBody = <<<EPICCOOL

            <div id="Credit" class="column" style="text-align:center;">
                <h1>Many Isles Credit</h1>
                <img src="/IndexImgs/coins.png" alt="coins" style='width:30%;display:block;margin:auto;padding: 1vw 0;' class='separator'>
                <p>You currently have <b>CURRENTTOTAL</b> in your account.</p>
                <div class="checkoutBox">
                    <a href="/ds/credit.php" target="_blank">
                        <button class="checkout">
                            <i class="fas fa-shopping-basket"></i>
                            <span>Add</span>
                        </button>
                    </a>
                    <a href="/wiki/h/digsto/credit.html" target="_blank">
                        <button class="checkout">
                            <i class="fas fa-arrow-right"></i>
                            <span>Payout</span>
                        </button>
                    </a>
                </div>
                <table class="theTable">
                    <thead>
                        <tr>
                            <td>Description</td><td>Source</td><td>Amount</td><td>Date</td>
                        </tr>
                    </thead>
                    <tbody>
                        COOLLINESHERE
                    </tbody>
                </table>

            </div>

EPICCOOL;

$creditBar = "";
$hasCredit = true;
$userCredit = new transaction($moneyconn, $id);

$query = "SELECT * FROM transfers_$userCredit->reference ORDER BY reg_date DESC LIMIT 0, 122";
if ($result = $moneyconn->query($query)){
    $count = 0;
    while ($row = $result->fetch_assoc()){
        $count++;
        if ($count == 100){break;}
        $motive = $row["motive"];
        $source = $row["source"];
        $amount = $row["amount"];
        $artRegdate = $row["reg_date"];
        $amount = makeHuman($amount);
        $date_array = date_parse($artRegdate);
        $artPubdate = $date_array["day"].".".$date_array["month"].".".$date_array["year"]." ".$date_array["hour"].":".$date_array["minute"];

        $newLine = "<tr><td>$motive</td> <td>$source</td> <td>".$amount."</td> <td>".$artPubdate."</td></tr> COOLLINESHERE";
        $creditBody = str_replace("COOLLINESHERE", $newLine, $creditBody);
    }
    $creditBody = str_replace("COOLLINESHERE", "", $creditBody);
    $creditBody = str_replace("CURRENTTOTAL", makeHuman($userCredit->total_credit), $creditBody);
}


$creditBar = <<<MEGA
    <li onclick='clinnation("Credit")'> <p id='CreditBar' class="Bar line">Many Isles Credit</p></li>
MEGA;


if ($ordersExist){
    $creditBar = str_replace(" line", "", $creditBar);
}

if (isset($_COOKIE["spellLists"])||$_GET["sl"]=="no"){
$spellPartly =<<<GREATSTUFF
    <h1>Saved Spell Lists</h1>
    <p>
        You can always find these or create new ones in the Many Isles <a href="/spells/SpellList.html" target="_blank">spell list</a>.
    </p>
        <table id="SLoptions">
            <thead>
                <tr>
                    <th>Slot</th>
                    <th>Name</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    <div style="width:30%;margin:auto;">
        <a class="popupButton spelly" style="margin-top:3vw;" href="/spells/SpellList.html">View</a>
    </div>
GREATSTUFF;
}
else {
$spellPartly =<<<GREATSTUFF

    <h1>Saved Spell Lists</h1>
    <p>
        Looks like you haven't saved any spell lists yet.
    </p>
    <div style="width:30%;margin:auto;">
        <a class="popupButton spelly" style="margin-top:3vw;" href="/spells/SpellList.html">Create</a>
    </div>
    <p>The Many Isles allows you to create custom spell lists of all 5eS spells for your characters. Learn more in the <a href="/wiki/h/spells.html" target="_blank">wiki article</a>.</p>
GREATSTUFF;
}


?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <link rel="icon" href="/Imgs/Favicon.png">
    <title>Account</title>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <link rel="stylesheet" type="text/css" href="/Code/CSS/Main.css">
    <link rel="stylesheet" type="text/css" href="/Code/CSS/pop.css">
    <link rel="stylesheet" type="text/css" href="g/acc.css">
    <link rel="stylesheet" type="text/css" href="/ds/g/ds-tables.css">
    <style>
    #region {
        width: 50%;
    }
        .credTable.orders {width: 80%;}
        .theTable {
            border-collapse: collapse;
            width: 80%;
            margin: 0 auto 200px;
            text-align: left;
            font-size: calc(7px + .9vw);
            font-family: 'Open Sans', Geneva, Verdana, sans-serif;
        }
            .credTable tbody > tr > td, .credTable thead > tr > td {padding: 10px;}

            .theTable tbody > tr > :nth-child(1) {
                width: 35%;
            }

            .theTable tbody > tr > :nth-child(2) {
                width: 25%;
            }

            .theTable tbody > tr > :nth-child(3) {
                width: 15%;
            }
            .theTable thead {font-weight:bold;}
            .theTable tbody > tr > td, .theTable thead > tr > td {
                border-bottom: 1px solid #ddd;
                padding: 10px;
                text-align: left;
            }

        .checkoutBox {
            width: 100%;
            margin: 4vw auto;
            display: inline-block;
        }
        button.checkout {
            background-color: #d1a720;
            border-radius: 10px;
            padding: 9px;
            font-size: 2vw;
            color: white;
            font-weight: normal;
            display: inline-block;
            margin: 0 10px;
        }
            button.checkout:hover {
                background-color: #f0c026;
                transition: .2s ease;
                cursor: pointer;
            }
    </style>
</head>
<body>
    <div w3-include-html="/Code/CSS/GTopnav.html" style="position:sticky;top:0;"></div>
    <div style="flex: 1 0 auto;">

        <div class="flex-container">
            <div class='left-col'>
                <ul id="myMenu">
                    <li onclick='clinnation("Over")'><p id='OverBar' class="Bar">Overview</p></li>
                    <?php if ($emailConfirmed != 1) {echo $confMailBar; } ?>
                    <li onclick='clinnation("Disc")'> <p id='DiscBar' class="Bar"><?php echo $disctitle; ?></p></li>
                    <li onclick='clinnation("Pat")'> <p id='PatBar' class="Bar">Connect Patreon</p></li>
                    <li> <a class="Bar line" href="/ds/tiers.php" target="_blank">Purchase Tier</a></li>
                    <?php echo $partBar; ?>
                    <?php echo $adminBar; ?>
                    <?php echo $creditBar; ?>
                    <?php if ($ordersExist) { echo $ordersBar; } ?>
                    <li onclick='clinnation("Spell")'> <p id='SpellBar' class="Bar">Spell Lists</p></li>
                    <li> <a class="Bar line" href="Beta.html">Beta Goodies</a></li>
                    <li onclick='clinnation("Del")'> <p id='DelBar' class="Bar">Delete Account</p></li>

                </ul>
            </div>

            <div id='Over' class='column'>
                <h1><?php echo $title." ".$name; ?></h1>
                    <img src="<?php echo $dl->user->image(); ?>" alt="WorkingMage" class='bannerI' class='separator'>
                <p>
                    Your account unlocks many awesome features, such as making your own spell list, access to premium content in the digital library, and getting early and free access to some of our products via mail!<br>
                    If you have any questions, problems or complaints, feel free to contact us at <a href="mailto:pantheon@manyisles.ch" target="_blank">pantheon@manyisles.ch</a>.
                </p>
                <div class="infoContain">
                    <p>Email: <span style="color:#d40000"><?php echo $email; ?></span></p><button class="popupButton" onclick="pop('email')">Change</button>
                </div>
                <div class="infoContain">
                    <p>Password</p><button class="popupButton" onclick="pop('psw-b')">Change</button>
                </div>
                <div class="infoContain" style="margin:5vw 37.5% auto;width:25%">
                        <form action="changeRegion.php" method="POST" >
                        <td> <label for="region"><b>Region</b></label></td>
                        <td> <select name="region" id="region" required>
                                <option value="1" <?php if ($region == 1){echo "selected";} ?>>1 (UTC)</option>
                                <option value="2" <?php if ($region == 2){echo "selected";} ?>>2 (UTC + 7)</option>
                                <option value="3" <?php if ($region == 3){echo "selected";} ?>>3 (UTC - 7)</option>
                            </select>
                        </td>
                        <div style="width:40%;margin:auto"><button class="popupButton" type="submit">Change</button></div>
                        </form>
                </div>
                <div>
                    <img src="/Imgs/Bar2.png" alt="GreyBar" class='separator'>
                </div>
                <h2>Sign Out</h2>
                <p>Sign out of your account.</p>
                <div style="width:20%;margin:auto;">
                    <div class="popupButton" style="margin-top:3vw;" onclick="signOut('friendly')">Sign Out</div>
                </div>
            </div>

            <?php
            if ($emailConfirmed != 1) {
                 echo $confMailBody;
            }
            ?>

            <div id='Disc' class='column'>
                <h1><?php echo $disctitle; ?></h1>
                <?php
                if ($emailConfirmed == null) { echo $discConMailBody; }
                else if ($discname == null) { echo $discConnBody; }
                else {echo $discBody; }
                ?>
            </div>

            <div id='Pat' class='column'>
                <h1>Connect Patreon</h1>
                <div style="margin:auto;">
                    <img src="/Imgs/Pat.png" alt="discord" style='width:20%;display:block;margin:auto;padding: 2vw 0;' class='separator'>
                </div>
                <p>
                    If you are a patron of the <a href="https://patreon.com/manyisles" target="_blank">Many Isles Patreon</a>, make sure to connect your account so that you can gain the higher title and tier.<br />
                    <span style="color:crimson">Note:</span> This method is deprecated. We recommend that you <a href="/ds/tiers.php">buy tiers in our digital store</a> instead.
                </p>
                <form action="PatConn.php" method="post">
                    <div class="container">
                        <input class="mainInput" type="text" name="patSubmit" placeholder="Elder Gods" autocomplete="off" required />
                    </div>
                    <p id="patSucc" style="display:none;color:green">Connected! Your tier should be updated in a moment.</p>
                    <button class="popupButton" type="submit" style="margin-top:3vw;">Submit</button>
                </form>
            </div>

            <?php echo $partBody; /*'*/ ?>

            <div id='Pol' class='column'>
                <h1>Trade Policy</h1>
                <p>
                    Find an outline of our trade policy below; for more information, check out the <a href="https://docs.google.com/document/d/1Q1CqPuaHVOM2Bz9GsZQ9S9QvrRZmyMFVo6_Iu7fq2K8/edit?usp=sharing" target="_blank">Trader's Agreement</a>.<br>
                    You agreed to this policy, and the Trader's Agreement, when you closed your partnership.
                </p>
                <p style="text-align:left">
                    &sect;0 Definitions<br />
                    &sect;0.1 Trader and Partner<br />
                    A trader is any person that publishes within the Many Isles. Partner, as a quasi-synonym, is a denomination for any owner of a partnership with the Many Isles.<br />
                    &sect;0.2 Products and Publishing<br />
                    In the Many Isles, a product is any thing - pdf, link, or image - posted by a partnership in the goal of sharing it to the community. The act of publishing is performed whenever a trader performs any action within the Many Isles publishing system, such as submitting a product.<br />
                    &sect;0.3 Partnership<br />
                    A partnership is the publishing entity that owns products published in the Many Isles digital library, which in turn is owned by an adventurer holding an account in the Many Isles and administered by Many Isles administrators.<br />
                    <br />&sect;1 Recognition<br />
                    &sect;1.1 Recognition of the Trader&rsquo;s Agreement<br />
                    The trader recognizes and accepts that all publishing actions taken within the Many Isles library are performed under the rules set by the Trader&rsquo;s Agreement. All actions concerning partnerships and their products are written in that document, which forms the base for all publishing. They further recognize that the Pantheon and Homeland Institute of Trade may at any time alter the Trader&rsquo;s Agreement for the benefice and efficacy of all.<br />
                    &sect;1.2 Recognition of the Trade Code<br />
                    The trader recognizes that as soon as they close a partnership with the Many Isles, they join the Homeland Institute of Trade and therefore underlie the institution&rsquo;s rules, represented in the Trade Code.<br />
                    &sect;1.3 Recognition of the Homeland Institute of Trade<br />
                    The trader recognizes that the entity directly administering publishing in the Many isles is the Homeland Institute of Trade, and that cooperation with this institution form the basis of any publishing.<br />
                    &sect;1.4 Recognition of the Adventurer&rsquo;s Agreement<br />
                    The trader, as an adventurer, recognizes and accepts the Adventurer&rsquo;s Agreement as core rules of the entire Many Isles community, and that it is the base for all, including the publishing system. They further understand that they must follow the Agreement&rsquo;s rules in addition to specific trade rules.<br />
                    &sect;1.5 Recognition of the Pantheon<br />
                    The trader, as an adventurer, recognizes that the Pantheon serves as final ruling authority over the Many Isles, and that it holds ultimate power over publishing and the trade institution.<br />
                    <br />&sect;2 Obligations of the Partner<br />
                    &sect;2.1 Obedience to Rules and Administrators<br />
                    The trader accepts that they must follow all instructions posed by applicable rules such as the Trader&rsquo;s Agreement. They also accept that administrators of the Homeland Institute of Trade as well as the Pantheon have, within the bounds of the Adventurer&rsquo;s Agreement and Trader&rsquo;s Agreement, total control on a partnership and its products.<br />
                    &sect;2.2 Partnership<br />
                    The trader&rsquo;s partnership must follow the Adventurer&rsquo;s Agreement, such as (1) an absence of vulgar or highly sexual language; (2) a sufficient quality throughout it.<br />
                    &sect;2.3 Product Requirements<br />
                    A product must follow the restrictions imposed by the Trader&rsquo;s Agreement, including but not restricted to: (1) absence of vulgar or highly sexual language; (2) ownership of the product; (3) following of the Wizards of the Coast&rsquo;s OGL; (4) correct title page layout; (5) no self-promotion except at the end of a document.<br />
                    <br />&sect;3 Rights of the Partner<br />
                    &sect;3.1 Right of Recursion<br />
                    The Trader&rsquo;s Agreement serves as the final decision in any situation concerning trade. Although administration may change the Agreement at any time, a trader may not be condemned for breaks against the Agreement that hadn&rsquo;t been outlined at the time of the break. <br />
                    &sect;3.2 Right of Fairness<br />
                    The Pantheon grants leeway to traders, and treats them fairly. If a problem were hidden or manipulated, the Pantheon will act in the best way possible for the trader and itself; it will not attempt to exploit the trader.<br />
                    &sect;3.3 Right of Preservation<br />
                    It is impossible for the Pantheon or trade administration to delete any product or partnership, or cause any lasting changes to a partnership or its products. Only a partner may edit and delete products and partnerships. This right has two exceptions: (1) The Pantheon or trade administration can at any time suspend a partnership according to &sect;3.6 of the Adventurer&rsquo;s Agreement, causing no lasting changes but temporarily removing all products from the digital library; (2) The Pantheon may start a salvation period and dissolve a partnership, transferring ownership of products and deleting a partnership, in certain very specific cases, as per &sect;3.7 of the Adventurer&rsquo;s Agreement.<br />
                    &sect;3.4 Right of Ownership<br />
                    Any products published by a partnership are fully owned by it. Any profits made through that product are compounded in the partner&rsquo;s interest, and the Many Isles have no ownership claims over the product. The Many Isles can take action upon products in certain cases:<br />
                    a. A partnership is suspended, in which case all products are temporarily invisible in the digital library, the partner can take no actions in publishing, and the partnership&rsquo;s value is reduced to 0.<br />
                    b. A partnership is dissolved, in which case the Pantheon may claim ownership over some or all of a partnership&rsquo;s products before deleting it in the dissolving process.<br />
                    &sect;3.5 Right of Payment<br />
                    A true partnership (instead of a companionship) has unlimited access to its funds and revenue according to its value as per &sect;2 and &sect;3 of the Adventurer&rsquo;s Agreement, which the Many Isles can in no way keep away from the partner. This right may be impeded by the suspension of a partnership, as per &sect;3.6 of the Adventurer&rsquo;s Agreement.<br />
                </p>
            </div>

            <?php echo $creditBody; ?>

            <?php if ($ordersExist) { echo $ordersBody; } ?>


            <div id='Spell' class='column'>
                <?php echo $spellPartly; ?>
            </div>

            <div id='Del' class='column'>
                <h1>Delete Account</h1>
                <div style="margin:auto;">
                    <img src="/Imgs/Recruit.png" alt="WorkingMage" style='width:80%;display:block;margin:auto;padding: 2vw 0;' class='separator'>
                </div>
                <p>
                    Deleting your account will forever remove everything your Many Isles avatar has done. This includes any partnerships, Many  Isles Credit, saved spell lists, or account connections.<br />
                    Please be aware that if you have an active partnership, the Pantheon may partially or fully appropriate its products.
                </p>
                <button class="popupButton" style="background:#363636;" onclick="pop('del')">Delete</button>
            </div>

        </div>

        <div class="icon" onclick="shoBar()">
            <i class="fas fa-bars rotate"></i>
        </div>
    </div>
    <footer style="flex-shrink:0;">
    </footer>

    <div id="modal" class="modal" onclick="pop('ded')">
    </div>

    <div id="email" class="modCol">
        <div class="modContent">
            <img src="/Imgs/PopupBar.png" alt="Hello There!" style="width: 100%; margin: 0; padding: 0; display: inline-block " />
            <h1>Change Email</h1>
            <p>
                Your current email is <span style="color:red"><?php echo $email; ?></span><br /><br />
                All information we send you will go to your new email. Please be aware that if you enter a wrong address, you will not get anything from us, be it free goodies or important updates.
            </p>
            <form style="padding:0 10% 0 10%" action="ChangeMail.php" autocomplete="off" method="POST">
                <input type="password" id="psw" name="psw" placeholder="CurrentUniquePassword22" required />
                <input type="email" id="newmail" name="newmail" placeholder="godsofmanyisles@gmail.com" autocomplete="new-password" required />
                <p id="emailWrongPsw" style="color:red;display:none">Incorrect Password.</p>
                <p id="emailAccomplished" style="color:green;display:none">Confirm new email to complete change</p>
                <button class="popupButton" style="margin-bottom:2%;padding:10px 20px 10px 20px;" type="submit">OK</button>
            </form>
        </div>
    </div>

    <div id="psw-b" class="modCol">
        <div class="modContent">
            <img src="/Imgs/PopupBar.png" alt="Hello There!" style="width: 100%; margin: 0; padding: 0; display: inline-block " />
            <h1>Change Password</h1>
            <p>
                Changing your password regularly is a good security measure. Keep it up!
            </p>
            <form style="padding:0 10% 0 10%" action="ChangePsw.php" autocomplete="off" method="POST">
                <input type="password" id="oldpsw" name="oldpsw" placeholder="currentUniquePassword22" required />
                <input type="password" id="newpsw" name="newpsw" placeholder="newUniquePassword22" pattern="[A-Za-z0-9]{1,}" required />
                <p id="pswWrongPsw" style="color:red;display:none">Incorrect Password.</p>
                <p id="pswAccomplished" style="color:green;display:none">Password successfully changed.</p>
                <button class="popupButton" style="margin-bottom:2%;padding:10px 20px 10px 20px;" type="submit">OK</button>
            </form>
        </div>
    </div>

    <div id="del" class="modCol">
        <div class="modContent" style="background:black">
            <img src="/Imgs/PopupBar.png" alt="Hello There!" style="width: 100%; margin: 0; padding: 0; display: inline-block " />
            <h1 style="color: grey">Sorry to see you leave...</h1>
            <p style="color: grey">
                This step is irreversible.
            </p>
            <form style="padding:0 10% 0 10%" action="DelAcc.php?dewIt=<?php echo $id; ?>">
                <input type="password" name="psw" placeholder="uniquePassword22" />
                <p id="delWrongPsw" style="color:red;display:none">Incorrect Password.</p>
                <button class="popupButton" style="margin-bottom:2%;padding:10px 20px 10px 20px;" type="submit">OK</button>
            </form>
        </div>
    </div>

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

    function checkCookie() {
        if (document.cookie.indexOf('loggedIn') == -1) {
            window.location.href = "Account.html?error=notSignedIn";
        }
        else if (document.cookie.indexOf('loggedP') == -1) {
            window.location.href = "Account.html?error=notSignedIn";
        }
    }
    checkCookie();

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

    var tab = "Over";
    function clinnation(clicked) {
        if (document.getElementById(clicked) == null){clicked = "Over";}
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
    clinnation("Over");

    function discGramm(x) {
        var input = x.value;
        var patt = new RegExp("#[0-9]{4}$");
        $("#discSubmit").removeAttr("style");
        if (!patt.test(input) && input.length != 0) { $("#discInputErr").show(); }
        else {$("#discInputErr").hide();}
    }

    var urlParams = new URLSearchParams(window.location.search);
    var show = urlParams.get('show');
    var display = urlParams.get('display');
    if (display != null) {
        clinnation(display);
    }
    if (show == "pat") {
        clinnation("Pat");
        $("#patSucc").show();
    }
    else if (show == "emailWrongPassword") {
        pop("email");
        document.getElementById('emailWrongPsw').style.display = 'block';
        document.getElementById('emailWrongPsw').innerHTML = 'Incorrect Password.';
    }
    else if (show == "emailAccomplished") {
        pop("email");
        document.getElementById('emailAccomplished').style.display = 'block';
    }
    else if (show == "emailChangConf") {
        pop("email");
        document.getElementById('emailAccomplished').style.display = 'block';
        document.getElementById('emailAccomplished').innerHTML = 'New email confirmed!';
    }
    else if (show == "emailDoubleMail") {
        pop("email");
        document.getElementById('emailWrongPsw').style.display = 'block';
        document.getElementById('emailWrongPsw').innerHTML = 'Email already used';
    }
    else if (show == "pswWrongPassword") {
        pop("psw-b");
        document.getElementById('pswWrongPsw').style.display = 'block';
    }
    else if (show == "pswWrongPassword") {
        pop("psw-b");
        document.getElementById('pswWrongPsw').style.display = 'block';
        $("#pswWrongPsw").innerHTML = "Incorrect input; use only numbers and letters.";
    }
    else if (show == "pswAccomplished") {
        pop("psw-b");
        document.getElementById('pswAccomplished').style.display = 'block';
    }
    else if (show == "delWrongPassword") {
        pop("del");
        clinnation("Del");
        document.getElementById('delWrongPsw').style.display = 'block';
    }
    else if (show == "resent") {
        clinnation("Conf");
        createPopup("d:acc;txt:A new confirmation mail was sent");
    }
    else if (show == "parSub") {
        document.getElementById('backPatD').style.display = 'block';
        document.getElementById('conPatD').style.display = 'block';
    }
    else if (show == "notConfirmed") {
        clinnation("Conf");
    }
    else if (show == "discDuplicate") {
        clinnation("Disc");
        document.getElementById("discDuplicateErr").style.display = "block";
    }
    else if (show == "discWrong") {
        clinnation("Disc");
        document.getElementById("discInputErr").style.display = "block";
        document.getElementById("discSubmit").style.backgroundColor = "#f46e6e";
    }
    else if (show == "discSucc") {
        clinnation("Disc");
    }

    fillMe();
function fillMe() {
        if (getCookie("spellLists") != "set") { a = getCookie("spellLists"); purify(a, "a"); }
        if (document.cookie.indexOf('spellb') != -1) { b = getCookie("spellb"); purify(b, "b"); }
        if (document.cookie.indexOf('spellc') != -1) { c = getCookie("spellc"); purify(c, "c"); }
        if (document.cookie.indexOf('spelld') != -1) { d = getCookie("spelld"); purify(d, "d"); }
        if (document.cookie.indexOf('spelle') != -1) { e = getCookie("spelle"); purify(e, "e"); }
}
function purify(x, y) {
    let SLtable = document.getElementById("SLoptions");
    x = x.replace(/\+/g, " ");
    x = x.split(",");
    let row = SLtable.insertRow();
    let cell1 = row.insertCell(0);
    cell1.innerHTML = y;
    let cell2 = row.insertCell(1);
    cell2.innerHTML = "<a href='/spells/SavedList.html?sl=".concat(y).concat("'>").concat(x[0]).concat("</a>");
}

function getCookie(name) {
    var cookieArr = document.cookie.split(";");
    for (var i = 0; i < cookieArr.length; i++) {
        var cookiePair = cookieArr[i].split("=");
        if (name == cookiePair[0].trim()) {
            return decodeURIComponent(cookiePair[1]);
        }
    }
    return null;
}


</script>
