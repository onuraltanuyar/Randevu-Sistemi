<?php
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: giris.php");
    exit();
}
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: guncel-randevular.php");
    exit();
}

$appointment_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

$stmt_check = $conn->prepare("SELECT id FROM appointments WHERE id = ? AND user_id = ? AND status != 'canceled'");
$stmt_check->bind_param("ii", $appointment_id, $user_id);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows == 1) {
    $stmt_update = $conn->prepare("UPDATE appointments SET status = 'canceled' WHERE id = ?");
    $stmt_update->bind_param("i", $appointment_id);
    $stmt_update->execute();
    $stmt_update->close();
} 

$stmt_check->close();

header("Location: guncel-randevular.php");
exit();

?>