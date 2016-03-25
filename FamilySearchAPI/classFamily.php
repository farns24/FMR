<?php
// Definition of class Family

class Family
{
	// Private data members
	private $id = "";
	public $members = array();
	
	// Just set the families' $id.  Set the other variables as the xml is parsed
	public function Family($id)
	{
		//echo "<br> Family created with family id = ".$id."<br>";
		// Set the families' id
		$this->id = $id;
	}
	
	// Public Properties
	public function setId($id) { $this->id = $id; }
	public function getId() { return $this->id; }
	
	
	// When adding a person to the array, use their id as key
	public function addMember($member) {
	//echo json_encode($this->members);
	$this->members[(string)$member->getId()] = $member; 
	
	}
	
	// When retrieving people from the array, use their id as the key to access the corresponding Person object
	public function getMember($id) {
	//echo "<h2>getMember</h2>".json_encode($this->members)."<h2>Id</h2>$id";
	return $this->members[(string)$id]; }
	
	// Gets the number of people corrently in the familys' array
	public function count() { return count($this->members); }
}

?>