<?php
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: index.php");
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
            color: black;
        }

        /* Additional CSS styling as needed */
        .rss-feed {
            margin-top: 20px;
        }

        /* CSS class for the title of the RSS feed */
        .rss-title {
            color: #000; /* Set the title text color to black */
        }

        /* CSS class for the description of the RSS feed */
        .rss-description {
            color: #000; /* Set the description text color to black */
        }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>
    <div class="container">
        <div class="text-center mt-5">
            <h1>The User News Dashboard</h1>
        </div>

        <!-- RSS feed selection -->
        <div class="mt-5">
            <h2 class="text-center">Select an News Feed</h2>
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="text-center">
                <select name="rss_feed" id="rss_feed" class="form-select mt-3">
                    <option value="">Select a Feed</option>
                    <option value="https://feeds.skynews.com/feeds/rss/uk.xml">UK</option>
                    <option value="https://feeds.skynews.com/feeds/rss/world.xml">World</option>
                    <option value="https://feeds.skynews.com/feeds/rss/business.xml">Business</option>
                    <option value="https://feeds.skynews.com/feeds/rss/politics.xml">Politics</option>
                    <option value="https://feeds.skynews.com/feeds/rss/us.xml">US</option>
                    <option value="https://feeds.skynews.com/feeds/rss/technology.xml">Technology</option>
                    <option value="https://feeds.skynews.com/feeds/rss/entertainment.xml">Entertainment</option>
                   
                </select>
                <input type="submit" value="Get News" class="btn btn-primary mt-3">
            </form>
        </div>

        <!-- Display selected RSS feed news -->
        <div class="mt-5">
            <?php
            if ($_SERVER["REQUEST_METHOD"] === "POST") {
                $selectedFeed = $_POST["rss_feed"];

                if (!empty($selectedFeed)) {
                    $rss = simplexml_load_file($selectedFeed);

                    if ($rss !== false) {
                        echo "<div class='rss-feed'>";
                        echo "<h4 class='text-center rss-title'>Latest News</h4>";
                        

                        foreach ($rss->channel->item as $item) {
                            $title = $item->title;
                            $link = $item->link;
                            $description = $item->description;

                            echo "<li>";
                            echo "<h3><a href='$link' class='rss-title'>$title</a></h3>";
                            echo "<p class='rss-description'>$description</p>";
                            echo "</li>";
                        }

                        echo "</ul>";
                    } else {
                        echo "Failed to load the RSS feed.";
                    }
                } else {
                    echo "Please select an RSS feed.";
                }
            }
            ?>
        </div>
    </div>
</body>
</html>