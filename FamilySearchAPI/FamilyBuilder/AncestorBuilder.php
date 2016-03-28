<?php

require_once("FamilyBuilder.php");

	class AncestorBuilder extends FamilyBuilder {
	
		public function connect($family, $member, $gen)
		{
			
			// Check generation - cannot be over 4
			if($gen > 4)
			{
				return;
				//echo "<div class='well'>Gen too big</div>";
			}
			// Check if father is in the $family array
			
			if(array_key_exists((string)$member->getFatherString(), $family->members ))
			{
				// Set the father of the $member if the father ID is DIFFERENT than the child ID
				if($family->getMember($member->getId()) != $family->getMember($member->getFatherString()))
				{
					$family->getMember($member->getId())->setFather($family->members[(string)$member->getFatherString()]);
				
					
					$family->members[(string)$member->getFatherString()]->addChild();
					
					// Set the generation
					$family->getMember($member->getId())->getFather()->setGen($gen);
					
					// Call setParents to set the next generation
					//echo "<div class='well'>$gen +1</div>";
					$this->connect($family, $family->getMember($member->getId())->getFather(), ($gen + 1));
				}

			}
			else
			{
				//echo "<div class='well'>No Father Found</div>";
			}

			
			// Check if mother is in the family array
			if(array_key_exists((string)$member->getMotherString(), $family->members ))
			{
				// Set the father of the $member
				if($family->getMember($member->getId()) != $family->getMember($member->getMotherString()))
				{
					$family->getMember($member->getId())->setMother($family->members[(string)$member->getMotherString()]);
					$family->members[(string)$member->getMotherString()]->addChild();
					//echo $family->getMember($member->getId())->getMother()->getId();
					
					// Set the generation
					$family->getMember($member->getId())->getMother()->setGen($gen);
					//echo " Mother ".$family->getMember($member->getId())->getMother()->getGen()."<br />";
					
					// Call setParents to set the next generation
					//echo "<div class='well'>$gen +1</div>";
					$this->connect($family, $family->getMember($member->getId())->getMother(), ($gen + 1));
				}
			}
			else
			{
				//echo "<div class='well'>No Mother Found</div>";
			}


		
		}
	
	}


?>