<?php namespace EternalSword\LPress;

use \DB as DB;
use \Eloquent as Eloquent;
use \Str as Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Collection;

class BaseModel extends Eloquent {
	protected $special_inputs = array('description' => 'text:textarea');

	protected $softDelete = TRUE;

	protected $pivots = array();

	protected function hasModelPermission($action) {
		$user = Auth::user();
		return $user->hasPermission('root');
	}

	protected function getAttributeValue($key) {
		$value = $this->getAttributeFromArray($key);

		// If the attribute has a get mutator, we will call that then return what
		// it returns as the value, which is useful for transforming values on
		// retrieval from the model to a form that is more useful for usage.
		if ($this->hasGetMutator($key)) {
			return $this->mutateAttribute($key, $value);
		}

		// If the attribute is an id, convert it to an int.
		if($key == 'id' || substr_compare($key, '_id', strlen($key)-3, 3) === 0) {
			return (int)$value;
		}

		// If the attribute is listed as a date, we will convert it to a DateTime
		// instance on retrieval, which makes it quite convenient to work with
		// date fields without having to create a mutator for each property.
		elseif (in_array($key, $this->getDates())) {
			if ($value) return $this->asDateTime($value);
		}

		return $value;
	}

	public function getPivots() {
		return $this->pivots;
	}

	public function setPivots($pivots) {
		if(is_array($pivots)) {
			$this->pivots = $pivots;
		}
	}

	public function saveItem($action) {
		if(!$this->hasModelPermission($action)) {
			return Redirect::back()->with(
				'std_errors',
				array(
					Lang::get('l-press::errors.executePermissionsError')
				)
			);
		}
		$this->save();
	}

	public function restoreItem() {
		if(!$this->hasModelPermission('restore')) {
			return Redirect::back()->with(
				'std_errors',
				array(
					Lang::get('l-press::errors.executePermissionsError')
				)
			);
		}
		$this->restore();
	}

	public function deleteItem() {
		if(!$this->hasModelPermission('delete')) {
			return Redirect::back()->with(
				'std_errors',
				array(
					Lang::get('l-press::errors.executePermissionsError')
				)
			);
		}
		if(self::all()->count() == 1) {
			return Redirect::back()->with(
				'std_errors',
				array(
					Lang::get('l-press::errors.lastModelItem')
				)
			);
		}
		$this->delete();
	}

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

	public function getDescendents($descendents = NULL) {
		if(is_null($descendents)) {
			$this->load('children');
			$descendents = $this->children;
		}
		if($descendents->count() == 0) {
			return new Collection;
		}
		$grandchildren = new Collection;
		foreach($descendents as $descendent) {
			$incubator = $descendent->children();
			if($incubator->count() > 0) {
				$grandchildren->merge($incubator);
			}
		}
		$incubator = $this->getDescendents($grandchildren);
		if($incubator->count() > 0) {
			$descendents->merge($incubator);
		}
		return $descendents;
	}
}
