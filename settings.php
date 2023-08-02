<?php
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
    <title>User Settings</title>
    <style>
        body {
            background-color: teal;
        }
        .container {
            margin-top: 50px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .content {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>
    <div class="container">
        <div class="header">
            <h1>User Settings</h1>
            <p>Manage your account settings</p>
        </div>
        <div class="content">
         
            <h2>Account Settings</h2>
            <p>Customize your account settings here:</p>
            <ul>
                 <p><a href="PasswordChange.php">Change password</a></p>
               
              
            </ul>
        </div>
    </div>
</body>
</html>