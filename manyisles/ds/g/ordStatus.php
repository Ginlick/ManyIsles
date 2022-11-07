<?php
if (!function_exists ("ordStatus")) {
    function ordStatus($statusNum, $mode){
        //mode 0 = do nothing

        if ($statusNum == 2){$ordStatus = "delivered";}
        else if ($statusNum == 1){$ordStatus = "shipping";}
        else {$ordStatus = "pending";}

        if ($mode == 1){
            if ($ordStatus == "delivered"){$ordStatus = "<span style='color:green;'>delivered</span>";}
            else if ($ordStatus == "shipping"){$ordStatus = "<span style='color:black;'>shipping</span>";}
            else {$ordStatus = "<span style='color:red;'>pending</span>";}        
        }
        return $ordStatus;
    }
}
?>