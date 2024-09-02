<?php
// Function to read tasks from the JSON file
function readTasks()
{
    $jsonData = file_get_contents('tasks.json');
    return json_decode($jsonData, true);
}

// Function to write tasks to the JSON file
function writeTasks($tasks)
{
    $jsonData = json_encode($tasks, JSON_PRETTY_PRINT);
    file_put_contents('tasks.json', $jsonData);
}

// Handle form submissions for adding, editing, and deleting tasks
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tasks = readTasks();

    if (isset($_POST['add'])) {
        // Add Task
        $newTask = [
            'id' => uniqid(),
            'title' => $_POST['title'],
            'description' => $_POST['description']
        ];
        $tasks[] = $newTask;
    } elseif (isset($_POST['edit'])) {
        // Edit Task
        foreach ($tasks as &$task) {
            if ($task['id'] === $_POST['id']) {
                $task['title'] = $_POST['title'];
                $task['description'] = $_POST['description'];
                break;
            }
        }
    } elseif (isset($_POST['delete'])) {
        // Delete Task
        $tasks = array_filter($tasks, function ($task) {
            return $task['id'] !== $_POST['id'];
        });
    }

    writeTasks($tasks);
    header('Location: index.php');
    exit();
}

// Read the tasks to display
$tasks = readTasks();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Task Manager</title>
</head>
<body>
    <h1>Task Manager</h1>

    <!-- Form to Add a New Task -->
    <h2>Add Task</h2>
    <form method="POST">
        <input type="text" name="title" placeholder="Task Title" required>
        <input type="text" name="description" placeholder="Task Description" required>
        <button type="submit" name="add">Add Task</button>
    </form>

    <!-- List All Tasks -->
    <h2>All Tasks</h2>
    <table border="1">
        <tr>
            <th>Title</th>
            <th>Description</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($tasks as $task): ?>
            <tr>
                <td><?php echo htmlspecialchars($task['title']); ?></td>
                <td><?php echo htmlspecialchars($task['description']); ?></td>
                <td>
                    <!-- Edit Task Form -->
                    <form method="POST" style="display:inline-block;">
                        <input type="hidden" name="id" value="<?php echo $task['id']; ?>">
                        <input type="text" name="title" value="<?php echo htmlspecialchars($task['title']); ?>" required>
                        <input type="text" name="description" value="<?php echo htmlspecialchars($task['description']); ?>" required>
                        <button type="submit" name="edit">Edit</button>
                    </form>

                    <!-- Delete Task Form -->
                    <form method="POST" style="display:inline-block;">
                        <input type="hidden" name="id" value="<?php echo $task['id']; ?>">
                        <button type="submit" name="delete">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
