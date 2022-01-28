<?php
if (str_contains(basename($_SERVER["REQUEST_URI"]), "f.php?") AND !str_contains($_SERVER["REQUEST_URI"], "/fandom/f.php")) {
    header("Location:/fandom/". basename($_SERVER["REQUEST_URI"]));
}
if (isset($_GET["id"])) {
    if (preg_match("/[^0-9]/", $_GET['id'])==1){header("Location:/fandom/home");exit();}
    $page = $_GET["id"];
    if ($page == 0){header("Location:/fandom/home");exit();}
}
else {$page = 1;}
if ($page == ""){$page = 1;}

require_once($_SERVER['DOCUMENT_ROOT']."/wiki/pageGen.php");
$gen = new gen("view", $page);
if ($gen->article->name == ""){
    header("Location:/fandom/home?i=nexist");exit();
}

$conn = $gen->conn;

$gen->doFandWork();


?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <?php echo $gen->giveFavicon(); ?>
    <title><?php echo $gen->article->name; ?> | Fandom</title>
</head>
<body style="<?php echo $gen->giveWikStyle(); ?>">
    <div class="content">
        <div class="fandomcoll">
            <?php
                echo $gen->giveLWikinfo();
                echo $gen->giveLParwikinfo();
                echo $gen->giveLAuthors(true);
                echo $gen->giveLChildren();
                echo $gen->giveLOutstanders();
                echo $gen->giveLShare();
            ?>
        </div>

        <div class="fandomrcoll">

            <?php
                echo $gen->giveRArticle(["root", "categLine", "titleBlock", "body"]);
                echo $gen->giveRAdmin();
                echo $gen->giveRWiki();
            ?>
        </div>
    </div>

    <?php echo $gen->giveRArticPops(); echo $gen->giveFooter(); ?>
    </div>
</body>
</html>

<?php
    echo $gen->giveScripts();
    echo $gen->giveArtScript();
?>
