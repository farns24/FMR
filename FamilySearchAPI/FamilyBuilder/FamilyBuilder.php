<?php

	abstract class FamilyBuilder {
		private $version = 5;
		public abstract function connect($family, $member, $gen);
	
	}

?>