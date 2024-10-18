<?php
// signup.php
session_start();
include 'connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Check if the username already exists
    $stmt = $conn->prepare("SELECT id FROM user_info WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Username already exists, show an error message
        $signup_err = "Username already taken. Please choose a different one.";
    } else {
        // Hash the password and insert the new user
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO user_info (email, username, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $email, $username, $hashed_password);

        if ($stmt->execute()) {
            header("Location: login.php");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
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
    <title>Sign up</title>
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
</head>
<body style="background-color: #000000">
<canvas class="background"></canvas>

    <br><br>
    <div class="container-lg">
        <div class="row justify-content-center" style="margin-top: 6vw">
            <div class="col-md-5 text-center text-md-start">
                <div class="login-form">
                <h2 style="color: black;">Sign up</h2>
                <?php 
                if (!empty($signup_err)) {
                    echo '<div style="color: red;">' . htmlspecialchars($signup_err) . '</div>';
                }        
                ?>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="row">
                        <label>Email</label>
                        <input type="email" name="email" required>
                        <label>Username</label>
                        <input type="text" name="username" required>
                        <label>Password</label>
                        <input type="password" name="password" required>
                        <input style="margin-top: 1vw" type="submit" value="Sign up">
                        <p>Already have an account? <a href="login.php">Login now</a></p>
                        <div style="margin-top: 1vw" class="col-md-5 text-center">
                            <img src="assets/bhs.png" class="img-fluid" alt="bhs logo">
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
