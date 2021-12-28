<?php
if (preg_match("/[^a-z0-9]/", $_GET['id'])==1){exit();}

require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/db_accounts.php");
if (!isset($_COOKIE["loggedIn"])){
    header("Location:/account/Account.html?error=notSignedIn"); exit();
}
else {
    $id = $_COOKIE["loggedIn"];
}

$code = $_GET["id"];
$correct = false;
$query = "SELECT code FROM confirmer WHERE id = $id";
if ($result = $conn->query($query)){
    while ($row = $result -> fetch_assoc()) {
        if ($code == $row["code"]) {
            $correct = true;
            $query = "DELETE FROM confirmer WHERE id = $id"; $conn->query($query);
        }
    }
}

$success = false;
if ($correct){
    $query = "UPDATE accountsTable SET emailConfirmed = 1 WHERE id = $id";
    if ($conn->query($query)){
        $success = true;
    }
}

$conn->close();
?>


<html>
<head>
    <meta charset="UTF-8" />
    <link rel="icon" href="../Imgs/Favicon.png">
    <title>Confirm Email | Account</title>
    <link rel="stylesheet" type="text/css" href="/Code/CSS/Main.css">
    <link rel="stylesheet" type="text/css" href="/Code/CSS/pop.css">
    <link rel="stylesheet" type="text/css" href="g/acc.css">
</head>
<body>

<div w3-include-html="/Code/CSS/GTopnav.html" style="position:sticky;top:0;"></div>


<h1><?php if ($success){echo " Email Confirmed" ; } else {echo "Email not Confirmed"; } ?></h1>
<div style="width:80%;margin:10px auto 10px auto;"><img src="<?php if ($success){echo "/Imgs/Recruit.png" ; } else {echo "/Imgs/Oops.png"; } ?>" alt="accountBanner" style='width:100%;'></div>

<?php if ($success){ echo "
<p>You're free to sail the Many Isles!<br><br>Note that some pages might not update immediately; you can always reload them.</p>
"; }
else {
    if ($correct){
        echo "<p>There was an error updating your account's status.</p>";
    }
    else {
        echo "<p>Your code does not seem to be correct.</p>";
    }
}

?>

<div><a class="popupButton" style="width:15%;margin-top:20px;" href="<?php if (isset($_COOKIE["loggedIn"])) {echo "SignedIn.php"; } else {echo "/home"; } ?>">OK</a></div>
</body>
</html>
<script src="/Code/CSS/global.js"></script>
