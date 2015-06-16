<?php
	require __DIR__ .'/BaseModel.php';

	class Task extends Model
	{
		protected static $table = 'tasks';

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