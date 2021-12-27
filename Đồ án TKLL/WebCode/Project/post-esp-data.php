<?php


$servername = "localhost";
$dbname = "id17945603_tdsesp8266";
$username = "id17945603_tungnguyen";
$password = "Tungdeptrai-333";

// Keep this API Key value to be compatible with the ESP8266 code provided in the project page. 
$api_key_value = "HCMUTK19";

$api_key= $sensor1 = $sensor2 = $location = $TDS = $EC = $TEMP = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $api_key = test_input($_POST["api_key"]);
    if($api_key == $api_key_value) {
        $sensor1 = test_input($_POST["sensor1"]);
        $sensor2 = test_input($_POST["sensor2"]);
        $location = test_input($_POST["location"]);
        $TDS = test_input($_POST["TDS"]);
        $EC = test_input($_POST["EC"]);
        $TEMP = test_input($_POST["TEMP"]);
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $date = date('Y-m-d H:i:s');
        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        } 
        
        $sql = "INSERT INTO sensordata (sensor1, sensor2, location, TDS, EC, TEMP, reading_time)
        VALUES ('" . $sensor1 . "','" . $sensor2 . "', '" . $location . "', '" . $TDS . "', '" . $EC . "', '" . $TEMP . "', '" . $date . "')";
        
        if ($conn->query($sql) === TRUE) {
            echo "New record created successfully";
        } 
        else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    
        $conn->close();
    }
    else {
        echo "Wrong API Key provided.";
    }

}
else {
    echo "No data posted with HTTP POST.";
}

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}