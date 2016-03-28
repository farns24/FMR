<?php
require_once("FamilyBuilder.php");
// Recursive function to set the generations of an entire family

/**
* Loop through the children of the member provided
*
*for each child of the target member
*
*Look up member in the family array
*
*if found, set the 
*
*/

	class DecendenceBuilder extends FamilyBuilder {
	
		/**
		* @pre Gen must be less than or equal to 4
		*/
		public function connect($family, $member, $gen)
		{
			//echo "<div class='well'>$gen</div>";
			// Check generation - cannot be over 4
			if($gen > 4)
			{
				return;
			}
			// Check if father is in the $family array

			//For each child of the member
			if ($member->getChildArray()){
				foreach($member->getChildArray() as $key => $child){
					
					//If the child listed is included in the family map
					if( array_key_exists((string)$child->attributes()->id, $family->members ))
					{
						//Indicates that individual is not being added to family array.
						//echo "<h1>Key exists</h1>";
						// Set the Child of the $member if the child ID is DIFFERENT than the parent ID
						if($family->getMember((string)$member->getId()) != $family->getMember($key))
						{
							$family->getMember($member->getId())->addChildModel($family->members[(string)$key]);
							$family->getMember((string)$member->getId())->getChildModel((string)$key)->addChild();
								
								// Set the generation
							$family->getMember((string)$member->getId())->getChildModel((string)$key)->setGen($gen);
							//echo "<div class='well'>$gen +1</div>";
							$this->connect($family, $family->getMember((string)$member->getId())->getChildModel((string)$key), ($gen + 1));
						}
						else
						{
							//echo "the father member has the same id as the child id";
						}
					}
					else
					{
						//echo "Lost child with id of ".(string)$child->attributes()->id;
					}
				}
			}
			else
			{
				//echo "No Children"; 
			}


		}
	
	}

?>