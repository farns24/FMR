<?php
	/**
	* Model for individuals in a generation. Tracks Males, females, and total
	*
	* TODO Memory optimization: remove total and combind mail and femail for needed operations.
	*/
	class StatsList {
	
		private $males = array();
		
		private $females = array();
		
		private $total = array();
		
		/**
		* Inserts a new person into the model
		*
		* @pre member must have a gender
		*
		* @throws Unknown Gender Exception
		*/
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
			else
			{
				throw new Exception("Unknown Gender Exception");
			}
			
			$this->total[] = $member;
		
		}
		
		/**
		* @return list of males in generation
		*/
		public function getMales()
		{
			return $this->males;
		}

		/**
		* @return list of females in generation
		*/
		public function getFemales()
		{
			return $this->females;
		}
		
		/**
		* @return list of all members of the generation
		*/
		public function getTotal()
		{
			return $this->total;
		}
		
		/**
		* @return total count of people in genration
		*/
		public function size()
		{
			return count($this->total);
		}

	}

?>