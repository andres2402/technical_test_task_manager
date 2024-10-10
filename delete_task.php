<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_GET['id'])) {
    $task_id = $_GET['id'];
    $user_id = $_SESSION['user_id'];

    $pdo->beginTransaction();

    try {
        $stmt = $pdo->prepare("DELETE FROM user_tasks WHERE task_id = ? AND user_id = ?");
        $stmt->execute([$task_id, $user_id]);

        $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ? AND NOT EXISTS (SELECT 1 FROM user_tasks WHERE task_id = ?)");
        $stmt->execute([$task_id, $task_id]);

        $pdo->commit();
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Error al eliminar la tarea: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Solicitud inválida']);
}
