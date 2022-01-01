<?php

$page = 0;
$parentWiki = 0;
if (isset($_GET["id"])) {
    if (preg_match("/[^0-9]/", $_GET['id'])==1){header("Location:/fandom/home");exit();}
    $page = $_GET["id"];
    $writingNew = false;
}
else {
    if (isset($_GET["w"])) {
        if (preg_match("/[^0-9]/", $_GET['w'])==1){header("Location:/fandom/home");exit();}
        $parentWiki = $_GET["w"];
    }
    $writingNew = true;
}
if (isset($_GET["new"])) {
    $writingNew = true;
}
if (isset($_GET["domain"])) {
    if (preg_match("/[^0-9a-zA-Z ]/", $_GET['domain'])==1){header("Location:/fandom/home");exit();}
    $domain = $_GET["domain"];
}
else {
    $domain = 0;
}

require_once($_SERVER['DOCUMENT_ROOT']."/wiki/pageGen.php");
$gen = new gen("edit", $page, $parentWiki, $writingNew, $domain);
$conn = $gen->conn;
if (!$gen->writingNew AND $gen->article->name == ""){ echo "<script>window.location.replace('".$gen->article->page."/".parse2Url($gen->article->shortName)."');</script>";}


?>

<!DOCTYPE html>
<html>
<head>
    <META HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE">
    <META HTTP-EQUIV="EXPIRES" CONTENT="Mon, 22 Jul 2002 11:12:01 GMT">
    <meta charset="UTF-8" />
    <?php echo $gen->giveFavicon(); ?>
    <title><?php  if ($writingNew) { echo "Write new Article"; } else { echo "Edit ".$gen->article->shortName." Article"; } ?> | Fandom</title>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <style>
        .topBanner {
            margin:0;
            width: calc(100% + 16px);
            transform: translate(-8px, -8px);
        }
        .selectCont {
            width:45%;padding: 9px 20px 9px 0;float:left;
        }
        h4 {
            padding-top: 20px;
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

<body style="<?php   echo $gen->giveWikStyle(); ?>">
    <?php
        echo $gen->giveTopBar();
    ?>
    <div class="content">
        <div class="fandomcoll">

               <?php
                    echo $gen->giveEditInfo();

                    if (!$writingNew) {
                        echo $gen->giveLAuthors();
                    }

                    echo $gen->giveCategs();
                    echo $gen->giveOutstans();

                ?>

        </div>
        <div class="fandomrcoll">
            <?php
                echo $gen->giveREdit();
            ?>
        </div>

    <?php echo $gen->giveFooter(); ?>

</body>
</html>
<?php
    echo $gen->giveScripts();
    echo $gen->giveEditScript();
?>
