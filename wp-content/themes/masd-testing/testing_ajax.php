<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP AJAX Demo</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        $(document).ready(function(){
            $.ajax({
                url: "testing_ajax_process.php",
                type: "POST",
                success: function(response){
                    $("#output").html(response); // Display response from PHP file
                }
            });
        });
    </script>
</head>
<body>
    <div id="output">
        <!-- PHP response will be displayed here -->
    </div>
</body>
</html>
