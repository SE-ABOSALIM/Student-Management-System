<?php
    session_start();
    include_once("php/database/connection.php");

    // Function to get statistics
    function getStatistics($pdo) {
        try {
            $stats = [];
            // Total students
            $stmt = $pdo->query("SELECT COUNT(*) as total FROM students");
            $stats['totalStudents'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            // Average attendance
            $stmt = $pdo->query("SELECT AVG(attendance) as avg_attendance FROM students");
            $avgAttendance = $stmt->fetch(PDO::FETCH_ASSOC)['avg_attendance'];
            $stats['averageAttendance'] = is_null($avgAttendance) ? '0%' : round($avgAttendance, 1) . '%';
            
            // Total courses
            $stmt = $pdo->query("SELECT COUNT(id) as total_courses FROM courses");
            $stats['coursesOffered'] = $stmt->fetch(PDO::FETCH_ASSOC)['total_courses'];
            
            return $stats;
        } catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
            return ['totalStudents' => 0, 'averageAttendance' => '0%', 'coursesOffered' => 0];
        }
    }
    $stats = getStatistics($pdo);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Management System</title>
    <link rel="stylesheet" href="./style/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>


    <!-- Sidebar -->
    <?php include("./compoents/sidebar.php") ?>

    <!-- Main Content -->
    <div class="main-wrapper" id="mainWrapper">
        <div class="stats-container">
            <div class="stat-card">
                <i class="fas fa-users"></i>
                <h3>Total Students</h3>
                <p><?php echo $stats['totalStudents']; ?></p>
            </div>
            <div class="stat-card">
                <i class="fas fa-chart-line"></i>
                <h3>Average Attendance</h3>
                <p><?php echo $stats['averageAttendance']; ?></p>
            </div>
            <div class="stat-card">
                <i class="fas fa-book"></i>
                <h3>Courses Offered</h3>
                <p><?php echo $stats['coursesOffered']; ?></p>
            </div>
        </div>
    </div>

    <script src="js/sidebar.js"></script>
</body>
</html>