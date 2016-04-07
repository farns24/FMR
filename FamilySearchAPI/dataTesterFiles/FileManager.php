<?php

	class FMRFileManager
	{
		public function storeGenerationData($rootList,$firstArray,$secondArray,$thirdArray,$fourthArray)
		{
			$rootOut = $this->getFileData($rootList);
			file_put_contents("data/root.txt", $rootOut);
			$firstOut = $this->getFileData($firstArray);
			file_put_contents("data/first.txt", $firstOut);
			$secondOut = $this->getFileData($secondArray);
			file_put_contents("data/second.txt", $secondOut);
			$thirdOut = $this->getFileData($thirdArray);
			file_put_contents("data/third.txt", $thirdOut);
			$fourthOut = $this->getFileData($fourthArray);
			file_put_contents("data/fourth.txt", $fourthOut);
	
		}
		
		private function getFileData($rootList){
			$rootOut = "";
			foreach($rootList as $p)
			{
				$rootOut .= $p->getId().",";
			}
			return $rootOut;
		}
	}
?>