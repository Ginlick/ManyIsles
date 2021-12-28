<?php
if (isset($_GET["w"])) {
    if (preg_match("/^[0-9]+$/", $_GET['w'])!==1){header("Location:/fandom/home");exit();}
    $parentWiki = $_GET["w"];
}
else {header("Location:/fandom/home");exit();}

$domain = "fandom";
if (isset($_GET["domain"])) {
    $domain = $_GET["domain"];
}

require_once($_SERVER['DOCUMENT_ROOT']."/wiki/pageGen.php");
$gen = new gen("edit", 0, $parentWiki, false, $domain);

if ($gen->power < 3){header("Location:$gen->artRootLink/$gen->parentWiki/home");exit();}
$conn = $gen->dbconn;


$query="SELECT * FROM wiki_settings WHERE id = '$gen->WSet'";
$result =  $conn->query($query);
$banners = $gen->banners;
if ($result->num_rows == 0){
    $dateB = $gen->defaultdateB;
    $dateA = $gen->defaultdateA;
    $defaultBanner = $gen->defaultBanner;
    $backgroundImg = $gen->defaultbackgroundImg;
    $backgroundColor = $gen->defaultbackgroundColor;
    $auths = $gen->defaultauths;
    $mods = $gen->defaultmods;
    $banned = $gen->defaultbanned;
}
else {
    while ($row = $result->fetch_assoc()){
        $dateNames = $row["dateName"];
        $defaultBanner = $row["defaultBanner"];
        $backgroundImg = $row["backgroundImg"];
        $backgroundColor = $row["backgroundColor"];
        $auths = $row["auths"];
        $mods = $row["mods"];
        $banned = $row["banned"];
        if ($row["banners"]!= null){
            $banners = json_decode($row["banners"], true);
        }
    }
    $dateNames = json_decode($dateNames, true);
    if (isset($dateNames['B'])) {$dateB = $dateNames['B'];} else {$dateB = "";}
    if (isset($dateNames['A'])) {$dateA = $dateNames['A'];} else {$dateA = "";}

    if ($defaultBanner == ""){$defaultBanner = "fandom.png";}
}

function createUserTable($auths, $d) {
    global $conn, $parentWiki;
    echo '
            <table class="credTable prods" style="width: 90%;margin:auto">
                <thead><tr><td>User</td><td>Edits</td><td></td></tr></thead>
                <tbody>
    ';

    $auths = explode(",", $auths);

    foreach ($auths as $author) {
        echo "<tr>";
        $query = "SELECT title, uname FROM accountsTable WHERE id = $author";
        if ($result = $conn->query($query)){
            while ($row = $result->fetch_assoc()){
                echo "<td>".$row["title"]." ".$row["uname"]." (u#".$author.")</td>";
            }
        }
        $query = "SELECT edits FROM poets WHERE id = $author";
        if ($result = $conn->query($query)){
            while ($row = $result->fetch_assoc()){
                echo "<td>".$row["edits"]."</td>";
            }
            echo '<td><a href="promote.php?dir=0&d='.$d.'&id='.$parentWiki.'&who='.$author.'">Remove</a></td>';
        }
        echo "</tr>";
    }
    echo '                </tbody>
            </table>';
}
function createPageTable($type) {
    global $conn, $parentWiki;
    if ($type == "reported"){
        echo '
                <table class="credTable prods" style="width: 90%;margin:auto">
                    <thead><tr><td>Reporter</td><td></td><td></td></tr></thead>
                    <tbody>
        ';
        $query = "SELECT * FROM reported";
        $result = $conn->query($query);
        if ($result->num_rows > 0) {
          while($row = $result->fetch_assoc()) {
            if (getWiki($row["id"]) != $parentWiki){continue;}
            echo "<tr>";
            echo "<td>".$row["uid"]."</td>";
            echo "<td><a href='f.php?id=".$row["id"]."' target='_blank'>View</a></td>";
            echo "<td><a href='report.php?w=undo&id=".$row["id"]."'>Clear</a></td>";
            echo "</tr>";
          }
        }
    }
    else if ($type == "reqcur"){
        echo '
                <table class="credTable prods" style="width: 90%;margin:auto">
                    <thead><tr><td>User</td><td>Email</td><td>Discord Tag</td><td></td><td></td></tr></thead>
                    <tbody>
        ';
        $query = "SELECT * FROM requests WHERE domain = 'wf$parentWiki'";
        $result = $conn->query($query);
        if ($result->num_rows > 0) {
          while($row = $result->fetch_assoc()) {
            if ($row["request"]!="auth"){continue;}
            $requestee = "Error - Failed to load";
            $email = "";
            $discname = "";
            $query = "SELECT title, uname, email, discname FROM accountsTable WHERE id = ".$row["requestee"];
            if ($result = $conn->query($query)){
                while($row2 = $result->fetch_assoc()) {
                    $requestee = $row2["title"]." ".$row2["uname"]." (u#".$row["requestee"].")";
                    $email = $row2["email"];
                    $discname = $row2["discname"];
                }
            }
            echo "<tr>";
            echo "<td>".$requestee."</td>";
            echo "<td><a href='mailto:$email' target='_blank'>$email</a></td>";
            echo "<td>$discname</td>";
            echo "<td><a href='/fandom/promote.php?dir=1&d=2&id=$parentWiki&who=".$row["requestee"]."'>Curate</a></td>";
            echo "<td><a href='/fandom/rcur.php?w=undo&wiki=$parentWiki&who=".$row["id"]."'>Clear</a></td>";
            echo "</tr>";
          }
        }
    }
    else {
        echo '
                <table class="credTable prods" style="width: 90%;margin:auto">
                    <thead><tr><td>Page</td><td>Id</td><td></td><td></td></tr></thead>
                    <tbody>
        ';
        $query = "SELECT a.*
        FROM pages a
        LEFT OUTER JOIN pages b
            ON a.id = b.id AND a.v < b.v
        WHERE a.status = 'suspended' AND b.id IS NULL LIMIT 222";
        $result =  $conn->query($query);
        $cunter = 0;
        while ($row = $result->fetch_assoc()){
            if (getWiki($row["root"]) != $parentWiki){continue;}
            else if ($cunter == 22){break;}
            $cunter++;
            echo "<tr>";
            echo "<td>".$row["name"]."</td>";
            echo "<td>".$row["id"]."</td>";
            echo "<td><a href='/fandom/f.php?id=".$row["id"]."&clear=true'>View</a></td>";
            echo "<td><a href='/fandom/suspend.php?id=".$row["id"]."&w=1'>Restore</a></td>";
            echo "</tr>";
        }
    }
        echo '                </tbody>
                </table>';
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <?php echo $gen->giveFavicon(); ?>
    <title><?php echo $gen->wikiName; ?> Settings | Fandom</title>
    <style>
    table {
        width:80%;
        margin:auto;
        text-align:left;
    }
    <?php
        if ($gen->acceptsTopBar){
            echo ".fandomcoll, .fandomrcoll {
    top: 60px;
        }";
        }
    ?>
    </style>
</head>
<body style="<?php echo $gen->giveWikStyle(); ?>">
    <?php
        echo $gen->giveTopBar();
    ?>
    <div class="content">
        <div class="fandomcoll">
                <?php
                    echo $gen->giveWsetHomer();
                ?>
        </div>

        <div class="fandomrcoll">
        <div class="col-r">
            <img src="<?php echo banner($defaultBanner); ?>" alt="oops" class="topBanner" />
            <p class="topinfo"><?php echo '<a href="'.$gen->homelink.'">'.$gen->domainName.'</a> - <a href="'.$gen->artRootLink.$parentWiki."/home"; ?>"><?php echo $gen->wikiName; ?></a> - <a href="#">Settings</a></p>

            <h1><?php echo ucwords($gen->groupName); ?> Setup</h1>
                <div class="bottButtCon">
                    <button class="wikiButton" onclick="window.location.assign('<?php echo "wsettings.php?w=$parentWiki"; ?>')">
                        <i class="fas fa-redo"></i>
                        <span>Reload</span>
                    </button>
                </div>
            <h2><?php echo ucwords($gen->groupName); ?> Settings</h2>
            <form action="/fandom/ediWiki.php" method="POST" class="pageForm">
                <?php
                    if ($gen->domain == "mystral") {
                        echo '
                            <h3>'.ucwords($gen->groupName).' Style</h3>
                            <select id="style" name="style">
                                <option value="current">current</option>
                            ';
                            foreach ($gen->styles as $sources){
                                $selected = "";
                                echo "<option value='".$sources."'>".$sources."</option>";
                            }
                        echo '
                            </select>
                        ';
                    }
                ?>
                <h3>Background</h3>
                <input type="color" onchange="this.nextElementSibling.value = this.value;" value="<?php if (preg_match("/#[0-9a-f]{6}/", $backgroundColor)){ echo $backgroundColor; } ?>">
                <input type="text" name="backgroundCol" placeholder="Color (css code)" value="<?php echo $backgroundColor; ?>"></input>
                <input type="text" name="backgroundImg" placeholder="Image (direct link, leave blank for none)" value="<?php echo $backgroundImg; ?>"></input>
                <h3>Date Format<span class="roundInfo green">Optional</span></h3>
                <input type="text" name="dateB" placeholder="Date Before 0" value="<?php echo $dateB; ?>"></input>
                <input type="text" name="dateA" placeholder="Date After 0" value="<?php echo $dateA; ?>"></input>
                <h3>Default Banner</h3>
                <div class="selectCont">
                    <label for="banner">Choose a banner:</label>
                    <select id="banner" name="banner">
                        <option value="current">current</option>
                        <option value="default">default</option>
                    <?php
                        foreach ($banners as $banner){
                            echo "<option value='".$banner["src"]."'>".$banner["name"]."</option>";
                        }
                    ?>
                    </select>
                </div>
                <input type="number" name="wId" value="<?php echo $parentWiki; ?>" style="display:none;opacity:0;visibility:hidden;"></input>
                <input type="number" name="dom" value="<?php echo $gen->domainnum; ?>" style="display:none;opacity:0;visibility:hidden;"></input>
                <div class="bottButtCon" style="padding-top: 60px">
                    <button id="submitButton" class="wikiButton" type="submit" onclick="setFormSubmitting()">Submit</button>
                </div>
            </form>
            <h2>Customize Banners</h2>
            <p>List the banners available for articles. Only direct links to hosted images allowed (except for the standard banners).</p>
            <form action="/fandom/newBanns.php" method="POST" class="pageForm" id="bannerForm">
                <textarea name="bannList" id="bannList" rows="22" required><?php
                        foreach ($banners as $banner){
                            echo $banner["name"].", ".$banner["src"]."\n";
                        }
                    ?></textarea>
                <input type="number" name="wId" value="<?php echo $parentWiki; ?>" style="display:none;opacity:0;visibility:hidden;"></input>
                <input type="number" name="dom" value="<?php echo $gen->domainnum; ?>" style="display:none;opacity:0;visibility:hidden;"></input>
                <div class="bottButtCon" style="padding: 60px 0">
                    <button id="submitButton" class="wikiButton" type="button" onclick="banner2JSON()">Submit</button>
                </div>
            </form>
            <?php
                if ($gen->changeableGenre){
                    echo '
                        <h2>Customize Genres</h2>
                        <p>Customize the dropdown list of genres. Note this will not update any existing genres, even if you delete an option.</p>
                        <form action="/mystral/newGenres.php" method="POST" class="pageForm" id="genreForm">
                            <textarea name="genreList" id="genreList" rows="22" required>';

                                    foreach ($gen->cateoptions as $genre){
                                        echo $genre["name"].", ".$genre["value"]."\n";
                                    }
                           echo '</textarea>
                            <input type="number" name="wId" value="'.$parentWiki.'" style="display:none;opacity:0;visibility:hidden;"></input>
                            <input type="number" name="dom" value="'.$gen->domainnum.'" style="display:none;opacity:0;visibility:hidden;"></input>
                            <div class="bottButtCon" style="padding: 60px 0">
                                <button id="submitButton" class="wikiButton" type="button" onclick="genre2JSON()">Submit</button>
                            </div>
                        </form>
                    ';
                }
            ?>
        </div>

        <?php
        if ($gen->domain == "fandom"){
            echo '<div class="col-r">
            <h1>Pages</h1>
                <h2>Suspended Pages</h2>
                <p>These cannot currently be visited.</p> ';

                createPageTable("suspended");

                echo '<h2>Reported Pages</h2>
                <p>Users have reported these articles. Check them, take action if necessary (eg. banning the author from the authors side-column), and clear the article.</p>';

                createPageTable("reported");
            echo '
            </div>
            <div class="col-r">
            <h1>Personnel</h1>
                <h2>Users</h2>
                <p>Please be aware that any promoted user - including a curated author - has a lot of power over this wiki, and may be able to do some serious damage.<br>Promote users via the authors panel on an article.</p>
                <h4>Banned Users</h4>';

                createUserTable($banned, 0);
                echo '
                            <h4>Curated Authors</h4>
                ';
                createUserTable($auths, 2);
                echo '
                            <h4>Moderators</h4>';
                createUserTable($mods, 3);
                echo '
                <h2>Requests</h2>
                <h4>Curation Requests</h4>
                <p>Please be careful and respectful with this personal information. Talk with the user to make sure their intentions are good, and promote them if you trust them.<br>Clear the request once it is processed.</p>
                ';
                createPageTable("reqcur");
                echo '
            </div>
            ';
        }
        ?>
        </div>
    </div>


    <?php echo $gen->giveFooter(); ?>


</body>
</html>
<?php echo $gen->giveScripts(); ?>
<script>
var urlParams = new URLSearchParams(window.location.search);
var why = urlParams.get('i');
if (why == "fail"){
    createPopup("d:poet;txt:Changes could not be effectuated.");
}
else if (why == "userup"){
    createPopup("d:poet;txt:User information updated.");
}
else if (why == "bigup"){
    createPopup("d:poet;txt:<?php echo ucwords($gen->groupName); ?> settings updated.");
}
else if (why == "reqCurDel"){
    createPopup("d:poet;txt:Curation request cleared.");
}

function banner2JSON() {
    var value = document.getElementById("bannList").value;
    var fullList = [];
    var messList = value.split("\n");
    for (let mess of messList) {
        let names = mess.substr(0, mess.indexOf(","));
        let src = mess.substr(mess.indexOf(", ")+2);
        if (src != "") {
            fullJSON = {"src":src, "name":names};
            fullList.push(fullJSON);
        }
    }
    document.getElementById("bannList").value = JSON.stringify(fullList);
    document.getElementById("bannerForm").submit();
}
function genre2JSON() {
    var value = document.getElementById("genreList").value;
    var fullList = [];
    var messList = value.split("\n");
    for (let mess of messList) {
        let names = mess.substr(0, mess.indexOf(","));
        let src = mess.substr(mess.indexOf(", ")+2);
        names = names.replace(/[^A-Za-z/\- ]/g, "");
        src = src.replace(/[^A-Za-z/\- ]/g, "");
        if (src != "") {
            fullJSON = {"value":src, "name":names};
            fullList.push(fullJSON);
        }
    }
    document.getElementById("genreList").value = JSON.stringify(fullList);
    document.getElementById("genreForm").submit();
}
</script>

