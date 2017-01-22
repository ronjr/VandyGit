<?php
require_once FILEROOT."BO/Project.php";
require_once FILEROOT."DAL/ProjectRepository.php";
require_once FILEROOT."BO/Setting.php";
require_once FILEROOT."BLL/SettingManager.php";

class ProjectManager
{
	//region Repository functions __________________________________________________________________________
	
	/**
	 * 
	 * @param string $name
	 * @return Project
	 */
	public function GetByName($name)
	{
		$pr = new ProjectRepository();
		$project = $pr->GetByName($name);
		return $project;
	}
	
	/**
	 * 
	 * @param int $id
	 * @return Project
	 */
	public function GetById($id)
	{
		$pr = new ProjectRepository();
		$project = $pr->GetById($id);
		return $project;
	}
	
	/**
	 * 
	 * @return Project[]
	 */
	public function GetAll()
	{
		$pr = new ProjectRepository();
		return $pr->GetAll();
	}
	
	/**
	 * 
	 * @param Project $project
	 * @return int
	 */
	public function Save(Project $project)
	{
		if(is_numeric($project->id) && $project->id > 0)
		{
			try 
			{
				return $this->Replace($project);
			}
			catch (Exception $e)
			{
				echo $e->getMessage();
				throw $e;
			}
		}
		else
		{
			try 
			{
				return $this->Create($project);
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}
	}
	
	protected function Update(Project $project)
	{
		$pr = new ProjectRepository();
		return $pr->Update($project);
	}
	
	protected function Create(Project $project)
	{
		$pr = new ProjectRepository();
		return $pr->Create($project);
	}
	
	protected function Replace(Project $project)
	{
		$pr = new ProjectRepository();
		return $pr->Replace($project);
	}
	
	
	/**
	 * 
	 * @param Project $project
	 * @return int RowsAffected
	 */
	public function Delete(Project $project)
	{
		$pr = new ProjectRepository();
		return $pr->Delete($project);
	}
	
	/**
	 * 
	 * @param Project $project
	 * @return int RowsAffected
	 */
	public function DeleteAll()
	{
		$pr = new ProjectRepository();
		return $pr->DeleteAll();
	}
	
	//endregion Repository functions _______________________________________________________________________
	
	//region Validation functions __________________________________________________________________________
	
	public function ValidatePost($post)
	{
		$errors = array();
		$rules = $this->GetValidationRules();
		$errors = validateFields($post, $rules);
		return $errors;
	}
	
	public function ValidateFileUpload($fileUpload)
	{
		$errors = array();
		$rules = $this->GetFileUploadValidationRules();
		$errors = validateFields($fileUpload, $rules);
		return $errors;
	}
	
	public function GetFieldErrors($field, $errors)
	{
		$fieldErrors = array();
		foreach($errors as $error)
		{
			if($error->Field == $field)
			{
				$fieldErrors[] = $error;
			}
		}
		return $fieldErrors;
	}
	
	public function GetValidationRules()
	{
		$rules = array();
		
		//Title
		$rules[] = "required|||name|||name is required.";
		$rules[] = "length=1-45|||name|||name requires 1-45 characters.";

		//Notes
		$rules[] = "required|||description|||description is required.";
		$rules[] = "length=1-255|||description|||description requires 1-255 characters.";
		
		//Height
		$rules[] = "required|||url|||url is required.";
		$rules[] = "length=1-255|||url|||url requires 1-255 characters.";
		
		return $rules;
	}

	//endregion Validation functions _______________________________________________________________________
	
	
	//region Public functions ______________________________________________________________________________
	
	/**
	 *
	 * @param int $minstars
	 * @return string
	 */
	public function ImportGitObjectsByStars($minstars)
	{
		$page_number = 1; //initial page
		$per_page = 100; //max 100 results per query allowed
		$pages = 0;
		$header = "";
		$body = "";
		
		$error = $this->FetchByStarsPaged($minstars, $per_page, $page_number, $header, $body);
		if($error == "")
		{
			$result = json_decode($body);
			$pages = floor($result->total_count / $per_page) + 1;
			$pages = $pages > 10 ? 10 : $pages; // max of 10 queries per minute
			$this->DeleteAll(); // delete any existing data before starting import
			$sm = new SettingManager();
			$setting = $sm->GetByName('time_imported');
			$setting->value = time();
			$sm->Save($setting);
			$this->ImportProjects($result->items);
		}
		while($error == "" && $body > "" && $pages >= $page_number) // If more than one page, keep querying data until pages reached or error
		{
			$page_number++;
			$error = $this->FetchByStarsPaged($minstars, $per_page, $page_number, $header, $body);
			$result = json_decode($body);
			$this->ImportProjects($result->items);
		}
		return $error;
	}

	
	/**
	 *
	 * @param int $minstars
	 * @return string
	 */
	public function UpdateGitObjectsByStars($minstars)
	{
		$page_number = 1; //initial page
		$per_page = 100; //max 100 results per query allowed
		$pages = 0;
		$header = "";
		$body = "";

		$sm = new SettingManager();
		$setting = $sm->GetByName('time_imported');

		$error = $this->UpdateByStarsPaged($minstars, $per_page, $page_number, $header, $body, $setting->value);
		if($error == "")
		{
			$result = json_decode($body);
			$pages = floor($result->total_count / $per_page) + 1;
			$pages = $pages > 10 ? 10 : $pages; // max of 10 queries per minute
			$this->ImportProjects($result->items);
		}
		while($error == "" && $body > "" && $pages >= $page_number) // If more than one page, keep querying data until pages reached or error
		{
			$page_number++;
			$error = $this->UpdateByStarsPaged($minstars, $per_page, $page_number, $header, $body, $setting->value);
			$result = json_decode($body);
			$this->ImportProjects($result->items);
		}
		$setting->value = time();
		$sm->Save($setting);
		return $error;
	}
	
	//endregion Public functions ___________________________________________________________________________
	
	
	//region Private functions _____________________________________________________________________________
	
	private function FetchByStarsPaged($minstars, $per_page, $page_number, &$header, &$body)
	{
		$error = "";
		$url = 'https://api.github.com/search/repositories?q=stars:%3E='.$minstars.'+is:public&sort=stars&order=desc&page='.$page_number.'&per_page='.$per_page;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_USERAGENT, 'VandyGit');
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
		curl_setopt($ch, CURLOPT_USERPWD, "GITUSER:GITPASS");
		curl_setopt($ch, CURLOPT_HEADER, true);
		$result = curl_exec($ch);
		$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		$header = $this->ParseGitHeaders(substr($result, 0, $header_size));
		$body = substr($result, $header_size);
		curl_close($ch);
		if(isset($header['X-RateLimit-Remaining']))
		{
			if($header['X-RateLimit-Remaining'] < 1)
			{
				$error = "Query Limit Reached.  Please wait 1 minute before querying again.";
			}
		}
		else
		{
			$error = "Unable to query results at this time."; 
		}
		return $error;
	}

	private function UpdateByStarsPaged($minstars, $per_page, $page_number, &$header, &$body, $time_imported)
	{
		$error = "";
		$url = 'https://api.github.com/search/repositories?q=stars:%3E='.$minstars.'+is:public+pushed:>='.date(DATE_ISO8601,$time_imported).'&sort=stars&order=desc&page='.$page_number.'&per_page='.$per_page;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_USERAGENT, 'VandyGit');
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
		curl_setopt($ch, CURLOPT_USERPWD, "GITUSER:GITPASS");
		curl_setopt($ch, CURLOPT_HEADER, true);
		$result = curl_exec($ch);
		$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		$header = $this->ParseGitHeaders(substr($result, 0, $header_size));
		$body = substr($result, $header_size);
		curl_close($ch);
		if(isset($header['X-RateLimit-Remaining']))
		{
			if($header['X-RateLimit-Remaining'] < 1)
			{
				$error = "Query Limit Reached.  Please wait 1 minute before querying again.";
			}
		}
		else
		{
			$error = "Unable to query results at this time.";
		}
		return $error;
	}
	
	private function ImportProjects($gitobjects)
	{
		foreach($gitobjects as $item)
		{
			$project = new Project();
			$this->ImportProject($item, $project);
			$this->Save($project);
		}
	}
	
	private function ImportProject($gitobject, &$project)
	{
		$vars = get_object_vars($project);
		foreach($vars as $key=>$value)
		{
			if($key == "updated_at" || $key == "created_at" || $key == "pushed_at")
			{
				$project->$key = strtotime($gitobject->$key);
			}
			else
			{
				$project->$key = $gitobject->$key;
			}
		}
	}
	
	private function ParseGitHeaders($raw_headers) {
		$headers = array();
		$key = '';

		foreach(explode("\n", $raw_headers) as $i => $h) {
			$h = explode(':', $h, 2);

			if (isset($h[1])) {
				if (!isset($headers[$h[0]]))
					$headers[$h[0]] = trim($h[1]);
					elseif (is_array($headers[$h[0]])) {
						$headers[$h[0]] = array_merge($headers[$h[0]], array(trim($h[1])));
					}
					else {
						$headers[$h[0]] = array_merge(array($headers[$h[0]]), array(trim($h[1])));
					}

					$key = $h[0];
			}
			else {
				if (substr($h[0], 0, 1) == "\t")
					$headers[$key] .= "\r\n\t".trim($h[0]);
					elseif (!$key)
					$headers[0] = trim($h[0]);
			}
		}

		return $headers;
	}
	
	//endregion Private functions __________________________________________________________________________
	
}