<html>
<body>

<?php

$subject = "New Spell!";
$message = $_POST["newSpell"];

if (!mail ("godsofmanyisles@gmail.com", $subject, $message))
  {echo "Sorry very sorry oops i failed you master";}
else
  {echo "Wow I love you you're so great you made me work";}
?>

 <?php header("Location: CreateMain.html?show=spells"); ?>

</body>
</html>