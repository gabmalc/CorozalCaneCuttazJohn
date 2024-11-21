<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../login.php");
    exit();
}

if($_SESSION["role_id"] != 1){
    header("Location: ../login.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>
    <link rel="stylesheet" href="../styles/admin.css" href="../styles/navbar-layout.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="styles/style2.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Schibsted+Grotesk:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">
</head>
<body>
<nav class="custom-navbar navbar navbar-expand-lg nav-link" style="border-radius:0px; border-bottom: 2px solid #FFD700;">
    <div class="container-fluid">
        <div class="navbar-header">
            <a class="navbar-brand nav-link" href="admin.php" style="font-family:'Schibsted Grotesk'; font-weight: bolder">CCMM </a>
        </div>
        <ul class="nav navbar-nav">
        </ul>
    </div>
</nav>

<h2><center>Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?></center></h2>
<hr>
<br>
    <div class="grid-container">
    <form action="modify-students.php">
         <input type="submit" value="Modify Students" />
    </form> 
    <form action="modify-class.php">
         <input type="submit" value="Modify Class" />
    </form> 
    <form action="modify-users.php">
         <input type="submit" value="Modify Users" />
    </form> 
    <form action="modify-courses.php">
         <input type="submit" value="Modify Courses" />
    </form> 
    <form action="modify-teachers.php">
         <input type="submit" value="Modify Teachers" />
    </form> 
    <form action="print-report.php">
         <input type="submit" value="Print Report Cards" />
    </form> 
    <form action="print-transcripts.php">
         <input type="submit" value="Print Transcripts" />
    </form> 
    <form action="../login.php">
         <input type="submit" value="Logout" />
    </form> 
    <script src="../scripts/admin.js">
        
    </script>
    </div>
   
</body>
</html>
