<style>
    .iframe {
        width: 100vw;
        height: 100vh;
        border: none;
        position: fixed;
        top: 9vh;
        left: 0;
    }

    .custom-navbar {
        background-color: #161a2d;
        border-bottom: 2px solid #4f52ba;
    }

    .custom-navbar .nav-link {
        color: #fff;
        transition: color 0.3s;
    }

    .custom-navbar .nav-link:hover {
        color: #4f52ba;
    }

    .custom-navbar .nav-link.active {
        background-color: #4f52ba;
        color: #ffffff;
        border-radius: 4px;
        padding: 10px 15px;
        transition: background-color 0.3s ease, color 0.3s ease;
    }
    .nav-link {
        color: #fff !important;
    }
    .nav-link:hover {
        color: #404B69 !important;;
    }
    .nav-link.active-tab {
        color: #161a2d !important;
        background: #fff !important;
    }

    .navbar {
        box-shadow: none !important;
    }

    .navbar-brand {
        color: #fff !important;
    }
    .navbar-brand:hover {
        color: #FFD700 !important;
    }

</style>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="styles/style2.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <title>trsuttt</title>
</head>
<body>
<nav class="custom-navbar navbar navbar-expand-lg nav-link" style="border-radius:0px; border-bottom: 2px solid #FFD700;">
    <div class="container-fluid">
        <div class="navbar-header">
            <a class="navbar-brand nav-link" href="#">Corozal Cane Cuttaz </a>
        </div>
        <ul class="nav navbar-nav">
            <li><a class="nav-link" href="home.php">Home</a></li>
            <li><a class="nav-link active-tab" href="trust.php">Grades</a></li>
            <li><a class="nav-link" href="calc.php">GPA Calculator</a></li>
            <li><a class="nav-link" href="home.php">Experiential Log</a></li>
        </ul>
        <iframe src="grades.php" class="iframe"></iframe>
    </div>
</nav>

</body>
</html>