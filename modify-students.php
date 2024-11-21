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
    // Update student details
    if (isset($_POST['update_student'])) {
        $student_id = $_POST['student_id'];
        $field = $_POST['field'];
        $value = $_POST['value'];
        
        // Check for duplicate email or username
        if ($field === 'email' || $field === 'username') {
            $checkStmt = $conn->prepare("SELECT id FROM user_info WHERE ($field = ? AND id != ?)");
            $checkStmt->bind_param("si", $value, $student_id);
            $checkStmt->execute();
            $result = $checkStmt->get_result();
            
            if ($result->num_rows > 0) {
                echo json_encode(['status' => 'error', 'message' => ucfirst($field) . ' already exists']);
                exit();
            }
        }
        
        // Update the field
        $sql = "UPDATE user_info SET $field = ? WHERE id = ? AND role_id = 2";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $value, $student_id);
        
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Update failed']);
        }
        exit();
    }

    // Update class
    if (isset($_POST['update_class'])) {
        $student_id = $_POST['student_id'];
        $new_class = $_POST['new_class'];
        
        $stmt = $conn->prepare("UPDATE user_info SET enrollment_id = ? WHERE id = ? AND role_id = 2");
        $stmt->bind_param("ii", $new_class, $student_id);
        
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Class update failed']);
        }
        exit();
    }

    // Add new student
    if (isset($_POST['add_student'])) {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $username = $_POST['username'];
        $password = password_hash('password1234', PASSWORD_DEFAULT);
        
        // Check for duplicate email or username
        $checkStmt = $conn->prepare("SELECT id FROM user_info WHERE email = ? OR username = ?");
        $checkStmt->bind_param("ss", $email, $username);
        $checkStmt->execute();
        if ($checkStmt->get_result()->num_rows > 0) {
            $error = "Username or email already exists";
        } else {
            $stmt = $conn->prepare("INSERT INTO user_info (name, email, username, password, role_id, enrollment_id) VALUES (?, ?, ?, ?, 2, 11)");
            $stmt->bind_param("ssss", $name, $email, $username, $password);
            if ($stmt->execute()) {
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            }
        }
    }

    // Delete student
    if (isset($_POST['delete_student'])) {
        $student_id = $_POST['student_id'];
        $stmt = $conn->prepare("DELETE FROM user_info WHERE id = ? AND role_id = 2");
        $stmt->bind_param("i", $student_id);
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Delete failed']);
        }
        exit();
    }
    if (isset($_POST['batch_add'])) {
        $students = explode("\n", trim($_POST['student_list']));
        $results = [];
        
        foreach ($students as $student) {
            $student = trim($student);
            if (empty($student)) continue;
            
            list($name, $email) = array_pad(explode(',', $student), 2, '');
            $name = trim($name);
            $email = trim($email);
            
            // Generate username from name
            $nameParts = explode(' ', strtolower($name));
            $firstName = $nameParts[0];
            $lastName = end($nameParts);
            $username = preg_replace('/[^a-z]/', '', $firstName . $lastName);
            
            // Check if username exists
            $checkStmt = $conn->prepare("SELECT id FROM user_info WHERE username = ?");
            $checkStmt->bind_param("s", $username);
            $checkStmt->execute();
            
            if ($checkStmt->get_result()->num_rows > 0) {
                $results[] = [
                    'status' => 'error',
                    'message' => "Username '$username' already exists for: $name"
                ];
                continue;
            }
            
            // Check email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $results[] = [
                    'status' => 'error',
                    'message' => "Invalid email format for: $name"
                ];
                continue;
            }
            
            $password = password_hash('password1234', PASSWORD_DEFAULT);
            $role_id = 2;
            $enrollment_id = 11;
            
            $stmt = $conn->prepare("INSERT INTO user_info (name, email, username, password, role_id, enrollment_id) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssii", $name, $email, $username, $password, $role_id, $enrollment_id);
            
            if ($stmt->execute()) {
                $results[] = [
                    'status' => 'success',
                    'message' => "Added: $name ($username)"
                ];
            } else {
                $results[] = [
                    'status' => 'error',
                    'message' => "Failed to add: $name"
                ];
            }
        }
        
        echo json_encode(['status' => 'complete', 'results' => $results]);
        exit();
    }
    
    if (isset($_POST['batch_delete'])) {
        $ids = json_decode($_POST['ids']);
        $deleted = [];
        $failed = [];
        
        foreach ($ids as $id) {
            $stmt = $conn->prepare("DELETE FROM user_info WHERE id = ? AND role_id = 2");
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                $deleted[] = $id;
            } else {
                $failed[] = $id;
            }
        }
        
        echo json_encode([
            'status' => 'complete',
            'deleted' => $deleted,
            'failed' => $failed
        ]);
        exit();
    }

}

// Fetch students
$sql = "SELECT id, name, email, username, enrollment_id FROM user_info WHERE role_id = 2";
if (isset($_GET['enrollment_id']) && $_GET['enrollment_id'] !== '') {
    $sql .= " AND enrollment_id = ?";
    $params[] = $_GET['enrollment_id'];
}
$sql .= " ORDER BY name";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param("i", ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Students</title>
    <link rel="stylesheet" href="../styles/admin-actions.css">
    <link rel="stylesheet" href="../styles/modify-students.css">
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
    <h2><center>Manage Students</center></h2>
    
    <div style="text-align: center; margin-bottom: 20px;">
       <center><a href="admin.php"><button type="button">Back</button></a></center> 
    </div>

    <!-- Add Student Form -->
    <div class="form-container">
    <!-- Single Student Add -->
    <div class="form-section">
        <h3><center>Add Single Student</center></h3>
        <form id="addStudentForm" method="POST">
            <input type="text" name="name" placeholder="Enter Student Name" required>
            <input type="email" name="email" placeholder="Enter Student Email" required>
            <input type="text" name="username" placeholder="Enter Username" required>
            <p style="color: #666; font-size: 0.9em; margin: 5px 0;">Note: Default password is set to "password1234"</p>
            <button type="submit" name="add_student">Add Student</button>
        </form>
    </div>

    <!-- Batch Add Students -->
    <div class="form-section">
        <h3><center>Batch Add Students</center></h3>
        <form id="batchAddForm" method="POST">
            <textarea name="student_list" rows="5" placeholder="Enter one student per line:&#10;FirstName LastName,Email&#10;Example:&#10;John Doe,johndoe@email.com" required></textarea>
            <br>
            <button type="submit" name="batch_add">Add Students</button>
        </form>
        <div id="batchAddResults" class="batch-results"></div>
    </div>
</div>


<?php if (isset($error)): ?>
    <p class="error" style="text-align: center;"><?php echo $error; ?></p>
<?php endif; ?>

    <!-- Class Filter -->
    <center> <form method="GET" style="text-align: center; margin: 20px;">
        <label for="classFilter">Filter by Class:</label>
         <select name="enrollment_id" id="classFilter" onchange="this.form.submit()">
            <option value="">All Classes</option>
            <option value="11" <?= (isset($_GET['enrollment_id']) && $_GET['enrollment_id'] == 11) ? 'selected' : '' ?>>Freshman 1</option>
            <option value="12" <?= (isset($_GET['enrollment_id']) && $_GET['enrollment_id'] == 12) ? 'selected' : '' ?>>Freshman 2</option>
            <option value="21" <?= (isset($_GET['enrollment_id']) && $_GET['enrollment_id'] == 21) ? 'selected' : '' ?>>Sophomore 1</option>
            <option value="22" <?= (isset($_GET['enrollment_id']) && $_GET['enrollment_id'] == 22) ? 'selected' : '' ?>>Sophomore 2</option>
            <option value="31" <?= (isset($_GET['enrollment_id']) && $_GET['enrollment_id'] == 31) ? 'selected' : '' ?>>Junior 1</option>
            <option value="32" <?= (isset($_GET['enrollment_id']) && $_GET['enrollment_id'] == 32) ? 'selected' : '' ?>>Junior 2</option>
            <option value="41" <?= (isset($_GET['enrollment_id']) && $_GET['enrollment_id'] == 41) ? 'selected' : '' ?>>Senior 1</option>
            <option value="42" <?= (isset($_GET['enrollment_id']) && $_GET['enrollment_id'] == 42) ? 'selected' : '' ?>>Senior 2</option>
        </select>
        
    </form>
    </center>
    <div class="batch-actions" style="margin: 20px 0; text-align: center;">
    <button onclick="toggleAllCheckboxes()" id="toggleAll">Select All</button>
        <br>
    <button onclick="batchDeleteSelected()" id="batchDelete" disabled>Delete Selected</button>
</div>

    <!-- Students Table -->
    <table>
    <thead>
        <tr>
            <th style="width: 50px;">
                <input type="checkbox" id="selectAll" onchange="toggleAllCheckboxes()">
            </th>
            <th>ID</th>
            <th>Name</th>
            <th>Username</th>
            <th>Email</th>
            <th>Class</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr id="student-<?php echo $row['id']; ?>">
                <td>
                    <input type="checkbox" class="student-checkbox" value="<?php echo $row['id']; ?>">
                </td>
                <td><?php echo $row['id']; ?></td>
                <td>
                    <span class="editable" 
                          onclick="makeEditable(this, <?php echo $row['id']; ?>, 'name')" 
                          data-original="<?php echo htmlspecialchars($row['name']); ?>">
                        <?php echo htmlspecialchars($row['name']); ?>
                    </span>
                </td>
                <td>
                    <span class="editable" 
                          onclick="makeEditable(this, <?php echo $row['id']; ?>, 'username')" 
                          data-original="<?php echo htmlspecialchars($row['username']); ?>">
                        <?php echo htmlspecialchars($row['username']); ?>
                    </span>
                </td>
                <td>
                    <span class="editable" 
                          onclick="makeEditable(this, <?php echo $row['id']; ?>, 'email')" 
                          data-original="<?php echo htmlspecialchars($row['email']); ?>">
                        <?php echo htmlspecialchars($row['email']); ?>
                    </span>
                </td>
                <td>
                    <select onchange="updateClass(this, <?php echo $row['id']; ?>)">
                        <option value="11" <?php echo $row['enrollment_id'] == 11 ? 'selected' : ''; ?>>Freshman 1</option>
                        <option value="12" <?php echo $row['enrollment_id'] == 12 ? 'selected' : ''; ?>>Freshman 2</option>
                        <option value="21" <?php echo $row['enrollment_id'] == 21 ? 'selected' : ''; ?>>Sophomore 1</option>
                        <option value="22" <?php echo $row['enrollment_id'] == 22 ? 'selected' : ''; ?>>Sophomore 2</option>
                        <option value="31" <?php echo $row['enrollment_id'] == 31 ? 'selected' : ''; ?>>Junior 1</option>
                        <option value="32" <?php echo $row['enrollment_id'] == 32 ? 'selected' : ''; ?>>Junior 2</option>
                        <option value="41" <?php echo $row['enrollment_id'] == 41 ? 'selected' : ''; ?>>Senior 1</option>
                        <option value="42" <?php echo $row['enrollment_id'] == 42 ? 'selected' : ''; ?>>Senior 2</option>
                    </select>
                </td>
                <td>
                    <button onclick="deleteStudent(<?php echo $row['id']; ?>)">Delete</button>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

    <script src="../scripts/modify-students.js">
    </script>
</body>
</html>