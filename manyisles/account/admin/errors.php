<?php
require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/promote.php");
$user = new adventurer;
$user->modcheck(4);
$conn = $user->conn;
$user->killCache();

function giveErrorTable($which){
  global $user, $conn;
  $return = "";
  $query = "SELECT * FROM errors ";
  if ($which == "serious"){
    $query .= " WHERE severity > 1";
  }
  else if ($which == "deprecated"){
    $query .= " WHERE type = 8192";
  }
  else {
    $query .= " WHERE severity <= 1 AND type != 8192";
  }
  $query .= " AND status = 1 ORDER BY occurrences DESC";
  if ($result = $conn->query($query)){
    $return .= "<table><thead><tr><td>latest</td><td>message</td><td>occurences</td><td></td></thead><tbody>";
    while ($row = $result->fetch_assoc()) {
      $return .= "<tr>";
      //$return .= "<td>".$row["id"]."</td>";
      $date = new DateTime($row["reg_date"]);
      $return .= "<td>".$date->format("d.m.y")."</td>";
      $return .= "<td>".$user->placeSpecChar($row["message"])."</td>";
      $return .= "<td>".$row["occurrences"]."</td>";
      $return .= "<td><a href='deleteError.php?id=".$row["id"]."'>Fixed</a></td>";
    }
    $return .= "</tbody></table>";
  }
  return $return;
}

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <link rel="icon" href="/Imgs/Favicon.png">
    <title>Tools</title>
    <link rel="stylesheet" type="text/css" href="/Code/CSS/Main.css">
    <link rel="stylesheet" type="text/css" href="/Code/CSS/diltou.css">
    <style>
    table {
      border-collapse: collapse;
    }
    table thead td {
      font-weight: bold;
    }
        table tbody tr:nth-child(odd) {
          background: var(--diltou-lines);
        }
        td {
          padding: 0.3em;
        }
    </style>
</head>
<body>
    <div w3-include-html="/Code/CSS/GTopnav.html" style="position:sticky;top:0;z-index:22;"></div>
    <div class="flex-container">
        <div class='left-col'>
            <h1 class="menutitle">Error Log</h1>
            <ul class="myMenu">
                <li><a class="Bar" href="../SignedIn">Back</a></li>
            </ul>
            <img src="/Imgs/Bar2.png" alt="GreyBar" class='separator'>
            <ul class="myMenu bottomFAQ">
            </ul>

        </div>

        <div class='column'>
            <h1>Error Log</h1>
            <h2>Serious Errors</h2>
            <p>The handler sends mails for these, so you better fix them.</p>
            <?php echo giveErrorTable("serious"); ?>
            <h2>Warnings</h2>
            <p>meh</p>
            <?php echo giveErrorTable("all"); ?>
            <h2>Deprecations</h2>
            <p>Literally no one cares about these annoying wimps. <i>Wuuhuhuuu don't pass null</i> omg stfu you're useless and stupid</p>
            <?php echo giveErrorTable("deprecated"); ?>
        </div>
    </div>
    <div w3-include-html="/Code/CSS/genericFooter.html" w3-create-newEl="true"></div>
</body>
</html>
<script src="/Code/CSS/global.js"></script>
