<?php
if (isset($_GET["id"])) {
    if (preg_match("/[^0-9]/", $_GET['id'])==1){header("Location:/home");exit();}
    $page = $_GET["id"];
    $writingNew = false;
}
else {
    $page = 1;
    $writingNew = true;
}
$domain = "docs";
if (isset($_GET["domain"])) {
    if (preg_match("/[^0-9a-zA-Z]/", $_GET['domain'])==1){header("Location:/home");exit();}
    $domain = $_GET["domain"];
}
if ($domain == "docs"){$parentWiki = 2;}else {$parentWiki = 1;}

require_once($_SERVER['DOCUMENT_ROOT']."/wiki/pageGen.php");
$gen = new gen("edit", $page, $parentWiki, $writingNew, $domain);
$conn = $gen->conn;

?>

<!DOCTYPE html>
<html>
<head>
    <META HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE">
    <META HTTP-EQUIV="EXPIRES" CONTENT="Mon, 22 Jul 2002 11:12:01 GMT">
    <meta charset="UTF-8" />
    <?php echo $gen->giveFavicon(); ?>
    <title><?php if ($writingNew) { echo "Write new ".ucwords($gen->pagename); } else { echo "Edit ".$gen->article->shortName." ".ucwords($gen->pagename); }?> | <?php echo $gen->domainName;  ?></title>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <style>
        .content {
            background-color: unset;
        }
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
    </style>
</head>
<body>
    <div class="content">
        <div class="fandomcoll">
            <?php
                echo $gen->giveEditInfo();
            ?>
        </div>
        <div class="fandomrcoll">
            <?php echo $gen->giveREdit(0); ?>
        </div>

</body>
</html>

<?php
    echo $gen->giveScripts();
    echo $gen->giveEditScript();
?>

