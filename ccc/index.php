<?php
session_start();
if(!$_SESSION['loggedin']){
   header("Location:login.php");
}
?>

<html lang="en">
<head>
  <title>Home Dashboard</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
 <H2>Cane Cuttaz Management System</H2>
  <ul class="nav nav-pills">
  <li class="active"><a data-toggle="pill" href="#home">Grades</a></li>
  <li><a data-toggle="pill" href="#menu1">GPA Calculator</a></li>
  <li><a data-toggle="pill" href="#menu2">Experiential Log</a></li>
  <li><a data-toggle="pill" href="#menu3">Announcements</a></li>
  <li><a data-toggle="pill" href="#menu4">Calendar</a></li>

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
</body>
</html>
