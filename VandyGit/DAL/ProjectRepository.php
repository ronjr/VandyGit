<?php
require_once FILEROOT."DAL/BaseRepositoryNoAI.php";
require_once FILEROOT."DAL/DB.php";
require_once FILEROOT."BO/Project.php";


class ProjectRepository extends BaseRepositoryNoAI
{
	//region public functions _____________________________________________________________
	
	/**
	 * 
	 * @param $name
	 * @return Project
	 */
	public function GetByName($name)
	{
		$stmt = DB::getInstance(DBDATABASE)->prepare("SELECT * FROM projects WHERE name = :name");
		$stmt->bindParam(':name', strtolower(trim($name)), PDO::PARAM_STR);
		$stmt->execute();
		$stmt->setFetchMode(PDO::FETCH_ASSOC);
		
		$project = null;
		
		while ($row = $stmt->fetch())
		{
			$project = $this->RowToObject($row);
		}
		return $project;
	}
	
	//endregion public functions __________________________________________________________
	
	
	//region CRUD functions _______________________________________________________________

	public function GetById($id)
	{
		return parent::DbGetById(DBDATABASE, 'projects', $id);
	}
	
	public function GetAll()
	{
		return parent::DbGetAll(DBDATABASE, 'projects', 'Name');
	}
	
	public function Create(Project $project)
	{
		return parent::DbCreate(DBDATABASE, 'projects', $project);
	}
	
	public function Update(Project $project)
	{
		return parent::DbUpdate(DBDATABASE, 'projects', $project);
	}

	public function Replace(Project $project)
	{
		$row = $this->ObjectToRow($project);
		$fields = $this->GetObjectFieldNames($row);
		$update = array();
		foreach($fields as $key => $value)
		{
			$update[] = "$value=:$value";
		}
		$update = 'SET ' . implode(', ', $update);
		$sql = sprintf('REPLACE INTO projects %s', $update);
		$stmt = DB::getInstance(DBDATABASE)->prepare($sql);
		
		foreach($row as $key => &$value)
		{
			if($this->GetParameterTypeForField($key) != null)
			{
				$field = ':'.$key;
				$stmt->bindParam($field, $value, $this->GetParameterTypeForField($key));
			}
		}
		return $stmt->execute();
	}

	public function Delete(Project $project)
	{
		return parent::DbDelete(DBDATABASE, 'projects', $project);
	}
	
	public function DeleteAll()
	{
		$stmt = DB::getInstance(DBDATABASE)->prepare("TRUNCATE TABLE projects");
		return $stmt->execute();
	}
	
	//endregion CRUD functions ____________________________________________________________
	
	
	//region Protected functions ____________________________________________________________

	protected function RowToObject($row)
	{
		$object = new Project();
		$object->id = $row['id'];
		$object->name =  $this->RowItemToString($row['name']);
		$object->description =  $this->RowItemToString($row['description']);
		$object->url =  $this->RowItemToString($row['url']);
		$object->created_at = $this->RowItemToDateTime($row['created_at']);
		$object->updated_at = $this->RowItemToDateTime($row['updated_at']);
		$object->pushed_at = $this->RowItemToDateTime($row['pushed_at']);
		$object->stargazers_count = $row['stargazers_count'];
		return $object;
	}
	
	protected function ObjectToRow($object)
	{
		$row = array();
		$row['id'] = $object->id;
		$row['name'] = $object->name;
		$row['description'] = $object->description;
		$row['url'] = $object->url;
		$row['created_at'] = $this->DateTimeToRowItem($object->created_at);
		$row['updated_at'] = $this->DateTimeToRowItem($object->updated_at);
		$row['pushed_at'] = $this->DateTimeToRowItem($object->pushed_at);
		$row['stargazers_count'] = $object->stargazers_count;
		return $row;
	}
	
	protected function GetParameterTypeForField($field)
	{
		switch($field) 
		{
			case "id":
			case "stargazers_count":
				return PDO::PARAM_INT;
				break;
			case "name":
			case "description":
			case "url":
			case "created_at": //DateTime
			case "updated_at": //DateTime
			case "pushed_at": //DateTime
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