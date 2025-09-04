<?php
    session_start();
    include_once("./database/connection.php");

    // Function to get all students
    function getStudents($pdo) {
        try {
            $stmt = $pdo->query("SELECT * FROM students");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
            return [];
        }
    }

    if(isset($_POST['delete_id'])) {
        try {
            // Begin transaction
            $pdo->beginTransaction();

            // First verify the student exists
            $checkStmt = $pdo->prepare("SELECT id FROM students WHERE id = ?");
            $checkStmt->execute([$_POST['delete_id']]);
            if (!$checkStmt->fetch()) {
                throw new PDOException("Student not found");
            }

            // First delete from students_courses_info table
            $deleteCoursesStmt = $pdo->prepare("DELETE FROM students_courses_info WHERE student_id = ?");
            if (!$deleteCoursesStmt->execute([$_POST['delete_id']])) {
                throw new PDOException("Failed to delete student's course information");
            }

            // Then delete from students table
            $deleteStudentStmt = $pdo->prepare("DELETE FROM students WHERE id = ?");
            if (!$deleteStudentStmt->execute([$_POST['delete_id']])) {
                throw new PDOException("Failed to delete student");
            }

            // Commit the transaction
            $pdo->commit();
            
            $_SESSION['success_message'] = "Student and related course information deleted successfully";
            header("Location: dashboard.students.php");
            exit();
        } catch(PDOException $e) {
            // Rollback the transaction on error
            $pdo->rollBack();
            $_SESSION['error_message'] = "Delete Error: " . $e->getMessage();
            header("Location: dashboard.students.php");
            exit();
        }
    }

    // Rest of the code remains the same...
    if(isset($_POST['edit_student'])) {
        try {
            $stmt = $pdo->prepare("UPDATE students SET student_id = ?, name = ?, surname = ?, phone_number = ?, email = ?, attendance = ? WHERE id = ?");
            $stmt->execute([
                $_POST['student_id'],
                $_POST['name'],
                $_POST['surname'],
                $_POST['phone_number'],
                $_POST['email'],
                $_POST['attendance'],
                $_POST['edit_id']
            ]);
            
            $_SESSION['success_message'] = "Student updated successfully";
            header("Location: dashboard.students.php");
            exit();
        } catch(PDOException $e) {
            $_SESSION['error_message'] = "Update Error: " . $e->getMessage();
            header("Location: dashboard.students.php");
            exit();
        }
    }

    $students = getStudents($pdo);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Management System</title>
    <link rel="stylesheet" href="../style/dashboard.students.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include("../compoents/sidebar.php")?>
    <!-- Main Content -->
    <div class="main-wrapper" id="mainWrapper">
        <?php if(isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success">
                <?php 
                    echo $_SESSION['success_message'];
                    unset($_SESSION['success_message']);
                ?>
            </div>
        <?php endif; ?>

        <?php if(isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger">
                <?php 
                    echo $_SESSION['error_message'];
                    unset($_SESSION['error_message']);
                ?>
            </div>
        <?php endif; ?>

        <div class="header">
            <div class="search-container">
                <i class="fas fa-search"></i>
                <input type="text" class="search-bar" placeholder="Search students..." id="searchInput">
            </div>
            <a href="dashboard.students.add_student.php" class="add-student-btn">
                <i class="fas fa-plus"></i>
                Add New Student
            </a>
        </div>

        <div class="main-content">
            <div class="content-header">
                <h2>Student List</h2>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Student ID</th>
                        <th>Name</th>
                        <th>Surname</th>
                        <th>Phone Number</th>
                        <th>Email</th>
                        <th>Attendance</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="studentTableBody">
                    <?php foreach($students as $student): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($student['student_id']); ?></td>
                        <td><?php echo htmlspecialchars($student['name']); ?></td>
                        <td><?php echo htmlspecialchars($student['surname']); ?></td>
                        <td><?php echo htmlspecialchars($student['phone_number']); ?></td>
                        <td><?php echo htmlspecialchars($student['email']); ?></td>
                        <td><?php echo htmlspecialchars($student['attendance']); ?>%</td>
                        <td>
                            <button class="action-btn edit-btn" data-id="<?php echo $student['id']; ?>"
                                    onclick="openEditModal(<?php echo htmlspecialchars(json_encode($student)); ?>)">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button type="button" class="action-btn delete-btn" 
                                    onclick="confirmDelete(<?php echo $student['id']; ?>, '<?php echo htmlspecialchars($student['name']); ?>')">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                            <button class="action-btn courses-btn" onclick="openCoursesModal(<?php echo $student['id']; ?>)">
                                <i class="fas fa-book"></i> Courses
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Edit Student Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeEditModal()">&times;</span>
            <h2>Edit Student</h2><br>
            <form id="editForm" method="POST">
                <input type="hidden" id="edit_id" name="edit_id">
                <div class="form-group">
                    <label for="student_id">Student ID:</label>
                    <input type="text" id="student_id" name="student_id" required>
                </div>
                <div class="form-group">
                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="surname">Surname:</label>
                    <input type="text" id="surname" name="surname" required>
                </div>
                <div class="form-group">
                    <label for="phone_number">Phone Number:</label>
                    <input type="text" id="phone_number" name="phone_number" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="text" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="attendance">Attendance (%):</label>
                    <input type="number" id="attendance" name="attendance" min="0" max="100" required>
                </div>
                <input type="hidden" name="edit_student" value="1">
                <button type="submit" class="btn-save">Save Changes</button>
            </form>
        </div>
    </div>

    <!-- Delete Modal -->
    <div id="deleteConfirmModal" class="modal">
        <div class="modal-content">
            <div class="delete-confirm-box">
                <div class="delete-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h3>Delete Student</h3>
                <p>Are you sure you want to delete <span id="studentNameSpan"></span>?</p>
                <div class="delete-actions">
                    <form method="POST" id="deleteForm">
                        <input type="hidden" name="delete_id" id="deleteStudentId">
                        <button type="button" class="cancel-btn" onclick="closeDeleteModal()">Cancel</button>
                        <button type="submit" class="confirm-delete-btn">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Courses Modal -->
    <div id="coursesModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeCoursesModal()">&times;</span>
            <h2 style="user-select: none;">Student Courses</h2><br>
            <div id="coursesList"></div>
        </div>
    </div>
    
    <script src="../js/students.action_buttons.js"></script>

</body>
</html>