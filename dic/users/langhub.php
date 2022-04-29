<?php
require_once($_SERVER['DOCUMENT_ROOT']."/dic/g/dicEngine.php");
$dic = new dicEngine();
$dic->checkCredentials(true);
//
// if (isset($_GET["w"])) {
//     if (preg_match("/^[0-9]+$/", $_GET['w'])!==1){header("Location:/fandom/home");exit();}
//     $parentWiki = $_GET["w"];
// }
// else {header("Location:/fandom/home");exit();}
//
// $domain = "fandom";
// if (isset($_GET["domain"])) {
//     $domain = $_GET["domain"];
// }
$gen = $dic->wiki;
$dicconn = $dic->dicconn;
$conn = $dic->conn;
$parentWiki = $dic->language;

if (!isset($dic->allLangs[$parentWiki])){
  $dic->go("home?i=notfound");
}

$query="SELECT * FROM languages WHERE id = '$parentWiki'";
$result =  $dicconn->query($query);
$banners = $gen->banners;
$defaultBanner = $gen->defaultBanner;

/*    $dateB = $gen->defaultdateB;
    $dateA = $gen->defaultdateA;

    $defaultBanner = $gen->defaultBanner;
    $backgroundImg = $gen->defaultbackgroundImg;
    $backgroundColor = $gen->defaultbackgroundColor;*/
if (mysqli_num_rows($result) == 0){
    $auths = $gen->defaultauths;
    $mods = $gen->defaultmods;
    $banned = $gen->defaultbanned;
}
else {
  while ($row = $result->fetch_assoc()){
      $auths = $row["auths"];
      $mods = $row["mods"];
      $banned = $row["banned"];
  }
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
            echo '<td><a href="promote.php?dir=0&d='.$d.'&dicd='.$parentWiki.'&who='.$author.'">Remove</a></td>';
        }
        echo "</tr>";
    }
    echo '                </tbody>
            </table>';
}
function createPageTable() {
  global $parentWiki, $conn;
  echo '
          <table class="credTable prods" style="width: 90%;margin:auto">
              <thead><tr><td>User</td><td>Email</td><td>Discord Tag</td><td></td><td></td></tr></thead>
              <tbody>
  ';
  $query = "SELECT * FROM requests WHERE domain = 'wd$parentWiki'";
  $result = $conn->query($query);
  if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
      if ($row["request"]!="auth"){continue;}
      $uid = $row["requestee"];
      $user2 = new adventurer($conn, $uid);
      echo "<tr>";
      echo "<td>".$user2->uname."</td>";
      echo "<td><a href='mailto:$user2->email' target='_blank'>$user2->email</a></td>";
      echo "<td>$user2->discname</td>";
      echo "<td><a href='/dic/users/promote.php?dir=1&d=2&dicd=$parentWiki&who=".$uid."'>Curate</a></td>";
      echo "<td><a href='/dic/users/rcur.php?w=undo&lang=$parentWiki&who=".$uid."'>Clear</a></td>";
      echo "</tr>";
    }
  }
  echo "</tbody></table>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <?php echo $gen->giveFavicon(); ?>
    <title><?php echo $gen->wikiName; ?> Settings | Dictionary</title>
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
<body>
    <?php
        echo $gen->giveTopBar();
    ?>
    <div class="content">
        <div class="fandomcoll">
          <div class="col-l">
              <h2><?php echo $dic->curPage; ?> Dictionary</h2>
              <p>Dictionary <a href="/docs/20/Fandom" target="_blank">documentation</a></p>
              <div class="bottButtCon">
                  <a href="/dic/home"><button id="submitButton" class="wikiButton" >Home</button></a>
              </div>
          </div>
        </div>

        <div class="fandomrcoll">
        <div class="col-r">
            <img src="<?php echo banner($defaultBanner); ?>" alt="oops" class="topBanner" />

            <h1><?php echo $dic->curPage; ?> <?php echo ucwords($gen->groupName); ?> Setup</h1>
            <div class="bottButtCon">
                <button class="wikiButton" onclick="window.location.assign('<?php echo "wsettings.php?w=$parentWiki"; ?>')">
                    <i class="fas fa-redo"></i>
                    <span>Reload</span>
                </button>
            </div>
          </div>
          <?php
              echo '
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
                  createPageTable();
                  echo '
              </div>
              ';
          ?>


          <?php
            if($gen->domain == "mystral"){
              echo '
              <div class="col-r">
              <h1>Delete '.ucwords($gen->groupName).'</h1>
                <p>Instantly delete this '.$gen->groupName.', including all its children.<br>This action cannot be undone.</p>
                <form action="/fandom/delWiki.php" method="POST">
                  <input type="text" name="WIKI" style="display:none" value="'.$gen->parentWiki.'" />
                  <input type="password" name="psw" placeholder="currentPassword22" style="margin: 20px auto"/>
                  <div class="bottButtCon">
                      <button class="wikiButton"><i class="fas fa-trash"></i> Delete</button>
                  </div>
                </form>
              </div>';
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
if (why == "failed"){
    createPopup("d:poet;txt:Changes could not be effectuated.");
}
else if (why == "userup"){
    createPopup("d:poet;txt:User information updated.");
}
else if (why == "bigup"){
    createPopup("d:poet;txt:<?php echo ucwords($gen->groupName); ?> settings updated.");
}
else if (why == "reqDel"){
    createPopup("d:poet;txt:Curation request cleared.");
}
else if (why == "catdel"){
    createPopup("d:poet;txt:Category deleted");
}
else if (why == "badpsw"){
    createPopup("d:poet;txt:Incorrect password");
}

</script>
