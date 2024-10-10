function markAsCompleted(taskId) {
  return new Promise((resolve, reject) => {
    fetch(`mark_completed.php?task_id=${taskId}`, {
      method: "POST",
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          const taskCard = document.querySelector(`[data-task-id="${taskId}"]`);
          if (taskCard) {
            taskCard.querySelector(".status").textContent = "Completada";

            taskCard.style.backgroundColor = "#a9fc81";

            const completeButton = taskCard.querySelector(".btn-success");
            if (completeButton) {
              completeButton.disabled = true;
            }
          }
          resolve(data);
        } else {
          reject(new Error(data.message));
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        reject(error);
      });
  });
}

function editTask(taskId) {
  window.location.href = `edit_task.php?id=${taskId}`;
}

function deleteTask(taskId) {
  if (confirm("¿Estás seguro de que quieres eliminar esta tarea?")) {
    fetch(`delete_task.php?id=${taskId}`, {
      method: "POST",
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          const taskCard = document.querySelector(`[data-task-id="${taskId}"]`);
          if (taskCard) {
            taskCard.remove();
          }
        } else {
          alert("Error al eliminar la tarea");
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        alert("Error al eliminar la tarea");
      });
  }
}
