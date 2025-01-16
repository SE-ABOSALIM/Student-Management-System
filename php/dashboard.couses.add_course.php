<?php
    session_start();
    include_once("./database/connection.php");

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $course_name = $_POST['course_name'];
        $course_id = $_POST['course_id'];
        $instructor_name = $_POST['instructor_name'];

        try {
            // Transaction başlat
            $pdo->beginTransaction();

            // Aynı course_id'ye sahip kayıt olup olmadığını kontrol et
            $checkQuery = "SELECT COUNT(*) FROM courses WHERE course_id = :course_id";
            $checkStmt = $pdo->prepare($checkQuery);
            $checkStmt->execute([':course_id' => $course_id]);
            $courseExists = $checkStmt->fetchColumn();

            if ($courseExists > 0) {
                // Aynı course_id mevcutsa hata ver
                $_SESSION['error'] = "Error: Course already exists!";
                header("Location: " . $_SERVER['PHP_SELF']);
                exit;
            }

            // Kursu veritabanına ekleme
            $query = "INSERT INTO courses (course_id, course_name, instructor_name) VALUES (:course_id, :course_name, :instructor_name)";
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                ':course_id' => $course_id,
                ':course_name' => $course_name,
                ':instructor_name' => $instructor_name,
            ]);

            // Transaction'ı tamamla (commit)
            $pdo->commit();

            // Başarılı ekleme durumunda yönlendirme işlemi
            $_SESSION['message'] = "Course added successfully!";
            header("Location: " . $_SERVER['PHP_SELF']); // Aynı sayfaya yönlendiriliyor
            exit;

        } catch (PDOException $e) {
            // Hata durumunda geri al (rollback)
            $pdo->rollBack();
            $_SESSION['error'] = "Error adding course: " . $e->getMessage();
            header("Location: " . $_SERVER['PHP_SELF']); // Aynı sayfaya yönlendiriliyor
            exit;
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

    <style>
        h1 {
            color:black;
        }

        button {
            background-color:rgb(100, 147, 13);
        }

        button:hover {
            background-color: green;
        }

        input[type="text"]:focus,
        input[type="number"]:focus,
        input[type="email"]:focus,
        input[type="date"]:focus {
            outline: none;
            border-color:rgb(56, 182, 34);
            box-shadow: 0 0 0 3px rgba(68, 243, 33, 0.08);
        }

    </style>

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
        <h1>Add New Course</h1>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="course_id">Course ID:</label>
                <input type="text" id="course_id" name="course_id" autocomplete="off" required>
            </div>

            <div class="form-group">
                <label for="course_name">Course Name:</label>
                <input type="text" id="course_name" name="course_name" autocomplete="off" required>
            </div>

            <div class="form-group">
                <label for="instructor_name">Instructor Name:</label>
                <input type="text" id="instructor_name" name="instructor_name" autocomplete="off" required>
            </div>

            <button type="submit">Add Course</button>
        </form>
    </div>
</body>
</html>