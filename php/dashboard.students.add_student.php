<?php
session_start();
include_once("./database/connection.php");

// Fetch courses for checkboxes
$courseQuery = "SELECT id, course_name FROM courses";
try {
    $courseStmt = $pdo->query($courseQuery);
    $courses = $courseStmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    echo "Error fetching courses: " . $e->getMessage();
    die();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $pdo->beginTransaction();

        // Insert into students table
        $stmt = $pdo->prepare("INSERT INTO students (student_id, name, surname, date_of_birth, phone_number, email, attendance) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['student_id'],
            $_POST['name'],
            $_POST['surname'],
            $_POST['date_of_birth'],
            $_POST['phone_number'],
            $_POST['email'],
            $_POST['attendance']
        ]);

        $studentId = $pdo->lastInsertId();

        // Insert selected courses
        if (isset($_POST['courses']) && !empty($_POST['courses'])) {
            $courseStmt = $pdo->prepare("INSERT INTO students_courses_info (student_id, course_id) VALUES (?, ?)");
            foreach ($_POST['courses'] as $courseId) {
                $courseStmt->execute([$studentId, $courseId]);
            }
        }

        $pdo->commit();
        $_SESSION['message'] = 'Student and courses added successfully!';
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();

    } catch(PDOException $e) {
        $pdo->rollBack();
        $_SESSION['error'] = 'Error: ' . $e->getMessage();
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style/dashboard.students.add_student.css">
    <title>Add New Student</title>
</head>
<body>
    <div class="container">
        <?php
        if (isset($_SESSION['message'])) {
            echo '<div class="alert alert-success">' . $_SESSION['message'] . '</div>';
            unset($_SESSION['message']);
        }
        if (isset($_SESSION['error'])) {
            echo '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
            unset($_SESSION['error']);
        }
        ?>
        <h1>Add New Student</h1>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="student_id">Student ID:</label>
                <input type="number" id="student_id" name="student_id" autocomplete="off" required>
            </div>

            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" autocomplete="off" required>
            </div>

            <div class="form-group">
                <label for="surname">Surname:</label>
                <input type="text" id="surname" name="surname" autocomplete="off" required>
            </div>

            <div class="form-group">
                <label for="phone_number">Phone Number:</label>
                <input type="text" id="phone_number" name="phone_number" autocomplete="off" required>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="text" id="email" name="email" autocomplete="off" required>
            </div>

            <div class="form-group">
                <label for="date_of_birth">Date of Birth:</label>
                <input type="date" id="date_of_birth" name="date_of_birth" required>
            </div>

            <div class="form-group">
                <label for="attendance">Attendance (%):</label>
                <input type="number" id="attendance" name="attendance" min="0" max="100" required>
            </div>

            <div class="course-selection">
                <h2>Select Courses</h2>
                <div class="checkbox-group">
                    <?php foreach ($courses as $course): ?>
                        <div class="checkbox-option">
                            <input type="checkbox" id="course_<?php echo $course['id']; ?>" 
                                   name="courses[]" value="<?php echo $course['id']; ?>">
                            <label for="course_<?php echo $course['id']; ?>">
                                <?php echo htmlspecialchars($course['course_name']); ?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <button type="submit">Add Student</button>
        </form>
    </div>
</body>
</html>