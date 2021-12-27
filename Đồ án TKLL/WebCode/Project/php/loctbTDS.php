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
        #back {
            float: left;
        }

        #back::after {
            content: "";
            display: table;
            clear: both;
        }
    </style>
</head>

<body>
    <div class="jumbotron text-center" style="margin-bottom:20px;">
            <a href="../index.php" id="back">
                <h2>Quay lại</h2>
            </a>
        <h1>Lọc TDS trung bình theo ngày của từng hộ gia đình</h1>
    </div>
    <div class="container">
        <!-- loc theo ngay -->
        <div>
            <form action="ajaxtbTDS.php" id="formFilterTDS" class="form-inline">
                <label>Chọn ngày: </label> 
                <input type="date" id="ngay" name="ngay">
                <button  type="submit" class="btn btn-primary">Xác nhận</button>
            </form>
            <br>
        </div>
        
        <!-- display data -->
        <table style="display:none;" id="tableDisplay" class="table table-hover">
            <thead>
                <tr>
                    <th>Location</th>
                    <th>TDS Average</th>
                </tr>
            </thead>
            <tbody id="displayData">
            </tbody>
        </table>
    </div>


    <script>
        $(document).ready(function() {
            $("#formFilterTDS").submit(function(event) {
                // Stop form from submitting normally
                event.preventDefault();

                let $form = $(this);
                let date = $("#ngay").val();
                let url = $form.attr("action");
                
                if(date) {
                    $.post(url, {
                            date: date
                        })
                        .done(function(data) {
                            if (data) {
                                $('#displayData').html(data);
                                $('#tableDisplay').slideDown();
                            }
                        });
                }
                
            });
        });
    </script>
 </body>

 </html>