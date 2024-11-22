<?php
session_start();
include '../connection.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role_id'] != 1) {
   header("Location: ../login.php");
   exit();
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
   if (isset($_POST['assign_class'])) {
       $student_id = $_POST['student_id'];
       $enrollment_id = $_POST['class_id'];
       
       $stmt = $conn->prepare("UPDATE user_info SET enrollment_id = ? WHERE id = ?");
       $stmt->bind_param("ii", $enrollment_id, $student_id);
       
       if ($stmt->execute()) {
           $message = "Student class updated successfully";
       } else {
           $error = "Error updating student class";
       }
   }
   // gives a course to a student
   if (isset($_POST['assign_course'])) {
       $student_id = $_POST['student_id'];
       $course_id = $_POST['course_id'];
       
       $check_stmt = $conn->prepare("SELECT id FROM e_course WHERE user_id = ? AND class = ?");
       $check_stmt->bind_param("ii", $student_id, $course_id);
       $check_stmt->execute();
       
       if ($check_stmt->get_result()->num_rows == 0) {
           $insert_stmt = $conn->prepare("INSERT INTO e_course (user_id, class) VALUES (?, ?)");
           $insert_stmt->bind_param("ii", $student_id, $course_id);
           
           if ($insert_stmt->execute()) {
               $message = "Course assigned successfully";
           } else {
               $error = "Error assigning course";
           }
       } else {
           $error = "Student is already enrolled in this course";
       }
   }

   if (isset($_POST['remove_course'])) {
       $student_id = $_POST['student_id'];
       $course_id = $_POST['course_id'];
       
       $delete_stmt = $conn->prepare("DELETE FROM e_course WHERE user_id = ? AND class = ?");
       $delete_stmt->bind_param("ii", $student_id, $course_id);
       
       if ($delete_stmt->execute()) {
           $message = "Course removed successfully";
       } else {
           $error = "Error removing course";
       }
   }
}

// Get all enrollment classes
$classes_query = "SELECT * FROM enrollment WHERE enrollment_id != 0 ORDER BY class_name";
$classes_result = $conn->query($classes_query);
$classes = [];
while ($class = $classes_result->fetch_assoc()) {
   $classes[] = $class;
}

// Get all courses
$courses = $conn->query("SELECT * FROM courses ORDER BY course_name");

function getStudentsByClass($class_id, $conn) {
   $stmt = $conn->prepare("SELECT * FROM user_info WHERE enrollment_id = ? AND role_id = 2 ORDER BY name");
   $stmt->bind_param("i", $class_id);
   $stmt->execute();
   return $stmt->get_result();
}

function getStudentCourses($student_id, $conn) {
   $stmt = $conn->prepare("SELECT c.* FROM courses c 
                          JOIN e_course e ON c.course_id = e.class 
                          WHERE e.user_id = ?");
   $stmt->bind_param("i", $student_id);
   $stmt->execute();
   return $stmt->get_result();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Class Management</title>
   <link rel="stylesheet" href="../styles/modify-class.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="styles/style2.css">
    <link href="https://fonts.googleapis.com/css2?family=Schibsted+Grotesk:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">
   <style>
       .remove-btn {
           background: #ff4444;
           color: white;
           padding: 2px 8px;
           border: none;
           border-radius: 3px;
           cursor: pointer;
           margin-left: 10px;
       }

       .course-item {
           display: flex;
           align-items: center;
           margin: 5px 0;
       }
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
   <div class="container">
       <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
           <h1>Class Management</h1>
           <a href="admin.php" class="back-button">Back</a>
       </div>

       <?php if (isset($message)): ?>
           <div class="message success"><?php echo $message; ?></div>
       <?php endif; ?>
       
       <?php if (isset($error)): ?>
           <div class="message error"><?php echo $error; ?></div>
       <?php endif; ?>

       <div class="class-grid">
           <?php foreach ($classes as $class): ?>
               <div class="class-card">
                   <div class="class-header">
                       <h2><?php echo htmlspecialchars($class['class_name']); ?></h2>
                   </div>

                   <div class="student-list">
                       <?php 
                       $students = getStudentsByClass($class['enrollment_id'], $conn);
                       if ($students->num_rows > 0):
                           while ($student = $students->fetch_assoc()):
                               $student_courses = getStudentCourses($student['id'], $conn);
                       ?>
                           <div class="student-card">
                               <h3><?php echo htmlspecialchars($student['name']); ?></h3>
                               <div class="course-list">
                                   <strong>Courses:</strong>
                                   <?php while ($course = $student_courses->fetch_assoc()): ?>
                                       <div class="course-item">
                                           <?php echo htmlspecialchars($course['course_name']); ?>
                                           <form method="POST" style="display: inline;">
                                               <input type="hidden" name="student_id" value="<?php echo $student['id']; ?>">
                                               <input type="hidden" name="course_id" value="<?php echo $course['course_id']; ?>">
                                               <button type="submit" name="remove_course" class="remove-btn">Remove</button>
                                           </form>
                                       </div>
                                   <?php endwhile; ?>
                               </div>
                               <form method="POST" style="margin-top: 10px;">
                                   <select name="course_id" required>
                                       <option value="">Add Course</option>
                                       <?php 
                                       $courses->data_seek(0);
                                       while ($course = $courses->fetch_assoc()): 
                                       ?>
                                           <option value="<?php echo $course['course_id']; ?>">
                                               <?php echo htmlspecialchars($course['course_name']); ?>
                                           </option>
                                       <?php endwhile; ?>
                                   </select>
                                   <input type="hidden" name="student_id" value="<?php echo $student['id']; ?>">
                                   <button type="submit" name="assign_course">Add Course</button>
                               </form>
                           </div>
                       <?php 
                           endwhile;
                       else:
                       ?>
                           <p>No students assigned</p>
                       <?php endif; ?>
                   </div>

                   <form class="add-student-form" method="POST" style="margin-top: 15px;">
                       <select name="student_id" required>
                           <option value="">Add Student to Class</option>
                           <?php 
                           $all_students = $conn->query("SELECT * FROM user_info WHERE role_id = 2 ORDER BY name");
                           while ($student = $all_students->fetch_assoc()):
                           ?>
                               <option value="<?php echo $student['id']; ?>">
                                   <?php echo htmlspecialchars($student['name']); ?>
                               </option>
                           <?php endwhile; ?>
                       </select>
                       <input type="hidden" name="class_id" value="<?php echo $class['enrollment_id']; ?>">
                       <button type="submit" name="assign_class">Add to <?php echo htmlspecialchars($class['class_name']); ?></button>
                   </form>
               </div>
           <?php endforeach; ?>
       </div>
   </div>
</body>
</html>