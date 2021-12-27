<?php
$db=array(
    'server'=>'localhost',
    'username'=>'id17945603_tungnguyen',
    'password'=>'Tungdeptrai-333',
    'dbname'=>'id17945603_tdsesp8266'
);
$conn=new mysqli($db['server'], $db['username'], $db['password'],$db['dbname']);
mysqli_set_charset($conn, 'utf8');

if ($conn->connect_error) {
    die("Connection failed: " . mysqli_connect_error());
}

?>