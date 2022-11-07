<?php
if (isset($_GET["c"])) {if (preg_match("/^[0-9]*$/", $_GET["c"])!=1){header("Location:settings.php");exit();} else $codeId =  $_GET["c"];} else { header("Location:settings.php");exit(); }

$redirect = "../home.php";
$checkDSpresence = true;
require_once("security.php");

$query = "SELECT affect FROM dscodes WHERE id = $codeId";
if ($result = $conn->query($query)){
    if (mysqli_num_rows($result) == 0) { header("Location:settings.php");exit(); }
    else {
        while ($row = $firstrow->fetch_assoc()) {
            if ($row["affect"] != "1,$pId") { header("Location:settings.php");exit(); }
        }
    }
}


//doing 
$query = "UPDATE dscodes SET status = 0 WHERE id = $codeId";
if ($result = $conn->query($query)){
    header("Location:settings.php?why=delCode#hRedCodes");exit();
}
else {
    $error = $conn->error;
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <link rel="icon" href="/Imgs/FaviconDS.png">
    <title>Error | Digital Store</title>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <link rel="stylesheet" type="text/css" href="/Code/CSS/Main.css">
    <link rel="stylesheet" type="text/css" href="../g/ds-g.css">
    <link rel="stylesheet" type="text/css" href="../g/ds-tables.css">
</head>
<body>
    <div w3-include-html="/Code/CSS/GTopnav.html" style="position:sticky;top:0;z-index:5;"></div>
    <div class="flex-container">
        <div class='left-col'>
            <a href="hub.php"><h1 class="menutitle">Partnership</h1></a>
            <ul class="myMenu">
                <li><a class="Bar" href="hub.php"><i class="fas fa-arrow-left"></i> Hub</a></li>
            </ul>
            <img src="/Imgs/Bar2.png" alt="GreyBar" class='separator'>
            <ul class="myMenu bottomFAQ">
                <li><a class="Bar" href="/wiki/h/publishing/ds" target="_blank">DS Publishing</a></li>
            </ul>
        </div>

        <div id='content' class='column'>
            <h1>Error Deleting Code </h1>
            <div class='dsBanner'><img src='/Imgs/Oops.png' alt:'Oopsie!'></div>
            <p>There was an error deleting your code.
            <br><br><b>Error:</b><br><?php echo $error; ?>
            </p>
            <div class="checkoutBox" style="margin-bottom:0;" onclick="location.reload();">
                <button class="checkout" type="submit">
                    <i class="fas fa-redo"></i>
                    <span>Retry</span>
                </button>
            </div>
        </div>
    </div>


    <div w3-include-html="../g/GFooter.html" w3-create-newEl="true"></div>


</body>
</html>
<script src="/Code/CSS/global.js"></script>
<script>

</script>