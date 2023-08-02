<?php
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: index.php");
}

$hostName = "localhost";
$dbUser = "root";
$dbPassword = "";
$dbName = "login_register";


try {
    $pdo = new PDO("mysql:host=$hostName;dbname=$dbName", $dbUser, $dbPassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!empty($_POST["new_password"]) && !empty($_POST["confirm_password"])) {
        $newPassword = $_POST["new_password"];
        $confirmPassword = $_POST["confirm_password"];
        $userId = $_SESSION["user"]["id"];

        if ($newPassword === $confirmPassword) {
           
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

          
            $stmt = $pdo->prepare("UPDATE users SET password = :password WHERE id = :id");
            $stmt->bindParam(":password", $hashedPassword);
            $stmt->bindParam(":id", $userId);

            if ($stmt->execute()) {
                $passwordUpdateMessage = "Password updated successfully.";
            } else {
                $passwordUpdateMessage = "Failed to update the password.";
            }
        } else {
            $passwordUpdateMessage = "New password and confirm password do not match.";
        }
    } else {
        $passwordUpdateMessage = "Please enter new password and confirm password.";
    }
}


$userId = $_SESSION["user"]["id"];
$stmt = $pdo->prepare("SELECT full_name, email FROM users WHERE id = :id");
$stmt->bindParam(":id", $userId);
$stmt->execute();
$profile = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <title>Change Your Password</title>
    <style>
        body {
            background-color: teal;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="container">
        <div class="text-center mt-5">
            <h1>Change Password</h1>
        </div>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="col-md-4 offset-md-4 mt-5">
            <div class="mb-3">
                <label for="new_password" class="form-label">New Password</label>
                <input type="password" class="form-control" id="new_password" name="new_password" required>
            </div>
            <div class="mb-3">
                <label for="confirm_password" class="form-label">Confirm Password</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-primary">Change Password</button>
            </div>
        </form>

        <?php
        if (isset($passwordUpdateMessage)) {
            echo '<div class="text-center mt-3">';
            echo '<p>' . $passwordUpdateMessage . '</p>';
            echo '</div>';
        }
        ?>
    </div>
</body>
</html>