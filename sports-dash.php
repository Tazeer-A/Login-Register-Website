<?php
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: index.php");
}


if ($_SERVER["REQUEST_METHOD"] === "POST") {
   
    $favoriteTeam = $_POST["favorite_team"];

    
    $_SESSION["favorite_team"] = $favoriteTeam;
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
    <title>User Dashboard</title>
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
            <h1>The User Sports Dashboard</h1>
        </div>

        <!-- Choose favorite team section -->
        <div class="mt-5">
            <h2 class="text-center">Choose Your Favorite Football Team</h2>
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="text-center">
                <select name="favorite_team" id="favorite_team" class="form-select mt-3">
                    <option value="">Select Team</option>
                    <option value="Barcelona">Barcelona</option>
                    <option value="Real Madrid">Real Madrid</option>
                    <option value="Liverpool">Liverpool</option>
                    <option value="Manchester United">Manchester United</option>
                  
                </select>
                <input type="submit" value="Save" class="btn btn-primary mt-3">
            </form>
        </div>

        <!-- Display favorite team -->
        <div class="mt-5">
            <?php
            if (isset($_SESSION["favorite_team"])) {
                echo "<h3 class='text-center'>Your Favorite Team: " . $_SESSION["favorite_team"] . "</h3>";

                $rssFeeds = array(
                    "Barcelona" => "https://fcbarcelonasoccer.webnode.page/rss/news-.xml",
                    "Real Madrid" => "https://www.footballcritic.com/rss/?team=real+madrid",
                    "Liverpool" => "https://www.liverpool.com/?service=rss",
                    "Manchester United" => "https://www.manchestereveningnews.co.uk/all-about/manchester%20united%20fc?service=rss"
                );

                $favoriteTeam = $_SESSION["favorite_team"];
                $rssFeed = $rssFeeds[$favoriteTeam];
                $rss = simplexml_load_file($rssFeed);

             
                echo "<div class='rss-feed'>";
                echo "<h4 class='text-center'>Latest News about " . $favoriteTeam . ":</h4>";
                foreach ($rss->channel->item as $item) {
                    $title = $item->title;
                    $link = $item->link;
                 
                    echo "<p><a href='$link'>$title</a></p>";
                    echo "</li>";
                }
                echo "</ul>";
              
            }
            ?>
        </div>
    </div>
</body>
</html>