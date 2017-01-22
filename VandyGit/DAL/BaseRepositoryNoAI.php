<?php

abstract class BaseRepositoryNoAI
{
	/**
	 * @param $database
	 * @param $table
	 * @param $id
	 * @return object
	 */
	protected function DbGetById($database, $table, $id)
	{
		$stmt = DB::getInstance($database)->prepare(sprintf('SELECT * FROM %s WHERE id = :id', $table));
		$stmt->bindParam(':id', $id, PDO::PARAM_INT);
		$stmt->execute();
		$stmt->setFetchMode(PDO::FETCH_ASSOC);
		
		$object = null;
		
		while($row = $stmt->fetch())
		{
			$object = $this->RowToObject($row);
		}
		return $object;
	}
	
	/**
	 * @param $database
	 * @param $table
	 * @param $order
	 * @return array of objects
	 */
	protected function DbGetAll($database, $table, $order)
	{
		$stmt = DB::getInstance($database)->prepare(sprintf('SELECT * FROM %s ORDER BY %s', $table, $order));
		$stmt->execute();
		$stmt->setFetchMode(PDO::FETCH_ASSOC);
		$result = array();
		while ($row = $stmt->fetch())
		{
			$result[] = $this->RowToObject($row);
		}
		return $result;
	}
	
	protected function DbCreate($database, $table, $object)
	{
		$row = $this->ObjectToRow($object);
		$fields = implode(', ', $this->GetObjectFieldNames($row));
		$parameters = ':' . implode(', :', $this->GetObjectFieldNames($row));
		$sql = sprintf('INSERT INTO %s (%s) VALUES (%s)', $table, $fields, $parameters);
		$stmt = DB::getInstance($database)->prepare($sql);

		foreach($row as $key => &$value)
		{
			if($this->GetParameterTypeForField($key) != null)
			{
				$field = ':'.$key;
				$stmt->bindParam($field, $value, $this->GetParameterTypeForField($key));
			}
		}
		$stmt->execute();
		return DB::getInstance($database)->lastInsertId();
	}
	
	protected function DbUpdate($database, $table, $object)
	{
		$row = $this->ObjectToRow($object);
		if(array_key_exists('id', $row))
		{
			unset($row['id']);
		}
		$fields = $this->GetObjectFieldNames($row);
		$update = array();
		foreach($fields as $key => $value)
		{
			$update[] = "$value=:$value";
		}
		$update = 'SET ' . implode(', ', $update);
		$sql = sprintf('UPDATE %s %s WHERE id=:id', $table, $update);
		$stmt = DB::getInstance($database)->prepare($sql);
		
		foreach($row as $key => &$value)
		{
			if($this->GetParameterTypeForField($key) != null)
			{
				$field = ':'.$key;
				$stmt->bindParam($field, $value, $this->GetParameterTypeForField($key));
			}
		}
		$stmt->bindParam(':id', $object->id, PDO::PARAM_INT);
		return $stmt->execute();
	}
	
	protected function DbDelete($database, $table, $object)
	{
		$sql = sprintf('DELETE FROM %s WHERE id=:id', $table);
		$stmt = DB::getInstance($database)->prepare($sql);
		$stmt->bindParam(':id', $object->id, PDO::PARAM_INT);
		return $stmt->execute();
	}

	protected function GetObjectFieldNames($object)
	{
		$fields = array();
		foreach($object as $key => $value)
		{
			$fields[] = $key;
		}
		
		return $fields;
	}
	
	protected function RowItemToString($rowItem)
	{
		if ( get_magic_quotes_gpc() )
			return htmlspecialchars_decode(stripslashes($rowItem));
		else
			return $rowItem;
	}
	
	protected function RowItemToNullableDateTime($rowItem)
	{
		return strtotime($rowItem) > strtotime('00/00/0000') ? date('c', strtotime($rowItem)) : '';
	}

	protected function NullableDateTimeToRowItem($item)
	{
		return $item == '' ? '' : date('c', strtotime($item));
	}

	protected function DateTimeToRowItem($rowItem)
	{
		return date('c', $rowItem);
	}
	
	protected function RowItemToDateTime($rowItem)
	{
		if($rowItem instanceof DateTime){
			return $rowItem->getTimestamp();
		} else {
			return strtotime($rowItem);
		}
	}
	
	/**
	 * returns an object from a database row
	 * @param $row
	 * @return $object
	 */
	abstract protected function RowToObject($row);

	/**
	 * returns a database row from an object
	 * @param $object
	 * @return $row
	 */
	abstract protected function ObjectToRow($object);
	
	/**
	 * return the PDO Parameter Type for the supplied field
	 * @param $field
	 * @return string
	 */
	abstract protected function GetParameterTypeForField($field);
}

?>