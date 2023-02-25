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
    $parentWiki = 0;
}

require_once($_SERVER['DOCUMENT_ROOT']."/wiki/pageGen.php");
$gen = new gen("edit", $page, $parentWiki, $writingNew, "fandom");
$conn = $gen->conn;
if (!$gen->writingNew AND $gen->article->name == ""){ $gen->redirect($gen->artRootLink.$gen->article->page."/".parse2Url($gen->article->shortName));}

if ($gen->article->root == 0) {$gen->redirect($gen->artRootLink.$gen->article->page."/".parse2Url($gen->article->shortName));}
$gen->pagename = "Source";

?>

<!DOCTYPE html>
<html>
<head>
    <META HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE">
    <META HTTP-EQUIV="EXPIRES" CONTENT="Mon, 22 Jul 2002 11:12:01 GMT">
    <meta charset="UTF-8" />
    <?php echo $gen->giveFavicon(); ?>
    <title><?php  if ($writingNew) { echo "Write new $gen->pagename"; } else { echo "Edit ".$gen->article->shortName." ".$gen->pagename; } ?> | Fandom</title>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <style>
    </style>
</head>

<body style="<?php   echo $gen->giveWikStyle(); ?>">
    <?php
        echo $gen->giveTopBar();
    ?>
    <div class="content">
        <div class="fandomcoll">

               <?php
               echo $gen->giveEditSrcInfo();
               if ($gen->domainType == "fandom"){
                 if (!$writingNew) {
                     echo $gen->giveLAuthors();
                 }
               }
                ?>

        </div>
        <div class="fandomrcoll">
            <?php
                echo $gen->giveREdit(2, "source");
            ?>
        </div>

    <?php echo $gen->giveFooter(); ?>

</body>
</html>
<?php
    echo $gen->giveScripts();
    echo $gen->giveEditScript();
?>
<script src="/Server-Side/src/fileportal/fpi-builder.js"></script>
<script>
var allSelectable = document.getElementsByClassName("selectable");
for (let selectable of allSelectable){
    if (!selectable.hasAttribute("shown")){
        selectable.style.display = "none";
    }
}

function changeSelectable(selectin){
    let name = selectin.value;
    console.log(name);
    for (let selectable of allSelectable){
        console.log(selectable.id);
        selectable.style.display = "none";
        if (selectable.id == name){
            selectable.style.display = "block";
        }
    }
}
changeSelectable(document.getElementById("srcSelector"));
var fpi = new fpi_builder(250);
var srcInput = document.getElementById("fpi-srcuploader");
fpi.createPortal(srcInput, "broad", 1);

</script>
