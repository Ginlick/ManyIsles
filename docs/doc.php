<?php
if (isset($_GET["id"])) {
    if (preg_match("/[^0-9]/", $_GET['id'])==1){header("Location:/docs/1/");exit();}
    $page = $_GET["id"];
    if ($page == 0){header("Location:/docs/1/");exit();}
}
require_once($_SERVER['DOCUMENT_ROOT']."/wiki/pageGen.php");
$gen = new gen("view", $page, 2, false, "docs");
$conn = $gen->conn;

if ($gen->article->name == ""){
    header("Location:/docs/2/Many_Isles"); exit();
}
$gen->prepareParse();

?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <?php echo $gen->giveFavicon(); ?>
    <title><?php echo $gen->article->shortName; ?> | <?php echo $gen->domainName; ?></title>
    <style>
.head1 h3 {
color: #2525b0;
}

.head2 h3 {
color: #b61c1c;
}

.head3 h3 {
color: #c7ad1a;
}
    </style>
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

