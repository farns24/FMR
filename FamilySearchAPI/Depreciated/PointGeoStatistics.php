<?php
// Library of geostatistical functions to aid analysis

//Determines the distance from one geolocation to another using an oblate sphereoid formula
// The first 4 parameters are the origin lat/lng and destination lat/lng
// The 5th parameter is the radius of the spherical body being traversed - default: Earth radius in Statute Miles
function greatCircleDist($lat1, $lng1, $lat2, $lng2, $radius = 3958.76, $degree="N")
{
	$rad = doubleval(M_PI/180.0);
	
	$lat1 = doubleval($lat1) * $rad;
	$lng1 = doubleval($lng1) * $rad;
	$lat2 = doubleval($lat2) * $rad;
	$lng2 = doubleval($lng2) * $rad;
	
	$theta = $lng2 - $lng1;
	// Spherical Law of Cosines - Compute distances on a sphere
	//$distSphere = acos(sin($lat1) * sin($lat2) + cos($lat1) *cos($lat2) * cos($theta));
	// Vincenty formula - Compute distances on an ellipsoid
	$dist = atan(sqrt(pow((cos($lat2)*sin($theta)), 2)+pow((cos($lat1)*sin($lat2))-(sin($lat1)*cos($lat2)*cos($theta)), 2))/((sin($lat1)*sin($lat2))+(cos($lat1)*cos($lat2)*cos($theta))));
	if ($dist < 0)
	{
		$dist += M_PI;
	}
	//return the degree distance if $degree == "Y"
	if($degree == "Y" || $degree == "y")
	{
		return $dist;
	}
	
	return $dist = $dist * $radius;
}

//Determines the direction from true north from point A to point B
function greatCircleDirection($lat1, $lng1, $lat2, $lng2, $radius = 3958.76)
{
	if($lat1 != -1 && $lng1 != -1)
	{
		$dist = greatCircleDist($lat1, $lng1, $lat2, $lng2, 3958.76, "Y");
		$rad = doubleval(M_PI/180.0);
		$lat1 = doubleval($lat1) * $rad;
		$lng1 = doubleval($lng1) * $rad;
		$lat2 = doubleval($lat2) * $rad;
		$lng2 = doubleval($lng2) * $rad;
		
		$direc = doubleval(180.0/M_PI)*acos((sin($lat2)-sin($lat1)*cos($dist))/(cos($lat1)*sin($dist)));
		if(sin($lng1-$lng2) < 0)
		{
			$direc = 360-$direc;
			return $direc;
		}
	}
	else
	{
		$direc = -1;
	}
	return 	$direc;
}

//Calculates the Mean Center of a group of points
// Accepts 2 arrays - 1 for latitudes, 1 for longitudes
// Returns a lat long pair as an array - access array properties using square brackets
// Ex: $mCenter = meanCenter($lats, $lons);
// 	 $latMean = mCenter.lat;
// 	 $lonMean = mCenter.lon;
function meanCenter($LatitudeArray, $LongitudeArray)
{
	$latMean = mean($LatitudeArray);
	$lonMean = mean($LongitudeArray);
	return array($latMean,$lonMean);
}

//Calculates the mean of an array of numbers
function mean($arr)
{
    if (!count($arr)) return -1;

	return array_sum($arr)/count($arr) ;
}

//Measures Standard Distance
// Accepts lat long pair of mean center location and an array of
// lat long points dispersed around the mean center
function standardDistance($mLat, $mLon, $pointsArray)
{
	$sum = 0;
	foreach($pointsArray as $point)
	{
		list($pLat, $pLon) = split(",", $point);
		$dist = greatCircleDist($mLat, $mLon, $pLat, $pLon);
		$distSq = pow($dist, 2);
		$sum += $distSq;
	}
	$sumDivCount = $sum / count($pointsArray);
	
	return sqrt($sumDivCount);
}

?>
