<?php
require_once FILEROOT."BO/Setting.php";
require_once FILEROOT."DAL/SettingRepository.php";

class SettingManager
{
	//region Repository functions __________________________________________________________________________
	
	/**
	 * 
	 * @param string $name
	 * @return Setting
	 */
	public function GetByName($name)
	{
		$sr = new SettingRepository();
		return $sr->GetByName($name);
	}
	
	/**
	 * 
	 * @param int $id
	 * @return Setting
	 */
	public function GetById($id)
	{
		$sr = new SettingRepository();
		return $sr->GetById($id);
	}
	
	/**
	 * 
	 * @return Setting[]
	 */
	public function GetAll()
	{
		$sr = new SettingRepository();
		return $sr->GetAll();
	}
	
	/**
	 * 
	 * @param Setting $setting
	 * @return int
	 */
	public function Save(Setting $setting)
	{
		if(is_numeric($setting->id) && $setting->id > 0)
		{
			try 
			{
				return $this->Update($setting);
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}
		else
		{
			try 
			{
				return $this->Create($setting);
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}
	}
	
	protected function Update(Setting $setting)
	{
		$sr = new SettingRepository();
		return $sr->Update($setting);
	}
	
	protected function Create(Setting $setting)
	{
		$sr = new SettingRepository();
		return $sr->Create($setting);
	}
	
	/**
	 * 
	 * @param Setting $setting
	 * @return int RowsAffected
	 */
	public function Delete(Setting $setting)
	{
		$sr = new SettingRepository();
		return $sr->Delete($setting);
	}

	//endregion Repository functions _______________________________________________________________________
	
	
	//region Validation functions __________________________________________________________________________
		
	
	//endregion Validation functions _______________________________________________________________________
	
	
	//region Public functions ______________________________________________________________________________

	
	//endregion Public functions ___________________________________________________________________________
	
	
	//region Private functions _____________________________________________________________________________
	
	
	//endregion Private functions __________________________________________________________________________
	
}