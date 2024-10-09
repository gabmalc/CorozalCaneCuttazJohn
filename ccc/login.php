<?php
session_start();

// Include the database connection
include 'connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Prepare and execute the SQL statement
    $stmt = $conn->prepare("SELECT username, password FROM user_info WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        
        // Verify the hashed password
        if (password_verify($password, $user["password"])) {
            $_SESSION["loggedin"] = true;
            $_SESSION["username"] = $user["username"];
            
            header("Location: index.php");
            exit;
        } else {
            $login_err = "Invalid username or password.";
        }
    } else {
        $login_err = "Invalid username or password.";
    }

    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
</head>
<body style="background-color: #000000">
<canvas class="background"></canvas>

    <br><br>
    <div class="container-lg">
        <div class="row justify-content-center" style="margin-top: 6vw">
            <div class="col-md-5 text-center text-md-start">
                <div class="login-form">
                <h2 style="color: black;">Login</h2>
                <?php 
                if (!empty($login_err)) {
                    echo '<div style="color: red;">' . htmlspecialchars($login_err) . '</div>';
                }        
                ?>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="row">
                        <label>Username</label>
                        <input type="text" name="username" required>
                        <label>Password</label>
                        <input type="password" name="password" required>
                        <input style="margin-top: 1vw" type="submit" value="Login">

                        <p style ="margin-top: 0.5vw">Don't have an account yet?<br> <a href="signup.php">Sign up now</a></p>
                        <div style="margin-top: 1vw" class="col-md-5 text-center">
                            <img src="assets\bhs.png" class="img-fluid" alt="bhs logo">
                        </div>       
                    </div>
                </form>
                </div>
            </div>       
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/particlesjs/2.2.2/particles.min.js"></script>
    <script>
    window.onload = function() {
        var particles = Particles.init({
            selector: '.background',
            color: ['#FFD700', '#404B69', '#DBEDF3'],
            connectParticles: true
        });
    };
    </script>
</body>
</html>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
