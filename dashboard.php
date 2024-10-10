<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT t.*, COUNT(CASE WHEN t.status = 'completed' THEN 1 END) as completed_count
                       FROM tasks t
                       JOIN user_tasks ut ON t.id = ut.task_id
                       WHERE ut.user_id = ?
                       GROUP BY t.id");
$stmt->execute([$user_id]);
$tasks = $stmt->fetchAll();

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
    <title>Dashboard - Sistema de Gestión de Tareas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .card.completed {
            background-color: #a9fc81;
        }

        .btn-disabled {
            pointer-events: none;
            opacity: 0.5;
        }

        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .container {
            flex: 1;
        }

        .btn-group {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-top: auto;
        }

        .btn-group .btn {
            flex: 1 1 auto;
            border-radius: 4px !important;
        }
    </style>
</head>

<body>
    <?php include 'includes/header.php'; ?>

    <div class="container mt-3">
        <h1 class="mb-4 text-center">Dashboard</h1>

        <div class="row">
            <?php if (empty($tasks)): ?>
                <div class="col-12 text-center">
                    <div class="alert alert-info" role="alert">
                        <h4 class="alert-heading">No hay tareas creadas</h4>
                        <p>Actualmente no tienes ninguna tarea. ¡Comienza creando una nueva tarea!</p>
                        <hr>
                        <p class="mb-0">
                            <a href="add_task.php" class="btn btn-primary">Crear nueva tarea</a>
                        </p>
                    </div>
                </div>
            <?php else: ?>
                <div>
                    <a href="add_task.php" class="btn btn-primary mb-4 fw-bold">Agregar nueva tarea</a>
                </div>

                <?php foreach ($tasks as $task): ?>
                    <div class="col-md-4 mb-5">
                        <div class="card <?php echo $task['status'] == 'completed' ? 'completed' : ''; ?>" data-task-id="<?php echo $task['id']; ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($task['title']); ?></h5>
                                <p class="card-text"><?php echo htmlspecialchars($task['description']); ?></p>
                                <p>Prioridad: <?php echo htmlspecialchars(priorityTranslate($task['priority'])); ?></p>
                                <p>Estado: <span class="status"><?php echo htmlspecialchars(stateTranslate($task['status'])); ?></span></p>
                                <div class="btn-group">
                                    <button class="btn btn-success"
                                        onclick="markAsCompleted(<?php echo $task['id']; ?>)"
                                        <?php echo $task['status'] == 'completed' ? 'disabled' : ''; ?>>
                                        Marcar como Completada
                                    </button>
                                    <button class="btn btn-primary" onclick="editTask(<?php echo $task['id']; ?>)">Editar</button>
                                    <button class="btn btn-danger" onclick="deleteTask(<?php echo $task['id']; ?>)">Eliminar</button>
                                    <a href="task_detail.php?id=<?php echo $task['id']; ?>" class="btn btn-info">Ver Detalles</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script src="js/tasks.js"></script>
</body>

</html>