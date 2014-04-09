<?php namespace EternalSword\LPress;

use \DB as DB;
use \Eloquent as Eloquent;
use \Str as Str;
use Illuminate\Support\Facades\Lang;

class BaseModel extends Eloquent {
	protected $special_inputs = array('description' => 'text:textarea');

	public function getColumns() {
		$schema = DB::getDoctrineSchemaManager();
		$connection = DB::connection();
		$connection->getSchemaBuilder();
		$table   = $connection->getTablePrefix() . $this->table;
		$columns = $schema->listTableColumns($table);
		$columns_array = array();
		foreach($columns as $column) {
			$column_name = $column->getName();
			if(!in_array($column_name, $this->fillable)) {
				continue;
			}
			$columns_array[] = array(
				'name' => $column_name,
				'label' => Lang::get("l-press::labels.${column_name}"),
				'type' => $column->getType()->getName()
			);
		}
		return $columns_array;
	}

	public function getRules() {
		return $this->rules;
	}

	// auto handle unique rule exclude existing, thanks Holger Weis https://github.com/betawax/role-model/blob/master/src/Betawax/RoleModel/RoleModel.php#L86-L101
	public function processRules($rules = array()) {
		$id = $this->getKey();
		if(count($rules) < 1) {
			$rules = $this->rules;
		}
		array_walk($rules, function(&$item) use ($id) {
			// Replace placeholders
			$item = stripos($item, ':id:') !== false ? str_ireplace(':id:', $id, $item) : $item;
		});

		return $rules;
	}

	public function getSpecialInputs() {
		return $this->special_inputs;
	}
}
