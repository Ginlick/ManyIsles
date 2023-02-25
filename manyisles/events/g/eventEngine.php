<?php
require($_SERVER['DOCUMENT_ROOT']."/Server-Side/promote.php");
class eventEngine {
  use allBase;
  function __constructor() {
    $this->eUser = new eventUser();
    $this->construct();
  }


  function giveHead() {
    $text =  <<<MAXX
    <meta charset="UTF-8" />
    <meta http-equiv="Expires" content="0" />
    <link rel="stylesheet" type="text/css" href="/Code/CSS/Main.css">
    <link rel="stylesheet" type="text/css" href="/Code/CSS/pop.css">
    <link rel="stylesheet" type="text/css" href="/events/g/events.css">
    MAXX;
    return $text;
  }
  function giveLeft() { //have button either be "sign in" leading to a pop acp portal, or Create Event
    $text = <<<MAXX
    <section class="column-left">
      <div class="inColumnLeft">
        <h2>Hello</h2>
        <p>Explore</p>
        <p>Search smth</p>
        <a href="/events/create"><button class="eventButton">Create Event</button></a>
      </div>
    </section>
    MAXX;
    return $text;
  }
}

class eventUser extends adventurer {

}

?>
