<?php

if (isset($_GET["id"])) {
    if (preg_match("/[^0-9]/", $_GET['id'])==1){header("Location:/mystral/hub");exit();}
    $page = $_GET["id"];
    if ($page == 0){header("Location:/mystral/hub");exit();}
}
else {$page = 1;}
if ($page == ""){$page = 1;}

require_once($_SERVER['DOCUMENT_ROOT']."/wiki/pageGen.php");
$gen = new gen("view", $page, 1, false, "mystral");

if ($gen->user == 0){echo "<script>window.location.replace('/mystral/hub');</script>";exit();}
else if ($gen->article->name == ""){
    echo "<script>window.location.replace('/mystral/hub?i=nexist&u$gen->user');</script>";exit();
}

$gen->doFandWork();


?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <?php echo $gen->giveFavicon(); ?>
    <title><?php echo $gen->article->name." | ".$gen->domainName; ?></title>
</head>
<style>
.fandomcoll, .fandomrcoll {
    top: 60px;
}
</style>
<body style="<?php echo $gen->giveWikStyle(); ?>">
    <?php
        echo $gen->giveTopBar();
    ?>
    <div class="content">
        <div class="fandomcoll">
            <?php
                echo $gen->homeL();
                echo $gen->giveLParwikinfo();
                echo $gen->giveLChildren();
                echo $gen->giveLOutstanders();
            ?>
        </div>

        <div class="fandomrcoll">

            <?php
                echo $gen->giveRArticle(["root", "categLine", "titleBlock", "body"]);
                echo $gen->giveRMAdmin();
            ?>
        </div>
    </div>

    <?php echo $gen->giveRArticPops(); echo $gen->giveFooter(); ?>

</body>
</html>

<?php
    echo $gen->giveScripts();
    echo $gen->giveArtScript();
?>
