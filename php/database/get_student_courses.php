<?php
include_once("./connection.php");

try {

    if(isset($_GET['id'])) {
        // Öğrencinin kurslarını getiren SQL sorgusu
        $stmt = $pdo->prepare("
            SELECT c.course_name, c.instructor_name
            FROM students_courses_info sci
            JOIN courses c ON sci.course_id = c.id
            WHERE sci.student_id = ?
        ");
        
        $stmt->execute([$_GET['id']]);
        $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // JSON başlığını ayarla
        header('Content-Type: application/json');
        
        // Sonuçları JSON olarak döndür
        echo json_encode($courses);
    }
} catch(PDOException $e) {
    // Hata durumunda hata mesajını JSON olarak döndür
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>