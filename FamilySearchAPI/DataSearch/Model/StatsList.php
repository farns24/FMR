<?php
	/**
	* List of males and females in a generation.
	*
	*/
	class StatsList {
	
		private $males = array();
		
		private $females = array();
		
		private $total = array();
		
		public function insert($member)
		{
			if(strtolower($member->getGender()) == "male")
			{
				$this->males[] = $member;
			}
			else if(strtolower($member->getgender()) == "female")
			{
				$this->females[] = $member;
			}
			
			$this->total[] = $member;
		
		}
		
		public function getMales()
		{
			return $this->males;
		}

		public function getFemales()
		{
			return $this->females;
		}
		
		public function getTotal()
		{
			return $this->total;
		}

	}

?>