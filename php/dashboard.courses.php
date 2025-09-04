<?php
    session_start();
    include_once("./database/connection.php");

    if(isset($_POST['delete_id'])) {
        try {
            // Begin transaction
            $pdo->beginTransaction();
            
            // First verify the course exists
            $checkStmt = $pdo->prepare("SELECT id FROM courses WHERE id = ?");
            $checkStmt->execute([$_POST['delete_id']]);
            if (!$checkStmt->fetch()) {
                throw new PDOException("Course not found");
            }

            // First delete all student-course relationships from students_courses_info
            $deleteRelationsStmt = $pdo->prepare("DELETE FROM students_courses_info WHERE course_id = ?");
            if (!$deleteRelationsStmt->execute([$_POST['delete_id']])) {
                throw new PDOException("Failed to delete course relationships");
            }
            
            // Then delete the course itself
            $deleteCourseStmt = $pdo->prepare("DELETE FROM courses WHERE id = ?");
            if (!$deleteCourseStmt->execute([$_POST['delete_id']])) {
                throw new PDOException("Failed to delete course");
            }
            
            // Commit transaction
            $pdo->commit();
            
            $_SESSION['success_message'] = "Course and all related student enrollments deleted successfully";
            header("Location: dashboard.courses.php");
            exit();
        } catch(PDOException $e) {
            // Rollback transaction
            $pdo->rollBack();
            $_SESSION['error_message'] = "Error deleting course: " . $e->getMessage();
            header("Location: dashboard.courses.php");
            exit();
        }
    }

    try {
        $query = "SELECT 
                    c.id,
                    c.course_id,
                    c.course_name,
                    c.instructor_name,
                    COUNT(DISTINCT sci.student_id) as student_count
                FROM courses c
                LEFT JOIN students_courses_info sci ON c.id = sci.course_id
                GROUP BY c.id, c.course_id, c.course_name, c.instructor_name";
        
        $stmt = $pdo->query($query);
        $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        die("Sorgu hatası: " . $e->getMessage());
    }
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../style/dashboard.courses.css">
    <title>Courses</title>
</head>
<body>

    <?php include("../compoents/sidebar.php") ?>
    <div class="main-wrapper" id="mainWrapper">
        <div class="container">
            <div class="header">
                <h1 class="title">Details</h1>
            </div>

            <div class="total-info">
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-title">Total Courses</div>
                        <div class="info-value"><?php echo count($courses); ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-title">Total Students</div>
                        <div class="info-value">
                            <?php 
                            $totalStudents = 0;
                            foreach($courses as $course) {
                                $totalStudents += $course['student_count'];
                            }
                            echo $totalStudents;
                            ?>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-title">Average Student/Course</div>
                        <div class="info-value">
                            <?php 
                            echo count($courses) > 0 ? 
                                round($totalStudents / count($courses), 1) : 0; 
                            ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="header">
                <h1 class="title">Courses</h1>
                <a href="dashboard.couses.add_course.php" class="add-course-btn">Add Course</a>
            </div>

            <div class="courses-grid">
                <?php foreach($courses as $course): ?>
                    <div class="course-card">
                        <div class="course-name"><?php echo htmlspecialchars($course['course_name']); ?></div>
                        <div class="course-info">
                            <strong>Course ID:</strong> <?php echo htmlspecialchars($course['course_id']); ?>
                        </div>
                        <div class="course-info">
                            <strong>Instructor:</strong> <?php echo htmlspecialchars($course['instructor_name']); ?>
                        </div>
                        <div class="student-count">
                            <?php echo $course['student_count']; ?> Student
                        </div>
                        <div class="course-actions">
                            <button class="delete-btn" onclick="confirmDelete(<?php echo $course['id']; ?>, '<?php echo htmlspecialchars($course['course_name']); ?>')">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    
    <!-- Delete Modal -->
    <div id="deleteConfirmModal" class="modal">
        <div class="modal-content">
            <div class="delete-confirm-box">
                <div class="delete-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h3>Delete Course</h3>
                <p>Are you sure you want to delete <span id="courseNameSpan"></span>?</p>
                <div class="delete-actions">
                    <form method="POST" id="deleteForm">
                        <input type="hidden" name="delete_id" id="deleteCourseId">
                        <button type="button" class="cancel-btn" onclick="closeDeleteModal()">Cancel</button>
                        <button type="submit" class="confirm-delete-btn">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
    const deleteModal = document.getElementById('deleteConfirmModal');

    function confirmDelete(courseId, courseName) {
        document.getElementById('deleteCourseId').value = courseId;
        document.getElementById('courseNameSpan').textContent = courseName;
        deleteModal.style.display = 'block';
    }

    function closeDeleteModal() {
        deleteModal.style.display = 'none';
    }

    // Modal dışına tıklandığında kapat
    window.onclick = function(event) {
        if (event.target == deleteModal) {
            closeDeleteModal();
        }
    }

    // ESC tuşu ile kapatma
    document.addEventListener('keydown', function(event) {
        if (event.key === "Escape") {
            closeDeleteModal();
        }
    });
</script>
</body>
</html>