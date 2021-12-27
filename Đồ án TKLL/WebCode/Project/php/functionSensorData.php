<?php
require_once("connect.php");
function getSensorData(){
    global $conn;
    $query="SELECT * FROM sensordata;";
    $result=$conn->query($query);
    return $result->fetch_all(MYSQLI_ASSOC);
}
function filterTBTDS($date){
    global $conn;
    $query="SELECT location,AVG(TDS) as avgTDS FROM sensordata WHERE DATE(reading_time)='$date' GROUP BY location ORDER BY AVG(TDS) DESC;";
    $result=$conn->query($query);
    return $result->fetch_all(MYSQLI_ASSOC);
}   

?>