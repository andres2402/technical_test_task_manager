<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_GET['task_id'])) {
    $task_id = $_GET['task_id'];
    $user_id = $_SESSION['user_id'];

    $stmt = $pdo->prepare("UPDATE tasks t
                           JOIN user_tasks ut ON t.id = ut.task_id
                           SET t.status = 'completed'
                           WHERE t.id = ? AND ut.user_id = ?");
    if ($stmt->execute([$task_id, $user_id])) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al actualizar la tarea']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Solicitud inválida']);
}
