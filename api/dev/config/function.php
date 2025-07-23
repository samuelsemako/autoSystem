<?php
class allClass
{
    function _getSequenceCount($conn, $counterId){
        $count=mysqli_fetch_array(mysqli_query($conn,"SELECT counterValue FROM counter_tab WHERE counterId = '$counterId' FOR UPDATE"));
        $num=$count[0]+1;
        mysqli_query($conn,"UPDATE counter_tab SET counterValue = '$num' WHERE counterId = '$counterId'")or die (mysqli_error($conn));
        if ($num<10){$no='00'.$num;}elseif($num>=10 && $num<100){$no='0'.$num;}else{$no=$num;}
        return '[{"no":"'.$no.'"}]';
    }

} //end of class
$callclass = new allClass();
?>
