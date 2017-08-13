<?php

require_once __DIR__ .'/Model.php';

class Task extends Model
{
	protected static $table = 'tasks';

	public function __construct($name)
	{
		parent::__construct();

		$now = date("Y-m-d H:i:s");
		
		$this->name = $name;
		$this->is_complete = 0;
		$this->updated_at = $now;
		$this->created_at = $now;
	}

	public function toggleComplete()
	{
		$this->is_complete = $this->is_complete == 1 ? 0 : 1;
		$this->updated_at = date('Y-m-d H:i:s');
		$this->save();
	}

	public static function clearComplete()
	{
		self::dbConnect();

		$stmt = self::$dbc->query('SELECT id FROM tasks WHERE is_complete = 1');
		$tasksToBeRemoved = $stmt->fetchAll(PDO::FETCH_ASSOC);

		foreach($tasksToBeRemoved as $task) {
			Task::delete($task['id']);
		}
	}
}
