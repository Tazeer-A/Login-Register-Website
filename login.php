<?php
session_start();

if (isset($_SESSION["user"])) {
    header("Location: index.php");
    exit();
}

$maxLoginAttempts = 4;
$lockoutDuration = 4 * 60;

if (!isset($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

if (isset($_POST["login"])) {
    if (!isset($_POST["csrf_token"]) || $_POST["csrf_token"] !== $_SESSION["csrf_token"]) {
        echo "<div class='alert alert-danger'>Invalid CSRF token. Please try again.</div>";
        exit();
    }

    $email = $_POST["email"];
    $password = $_POST["password"];
    require_once "database.php";
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_array($result);
    mysqli_stmt_close($stmt);

    if ($user) {
        if ($user["locked_until"] && intval($user["locked_until"]) > time()) {
            $remainingTime = intval($user["locked_until"]) - time();
            $minutes = ceil($remainingTime / 60);
            echo "<div class='alert alert-danger'>Account locked. Please try again in $minutes minutes.</div>";
            exit();
        }

        if (password_verify($password, $user["password"])) {
            $sql = "UPDATE users SET login_attempts = 0, locked_until = NULL WHERE email = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "s", $email);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
            $_SESSION["user"] = array("id" => $user["id"], "email" => $user["email"]);

            header("Location: index.php");
            exit();
        } else {
            incrementLoginAttempts($conn, $email, $maxLoginAttempts, $lockoutDuration);
            echo "<div class='alert alert-danger'>Password does not match</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>Email does not match</div>";
    }
}

function incrementLoginAttempts($conn, $email, $maxAttempts, $lockoutDuration)
{
    require_once "database.php";
    $sql = "UPDATE users SET login_attempts = login_attempts + 1 WHERE email = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    $sql = "SELECT login_attempts FROM users WHERE email = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    $loginAttempts = $row["login_attempts"];

    if ($loginAttempts >= $maxAttempts) {
        $lockoutTime = time() + $lockoutDuration;
        $sql = "UPDATE users SET login_attempts = 0, locked_until = ? WHERE email = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "is", $lockoutTime, $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
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
    <title>Login Form</title>
    <style>
        body {
            background-color: teal;
        }
    </style>
</head>
<body>
    <div class="container">
        
        <form action="login.php" method="post">
            <div class="form-group">
                <input type="email" placeholder="Enter Email:" name="email" class="form-control">
            </div>
            <div class="form-group">
                <input type="password" placeholder="Enter your Password:" name="password" id="password" class="form-control">
                <input type="checkbox" id="showPassword"> Show Password
            </div>
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION["csrf_token"]; ?>">
            <script>
                document.getElementById("showPassword").addEventListener("change", function() {
                    var passwordInput = document.getElementById("password");
                    if (this.checked) {
                        passwordInput.type = "text";
                    } else {
                        passwordInput.type = "password";
                    }
                });
            </script>
            <div class="form-btn">
                <input type="submit" value="Login" name="login" class="btn btn-primary">
            </div>
        </form>
        <div>
            <p>Not registered yet? <a href="register.php">Register over here.</a></p>
        </div>
    </div>
</body>
</html>