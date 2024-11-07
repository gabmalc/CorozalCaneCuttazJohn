<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

include 'connection.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Home Dashboard</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <link rel="stylesheet" href="styles/style2.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>
  <div class="container">
    <h2 align="left">Cane Cuttaz Management System</h2>
    <h5>Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?></h5>
    <ul class="nav nav-pills nav-justified">
      <li class="active"><a href="grades.php">Grades</a></li>
      <li><a data-toggle="pill" href="calc.php">GPA Calculator</a></li>
      <li><a data-toggle="pill" href="experiential-log.php">Experiential Log</a></li>
      <li><a data-toggle="pill" href="announcements.php">Announcements</a></li>
      <li><a data-toggle="pill" href="calendar.php">Calendar</a></li>
      <li><a href="login.php">Logout</a></li>
    </ul>
  </div>

  <div class="tab-content">
    <div id="home" class="tab-pane fade in active">
      <h3>Grades</h3>
      <div id="class1"></div>
    </div>
    <div id="menu1" class="tab-pane fade">
      <h3>GPA Calculator</h3>
      <p>Some content in menu 1.</p>
      <button class="compute-btn">Compute</button>
    </div>

    <div id="menu2" class="tab-pane fade">
      <h3>Experiential Log</h3>
      <p>Some content in menu 2.</p>
    </div>
    <div id="menu3" class="tab-pane fade">
      <h3>Announcements</h3>
      <p>Some content in menu 3.</p>
    </div>
    <div id="menu4" class="tab-pane fade">
      <h3>Calendar</h3>
      <p>Some content in menu 4.</p>
    </div>
  </div>
  <iframe src="grades.php" <iframe style="
    width: 100vw;
    height: 100vh;
    border: none;
    position: fixed;
    top: -1;
    left: 0;
  "></iframe></iframe>

  <script src="scripts/script.js"></script>
</body>
</html>
