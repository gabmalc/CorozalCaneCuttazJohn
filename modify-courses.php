<?php
session_start();
include '../connection.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role_id'] != 1) {
    header("Location: ../login.php");
    exit();
}

// Initial query to fetch courses
$sql = "SELECT c.*, u.name as instructor_name 
        FROM courses c 
        LEFT JOIN user_info u ON c.instructor = u.name 
        ORDER BY c.course_name";
$result = $conn->query($sql);

if (!$result) {
    die("Query failed: " . $conn->error);
}

// Add database transaction and verification when adding courses
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_course'])) {
        $course_name = $_POST['course_name'];
        $credits = $_POST['credits'];
        
        $conn->begin_transaction();
        try {
            // Check for existing course
            $checkStmt = $conn->prepare("SELECT course_id FROM courses WHERE course_name = ?");
            $checkStmt->bind_param("s", $course_name);
            $checkStmt->execute();
            
            if ($checkStmt->get_result()->num_rows > 0) {
                echo json_encode(['status' => 'error', 'message' => 'Course name already exists']);
                exit();
            }

            // Add the course
            $stmt = $conn->prepare("INSERT INTO courses (course_name, credits, instructor) VALUES (?, ?, '')");
            $stmt->bind_param("ss", $course_name, $credits);
            
            if ($stmt->execute()) {
                $course_id = $conn->insert_id;
                $verify_stmt = $conn->prepare("SELECT * FROM courses WHERE course_id = ?");
                $verify_stmt->bind_param("i", $course_id);
                $verify_stmt->execute();
                
                if ($verify_stmt->get_result()->num_rows > 0) {
                    $conn->commit();
                    echo json_encode(['status' => 'success']);
                } else {
                    throw new Exception("Course verification failed");
                }
            } else {
                throw new Exception("Failed to add course");
            }
        } catch (Exception $e) {
            $conn->rollback();
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        exit();
    }

    if (isset($_POST['update_course'])) {
        $course_id = $_POST['course_id'];
        $field = $_POST['field'];
        $value = $_POST['value'];
        
        $conn->begin_transaction();
        try {
            if ($field === 'course_name') {
                $checkStmt = $conn->prepare("SELECT course_id FROM courses WHERE course_name = ? AND course_id != ?");
                $checkStmt->bind_param("si", $value, $course_id);
                $checkStmt->execute();
                
                if ($checkStmt->get_result()->num_rows > 0) {
                    throw new Exception("Course name already exists");
                }
            }

            $sql = "UPDATE courses SET $field = ? WHERE course_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $value, $course_id);
            
            if ($stmt->execute()) {
                $verify_stmt = $conn->prepare("SELECT * FROM courses WHERE course_id = ? AND $field = ?");
                $verify_stmt->bind_param("is", $course_id, $value);
                $verify_stmt->execute();
                
                if ($verify_stmt->get_result()->num_rows > 0) {
                    $conn->commit();
                    echo json_encode(['status' => 'success']);
                } else {
                    throw new Exception("Course update verification failed");
                }
            } else {
                throw new Exception("Update failed");
            }
        } catch (Exception $e) {
            $conn->rollback();
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        exit();
    }

    if (isset($_POST['delete_course'])) {
        $course_id = $_POST['course_id'];
        
        $stmt = $conn->prepare("DELETE FROM courses WHERE course_id = ?");
        $stmt->bind_param("i", $course_id);
        
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Delete failed']);
        }
        exit();
    }
}

?>
<style>.custom-navbar {
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
    }</style>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Courses</title>
    <link rel="stylesheet" href="../styles/modify-courses.css" href="../styles/navbar-layout.css">
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
    <h2 style="text-align: center;">Manage Courses</h2>

    <div style="text-align: center; margin-bottom: 20px;">
      <center> <a href="admin.php"><button type="button">Back</button></a>
      </center>  </div>

    <!-- Add Course Form -->
    <form id="addCourseForm">
        <h3>Add New Course</h3>
        <input type="text" name="course_name" placeholder="Enter Course Name" required>
        <div style="margin: 10px 0;">
            <label for="credits">Credits:</label>
            <input type="number" name="credits" class="credits-input" min="1" max="10" value="6" required>
        </div>
        <button type="submit" name="add_course">Add Course</button>
    </form>

    <!-- Courses Table -->
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Course Name</th>
                <th>Credits</th>
                <th>Current Instructor</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr id="course-<?php echo $row['course_id']; ?>">
                    <td><?php echo $row['course_id']; ?></td>
                    <td>
                        <span class="editable"
                              onclick="makeEditable(this, <?php echo $row['course_id']; ?>, 'course_name')"
                              data-original="<?php echo htmlspecialchars($row['course_name']); ?>">
                            <?php echo htmlspecialchars($row['course_name']); ?>
                        </span>
                    </td>
                    <td>
                        <span class="editable"
                              onclick="makeEditable(this, <?php echo $row['course_id']; ?>, 'credits', 'number')"
                              data-original="<?php echo htmlspecialchars($row['credits']); ?>">
                            <?php echo htmlspecialchars($row['credits']); ?>
                        </span>
                    </td>
                    <td>
                        <?php echo $row['instructor'] ? htmlspecialchars($row['instructor']) : 'No instructor assigned'; ?>
                    </td>
                    <td>
                        <button class="delete-btn" onclick="deleteCourse(<?php echo $row['course_id']; ?>)">Delete</button>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <script src="../scripts/modify-courses.js"></script>
</body>
</html>