<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hệ thống điều khiển</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
 
  
</head>
<body  align="center">
 		<br />
		<header> 
         <h2> <span style="color:red;font-weight:500">HỆ THỐNG CẢNH BÁO NỒNG ĐỘ TDS VƯỢT NGƯỠNG </span><br />
      </h2>
      </header>
   <?php
  
   $jsonString = file_get_contents("test/test.json");
   $data = json_decode($jsonString, true);
	
	  $user='abcd_ef';
	if(isset($_POST['LED_ON']))
	{
		if($user==$_POST['LED_ON'])
		{
		$data['led'] = "on";		
		}
	}
 
	if(isset($_POST['LED_OFF']))
	{
		if($user==$_POST['LED_OFF'])
		{
		$data['led'] = "off";
		}
	}	
   
	$newJsonString = json_encode($data);
	file_put_contents("test/test.json", $newJsonString);
 
   ?>     
   
 <form action="esp-control.php" method="post">       
	<table border="2" width=70% height="300px" align="center">
		<tr class="indam">
        	<td bgcolor="#FFCC00" style="font-weight:600;font-size:20px">TÊN THIẾT BỊ</td>
            <td bgcolor="#FFCC00" style="font-weight:600;font-size:20px">TRẠNG THÁI</td>
            <td bgcolor="#FFCC00" style="font-weight:600;font-size:20px"> ĐIỀU KHIỂN</td>
            
        </tr>
        <tr>
        	<td><h2>ONBOARD LED</h2></td>
            <td>
            	<img id="myImage" src="off.png" width="60" height="60">
            </td>
            <td> <p>
            <?php
			$user='abcd_ef';
             echo "   <button class='btn btn-success' type='submit'  name='LED_ON' value='$user'>ON</button>
             ";
             echo "   <button class='btn btn-danger' type='submit'  name='LED_OFF' value='$user'>OFF</button> ";
			?>
				</p>
                </td>
        </tr>
       
 
    </table>
 </form>
 
 <?php
   $jsonString = file_get_contents("test/test.json");
	$data = json_decode($jsonString, true);
 
 if ($data['led'] == 'on')
 {
	echo "	  <script>";
   	echo " document.getElementById('myImage').src = 'on.png' ";
   	echo "    </script> ";
 }
 
 
 ?>