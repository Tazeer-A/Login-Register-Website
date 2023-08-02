<style>
  
  nav {
    background-color: darkslategray;
    border-bottom: 1px solid #ccc;
    padding: 15px;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  ul {
    list-style-type: none;
    margin: 0;
    padding: 0;
    display: flex;
    align-items: center;
  }

  li {
    margin-right: 15px;
  }

  li a {
    text-decoration: none;
    padding: 8px 12px;
    border: 1px solid #ccc;
    border-radius: 5px;
    color: white;
    font-size: 16px;
  }

  li a:hover {
    background-color: #ccc;
  }
</style>

<nav>
  <ul>
    <li><a href="index.php">Home</a></li>
    <li><a href="sports-dash.php">Sports</a></li>
    <li><a href="newsdash.php">News</a></li>
    
    <!-- Add more menu items as needed -->
  </ul>
  <ul>
    <li><a href="Profile.php">View Profile</a></li>
    <li><a href="logout.php">Logout</a></li>

  </ul>
</nav>