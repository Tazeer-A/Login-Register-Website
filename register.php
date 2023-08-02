<?php

session_start();

$hostName = "localhost";
$dbUser = "root";
$dbPassword = "";
$dbName = "login_register";
$conn = mysqli_connect($hostName, $dbUser, $dbPassword, $dbName);

if (!$conn) {
    die("Something went wrong!");
}

if (isset($_POST["submit"])) {
    $fullName = $_POST["fullname"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $passwordRepeat = $_POST["repeat_password"];
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $errors = array();

    // Validate the email format using regular expression
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        array_push($errors, "Invalid email format");
    } else {
        // Extract the domain from the email
        $emailDomain = substr(strrchr($email, "@"), 1);
        
        // Check if the domain has valid DNS records
        if (!checkdnsrr($emailDomain, "MX")) {
            array_push($errors, "The email domain does not have valid DNS records.");
        }

        // Check if the domain is from an allowed list
        $allowedDomains = array('gmail.com', 'outlook.com', 'hotmail.co.uk'); 
        if (!in_array($emailDomain, $allowedDomains)) {
            array_push($errors, "Only specific email domains are allowed.");
        }
    }

    // Validate other fields
    if (empty($fullName) || empty($email) || empty($password) || empty($passwordRepeat)) {
        array_push($errors, "All fields are required");
    }

    if (strlen($password) < 8) {
        array_push($errors, "The password must be 8 characters long");
    }

    if (!preg_match('/[a-z]/', $password)) {
        array_push($errors, "Password must contain letters");
    }

    if ($password !== $passwordRepeat) {
        array_push($errors, "The passwords do not match");
    }

    // Check for existing email in the database
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    if ($user) {
        array_push($errors, "Email already exists");
    }

    // If there are errors, display them
    if (count($errors) > 0) {
        foreach ($errors as $error) {
            echo "<div class='alert alert-danger'>$error</div>";
        }
    } else {
        // Insert new user into the database
        $verificationToken = bin2hex(random_bytes(32));

        $sql = "INSERT INTO users (full_name, email, password, verification_token) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssss", $fullName, $email, $passwordHash, $verificationToken);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        echo "<div class='alert alert-success'>Registration successful. You can now log in.</div>";
    }
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
    <title>Registration Form</title>
    <style>
        body {
            background-color: teal;
        }
    </style>
</head>
<body>
    <div class="container">
        <form action="register.php" method="post">
            <div class="form-group">
                <input type="text" class="form-control" name="fullname" placeholder="Full Name">
            </div>
            <div class="form-group">
                <input type="text" class="form-control" name="email" placeholder="Email">
            </div>
            <div class="form-group">
                <input type="password" class="form-control" name="password" placeholder="Password" id="password">
                <input type="checkbox" id="showPassword"> Show Password
            </div>
            <div class="form-group">
                <input type="password" class="form-control" name="repeat_password" placeholder="Repeat Password" id="repeat_password">
                <input type="checkbox" id="showRepeatPassword"> Show Password
            </div>
            <div class="form-btn">
                <input type="submit" class="btn btn-primary" value="Register" name="submit">
            </div>
        </form>
        <div>
            <p>Already Registered? <a href="login.php">Login over here</a></p>
        </div>
    </div>
    <script>
        document.getElementById("showPassword").addEventListener("change", function() {
            var passwordInput = document.getElementById("password");
            if (this.checked) {
                passwordInput.type = "text";
            } else {
                passwordInput.type = "password";
            }
        });

        document.getElementById("showRepeatPassword").addEventListener("change", function() {
            var repeatpasswordInput = document.getElementById("repeat_password");
            if (this.checked) {
                repeatpasswordInput.type = "text";
            } else {
                repeatpasswordInput.type = "password";
            }
        });
    </script>
</body>
</html>