<?php
// Definition of class Person
// To be used to calculate spatial statistics across multiple generations		
class Person
{
	// Declare private variables to be used to store the person/relation information
	// $id should be formatted as a string - "****-***"
	private $id = "";
	// $gen indicates generation - valid values = '0', '1', '2', '3', '4'
	private $gen = "";
	
	// how many people count this person as a parent?
	private $numChild = 0;
	
	private $name = "";
	
	public function setName($name)
	{
		$this->name = $name;
	}
	
	// $father and $mother are both $person objects
	private $father = null;
	private $mother = null;
	
	private $children = array();
	
	private $childModel = array();
	// Parents reference information
	private $fatherString = null;
	private $motherString = null;
	// $birthPlace and $deathPlace are Place objects
	private $birthPlace = null;
	private $deathPlace = null;
	
	private $birthDate = -999;
	private $deathDate = -999;
	private $gender = "-999";
	private $birthPlaceStr = "None Listed";
	private $deathPlaceStr = "None Listed";
	
	// Distances to family members
	private $distToParents = -999;
	private $distToGParents = -999;
	private $distToGGParents = -999;
	private $distToGGGParents = -999;
	
	// Just set the persons' $id.  Set the other variables as the xml is parsed
	public function __construct($id,$credentials)
	{
		// Set the person id
		
		$this->id = $id;
		$this->birthPlace = new Place(0, "-999", -999, -999,$credentials,"");
		$this->deathPlace = new Place(0, "-999", -999, -999,$credentials,"");
		return $this;
	}
	
	// $id property accesors
	public function setId($id) { $this->id = $id; }
	public function getId() { return $this->id; }

	// $gen property accesors
	public function setGen($gen)
	{
		if($gen == 0 || $gen == 1 || $gen == 2 || $gen == 3 || $gen == 4)
		{
			$this->gen = $gen;
		}
		else
		{
			echo "INVALID GENERATION INDICATOR FOR ".$id;
		}
	}
	public function getGen() { return $this->gen; }
	
	public function getNumChild() { return $this->numChild;}
	public function addChild() { $this->numChild++; }
	
	// Distances  property accesors
	public function setDistTo($gen, $dist)
	{
		switch ($gen)
		{
			case '1':
				$this->distToParents = $dist;
				break;
			case '2':
				$this->distToGParents = $dist;				
				break;
			case '3':
				$this->distToGGParents = $dist;
				break;
			case '4':
				$this->distToGGGParents = $dist;
				break;
		}
	}
	public function getDistTo($gen)
	{
		switch ($gen)
		{
			case '1':
				return $this->distToParents;
				break;
			case '2':
				return $this->distToGParents;
				break;
			case '3':
				return $this->distToGGParents;
				break;
			case '4':
				return $this->distToGGGParents;
				break;
		}
	}
	
	// $father property accesors
	public function setFather($father) { $this->father = $father; }
	public function getFather() { return $this->father; }
	
	// $mother property accesors
	public function setMother($mother) { $this->mother = $mother; }
	public function getMother() { return $this->mother; }
	
	// $fatherString property accesors
	public function setFatherString($fatherString) { $this->fatherString = $fatherString; }
	public function getFatherString() { return $this->fatherString; }
	
	// $motherString property accesors
	public function setMotherString($motherString) { $this->motherString = $motherString; }
	public function getMotherString() { return $this->motherString; }
	
	// $childString property accesser
	public function getChildArray()
	{ 
		if (isset($this->children)){
			//echo "<h4>getChildArray()</h4>".json_encode($this->children)."<h4>count</h4>".count($this->children)."<h3>Id</h3>$this->id";
			return $this->children;
		}
		else
		{
			return array();
		}
	}
	
	
	// $birthPlace property accesors
	public function setBirthPlace($birthPlace) { $this->birthPlace = $birthPlace; }
	public function getBirthPlace() { return $this->birthPlace; }
	
	// $deathPlace property accesors
	public function setDeathPlace($deathPlace) { $this->deathPlace = $deathPlace; }
	public function getDeathPlace() { return $this->deathPlace; }
	
	// $birthDate property accesors
	public function setBirthDate($birthDate) { $this->birthDate = $birthDate; }
	public function getBirthDate() { return $this->birthDate; }
	
	// $deathDate property accesors
	public function setDeathDate($deathDate) { $this->deathDate = $deathDate; }
	public function getDeathDate() { return $this->deathDate; }
	
	// $gender property accesors
	public function setGender($gender) { $this->gender = $gender; }
	public function getGender() { return $this->gender; }
	
	// Function to stub out a filler person with 'valid' (null) place holders
	static function stubPerson()
	{
		// Create a new Person object
		$person = new Person(-999);
				
		// $father and $mother are both $person objects
		$person->setFather(-999);
		$person->setMother(-999);
		// $birthPlace and $deathPlace are Place objects
		$person->setBirthPlace = new Place(0, "-999", -999, -999);
		$person->setDeathPlace = new Place(0, "-999", -999, -999);
		$person->setChilrdenArray(array());
		return $person;
	}
	
	public function __toString()
    {
        return (string)$this->id;
    }
	
	public function addNewChild($child)
	{
		
		if (isset($this->children)==false)
		{
			$this->children = array();
		}
			
		$this->children[(string)$child["id"]] = $child;

	}

	public function echoState()
	{

	}
	/**
	*@param - classPerson instance
	*/
	public function addChildModel($childModel)
	{
		array_push($this->childModel,$childModel);
	}
	
	public function getChildModel($key)
	{

		foreach($this->childModel as $model)
		{

			if ((string)$model->getId()==(string)$key)
			{
				return $model;
			}
		}
		return null;
	}
	public function getChildModelArray()
	{
		//echo "<H1>getChildModelArray()</h1>".json_encode($this->childModel);
		return $this->childModel;
	}
	
	public function getMapLayout()
	{
		
		$stats= "<Name</b> ".$this->name."<br><b>ID </b>".$this->id;
		//var_dump($this->father);
		if(isset($this->father)&&function_exists('getMapLayout')){
			$stats.= "<br><b>Father</b> ".$this->father.getMapLayout();
		}

		if (isset($this->birthPlaceStr))
		{
			//$stats.="<br><b>BirthPlace</b> ".$this->birthPlaceStr;
		}
		$stats = str_replace("'","\'",$stats);
		return $stats;
	}
	public function setBirthPlaceStr($birthValue)
	{
		$this->birthPlaceStr = $birthValue->place->original;
		
	}
	public function getBirthPlaceStr()
	{
		return $this->birthPlaceStr;
	}
	
	public function setDeathPlaceStr($birthValue)
	{
		$this->deathPlaceStr = $birthValue->place->original;
		
	}
	public function getDeathPlaceStr()
	{
		return $this->deathPlaceStr;
	}
}
?>