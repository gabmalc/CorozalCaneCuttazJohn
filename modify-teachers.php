<?php
session_start();
include '../connection.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../login.php");
    exit();
}

if ($_SESSION["role_id"] != 1) {
    header("Location: ../login.php");
    exit();
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Update teacher details
    if (isset($_POST['update_teacher'])) {
        $teacher_id = $_POST['teacher_id'];
        $field = $_POST['field'];
        $value = $_POST['value'];
        
        // Check for duplicate email or username
        if ($field === 'email' || $field === 'username') {
            $checkStmt = $conn->prepare("SELECT id FROM user_info WHERE ($field = ? AND id != ?)");
            $checkStmt->bind_param("si", $value, $teacher_id);
            $checkStmt->execute();
            $result = $checkStmt->get_result();
            
            if ($result->num_rows > 0) {
                echo json_encode(['status' => 'error', 'message' => ucfirst($field) . ' already exists']);
                exit();
            }
        }
        
        // Start transaction
        $conn->begin_transaction();
        
        try {
            // Update teacher info
            $sql = "UPDATE user_info SET $field = ? WHERE id = ? AND role_id = 3";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $value, $teacher_id);
            $stmt->execute();
            
            // If name is being updated, also update course assignments
            if ($field === 'name') {
                $updateCoursesStmt = $conn->prepare("UPDATE courses SET instructor = ? WHERE instructor = (SELECT name FROM user_info WHERE id = ?)");
                $updateCoursesStmt->bind_param("si", $value, $teacher_id);
                $updateCoursesStmt->execute();
            }
            
            $conn->commit();
            echo json_encode(['status' => 'success']);
        } catch (Exception $e) {
            $conn->rollback();
            echo json_encode(['status' => 'error', 'message' => 'Update failed']);
        }
        exit();
    }

    // Update course assignments
    if (isset($_POST['update_courses'])) {
        $teacher_id = $_POST['teacher_id'];
        $courses = json_decode($_POST['courses'], true);
        
        $conn->begin_transaction();
        
        try {
            // Get teacher name
            $nameStmt = $conn->prepare("SELECT name FROM user_info WHERE id = ?");
            $nameStmt->bind_param("i", $teacher_id);
            $nameStmt->execute();
            $teacher_name = $nameStmt->get_result()->fetch_assoc()['name'];
            
            // Clear existing assignments
            $clearStmt = $conn->prepare("UPDATE courses SET instructor = '' WHERE instructor = ?");
            $clearStmt->bind_param("s", $teacher_name);
            $clearStmt->execute();
            
            // Assign new courses
            if (!empty($courses)) {
                $assignStmt = $conn->prepare("UPDATE courses SET instructor = ? WHERE course_id = ?");
                foreach ($courses as $course_id) {
                    $assignStmt->bind_param("si", $teacher_name, $course_id);
                    $assignStmt->execute();
                }
            }
            
            $conn->commit();
            echo json_encode(['status' => 'success']);
        } catch (Exception $e) {
            $conn->rollback();
            echo json_encode(['status' => 'error', 'message' => 'Failed to update courses']);
        }
        exit();
    }

    // Add new teacher
    if (isset($_POST['add_teacher'])) {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $username = $_POST['username'];
        $password = password_hash('password1234', PASSWORD_DEFAULT);
        $courses = isset($_POST['courses']) ? $_POST['courses'] : [];
        
        // Check for duplicate email or username
        $checkStmt = $conn->prepare("SELECT id FROM user_info WHERE email = ? OR username = ?");
        $checkStmt->bind_param("ss", $email, $username);
        $checkStmt->execute();
        
        if ($checkStmt->get_result()->num_rows > 0) {
            $error = "Username or email already exists";
        } else {
            $conn->begin_transaction();
            
            try {
                // Insert teacher
                $stmt = $conn->prepare("INSERT INTO user_info (name, email, username, password, role_id, enrollment_id) VALUES (?, ?, ?, ?, 3, 0)");
                $stmt->bind_param("ssss", $name, $email, $username, $password);
                $stmt->execute();
                
                // Assign courses if any selected
                if (!empty($courses)) {
                    $courseStmt = $conn->prepare("UPDATE courses SET instructor = ? WHERE course_id = ?");
                    foreach ($courses as $course_id) {
                        $courseStmt->bind_param("si", $name, $course_id);
                        $courseStmt->execute();
                    }
                }
                
                $conn->commit();
                $success = "Teacher added successfully";
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            } catch (Exception $e) {
                $conn->rollback();
                $error = "Error adding teacher";
            }
        }
    }

    // Delete teacher
    if (isset($_POST['delete_teacher'])) {
        $teacher_id = $_POST['teacher_id'];
        
        $conn->begin_transaction();
        
        try {
            // Get teacher name first
            $nameStmt = $conn->prepare("SELECT name FROM user_info WHERE id = ?");
            $nameStmt->bind_param("i", $teacher_id);
            $nameStmt->execute();
            $teacher_name = $nameStmt->get_result()->fetch_assoc()['name'];
            
            // Clear course assignments
            $clearStmt = $conn->prepare("UPDATE courses SET instructor = '' WHERE instructor = ?");
            $clearStmt->bind_param("s", $teacher_name);
            $clearStmt->execute();
            
            // Delete teacher
            $deleteStmt = $conn->prepare("DELETE FROM user_info WHERE id = ? AND role_id = 3");
            $deleteStmt->bind_param("i", $teacher_id);
            $deleteStmt->execute();
            
            $conn->commit();
            echo json_encode(['status' => 'success']);
        } catch (Exception $e) {
            $conn->rollback();
            echo json_encode(['status' => 'error', 'message' => 'Delete failed']);
        }
        exit();
    }
}

// Fetch teachers with their course assignments
$sql = "SELECT u.id, u.name, u.email, u.username 
        FROM user_info u 
        WHERE u.role_id = 3 
        ORDER BY u.name";
$teachers_result = $conn->query($sql);

// Fetch all courses
$courses_sql = "SELECT * FROM courses ORDER BY course_name";
$courses_result = $conn->query($courses_sql);
$courses = $courses_result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Teachers</title>
    <link rel="stylesheet" href="../styles/admin-actions.css">
    <link rel="stylesheet" href="../styles/modify-students.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="styles/style2.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Schibsted+Grotesk:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">
    <style>
        .editable { cursor: pointer; padding: 5px; }
        .editable:hover { background-color: #fff; }
        .editing { padding: 5px; border: 1px solid #4CAF50; }
        .error { color: red; }
        .success { color: green; }
        .course-list { margin: 10px 0; }
        .course-list label { display: inline-block; margin-right: 10px; margin-bottom: 5px; }
        .default-password-note { color: #666; font-size: 0.9em; margin: 5px 0; }
    </style>
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
    <h2><center>Manage Teachers</center></h2>
    
    <div style="text-align: center; margin-bottom: 20px;">
        <center><a href="admin.php"><button type="button">Back</button></a></center>
    </div>

    <!-- Add Teacher Form -->
    <h2><center>New Teacher Form</center></h2>
    <form id="addTeacherForm" method="POST">
        <input type="text" name="name" placeholder="Enter Teacher Name" required>
        <input type="email" name="email" placeholder="Enter Teacher Email" required>
        <input type="text" name="username" placeholder="Enter Username" required>
        <p class="default-password-note">Note: Default password is set to "password1234"</p>
        
        <div class="course-list">
            <h4>Assign Courses:</h4>
            <?php foreach ($courses as $course): ?>
            <label>
                <input type="checkbox" name="courses[]" value="<?php echo $course['course_id']; ?>">
                <?php echo htmlspecialchars($course['course_name']); ?>
            </label>
            <?php endforeach; ?>
        </div>
        
        <button type="submit" name="add_teacher">Add Teacher</button>
    </form>

    <?php if (isset($error)): ?>
        <p class="error" style="text-align: center;"><?php echo $error; ?></p>
    <?php endif; ?>
    <?php if (isset($success)): ?>
        <p class="success" style="text-align: center;"><?php echo $success; ?></p>
    <?php endif; ?>

    <!-- Teachers Table -->
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Username</th>
                <th>Email</th>
                <th>Assigned Courses</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($teacher = $teachers_result->fetch_assoc()): ?>
                <tr id="teacher-<?php echo $teacher['id']; ?>">
                    <td><?php echo $teacher['id']; ?></td>
                    <td>
                        <span class="editable" 
                              onclick="makeEditable(this, <?php echo $teacher['id']; ?>, 'name')" 
                              data-original="<?php echo htmlspecialchars($teacher['name']); ?>">
                            <?php echo htmlspecialchars($teacher['name']); ?>
                        </span>
                    </td>
                    <td>
                        <span class="editable" 
                              onclick="makeEditable(this, <?php echo $teacher['id']; ?>, 'username')" 
                              data-original="<?php echo htmlspecialchars($teacher['username']); ?>">
                            <?php echo htmlspecialchars($teacher['username']); ?>
                        </span>
                    </td>
                    <td>
                        <span class="editable" 
                              onclick="makeEditable(this, <?php echo $teacher['id']; ?>, 'email')" 
                              data-original="<?php echo htmlspecialchars($teacher['email']); ?>">
                            <?php echo htmlspecialchars($teacher['email']); ?>
                        </span>
                    </td>
                    <td>
                        <div class="course-list">
                            <?php foreach ($courses as $course): ?>
                            <label>
                                <input type="checkbox" 
                                       class="course-checkbox" 
                                       data-teacher-id="<?php echo $teacher['id']; ?>"
                                       value="<?php echo $course['course_id']; ?>"
                                       <?php echo ($course['instructor'] == $teacher['name']) ? 'checked' : ''; ?>
                                       onchange="updateCourses(<?php echo $teacher['id']; ?>)">
                                <?php echo htmlspecialchars($course['course_name']); ?>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </td>
                    <td>
                        <button onclick="deleteTeacher(<?php echo $teacher['id']; ?>)">Delete</button>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    </body>
    </html>