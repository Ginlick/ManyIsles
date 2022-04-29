<?php

require_once("g/dicEngine.php");
$dic = new dicEngine();

$randId = 1;
$query = "SELECT id FROM words ORDER BY RAND() LIMIT 1";
if ($result = $dic->dicconn->query($query)) {
    while ($row = $result->fetch_assoc()) {
      $randId = $row["id"];
  }
}
?>
<!DOCTYPE html>
<html>
<head>
  <title> Home | Dictionary</title>
    <?php echo $dic->giveStyles(); ?>
</head>
<style>

</style>
<body>
    <?php echo $dic->giveTopnav(); ?>
    <div class="flex-container">
        <div class='left-col'>
            <?php echo $dic->giveLeftcol(); ?>
        </div>

        <div class='column'>
          <div class="columnCont">
            <?php echo $dic->giveSignPrompt(); ?>
            <h1>Explore Dictionary</h1>
            <?php echo $dic->giveFindWords();
              echo $dic->giveWordLink($randId, "I'm feeling lucky");
             ?>
          </div>
        </div>
    </div>
    <?php echo $dic->giveFooter(); ?>

</div>



</body>
</html>
<?php echo $dic->giveScripts(); ?>
<script>
var urlParams = new URLSearchParams(window.location.search);
var why = urlParams.get('i');
if (why == "error"){
  createPopup("d:poet;txt:Error.");
}
else if (why == "notfound"){
  createPopup("d:poet;txt:Error. Page could not be found.");
}
else if (why == "req"){
  createPopup("d:poet;txt:Request sent.");
}


</script>
