<?php
include 'db.php';

if (isset($_GET['subject_id'])) {
    $subject_id = (int)$_GET['subject_id'];

    $stmt = $conn->prepare("
        SELECT teacher_id, classroom_id 
        FROM subject 
        WHERE id = ?
    ");
    $stmt->bind_param("i", $subject_id);
    $stmt->execute();

    $result = $stmt->get_result();
    $data = $result->fetch_assoc();

    echo json_encode($data);
}
?>
