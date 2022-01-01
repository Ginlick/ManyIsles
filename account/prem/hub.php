<?php
require_once($_SERVER['DOCUMENT_ROOT']."/dl/global/engine.php");
require_once($_SERVER['DOCUMENT_ROOT']."/ds/g/makeHuman.php");
require_once("partAmount.php");
$dl = new dlengine();
$dl->partner();
if ($dl->ppower == 0 OR $dl->partStat != "active") {$dl->go("Publish?i=405", "p");}

?>
<!DOCTYPE HTML>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="/ds/g/ds-item.css">
    <?php echo $dl->styles("p"); ?>
    <title>Premium Hub | Partnership</title>
<style>
.procol {
text-align:center;
display:block;
padding:0;
}
</style>
</head>
<body>
  <?php
      echo $dl->giveGlobs();
  ?>

  <div class="flex-container">
      <div class='left-col'>
          <h1 class="menutitle">Partnership</h1>
          <ul class="myMenu">
              <li><a class="Bar" href="/account/Publish"><i class="fas fa-arrow-left"></i> Main Page</a></li>
          </ul>
          <img src="/Imgs/Bar2.png" alt="GreyBar" class='separator'>
          <ul class="myMenu bottomFAQ">
            <li><a class="Bar" href="/docs/4/Partnerships" target="_blank">Partnership Program</a></li>
            <li><a class="Bar" href="/docs/63/Premium_Extension" target="_blank">Premium Extension</a></li>
          </ul>
      </div>

      <div class='column'>
        <?php
        echo $dl->giveAccTab();
         ?>
      <h1>Premium Partnership Hub</h1>
      <img src="/IndexImgs/coins.png" alt="coins" class='bannerI smol'>
      <p>Your Full Partnership allows you to publish tiered products, and receive a fair share whenever someone purchases a tier. <a href="/docs/63/Premium_Extension" target="_blank">More info</a></p>
      <div class="contentBlockT">
        <h1>Tiered Products</h1>
        <p>Your products published to each of the three tiers give you a part of the total.</p>
        <?php
          $amounts = [1=>100, 2=>250, 3=> 500];
          for ($i = 1; $i < 4; $i++){
            echo "<h3>Tier $i</h3>";
            $tierValue = giveTierValue($dl->partId, $i);
            $receives =  makeHuman(floor($amounts[$i] * $tierValue));
            echo "<p>Current share: ".$tierValue * 100 ."%<br>You receive on each purchase: $receives</p>";
            $query = 'SELECT * FROM products WHERE status != "deleted" AND partner = '.$dl->partId.' AND tier = '.$i.' order by name ASC';
            if ($firstrow = $dl->dlconn->query($query)) {
                while ($row = $firstrow->fetch_assoc()) {
                $column = "<a class='procol' href='/account/%%URL'>%%MEHA (%%GENRE)</a>";
                $column = str_replace("%%URL", "Product?id=".$row["id"], $column);
                $column = str_replace("%%MEHA", $row["name"], $column);
                $column = str_replace("%%GENRE", $dl->typeNames[$row["genre"]], $column);
                echo $column;
              }
            }
          }
        ?>
      </div>

  </div>
</div>
<?php
echo $dl->giveFooter();
?>
</body>
</html>
<?php
  echo $dl->scripts("p");
 ?><script>

var urlParams = new URLSearchParams(window.location.search);
var why = urlParams.get('i');

</script>
