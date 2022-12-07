<?php
require_once("g/eventEngine.php");
$eventE = new eventEngine;

?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Event</title>
    <?php
      echo $eventE->giveHead();
     ?>
    <style>
    </style>
</head>
<body>
  <div w3-include-html="/Code/CSS/GTopnav.html" w3-create-newEl = "true"></div>
  <section class="cont-cont">
    <section class="contcol-wrapper eContCol">
      <?php
        echo $eventE->giveLeft();
      ?>
      <section class="column-main">
        <h1>Create Event</h1>
        <p>later: Series here (button to select parent series where helper/owner, none, or "create new")</p>
        <div>
          <form>
            <input type="text" placeholder="Event Name" />
            <input type="text" placeholder="Date and Time" />
            <input type="text" placeholder="Location" />
            <input type="text" placeholder="Description" />
            <p>settings (repeats weekly, participant cap)</p>
          </form>
        </div>
      </section>
    </section>
  </section>
  <div w3-include-html="/Code/CSS/genericFooter.html" w3-create-newEl="true"></div>
</body>
</html>
<script src="/Code/CSS/global.js"></script>
<script>
var urlParams = new URLSearchParams(window.location.search);
var why = urlParams.get('why');
if (why =="itemDeleted"){
}


</script>
