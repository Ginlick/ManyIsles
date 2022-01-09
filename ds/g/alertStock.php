<?php
if (!function_exists ("alertStock")) {
    function alertStock($stock){
        global $stockStatus;
        if ($stock > 10) {
            $span = "<span style='color:#2a7d14'>$stock</span>";
            $status = 2;
        }
        else if ($stock >= 5) {
            $span = "<span style='color:black'>$stock</span>";
            $status = 1;
        }
        else {
            $span = "<span style='color:#cf2715'>$stock</span>";
             $status = 0;
        }

        if (isset($stockStatus)){
            return $status;
        }
        else {
            return $span;

        }
    }
}
if (!function_exists ("prodStatSpan")) {
    function prodStatSpan($status){
        if ($status == "active") {
            $span = "<span style='color:var(--ds-status-green)'>$status</span>";
        }
        else if ($status == "paused") {
            $span = "<span style='color:var(--ds-status-blue)'>$status</span>";
        }
        else {
            $span = "<span style='color:black'>deleted</span>";
        }
        return $span;
    }
}
if (!function_exists ("hasAnyStock")) {
    function hasAnyStock($specs, $baseStock){
      if (gettype($specs)=="string"){$specs = json_decode($specs, true);}
      $smartstock = false;
      $totalStock = 0;
      foreach ($specs as $spec){
        if (isset($spec["smartstock"]) AND $spec["smartstock"] == 1){
          $smartstock = true;
          foreach ($spec["options"] as $option){
            if (isset($option["stock"])){
              $totalStock += $option["stock"];
            }
          }
        }
      }
      if (!$smartstock){
        $totalStock = $baseStock;
      }

      return $totalStock;
    }
}
?>
