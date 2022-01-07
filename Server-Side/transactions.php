<?php

class transaction {
    public $moneyconn;
    public $target;
    public $total_credit = 0;
    public $reference;

    function __construct($moneyconn, $target) {
        $this->moneyconn = $moneyconn;
        $this->target = $target;

        $query = "SELECT * FROM global_credit WHERE id = $target";
        if ($result = $this->moneyconn->query($query)) {
          if (mysqli_num_rows($result) == 0) {
              $reference = rand(10000000, 99999999);
              $reference = $target.$reference;
              $squery = "INSERT INTO global_credit (id, credit, reference) VALUES ($target, 0, $reference)";
              $this->moneyconn->query($squery);
              $this->reference = $reference;
              $squery = sprintf("SELECT id FROM transfers_%s ORDER BY id DESC LIMIT 1", $reference);
              if (!$this->moneyconn->query($squery)) {
                  $squery = sprintf("CREATE TABLE transfers_%s LIKE transfers_1422222222", $reference);
                  $this->moneyconn->query($squery);
              }
            }
            else {
                while ($row = $result->fetch_assoc()) {
                    $this->total_credit = $row["credit"];
                    $this->reference = $row["reference"];
                }
            }
        }
    }
    function new(int $amount, $source, $desc){
        if ($amount != null AND $amount != 0 AND $this->total_credit + $amount > 0){
            $query = "UPDATE global_credit SET credit = credit + $amount WHERE reference = $this->reference";
            if ($this->moneyconn->query($query)){
                $query = 'INSERT INTO transfers_'.$this->reference.' (motive, source, amount) VALUES ("'.$desc.'", "'.$source.'", '.$amount.')';
                if ($this->moneyconn->query($query)){
                    $this->total_credit += $amount;
                    return true;
                }
            }
        }
        return false;
    }
}

?>
