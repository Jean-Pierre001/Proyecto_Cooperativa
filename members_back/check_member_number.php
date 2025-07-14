<?php
include '../includes/conn.php';

if (isset($_GET['member_number'])) {
    $member_number = $_GET['member_number'];

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM members WHERE member_number = :member_number");
    $stmt->execute([':member_number' => $member_number]);
    $count = $stmt->fetchColumn();

    if ($count > 0) {
        echo json_encode(['exists' => true]);
    } else {
        echo json_encode(['exists' => false]);
    }
} else {
    echo json_encode(['exists' => false]);
}
?>
