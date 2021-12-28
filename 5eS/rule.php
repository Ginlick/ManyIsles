<?php
if (isset($_GET["id"])) {
    if (preg_match("/[^0-9]/", $_GET['id'])==1){header("Location:/5eS/1/home");exit();}
    $page = $_GET["id"];
    if ($page == 0){header("Location:/5eS/1/home");exit();}
}
require_once($_SERVER['DOCUMENT_ROOT']."/wiki/pageGen.php");
$gen = new gen("view", $page, 1, false, "5eS");
$conn = $gen->conn;

if ($gen->article->name == ""){
    header("Location:/5eS/1/home"); exit();
}
$gen->prepareParse();

?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <?php echo $gen->giveFavicon(); ?>
    <title><?php echo $gen->article->shortName; ?> | <?php echo $gen->domainName; ?></title>
</head>
<body>
        <?php
            echo $gen->giveTopBar();
        ?>
    <div class="content">
        <div class="coll">
            <?php
                echo $gen->giveDocSide();
            ?>
        </div>
        <div class="colr">
            <?php
                echo $gen->giveRDocArticle();
            ?>
        </div>
    </div>
    <div class="footer">

        <p class="centerer">            <span>Powered by </span><span class="logoWiki">Many Isles Wiki</span><br>
© Many Isles 2021</p>
    </div>
</body>
</html>
<?php
    echo $gen->giveScripts();
    echo $gen->giveDocScript();
?>

