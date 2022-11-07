<?php

class subHandler {
    public $conn; public $moneyconn;
    public $subBasket;
    public $means;
    public $plan = [];
    public $user;

    function __construct($mycode, $means = "credit", $plan = null) {
        require($_SERVER['DOCUMENT_ROOT']."/Server-Side/db_accounts.php");
        $this->conn = $conn;
        require($_SERVER['DOCUMENT_ROOT']."/Server-Side/db_money.php");
        $this->moneyconn = $moneyconn;
        $this->plan = $plan;
    }

    function newSub(int $clid) {//stripe-specific
        $query = "SELECT datas, buyer FROM ds_asubs WHERE id = $clid";
        if ($toprow = $this->moneyconn->query($query)) {
            while ($row = $toprow->fetch_assoc()) {
                $datas = json_decode($row["datas"], true);
                $buyer = $row["buyer"];
            }
        }
        $datas["stripe_customer"] = $this->plan->customer;
        $datas = json_encode($datas);
        $query = "UPDATE ds_asubs SET validity =  366, fullId = '".$this->plan->subscription."', datas = '".$datas."' WHERE id = $clid";
        if ($this->moneyconn->query($query)) {$this->subProfit($clid, $buyer); return true;}
    }
    function subProfit($sid, $user){
        require($_SERVER['DOCUMENT_ROOT']."/Server-Side/promote.php");
        $adventurer = new adventurer($this->conn, $user);
        if ($sid == 1){
            $adventurer->promote("Loremaster");
        }
        else if ($sid == 2){
            echo "promoting";
            $adventurer->promote("Grand Poet");
        }
    }
    function delSub($clid) {
        $query = "DELETE FROM ds_asubs fullId = '$clid'";
        if ($this->moneyconn->query($query)) {return true;}
    }
    function upSub($clid) { //stripe
        $status = "active";
        if ($this->plan->cancel_at != null){
            $status = "canceled";
        }
        $startDate = $this->plan->billing_cycle_anchor;
        $endDate =  $this->plan->current_period_end;
        $timeSpan = ceil(($endDate - $startDate) / (24 * 60 *  60));
        $query = "UPDATE ds_asubs SET validity = $timeSpan, status = '$status' WHERE fullId = '$clid'";
        if ($this->moneyconn->query($query)) {return true;}
    }
    function statSub($sid, $dir = 1){ //credit
        if ($dir == 1){$status = "active";}else {$status = "canceled";}
        $query = "UPDATE ds_asubs SET status = '$status' WHERE id = $sid";
        if ($this->moneyconn->query($query)){return true;}
    }
    /*function cancelLesser(int $buyer){
        $query = "SELECT datas "

        foreach ($lesser as $sid){
            $query = "UPDATE ds_asubs SET status = 'canceled' WHERE plan = $sid AND buyer = $buyer";
            $this->moneyconn->query($query);
        }
    }*/

    function checkUser(int $sid){
        $conn = $this->conn;
        require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/security.php");

        $query = "SELECT buyer FROM ds_asubs WHERE id = $sid";
        if ($toprow = $this->moneyconn->query($query)){
            while ($row = $toprow->fetch_assoc()) {
                if ($row["buyer"]==$_COOKIE["loggedIn"]){
                    return true;
                }
            }
        }
        return false;
    }
}

function activeSubs($domain) {

}

function subPower($domain, int $account, $moneyconn = null) {
    if ($moneyconn == null){
        require($_SERVER['DOCUMENT_ROOT']."/Server-Side/db_money.php");
    }

    if ($domain == "mystral"){
        $query = "SELECT id FROM ds_asubs WHERE plan = 2 AND validity != 0 AND buyer = $account";
        if ($result = $moneyconn->query($query)) {
            if (mysqli_num_rows($result) > 0){
                return 2;
            }
        }
        $query = "SELECT id FROM ds_asubs WHERE plan = 1 AND validity != 0 AND buyer = $account";
        if ($result = $moneyconn->query($query)) {
            if (mysqli_num_rows($result) > 0){
                return 1;
            }
        }
    }
    return 0;
}
?>
