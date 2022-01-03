<?php

require_once($_SERVER['DOCUMENT_ROOT']."/wiki/domInfo.php");
class gay {} $gen = new gay;
equipDom($gen, "mystral");

$method ="";
if (isset($_GET["notebookType"])) {$method = $_GET["notebookType"];}

function generateNotebooks($gen, $starterId, $method = 0){
  if ($method == 2){
    //fantasy wiki
    $style = "Fandom";
    $genres = json_encode($gen->cateoptions);
    $query = 'INSERT INTO '.$gen->database.' (id, v, name, shortName, banner, root, body, cate) VALUES ('.$starterId.', 0, "New Manual", "New Manual",  "plains.png", 0, "This is your fantasy notebook\'s homepage. [Edit](/mystral/edit?id='.$starterId.') \n [wiki:children]", "Homepage")';
    $gen->dbconn->query($query); $rId = $starterId + 1;
    $query = 'INSERT INTO '.$gen->database.' (id, v, name, shortName, banner, root, body, cate) VALUES ('.$rId.', 0, "Landmasses", "Landmasses",  "mounts.png", '.$starterId.', "The awesome continents of my fantasy world.", "Geography")';
    $gen->dbconn->query($query); $rId++;
    $query = 'INSERT INTO '.$gen->database.' (id, v, name, shortName, banner, root, body, cate) VALUES ('.$rId.', 0, "States", "States",  "city2.jpg", '.$starterId.', "The awesome kingdoms and empires of my fantasy world. \n [wiki:children]", "Organization")';
    $gen->dbconn->query($query);  $rId++;
    $query = 'INSERT INTO '.$gen->database.' (id, v, name, shortName, banner, root, body, cate) VALUES ('.$rId.', 0, "Culture", "Culture",  "city1.jpg", '.$starterId.', "The cultures of my fantasy world. \n [wiki:children]", "Culture")';
    $gen->dbconn->query($query); $cultId = $rId; $rId++;
    $query = 'INSERT INTO '.$gen->database.' (id, v, name, shortName, banner, root, body, cate) VALUES ('.$rId.', 0, "Clergy", "Clergy",  "icehall.jpg", '.$cultId.', "The religions and beliefs of my fantasy world.", "Culture")';
    $gen->dbconn->query($query); $rId++;
    $query = 'INSERT INTO '.$gen->database.' (id, v, name, shortName, banner, root, body, cate) VALUES ('.$rId.', 0, "Languages", "Language",  "river.jpg", '.$cultId.', "The languages of my fantasy world.", "Culture")';
    $gen->dbconn->query($query); $rId++;
    $query = 'INSERT INTO '.$gen->database.' (id, v, name, shortName, banner, root, body, cate) VALUES ('.$rId.', 0, "Sciences", "Science",  "warcamp.jpg", '.$cultId.', "The technology of my fantasy world.", "Technology")';
    $gen->dbconn->query($query); $rId++;
    $query = 'INSERT INTO '.$gen->database.' (id, v, name, shortName, banner, root, body, cate) VALUES ('.$rId.', 0, "Races", "Races",  "trees.png", '.$starterId.', "The races of my fantasy world.", "Races")';
    $gen->dbconn->query($query); $rId++;
    $query = "INSERT INTO wiki_settings (id, mods, genres, styles) VALUES ('$starterId', '$gen->user', '$genres', '$style')";
    $gen->dbconn->query($query);
    return true;
  }
  else if ($method == 1){
    //campaign
    $style = $gen->defaultStyle;
    if (in_array("Great Gamemaster", $gen->styles)){$style = "Great Gamemaster";}

    exit();

    $genres = '[{"value":"Note","name":"Generic Note"},{"value":"File","name":"File"},{"value":"Character","name":"Character"},{"value":"NPC","name":"NPC"},{"value":"Condition","name":"Condition / Disease"},{"value":"Conflict","name":"Conflict"},{"value":"Culture","name":"Culture"},{"value":"Document","name":"Document"},{"value":"Event","name":"Event / Legend"},{"value":"Geography","name":"Geography"},{"value":"Item","name":"Item"},{"value":"Language","name":"Language"},{"value":"Magic","name":"Magic"},{"value":"Organization","name":"Organization / State"},{"value":"Politics","name":"Politics"},{"value":"Technology","name":"Technology"},{"value":"Race","name":"Race / Ethnicity"},{"value":"Relation","name":"Relation / Treaty"},{"value":"Religion","name":"Religion"},{"value":"Settlement","name":"Settlement / Location"}]';
    $query = 'INSERT INTO '.$gen->database.' (id, v, name, shortName, banner, root, body, cate) VALUES ('.$starterId.', 0, "My Campaign", "Campaign",  "plains.png", 0, "This campaign was started today. \n [wiki:children]", "Homepage")';
    $gen->dbconn->query($query); $rId = $starterId + 1;
    $query = 'INSERT INTO '.$gen->database.' (id, v, name, shortName, banner, root, body, cate) VALUES ('.$rId.', 0, "Party", "Party",  "mounts.png", '.$starterId.', "The initial party consisted of these players. \n [wiki:children]", "File")';
    $gen->dbconn->query($query); $partId = $rId; $rId++;
    $query = 'INSERT INTO '.$gen->database.' (id, v, name, shortName, banner, root, body, cate) VALUES ('.$rId.', 0, "Character 1", "Character 1",  "city2.jpg", '.$partId.', "Character 1 was a human fighter, born in Chessenta. He was devoted to Auril. \n ##Background \n After coming up in the harsh Rogg-Vaasan plains, Chuck moved to Mittendale. There, the Cleric Lotin helped his education as a true warrior. Thereafter, he was an adventurer, roaming across the Mruggenrykz. \n ##In the Party \n [note]Only write about the parts where Chuck was *not* with the party.[/note] \n Chuck joined on to explore Mittendale%single_quote%s silence. He proved his fighting abilities on the way. While in Kalder Ein, he received a message from Lotin and visited her in the eastern Tharmounts. Chuck found out that she was mainly worried about increasing numbers of %double_quote%fire-worms%double_quote% in the Vaasa glacier, and how they were destroying the balance of the mountains and Auril.", "Character")';
    $gen->dbconn->query($query);  $rId++;
    $query = 'INSERT INTO '.$gen->database.' (id, v, name, shortName, banner, root, body, cate) VALUES ('.$rId.', 0, "NPCs", "NPC",  "snowycliff.jpg", '.$starterId.', "Keep track of the major NPCs. \n [wiki:children]", "File")';
    $gen->dbconn->query($query); $cultId = $rId; $rId++;
    $query = 'INSERT INTO '.$gen->database.' (id, v, name, shortName, banner, root, body, cate, sidetabTitle, sidetabImg, sidetabText) VALUES ('.$rId.', 0, "Raaza the Clever", "Raaza",  "stones.jpg", '.$cultId.', "Raaza the Clever was Dag\'Rutha\'s consort and the most capable necromancer of the Black-Bone tribe. \n ##Appearances \n She led the Dead Guard army besieging Kalder Ein. \n\n On day 13, after Jeff is escorted to the warcamp, he is questioned by her, and she lays a fake spell on him. She tries to convince him to spy on the party for her, not yet knowing he\'s also a White agent.  \n\n On day 15, the giants attack the warcamp. While she initially self-confidently stands in the rear, she is attacked by the party. She repeatedly defeats their magical attacks, though she gets damaged twice by fireballs, until she is killed by the rogue.", "NPC", "Raaza the Clever", "https://i.pinimg.com/564x/85/4a/6e/854a6ee5923620d703c85b0cb6dc4982.jpg","####Race\nOrc")';
    $gen->dbconn->query($query); $rId++;
    $query = 'INSERT INTO '.$gen->database.' (id, v, name, shortName, banner, root, body, cate) VALUES ('.$rId.', 0, "Campaign Tracker", "Tracker",  "snowriver.jpg", '.$starterId.', "You could keep track of the days spent in adventure, starting at day 0, so that you can correctly align side-plots (with absent characters\' activities and NPC actions). \n\n Additionally, if you\'re a nerd, you could create an attendance spreadsheet and link it here.  \n [wiki:children]", "File")';
    $gen->dbconn->query($query);$trackId = $rId;  $rId++;
    $query = 'INSERT INTO '.$gen->database.' (id, v, name, shortName, banner, root, body, cate) VALUES ('.$rId.', 0, "Session 1 - Plains Travel", "Session 1",  "stones.jpg", '.$trackId.', "Campaign Days: 0 - 7\n\n[note]\nIf you use adventure modules, or have notes written down somewhere, you could link them on this page. For example, you can photograph a note paper, upload it to Mystral Images, and embed the image link with ctrl + alt + i.\n[/note]\n\nJeff, Tucker, Chuck and Suugma met in Maerdooth, learning that Elder Karman Gensman is interested in knowing why no news has come down from Mittendale recently. The party fetched supplies from the Gutham brothers, Suugma tried seducing Priest Kjermohan, and the group hired a boat to sail down the Methflow. They then hiked up northwards along the Walmoor, encountering and exterminating a small group of goblins. In Vercy, they encounter and kill a mruggen herd, and meet an orcish tribe that is fleeing dwarven genociders.\n\nTheir journey ends in the Thar dwarven lumber yards, at the bottom of Mittendale Pass.", "Event")';
    $gen->dbconn->query($query); $rId++;
    $query = 'INSERT INTO '.$gen->database.' (id, v, name, shortName, banner, root, body, cate) VALUES ('.$rId.', 0, "Quotes Tracker", "Quotes",  "caves.png", '.$starterId.', "[note]You can write down all important lines of NPCs and such, so you can draw them up later. An *Animal Messenger* message could also be kept track of here.[/note]\n###Session 3\n\n - **Chuck - Lotin**. The fire-worms are destroying %double_quote%the balance of the mountains and Auril%double_quote%", "Event")';
    $gen->dbconn->query($query); $rId++;
    $query = "INSERT INTO wiki_settings (id, mods, genres, styles) VALUES ('$starterId', '$gen->user', '$genres', '$style')";
    $gen->dbconn->query($query);
    return true;
  }
  else {
    $query = 'INSERT INTO '.$gen->database.' (id, v, name, shortName, banner, root, body, parseClear, cate) VALUES ('.$starterId.', 0, "New Notebook", "New Notebook",  "default", 0, "This is your notebook\'s homepage. [Edit](/mystral/edit?id='.$starterId.')", 1, "Homepage")';
    if ($gen->dbconn->query($query)) {
      return true;
    }
  }
  return false;
}


$query = "SELECT a.id
FROM $gen->database a
LEFT OUTER JOIN $gen->database b
    ON a.id = b.id AND a.v < b.v
WHERE b.id IS NULL AND a.root = 0";
if ($result = $gen->dbconn->query($query)){
    if (mysqli_num_rows($result) >= $gen->mystData["notebooks"]) {
        echo "<script>window.location.replace('$gen->homelink?view=sub');</script>";exit();
    }
    else {
        $max =$gen->dbconn->query("SELECT MAX( id ) AS max FROM $gen->database");
        list($max) = mysqli_fetch_row($max); $max += 1;
        if (generateNotebooks($gen, $max, $method)){
            header("Location:".$gen->artRootLink.$max."/home?i=created");exit();
        }
    }
}
else {
    $query = "CREATE TABLE $gen->database LIKE notes";
    if ($gen->dbconn->query($query)){
        if (generateNotebooks($gen, 1, $method)){
            header("Location:".$gen->artRootLink."1/home?i=created");exit();
        }
    }
}





?>
