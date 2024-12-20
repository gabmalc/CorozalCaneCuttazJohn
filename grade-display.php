<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

include '../../connection.php';

// Get the current user's ID from the session
$user_id = $_SESSION['id'];

// Fetch the student's enrolled courses
$enrolled_courses_query = "
    SELECT c.course_id, c.course_name, c.instructor 
    FROM courses c 
    INNER JOIN e_course ec ON c.course_id = ec.class 
    WHERE ec.user_id = ?";

$stmt = $conn->prepare($enrolled_courses_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result_courses = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Grades</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200">
    <style> .iframe {
            width: 100%;
            height: 91.3vh;
            border: none;
            position: sticky;
            top: 8vh;
            left: 0;
        }

       
/* Importing Google font - Poppins */
@import url("https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap");
/* Importing Google font - Poppins */
@import url("https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap");

* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: "Poppins", sans-serif;
}

body {
  min-height: 100vh;
  background: #f0f8ff;
}

.sidebar {
  position: fixed;
  top: -1;
  left: 0;
  height: 100%;
  width: 85px;
  display: flex;
  overflow-x: hidden;
  flex-direction: column;
  background: #161a2d;
  padding: 25px 20px;
  transition: all 0.4s ease;
}

.sidebar:hover {
  width: 260px;
}

.sidebar .sidebar-header {
  display: flex;
  align-items: center;
}

.sidebar .sidebar-header img {
  width: 42px !important;
  border-radius: 50% !important;
}

.sidebar .sidebar-header h2 {
  color: #FFD700;
  font-size: 1.25rem;
  font-weight: 600;
  white-space: nowrap;
  margin-left: 23px;
}

.sidebar-links h4 {
  color: #FFD700;
  font-weight: 500;
  white-space: nowrap;
  margin: 10px 0;
  position: relative;
}

.sidebar-links h4 span {
  opacity: 0;
}

.sidebar:hover .sidebar-links h4 span {
  opacity: 1;
}

.sidebar-links .menu-separator {
  position: absolute;
  left: 0;
  top: 50%;
  width: 100%;
  height: 1px;
  transform: scaleX(1);
  transform: translateY(-50%);
  background: #FFD700;
  transform-origin: right;
  transition-delay: 0.2s;
}

.sidebar:hover .sidebar-links .menu-separator {
  transition-delay: 0s;
  transform: scaleX(0);
}

.sidebar-links {
  list-style: none;
  margin-top: 20px;
  height: 80%;
  overflow-y: auto;
  scrollbar-width: none;
}

.sidebar-links::-webkit-scrollbar {
  display: none;
}

.sidebar-links li a {
  display: flex;
  align-items: center;
  gap: 0 20px;
  color: #fff;
  font-weight: 500;
  white-space: nowrap;
  padding: 15px 10px;
  text-decoration: none;
  transition: 0.2s ease;
}

.sidebar-links li a:hover {
  color: #161a2d;
  background: #fff;
  border-radius: 4px;
}

.user-account {
  margin-top: auto;
  padding: 12px 10px;
  margin-left: -10px;
}

.user-profile {
  display: flex;
  align-items: center;
  color: #161a2d;
}

.user-profile img {
  width: 42px;
  border-radius: 50%;
  border: 2px solid #fff;
}

.user-profile h3 {
  font-size: 1rem;
  font-weight: 600;
}

.user-profile span {
  font-size: 0.775rem;
  font-weight: 600;
}

.user-detail {
  margin-left: 23px;
  white-space: nowrap;
}

.sidebar:hover .user-account {
  background: #fff;
  border-radius: 4px;
}


      

        .main-content {
            margin-left: 280px;
            padding: 20px;
        }

        .grade-container {
            display: none;
            padding: 20px;
            margin: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .grade-container.active {
            display: block;
        }

        .course-header {
            background-color: #161a2d;
            color: #FFD700;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .grade-table {
            width: 100%;
            margin-top: 15px;
            background: #fff;
        }

        .grade-table th {
            background-color: #f8f9fa;
            padding: 12px;
        }

        .grade-table td {
            padding: 12px;
            border-top: 1px solid #dee2e6;
        }
        
      
        .no-courses {
            text-align: center;
            padding: 40px;
            color: #6c757d;
            font-style: italic;
        
      
       
        /* Added for proper sticky positioning with navbar */
     
        
        .main-content {
            margin-top: 8vh;
        }</style>
</head>
<body>
    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="menu-separator" style="color: #FFD700">
                <svg xmlns="http://www.w3.org/2000/svg" width="45" height="45" fill="currentColor" class="bi bi-award" viewBox="0 0 16 16">
                    <path d="M9.669.864 8 0 6.331.864l-1.858.282-.842 1.68-1.337 1.32L2.6 6l-.306 1.854 1.337 1.32.842 1.68 1.858.282L8 12l1.669-.864 1.858-.282.842-1.68 1.337-1.32L13.4 6l.306-1.854-1.337-1.32-.842-1.68zm1.196 1.193.684 1.365 1.086 1.072L12.387 6l.248 1.506-1.086 1.072-.684 1.365-1.51.229L8 10.874l-1.355-.702-1.51-.229-.684-1.365-1.086-1.072L3.614 6l-.25-1.506 1.087-1.072.684-1.365 1.51-.229L8 1.126l1.356.702z"/>
                    <path d="M4 11.794V16l4-1 4 1v-4.206l-2.018.306L8 13.126 6.018 12.1z"/>
                </svg>
            </div>
            <h2>My Courses</h2>
        </div>
        <ul class="sidebar-links">
            <?php if ($result_courses->num_rows > 0): ?>
                <?php while ($course = $result_courses->fetch_assoc()): ?>
                    <li>
                        <a href="#" class="course-link" data-course="<?php echo $course['course_id']; ?>">
                            <svg class="course-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M1 2.828c.885-.37 2.154-.769 3.388-.893 1.33-.134 2.458.063 3.112.752v9.746c-.935-.53-2.12-.603-3.213-.493-1.18.12-2.37.461-3.287.811V2.828zm7.5-.141c.654-.689 1.782-.886 3.112-.752 1.234.124 2.503.523 3.388.893v9.923c-.918-.35-2.107-.692-3.287-.81-1.094-.111-2.278-.039-3.213.492V2.687zM8 1.783C7.015.936 5.587.81 4.287.94c-1.514.153-3.042.672-3.994 1.105A.5.5 0 0 0 0 2.5v11a.5.5 0 0 0 .707.455c.882-.4 2.303-.881 3.68-1.02 1.409-.142 2.59.087 3.223.877a.5.5 0 0 0 .78 0c.633-.79 1.814-1.019 3.222-.877 1.378.139 2.8.62 3.681 1.02A.5.5 0 0 0 16 13.5v-11a.5.5 0 0 0-.293-.455c-.952-.433-2.48-.952-3.994-1.105C10.413.809 8.985.936 8 1.783z"/>
                            </svg>
                            <?php echo htmlspecialchars($course['course_name']); ?>
                        </a>
                    </li>
                <?php endwhile; 
                $result_courses->data_seek(0); // Reset result pointer
                ?>
            <?php else: ?>
                <li class="no-courses">No courses enrolled</li>
            <?php endif; ?>
        </ul>
    </aside>

    <div class="main-content">
        <?php if ($result_courses->num_rows > 0): ?>
            <?php while ($course = $result_courses->fetch_assoc()): ?>
                <div id="course-<?php echo $course['course_id']; ?>" class="grade-container">
                    <div class="course-header">
                        <h3><?php echo htmlspecialchars($course['course_name']); ?></h3>
                        <p>Instructor: <?php echo htmlspecialchars($course['instructor']); ?></p>
                    </div>
                    
                    <?php
                    $grades_query = "
                        SELECT a.assignment_name, a.total_points as max_points, 
                               g.score, a.due_date, gc.category_name, gc.weight
                        FROM assignments a
                        LEFT JOIN grades g ON a.assignment_id = g.assignment_id AND g.user_id = ?
                        LEFT JOIN grade_categories gc ON a.category_id = gc.category_id
                        WHERE a.course_id = ?
                        ORDER BY a.due_date DESC";
                    
                    $stmt_grades = $conn->prepare($grades_query);
                    $stmt_grades->bind_param("ii", $user_id, $course['course_id']);
                    $stmt_grades->execute();
                    $result_grades = $stmt_grades->get_result();
                    ?>

                    <?php if ($result_grades->num_rows > 0): ?>
                        <table class="grade-table">
                            <thead>
                                <tr>
                                    <th>Assignment</th>
                                    <th>Category</th>
                                    <th>Due Date</th>
                                    <th>Score</th>
                                    <th>Max Points</th>
                                    <th>Percentage</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($grade = $result_grades->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($grade['assignment_name']); ?></td>
                                        <td><?php echo htmlspecialchars($grade['category_name']); ?> 
                                            (<?php echo htmlspecialchars($grade['weight']); ?>%)</td>
                                        <td><?php echo date('M d, Y', strtotime($grade['due_date'])); ?></td>
                                        <td><?php echo $grade['score'] ?? 'Not graded'; ?></td>
                                        <td><?php echo $grade['max_points']; ?></td>
                                        <td>
                                            <?php 
                                            if (isset($grade['score'])) {
                                                echo round(($grade['score'] / $grade['max_points']) * 100, 1) . '%';
                                            } else {
                                                echo 'N/A';
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p class="text-center">No assignments found for this course.</p>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="no-courses">
                <h3>No courses found</h3>
                <p>You are not currently enrolled in any courses.</p>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const courseLinks = document.querySelectorAll('.course-link');
            const gradeContainers = document.querySelectorAll('.grade-container');

            // Function to show course grades
            function showCourseGrades(courseId) {
                // Hide all grade containers
                gradeContainers.forEach(container => {
                    container.classList.remove('active');
                });

                // Remove active class from all links
                courseLinks.forEach(link => {
                    link.classList.remove('active');
                });

                // Show selected course's grades
                const selectedContainer = document.getElementById(`course-${courseId}`);
                if (selectedContainer) {
                    selectedContainer.classList.add('active');
                }

                // Add active class to clicked link
                const selectedLink = document.querySelector(`[data-course="${courseId}"]`);
                if (selectedLink) {
                    selectedLink.classList.add('active');
                }
            }

            // Add click event listeners to course links
            courseLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const courseId = this.getAttribute('data-course');
                    showCourseGrades(courseId);
                });
            });

            // Show first course by default
            if (courseLinks.length > 0) {
                const firstCourseId = courseLinks[0].getAttribute('data-course');
                showCourseGrades(firstCourseId);
            }
        });
    </script>
</body>
</html>