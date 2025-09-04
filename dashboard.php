<?php
    session_start();
    include_once("php/database/connection.php");

    // Enhanced getStatistics function
    function getStatistics($pdo) {
        try {
            $stats = [];
            
            // Basic stats
            $stmt = $pdo->query("SELECT COUNT(*) as total FROM students");
            $stats['totalStudents'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

            $stmt = $pdo->query("SELECT AVG(attendance) as avg_attendance FROM students");
            $avgAttendance = $stmt->fetch(PDO::FETCH_ASSOC)['avg_attendance'];
            $stats['averageAttendance'] = is_null($avgAttendance) ? '0%' : round($avgAttendance, 1) . '%';

            $stmt = $pdo->query("SELECT COUNT(id) as total_courses FROM courses");
            $stats['coursesOffered'] = $stmt->fetch(PDO::FETCH_ASSOC)['total_courses'];

            // Get student attendance data for chart
            $stmt = $pdo->query("SELECT name, attendance FROM students ORDER BY attendance DESC");
            $stats['attendanceData'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Get course enrollment distribution
            $stmt = $pdo->query("
                SELECT c.course_name, COUNT(sci.student_id) as student_count 
                FROM courses c 
                LEFT JOIN students_courses_info sci ON c.id = sci.course_id 
                GROUP BY c.id, c.course_name
            ");
            $stats['courseDistribution'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Get recent enrollments
            $stmt = $pdo->query("
                SELECT s.name, s.surname, c.course_name, s.created_at
                FROM students s
                JOIN students_courses_info sci ON s.id = sci.student_id
                JOIN courses c ON sci.course_id = c.course_id
                ORDER BY s.created_at DESC
                LIMIT 5
            ");
            $stats['recentEnrollments'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $stats;
        } catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
            return [
                'totalStudents' => 0,
                'averageAttendance' => '0%',
                'coursesOffered' => 0,
                'attendanceData' => [],
                'courseDistribution' => [],
                'recentEnrollments' => []
            ];
        }
    }
    
    $stats = getStatistics($pdo);
    $statsJson = json_encode($stats);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Management System</title>
    <link rel="stylesheet" href="./style/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <!-- Sidebar -->
    <?php include("./compoents/sidebar.php") ?>

    <!-- Main Content -->
    <div class="main-wrapper" id="mainWrapper">
        <!-- Stats Cards -->
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

        <!-- Charts Container -->
        <div class="charts-container">
            <div class="chart-card">
                <h3>Student Attendance Rates</h3>
                <canvas id="attendanceChart"></canvas>
            </div>
            <div class="chart-card">
                <h3>Course Distribution</h3>
                <canvas id="courseDistributionChart"></canvas>
            </div>
        </div>
    </div>

    <script src="js/sidebar.js"></script>
    <script>
        // Initialize charts with PHP data
        const stats = <?php echo $statsJson; ?>;

        // Attendance Chart
        const attendanceCtx = document.getElementById('attendanceChart').getContext('2d');
        new Chart(attendanceCtx, {
            type: 'bar',
            data: {
                labels: stats.attendanceData.map(item => item.name),
                datasets: [{
                    label: 'Attendance Rate (%)',
                    data: stats.attendanceData.map(item => item.attendance),
                    backgroundColor: '#4a90e2',
                    borderColor: '#2171c7',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100
                    }
                }
            }
        });

        // Course Distribution Chart
        const distributionCtx = document.getElementById('courseDistributionChart').getContext('2d');
        
        // Add console logging to debug the data
        console.log('Course Distribution Data:', stats.courseDistribution);
        
        // Course Distribution Chart için güncellenen konfigürasyon
        new Chart(distributionCtx, {
            type: 'pie',
            data: {
                labels: stats.courseDistribution.map(item => item.course_name),
                datasets: [{
                    data: stats.courseDistribution.map(item => parseInt(item.student_count) || 0),
                    backgroundColor: [
                        '#4a90e2',
                        '#2ecc71',
                        '#f1c40f',
                        '#e74c3c',
                        '#9b59b6'
                    ],
                    borderColor: '#ffffff',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                layout: {
                    padding: 20
                },
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            boxWidth: 12,
                            font: {
                                size: 12
                            }
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>