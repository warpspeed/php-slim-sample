<?php

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
		if (!self::$dbc) {
			self::$dbc = new PDO("mysql:host=" . $_ENV['DB_HOST'] . ";dbname=" . $_ENV['DB_NAME'], $_ENV['DB_USER'], $_ENV['DB_PASS']);
			self::$dbc->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}
	}

	public function save()
	{
		if (!empty($this->attributes)) {

			$attributes = $this->attributes;
			$id = null;

			if (isset($attributes['id'])) {
				$id = $attributes['id'];
				unset($attributes['id']);
			}

			$attributeNames = array_keys($this->attributes);
			$attributeValues = array_values($this->attributes);

			if ($id) {

				// existing record, perform update
				$query = 'UPDATE ' . static::$table . ' SET ';

				foreach ($attributeNames  as $attributeName) {
					$query .= "$attributeName = ?, ";
				}

				$query = substr($query, 0, -2); // remove trailing ', '
				$query .= ' WHERE id = ?';
				$stmt = self::$dbc->prepare($query);

				array_push($attributeValues, $id); // add id to end of array

				$stmt->execute(array_values($attributeValues));

			} else {

				// new record, perform insert
				$query = 'INSERT INTO ' . static::$table . ' (';
				$params = '';

				foreach ($attributeNames  as $attributeName) {
					$query .= "$attributeName, ";
					$params .= '?, ';
				}

				$query = substr($query, 0, -2); // remove trailing ', '
				$params = substr($params, 0, -2); // remove trailing ', '

				$query .= ") values ($params)";
				$stmt = self::$dbc->prepare($query);

				$stmt->execute(array_values($attributeValues));

				$this->attributes['id'] = self::$dbc->lastInsertId(); // add id to instance
			}
		}
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

		$instance = null;

		$stmt = self::$dbc->prepare("SELECT * FROM " . static::$table . " WHERE id = :id");
		$stmt->bindValue(':id', $id, PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);


		if ($result) {
			$instance = new static;
			$instance->attributes = $result;
		}

		return $instance;
	}

	public static function all()
	{
		self::dbConnect();

		$instances = array();
		$stmt = self::$dbc->prepare('SELECT * FROM ' . static::$table . ' ORDER BY id DESC;');
		$stmt->execute();
		$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

		if ($results) {
			foreach ($results as $result) {
				$instance = new static;
				$instance->attributes = $result;
				array_push($instances, $instance);
			}
		}

		return $instances;
	}
}
