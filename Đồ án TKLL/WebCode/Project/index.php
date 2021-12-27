<!DOCTYPE html>
<html lang="en">
<head>
    <title>ĐỒ ÁN TKLL-ĐỀ TÀI 1</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <!-- jQuery library -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <!-- Popper JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>

    <!-- Latest compiled JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <style>
        .searchdulieu{
            margin-bottom: 40px;
            float: right;
        }
        .searchdulieu::after{
            content: "";
            display: table;
            clear: both;
        }
        #ngay{
            margin-right: 20px;
        }
    </style>
</head>

<body>
    <div class="jumbotron text-center" style="margin-bottom:20px;">
        <h1>Bảng dữ liệu Sensor</h1>
    </div>
    <div class="container">
        <!-- search dia chi or ngay -->
        <div class="searchdulieu">
            <label>SEARCH: </label> <input id="inputSearch" type="text" placeholder="Search Date or location...">
        </div>

        <!-- loc theo ngay -->
        <div>
            <button onclick="location.href = 'php/loctbTDS.php';" type="button" class="btn btn-primary">Lọc TDS trung bình theo ngày</button>
        </div>


        <!-- display data -->
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Sensor1</th>
                    <th>Sensor2</th>
                    <th>Location</th>
                    <th>TDS</th>
                    <th>EC</th>
                    <th>Temp</th>
                    <th>Time</th>
                </tr>
            </thead>
            <tbody id="sensorData">
                <?php require_once("php/functionSensorData.php");
                    $objs=getSensorData();
                    foreach($objs as $obj){ ?>
                    <tr>
                    <td><?php echo $obj['id'] ?></td>
                    <td><?php echo $obj['sensor1'] ?></td>
                    <td><?php echo $obj['sensor2'] ?></td>
                    <td><?php echo $obj['location'] ?></td>
                    <td><?php echo $obj['TDS'] ?></td>
                    <td><?php echo $obj['EC'] ?></td>
                    <td><?php echo $obj['TEMP'] ?></td>
                    <td><?php echo $obj['reading_time'] ?></td>
                    </tr>
                <?php    }?>
                
            </tbody>
        </table>
    </div>

    <script>
        $(document).ready(function(){
            $("#inputSearch").on("keyup",function(){
                let value=$(this).val().toLowerCase();
                $("#sensorData tr").filter(function(){
                    $(this).toggle($(this).text().toLowerCase().indexOf(value)>-1 );
                });
            });
        });
    </script>

</body>
</html>