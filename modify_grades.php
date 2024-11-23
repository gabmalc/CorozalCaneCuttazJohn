<?php
session_start();
include '../connection.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role_id'] != 3) {
    header("Location: ../login.php");
    exit();
}

$teacher_name = $_SESSION['name'];
$message = '';
$error = '';

// Get teacher's courses
$courses_query = "SELECT * FROM courses WHERE instructor = ?";
$stmt = $conn->prepare($courses_query);
$stmt->bind_param("s", $teacher_name);
$stmt->execute();
$courses_result = $stmt->get_result();

// Get all grade categories
$categories_query = "SELECT * FROM grade_categories ORDER BY category_name";
$categories = $conn->query($categories_query);

// Calculate course average if course is selected
$course_average = 0;
if (isset($_GET['course_id'])) {
    $selected_course_id = $_GET['course_id'];
    $avg_query = "SELECT AVG(g.score/a.total_points * 100) as course_avg
                  FROM grades g
                  JOIN assignments a ON g.assignment_id = a.assignment_id
                  WHERE a.course_id = ?";
    $stmt = $conn->prepare($avg_query);
    $stmt->bind_param("i", $selected_course_id);
    $stmt->execute();
    $avg_result = $stmt->get_result()->fetch_assoc();
    $course_average = number_format($avg_result['course_avg'] ?? 0, 0);
}

// Handle new assignment creation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_assignment'])) {
    $course_id = $_POST['course_id'];
    $category_id = $_POST['category_id'];
    $assignment_name = $_POST['assignment_name'];
    $total_points = $_POST['total_points'];
    $max_points = $_POST['max_points'];
    $due_date = $_POST['due_date'];
    $bonus_points = $_POST['bonus_points'] ?? 0;
    $comments = $_POST['comments'] ?? '';

    $stmt = $conn->prepare("INSERT INTO assignments (course_id, category_id, assignment_name, total_points, 
                           max_points, due_date, bonus_points, comments, created_date) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("iisiiiis", $course_id, $category_id, $assignment_name, $total_points, 
                      $max_points, $due_date, $bonus_points, $comments);

    if ($stmt->execute()) {
        $message = "Assignment added successfully";
    } else {
        $error = "Error adding assignment";
    }
}

// Handle grade updates
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_grade'])) {
    $assignment_id = $_POST['assignment_id'];
    $user_id = $_POST['user_id'];
    $score = $_POST['score'];
    $bonus_earned = $_POST['bonus_earned'] ?? 0;
    $absent = isset($_POST['absent']) ? 1 : 0;
    $comments = $_POST['comments'] ?? '';

    // Start transaction
    $conn->begin_transaction();

    try {
        // Check if grade exists
        $check_stmt = $conn->prepare("SELECT grade_id, score FROM grades WHERE assignment_id = ? AND user_id = ?");
        $check_stmt->bind_param("ii", $assignment_id, $user_id);
        $check_stmt->execute();
        $existing_grade = $check_stmt->get_result()->fetch_assoc();

        if ($existing_grade) {
            // Add to grade history
            $hist_stmt = $conn->prepare("INSERT INTO grade_history (grade_id, old_score, new_score, changed_date) 
                                       VALUES (?, ?, ?, NOW())");
            $hist_stmt->bind_param("idd", $existing_grade['grade_id'], $existing_grade['score'], $score);
            $hist_stmt->execute();

            // Update grade
            $update_stmt = $conn->prepare("UPDATE grades SET score = ?, earned_bonus_points = ?, 
                                         is_absent = ?, comments = ?, date_submitted = NOW() 
                                         WHERE assignment_id = ? AND user_id = ?");
            $update_stmt->bind_param("diisii", $score, $bonus_earned, $absent, $comments, 
                                   $assignment_id, $user_id);
            $update_stmt->execute();
        } else {
            // Insert new grade
            $insert_stmt = $conn->prepare("INSERT INTO grades (assignment_id, user_id, score, 
                                         earned_bonus_points, is_absent, comments, date_submitted) 
                                         VALUES (?, ?, ?, ?, ?, ?, NOW())");
            $insert_stmt->bind_param("iidiis", $assignment_id, $user_id, $score, $bonus_earned, 
                                   $absent, $comments);
            $insert_stmt->execute();
        }

        $conn->commit();
        $message = "Grade updated successfully";
    } catch (Exception $e) {
        $conn->rollback();
        $error = "Error updating grade: " . $e->getMessage();
    }
}

// Delete assignment
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_assignment'])) {
    $assignment_id = $_POST['assignment_id'];

    $conn->begin_transaction();

    try {
        // Delete grade history
        $stmt = $conn->prepare("DELETE FROM grade_history WHERE grade_id IN 
                              (SELECT grade_id FROM grades WHERE assignment_id = ?)");
        $stmt->bind_param("i", $assignment_id);
        $stmt->execute();

        // Delete grades
        $stmt = $conn->prepare("DELETE FROM grades WHERE assignment_id = ?");
        $stmt->bind_param("i", $assignment_id);
        $stmt->execute();

        // Delete assignment
        $stmt = $conn->prepare("DELETE FROM assignments WHERE assignment_id = ?");
        $stmt->bind_param("i", $assignment_id);
        $stmt->execute();

        $conn->commit();
        $message = "Assignment deleted successfully";
    } catch (Exception $e) {
        $conn->rollback();
        $error = "Error deleting assignment: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grade Management</title>
    <link rel="stylesheet" href="../styles/teacher.css">
    <style>
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .course-header {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f5f5f5;
            border-radius: 5px;
        }
        
        .tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }
        
        .tab {
            padding: 10px 20px;
            background-color: #f0f0f0;
            border: 1px solid #ddd;
            cursor: pointer;
        }
        
        .tab.active {
            background-color: #fff;
            border-bottom: none;
        }
        
        .grade-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .grade-table th,
        .grade-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        
        .grade-table th {
            background-color: #f5f5f5;
        }
        
        .grade-table tbody tr:hover {
            background-color: #f9f9f9;
        }
        
        .student-name {
            color: #2196F3;
            cursor: pointer;
        }
        
        .grade-input {
            width: 60px;
            padding: 4px;
            text-align: center;
        }
        
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        
        .modal-content {
            background-color: white;
            margin: 15% auto;
            padding: 20px;
            width: 80%;
            max-width: 500px;
            border-radius: 5px;
            position: relative;
        }
    </style>
</head>
<body>

        <form action="teacher.php">
            <input type="submit" value="Back">
        </form>
    <div class="container">
    <div class="course-header">
    <h2>
        <?php 
        if (isset($_GET['course_id']) && $_GET['course_id'] != ''): 
            $course_query = "SELECT course_name FROM courses WHERE course_id = ?";
            $stmt = $conn->prepare($course_query);
            $stmt->bind_param("i", $_GET['course_id']);
            $stmt->execute();
            $course_name = $stmt->get_result()->fetch_assoc()['course_name'];
            echo htmlspecialchars(strtoupper($course_name)) . ", Sem: 1";
        else:
            echo "NO COURSE SELECTED";
        endif;
        ?>
    </h2>
    <div>Course Average: 
        <?php 
        if (isset($_GET['course_id']) && $_GET['course_id'] != ''): 
            echo $course_average . '%';
        else:
            echo "N/A";
        endif;
        ?>
    </div>
</div>

        <!-- Course Selection -->
        <form method="get" id="courseForm">
    <select name="course_id" onchange="this.form.submit()">
        <option value="">Select Course</option>
        <?php while ($course = $courses_result->fetch_assoc()): ?>
            <option value="<?php echo $course['course_id']; ?>"
                <?php echo (isset($_GET['course_id']) && $_GET['course_id'] == $course['course_id']) ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($course['course_name']); ?>
            </option>
        <?php endwhile; ?>
    </select>
</form>

        <?php if (isset($_GET['course_id']) && $_GET['course_id'] != ''): 
 ?>
            <!-- Category Tabs -->
            <div class="tabs">
                <?php 
                $categories->data_seek(0);
                while ($category = $categories->fetch_assoc()): ?>
                    <div class="tab <?php echo (isset($_GET['category_id']) && $_GET['category_id'] == $category['category_id']) ? 'active' : ''; ?>"
                         onclick="location.href='?course_id=<?php echo $_GET['course_id']; ?>&category_id=<?php echo $category['category_id']; ?>'">
                        <?php echo strtoupper($category['category_name']); ?>
                    </div>
                <?php endwhile; ?>
            </div>

            <?php
            if (isset($_GET['category_id'])) {
                $selected_category = $_GET['category_id'];
                
                // Get category info
                $cat_query = "SELECT * FROM grade_categories WHERE category_id = ?";
                $stmt = $conn->prepare($cat_query);
                $stmt->bind_param("i", $selected_category);
                $stmt->execute();
                $category_info = $stmt->get_result()->fetch_assoc();
                
                // Get assignments for this category
                $assignments_query = "SELECT a.*, 
                                    (SELECT AVG(g.score/a.total_points * 100) 
                                     FROM grades g 
                                     WHERE g.assignment_id = a.assignment_id) as avg_score
                                    FROM assignments a 
                                    WHERE a.course_id = ? AND a.category_id = ?
                                    ORDER BY a.due_date";
                $stmt = $conn->prepare($assignments_query);
                $stmt->bind_param("ii", $_GET['course_id'], $selected_category);
                $stmt->execute();
                $assignments = $stmt->get_result();
                ?>

                <div class="category-content">
                    <h3><?php echo strtoupper($category_info['category_name']); ?> Average: 
                        <?php
                        $category_avg_query = "SELECT AVG(g.score/a.total_points * 100) as cat_avg
                                             FROM grades g
                                             JOIN assignments a ON g.assignment_id = a.assignment_id
                                             WHERE a.course_id = ? AND a.category_id = ?";
                        $stmt = $conn->prepare($category_avg_query);
                        $stmt->bind_param("ii", $_GET['course_id'], $selected_category);
                        $stmt->execute();
                        $cat_avg = $stmt->get_result()->fetch_assoc()['cat_avg'];
                        echo number_format($cat_avg ?? 0, 0);
                        ?>
                    </h3>

                    <table class="grade-table">
                        <!-- Update the table header section: -->
<thead>
    <tr>
        <th>Description</th>
        <th>Date given</th>
        <th>Due Date</th>
        <th>Grade</th>
        <th>Bonus pts</th>
        <th>Max. Pts</th>
        <th>Max. Bon.</th>
        <th>%</th>
        <th>Absent</th>
        <th>Comments</th>
        <th>Actions</th>
    </tr>
</thead>
<tbody>
    <?php while ($assignment = $assignments->fetch_assoc()): 
        // Calculate the percentage for this assignment
        $percentage = 0;
        if ($assignment['max_points'] > 0) {
            $total_scored = $assignment['total_points'];
            $percentage = round(($total_scored / $assignment['max_points']) * 100);
        }
    ?>
        <tr>
            <td class="student-name" onclick="showStudentGrades(<?php echo $assignment['assignment_id']; ?>)">
                <?php echo htmlspecialchars($assignment['assignment_name']); ?>
            </td>
            <td><?php echo date('Y-m-d', strtotime($assignment['created_date'])); ?></td>
            <td><?php echo date('Y-m-d', strtotime($assignment['due_date'])); ?></td>
            <td><?php echo $assignment['total_points']; ?></td>
            <td><?php echo $assignment['bonus_points']; ?></td>
            <td><?php echo $assignment['max_points']; ?></td>
            <td><?php echo $assignment['max_bonus']; ?></td>
            <td><?php echo $percentage; ?>%</td>
            <td>NO</td>
            <td><?php echo htmlspecialchars($assignment['comments']); ?></td>
            <td>
                <button onclick="editAssignment(<?php echo $assignment['assignment_id']; ?>)">Edit</button>
                <button onclick="deleteAssignment(<?php echo $assignment['assignment_id']; ?>)">Delete</button>
            </td>
        </tr>
    <?php endwhile; ?>
</tbody>

<!-- Update the Add Assignment Modal form - remove percentage worth field -->
<div id="addAssignmentModal" class="modal">
    <div class="modal-content">
        <h3>Add New Assignment</h3>
        <form method="post" class="grade-form">
            <input type="hidden" name="course_id" value="<?php echo $_GET['course_id'] ?? ''; ?>">
            <input type="hidden" name="category_id" value="<?php echo $_GET['category_id'] ?? ''; ?>">
            
            <div class="form-group">
                <label>Description:</label>
                <input type="text" name="assignment_name" required>
            </div>
            
            <div class="form-group">
                <label>Total Points:</label>
                <input type="number" name="total_points" required>
            </div>
            
            <div class="form-group">
                <label>Max Points:</label>
                <input type="number" name="max_points" required>
            </div>
            
            <div class="form-group">
                <label>Max Bonus Points:</label>
                <input type="number" name="bonus_points" value="0">
            </div>
            
            <div class="form-group">
                <label>Due Date:</label>
                <input type="date" name="due_date" required>
            </div>
            
            <div class="form-group">
                <label>Comments:</label>
                <textarea name="comments"></textarea>
            </div>
            
            <button type="submit" name="add_assignment">Add</button>
            <button type="button" onclick="hideAddAssignmentModal()">Cancel</button>
        </form>
    </div>
</div>
                    </table>

                    <button onclick="showAddAssignmentModal()">Add <?php echo ucfirst($category_info['category_name']); ?></button>
                </div>
            <?php } ?>
        <?php endif; ?>
    </div>

    <!-- Add Assignment Modal -->
    <div id="addAssignmentModal" class="modal">
        <div class="modal-content">
            <h3>Add New Assignment</h3>
            <form method="post" class="grade-form">
                <input type="hidden" name="course_id" value="<?php echo $_GET['course_id'] ?? ''; ?>">
                <input type="hidden" name="category_id" value="<?php echo $_GET['category_id'] ?? ''; ?>">
                
                <div class="form-group">
                    <label>Description:</label>
                    <input type="text" name="assignment_name" required>
                </div>
                
                <div class="form-group">
                    <label>Total Points:</label>
                    <input type="number" name="total_points" required>
                </div>
                
                <div class="form-group">
                    <label>Max Points:</label>
                    <input type="number" name="max_points" required>
                </div>
                
                <div class="form-group">
                    <label>Max Bonus Points:</label>
                    <input type="number" name="bonus_points" value="0">
                </div>

                <div class="form-group">
                    <label>Percentage Worth:</label>
                    <input type="number" name="percent_worth" step="0.01" required>
                </div>
                
                <div class="form-group">
                    <label>Due Date:</label>
                    <input type="date" name="due_date" required>
                </div>
                
                <div class="form-group">
                    <label>Comments:</label>
                    <textarea name="comments"></textarea>
                </div>
                
                <button type="submit" name="add_assignment">Add</button>
                <button type="button" onclick="hideAddAssignmentModal()">Cancel</button>
            </form>
        </div>
    </div>

    <!-- Student Grades Modal -->
    <div id="studentGradesModal" class="modal">
        <div class="modal-content">
            <h3>Student Grades</h3>
            <div id="studentGradesList">
                <!-- Will be populated by JavaScript -->
            </div>
            <button onclick="hideStudentGradesModal()">Close</button>
        </div>
    </div>

    <!-- Edit Grade Modal -->
    <div id="editGradeModal" class="modal">
        <div class="modal-content">
            <h3>Edit Grade</h3>
            <form method="post" id="editGradeForm">
                <input type="hidden" name="assignment_id" id="editAssignmentId">
                <input type="hidden" name="user_id" id="editUserId">
                
                <div class="form-group">
                    <label>Grade:</label>
                    <input type="number" name="score" id="editScore" step="0.1" required>
                </div>
                
                <div class="form-group">
                    <label>Bonus Points:</label>
                    <input type="number" name="bonus_earned" id="editBonus" value="0">
                </div>
                
                <div class="form-group">
                    <label>Absent:</label>
                    <input type="checkbox" name="absent" id="editAbsent">
                </div>
                
                <div class="form-group">
                    <label>Comments:</label>
                    <textarea name="comments" id="editComments"></textarea>
                </div>
                
                <button type="submit" name="update_grade">Save</button>
                <button type="button" onclick="hideEditGradeModal()">Cancel</button>
            </form>
        </div>
    </div>

    <script src="../scripts/modify-class.js">
    </script>

    <style>
        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .form-group input[type="text"],
        .form-group input[type="number"],
        .form-group input[type="date"],
        .form-group textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .student-grade-item {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .student-grade-item:last-child {
            border-bottom: none;
        }

        button {
            background-color: #4CAF50;
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-right: 5px;
        }

        button:hover {
            background-color: #45a049;
        }

        button.delete-btn {
            background-color: #f44336;
        }

        button.delete-btn:hover {
            background-color: #da190b;
        }
    </style>
</body>
</html>