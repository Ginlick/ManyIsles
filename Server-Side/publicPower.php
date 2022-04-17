<?php
//for checking the user credentials of adventurers on public-access applications, though not implemented in wiki yet (headache)

if (!trait_exists("publicPower")){
  trait publicPower {
    // public $power = 0;
    // public $power_canedit = false;


    // public $applicationsDetails = [
    //   "globalTable" => "users",
    //
    //
    // ];
    // function checkPower($user, $dbconn) {
    //   if ($user->signedIn){
    //     $query = "SELECT power FROM ".$this->applicationsDetails["globalTable"]." WHERE id = ".$user->user;
    //       //get power
    //   }
    //   else {
    //     $this->canedit = false;
    //   }
    //   if ($this->power < $this->minPower){$this->canedit = false; $this->ediProblem = "Status";}
    // }
  }
}


?>
