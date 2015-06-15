<?php

$_ENV = require __DIR__ . '/../.env.php';

class Model
	{
		protected static $table;
		protected static $update;
		protected static $insert;
		protected static $dbc;
		private $attributes = [];

		public function __construct()
		{
			self::dbConnect();
		}

		public function __set($name, $value)
		{
			$this->attributes[$name] = $value;
		}

		public function __get($name)
		{
			return array_key_exists($name, $this->attributes) ? $this->attributes[$name] : null;
		}

		protected static function dbConnect()
		{

			if(!self::$dbc)
			{
				self::$dbc = new PDO("mysql:host=" . $_ENV['DB_HOST'] . ";dbname=" . $_ENV['DB_NAME'], $_ENV['DB_USER'], $_ENV['DB_PASS']);
				self::$dbc->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			}
		}

		public function save()
		{
			if(!empty($this->attributes))
			{
				isset($this->attributes['id']) ? $this->update() : $this->insert();
			}
		}

		protected function update()
		{
			$stmt = self::$dbc->prepare(static::$update);

			foreach($this->attributes as $attribute => $value)
			{
				$type = is_numeric($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
				$stmt->bindValue(":$attribute", $value, $type);
			}

			$stmt->execute();
		}

		protected function insert()
		{

			$stmt = self::$dbc->prepare(static::$insert);

			foreach($this->attributes as $attribute => $value)
			{
				$type = is_numeric($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
				$stmt->bindValue(":$attribute", $value, $type);
			}

			$stmt->execute();

			$stmt = self::$dbc->prepare("SELECT id FROM " . static::$table . " ORDER BY id DESC LIMIT 1");
			$insertedTask = $stmt->fetch(PDO::FETCH_ASSOC);
			$this->attributes['id'] = $insertedTask['id'];
		}

		public static function delete($id)
		{
			self::dbConnect();

			$stmt = self::$dbc->prepare("DELETE FROM " . static::$table . " WHERE id = :id");
			$stmt->bindValue(':id', $id, PDO::PARAM_INT);
			$stmt->execute();
		}

		public static function find($id)
		{
			self::dbConnect();

			$stmt = self::$dbc->prepare("SELECT * FROM " . static::$table . " WHERE id = :id");
			$stmt->bindValue(':id', $id, PDO::PARAM_INT);
			$stmt->execute();
			$results = $stmt->fetch(PDO::FETCH_ASSOC);

			$instance = null;
			if($results)
			{
				$instance = new static;
				$instance->attributes = $results;
			}

			return $instance;
		}

		public static function all()
		{
			self::dbConnect();

			return self::$dbc->query('SELECT * FROM ' . static::$table . ' ORDER BY id DESC;')->fetchAll(PDO::FETCH_ASSOC);
		}
	}



?>