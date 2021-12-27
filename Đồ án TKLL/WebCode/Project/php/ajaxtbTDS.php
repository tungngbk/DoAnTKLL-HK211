<?php
if (isset($_POST['date'])) {
    $date=$_POST['date'];
    require_once("functionSensorData.php");
    $objs=filterTBTDS($date);
    global $conn;
    $conn->close();
    $dataout="";
    foreach($objs as $obj) {
        $dataout.='<tr>
                <td>'. $obj["location"] .'</td>
                <td>'.$obj["avgTDS"].'</td>
            </tr>';
    } 
    echo $dataout;
}

?>




