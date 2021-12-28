<html>
<body>

<?php

$subject = "Patreon Connection";
$message = $_GET["patname"];

if (mail ("pantheon@manyisles.com", $subject, $message))
  {echo "Yes Master";
   header("Location: SignedIn.php?show=pat");
}

else
  {echo "Your mail wasn't sent. Noob.";}
 ?>

</body>
</html>