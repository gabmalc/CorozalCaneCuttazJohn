<?php
session_start();



if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    $stmt = $conn->prepare("SELECT username, password FROM user_info WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        
        // Assuming passwords are stored as plain text for now
        if ($password === $user["password"]) {
            $_SESSION["loggedin"] = true;
            $_SESSION["username"] = $user["username"];
            
            echo "Login successful. Welcome, " . htmlspecialchars($_SESSION["username"]) . "!";
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
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body style="background-color: #000000">
<canvas class="background"></canvas>

    <br><br>
    <div class="container">
        <div class="form-container">
            <div class="login-form">
                <h2>Login</h2>
                <?php 
                if (!empty($login_err)) {
                    echo '<div class="error">' . htmlspecialchars($login_err) . '</div>';
                }
                ?>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username" required>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" required>
                    </div>
                    <input type="submit" value="Login" class="submit-btn">
                </form>
                <div class="logo-container">
                    <img src="/Users/caesarmelendez/Projects/Corozal Cane Cuttaz/assets/BHS.png" class="img-fluid" alt="bhs logo">
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
