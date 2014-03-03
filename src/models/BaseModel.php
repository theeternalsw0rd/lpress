<?php namespace EternalSword\LPress;

use \DB as DB;
use \Eloquent as Eloquent;
use \Str as Str;

class BaseModel extends Eloquent {

	public function getColumns() {
		$schema = DB::getDoctrineSchemaManager();
		$connection = DB::connection();
		$connection->getSchemaBuilder();
		$table   = $connection->getTablePrefix() . $this->table;
		$columns = $schema->listTableColumns($table);
		$columns_array = array();
		foreach($columns as $column) {
			if(!in_array($column->getName(), $this->fillable)) {
				continue;
			}
			$columns_array[] = array(
				'name' => $column->getName(),
				'label' => str_replace('_', ' ', Str::title($column->getName())),
				'type' => $column->getType()->getName()
			);
		}
		return $columns_array;
	}

	public function getRules() {
		return $this->rules;
	}
}
