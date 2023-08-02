<?php
session_start();

if (!isset($_SESSION["user"]) || !isset($_SESSION["user"]["id"])) {
    header("Location: index.php");
    exit;
}

$userId = $_SESSION["user"]["id"];

require_once "database.php";
$conn = mysqli_connect($hostName, $dbUser, $dbPassword, $dbName);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $newName = $_POST["full_name"];
    $newEmail = $_POST["email"];

   
    $updateQuery = "UPDATE users SET full_name = '$newName', email = '$newEmail' WHERE id = $userId";
    $updateResult = mysqli_query($conn, $updateQuery);

    if ($updateResult) {
        
        $updateMessage = "Profile updated successfully!";
        header("Location: Profile.php");
        exit;
    } else {
       
        echo "Error updating profile: " . mysqli_error($conn);
    }
}

$query = "SELECT full_name, email FROM users WHERE id = $userId";
$result = mysqli_query($conn, $query);
$profile = mysqli_fetch_assoc($result);

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
    <title>User Profile</title>
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
            <h1>User Profile</h1>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-6">
                <form method="POST" action="profile.php">
                    <div class="mb-3">
                        <label for="full_name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo $profile['full_name']; ?>">
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo $profile['email']; ?>">
                    </div>
                    <button type="submit" class="btn btn-primary">Update Profile</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>