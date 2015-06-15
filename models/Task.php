<?php
	require __DIR__ .'/BaseModel.php';

	class Task extends Model
	{
		protected static $table = 'tasks';
		protected static $update = "UPDATE tasks SET name = :name, is_complete = :is_complete, updated_at = :updated_at, created_at = :created_at WHERE id = :id";
		protected static $insert = "INSERT INTO tasks (name, is_complete, created_at, updated_at) VALUES (:name, :is_complete, :created_at, :updated_at)";

		public static function clearComplete()
		{
			self::dbConnect();

			$stmt = self::$dbc->query('SELECT id FROM tasks WHERE is_complete = TRUE');
			$tasksToBeRemoved = $stmt->fetchAll(PDO::FETCH_ASSOC);

			foreach($tasksToBeRemoved as $task)
			{
				Task::delete($task['id']);
			}

		}
	}

?>