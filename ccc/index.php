<?php
session_start();
if(!$_SESSION['loggedin']){
   header("Location:login.php");
}

?>
<style>
  table, th, td {
    border: 1px solid black;
    padding: 2px;
  }
</style>

<html lang="en">
<head>
  <title>Home Dashboard</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <link rel="stylesheet" href="style2.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
</head>
<body>
<div class="container">
 <h2>Cane Cuttaz Management System</h2>
  <ul class="nav nav-pills">
  <li class="active"><a data-toggle="pill" href="#home">Grades</a></li>
  <li><a data-toggle="pill" href="#menu1">GPA Calculator</a></li>
  <li><a data-toggle="pill" href="#menu2">Experiential Log</a></li>
  <li><a data-toggle="pill" href="#menu3">Announcements</a></li>
  <li><a data-toggle="pill" href="#menu4">Calendar</a></li>
  <li><a href="login.php">Logout</a></li>
  </ul>
</div>

<div class="tab-content">
  <div id="home" class="tab-pane fade in active">
    <h3>Grades</h3>
    <ul class="nav nav-pills">
  <li class="active"><a data-toggle="pill" href="#home">Math</a></li>
  <li><a data-toggle="pill" href="#menu1">English</a></li>
  <li><a data-toggle="pill" href="#menu2">Web App Development</a></li>
  <li><a data-toggle="pill" href="#menu3">Operating Systems</a></li>
  <li><a data-toggle="pill" href="#menu4">Physics</a></li>
  <li><a data-toggle="pill" href="#menu5">Literature</a></li>
  <li><a data-toggle="pill" href="#menu6">Spanish</a></li>
  <li><a data-toggle="pill" href="#menu7">Music</a></li>
  <li><a data-toggle="pill" href="#menu8">Physcial Education</a></li>
  <li><a data-toggle="pill" href="#menu9">Sociology</a></li>
  <li><a data-toggle="pill" href="#menu10">Information Technology</a></li>
  <li><a href="login.php">Logout</a></li>
  </ul>
  <div id="class1"></div>
  </div>
  <div id="menu1" class="tab-pane fade">
    <h3>GPA Calculator</h3>
    <table style="width:100%">
      <tr>
        <th>aaa</th>
        <th>bbb</th>
        <th>ccc</th>
      </tr>
      <tr>
        <td></td>
        <th>eee</th>
        <th>fff</th>
      </tr>
    </table>
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
