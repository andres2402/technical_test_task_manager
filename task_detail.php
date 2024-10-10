<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

$task_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = ?");
$stmt->execute([$task_id]);
$task = $stmt->fetch();

if (!$task) {
    header("Location: dashboard.php");
    exit();
}

function priorityTranslate($priority)
{
    switch ($priority) {
        case 'low':
            return 'Baja';
        case 'medium':
            return 'Media';
        case 'high':
            return 'Alta';
        default:
            return $priority;
    }
}

function stateTranslate($status)
{
    switch ($status) {
        case 'pending':
            return 'Pendiente';
        case 'completed':
            return 'Completada';
        default:
            return $status;
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de Tarea - Sistema de Gestión de Tareas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include 'includes/header.php'; ?>

    <div class="container mt-4">
        <h2 class="mb-4">Detalles de la tarea</h2>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-3"><?php echo htmlspecialchars($task['title']); ?></h5>
                <p class="card-text"><?php echo htmlspecialchars($task['description']); ?></p>
                <p><strong>Prioridad:</strong> <?php echo htmlspecialchars(priorityTranslate($task['priority'])); ?></p>
                <p><strong>Estado:</strong> <span class="status"><?php echo htmlspecialchars(stateTranslate($task['status'])); ?></span></p>
                <a href="edit_task.php?id=<?php echo $task['id']; ?>" class="btn btn-primary">Editar</a>
                <a href="dashboard.php" class="btn btn-secondary">Volver al Dashboard</a>
            </div>
        </div>
    </div>
</body>

</html>