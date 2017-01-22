<?php
require_once FILEROOT."DAL/BaseRepository.php";
require_once FILEROOT."DAL/DB.php";
require_once FILEROOT."BO/Setting.php";


class SettingRepository extends BaseRepository
{
	//region public functions _____________________________________________________________
	
	/**
	 * 
	 * @param $name
	 * @return Setting
	 */
	public function GetByName($name)
	{
		$stmt = DB::getInstance(DBDATABASE)->prepare("SELECT * FROM settings WHERE name = :name");
		$stmt->bindParam(':name', strtolower(trim($name)), PDO::PARAM_STR);
		$stmt->execute();
		$stmt->setFetchMode(PDO::FETCH_ASSOC);
		
		$setting = null;
		
		while ($row = $stmt->fetch())
		{
			$setting = $this->RowToObject($row);
		}
		return $setting;
	}
	
	//endregion public functions __________________________________________________________
	
	
	//region CRUD functions _______________________________________________________________

	public function GetById($id)
	{
		return parent::DbGetById(DBDATABASE, 'settings', $id);
	}
	
	public function GetAll()
	{
		return parent::DbGetAll(DBDATABASE, 'settings', 'Name');
	}
	
	public function Create(Setting $setting)
	{
		return parent::DbCreate(DBDATABASE, 'settings', $setting);
	}
	
	public function Update(Setting $setting)
	{
		return parent::DbUpdate(DBDATABASE, 'settings', $setting);
	}

	public function Delete(Setting $setting)
	{
		return parent::DbDelete(DBDATABASE, 'settings', $setting);
	}
	
	//endregion CRUD functions ____________________________________________________________
	
	
	//region Protected functions ____________________________________________________________

	protected function RowToObject($row)
	{
		$object = new Setting();
		$object->id = $row['id'];
		$object->name =  $this->RowItemToString($row['name']);
		$object->value =  $this->RowItemToString($row['value']);
		return $object;
	}
	
	protected function ObjectToRow($object)
	{
		$row = array();
		$row['id'] = $object->id;
		$row['name'] = $object->name;
		$row['value'] = $object->value;
		return $row;
	}
	
	protected function GetParameterTypeForField($field)
	{
		switch($field) 
		{
			case "id":
				return PDO::PARAM_INT;
				break;
			case "name":
			case "value":
				return PDO::PARAM_STR;
				break;
			default:
				return null;
				break;
		}
	}
	
	//endregion Protected functions _________________________________________________________
	
}

?>