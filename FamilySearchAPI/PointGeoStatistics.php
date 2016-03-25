<?php
// Library of geostatistical functions to aid analysis

//Determines the distance from one geolocation to another using an oblate sphereoid formula
// The first 4 parameters are the origin lat/lng and destination lat/lng
// The 5th parameter is the radius of the spherical body being traversed - default: Earth radius in Statute Miles
function greatCircleDist($lat1, $lng1, $lat2, $lng2, $radius = 3958.76, $degree="N")
{
	if ($lat1 == -999 ||$lng1 == -999 ||$lat2 == -999 ||$lng2 == -999 ||
		$lat1 == -1 ||$lng1 == -1 ||$lat2 == -1 ||$lng2 == -1 ||
		$lat1 == null ||$lng1 == null ||$lat2 == null ||$lng2 == null) {
		return null;
	}

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

// A more accurate distance finder
/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -  */
/* Vincenty Inverse Solution of Geodesics on the Ellipsoid (c) Chris Veness 2002-2010             					*/
/*                                                                                      	          										*/
/* from: Vincenty inverse formula - T Vincenty, "Direct and Inverse Solutions of Geodesics on the 					*/
/*       Ellipsoid with application of nested equations", Survey Review, vol XXII no 176, 1975   					*/
/*       http://www.ngs.noaa.gov/PUBS_LIB/inverse.pdf                                             							*/
/*       Converted to PHP by Brian Bunker, BYU Family Migration Research Project, Geography Department, BYU 			*/
/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -  */

/**
 * Calculates geodetic distance between two points specified by latitude/longitude using 
 * Vincenty inverse formula for ellipsoids
 *
 * @param   {Number} lat1, lon1: first point in decimal degrees
 * @param   {Number} lat2, lon2: second point in decimal degrees
 * @returns (Number} distance in metres between points
 */
function distVincenty($lat1, $lon1, $lat2, $lon2) {
	if ($lat1 == -999 ||$lon1 == -999 ||$lat2 == -999 ||$lon2 == -999 ||
		$lat1 == -1 ||$lon1 == -1 ||$lat2 == -1 ||$lon2 == -1 ||
		$lat1 == -0 ||$lon1 == -0 ||$lat2 == -0 ||$lon2 == -0 ||
		$lat1 == 0 ||$lon1 == 0 ||$lat2 == 0 ||$lon2 == 0 ||
		$lat1 == null ||$lon1 == null ||$lat2 == null ||$lon2 == null) {
		return null;
	}

  // WGS-84 ellipsoid params
  $a = 6378137;
  $b = 6356752.3142;
  $f = 1/298.257223563;
  $L = deg2rad($lon2-$lon1);
  $U1 = atan((1-$f) * tan(deg2rad($lat1)));
  $U2 = atan((1-$f) * tan(deg2rad($lat2)));
  $sinU1 = sin($U1);
  $cosU1 = cos($U1);
  $sinU2 = sin($U2);
  $cosU2 = cos($U2);
  
  $lambda = $L;
  $lambdaP;
  $iterLimit = 100;
  do {
    $sinLambda = sin($lambda);
	$cosLambda = cos($lambda);
    $sinSigma = sqrt(($cosU2*$sinLambda) * ($cosU2*$sinLambda) + ($cosU1*$sinU2-$sinU1*$cosU2*$cosLambda) * ($cosU1*$sinU2-$sinU1*$cosU2*$cosLambda));
    if ($sinSigma==0) return 0;  // co-incident points
    $cosSigma = $sinU1*$sinU2 + $cosU1*$cosU2*$cosLambda;
    $sigma = atan2($sinSigma, $cosSigma);
    $sinAlpha = $cosU1 * $cosU2 * $sinLambda / $sinSigma;
    $cosSqAlpha = 1 - $sinAlpha*$sinAlpha;
    $cos2SigmaM = $cosSigma - 2*$sinU1*$sinU2/$cosSqAlpha;
    if (is_nan($cos2SigmaM)) $cos2SigmaM = 0;  // equatorial line: cosSqAlpha=0 (§6)
    $C = $f/16*$cosSqAlpha*(4+$f*(4-3*$cosSqAlpha));
    $lambdaP = $lambda;
    $lambda = $L + (1-$C) * $f * $sinAlpha * ($sigma + $C*$sinSigma*($cos2SigmaM+$C*$cosSigma*(-1+2*$cos2SigmaM*$cos2SigmaM)));
  } while (abs($lambda-$lambdaP) > 1e-12 && --$iterLimit>0);

  if ($iterLimit==0) return acos(8);  // formula failed to converge acos(8) is equivlent to NaN

  $uSq = $cosSqAlpha * ($a*$a - $b*$b) / ($b*$b);
  $A = 1 + $uSq/16384*(4096+$uSq*(-768+$uSq*(320-175*$uSq)));
  $B = $uSq/1024 * (256+$uSq*(-128+$uSq*(74-47*$uSq)));
  $deltaSigma = $B*$sinSigma*($cos2SigmaM+$B/4*($cosSigma*(-1+2*$cos2SigmaM*$cos2SigmaM)-$B/6*$cos2SigmaM*(-3+4*$sinSigma*$sinSigma)*(-3+4*$cos2SigmaM*$cos2SigmaM)));
  $s = $b*$A*($sigma-$deltaSigma);
  
  $s = $s*0.000621371192; // convert to miles
  return $s;
}

//Determines the direction from true north from point A to point B
function greatCircleDirection($lat1, $lng1, $lat2, $lng2, $radius = 3958.76)
{
	if ($lat1 == -999 ||$lon1 == -999 ||$lat2 == -999 ||$lon2 == -999 ||
		$lat1 == -1 ||$lon1 == -1 ||$lat2 == -1 ||$lon2 == -1 ||
		$lat1 == -0 ||$lon1 == -0 ||$lat2 == -0 ||$lon2 == -0 ||
		$lat1 == 0 ||$lon1 == 0 ||$lat2 == 0 ||$lon2 == 0 ||
		$lat1 == null ||$lon1 == null ||$lat2 == null ||$lon2 == null) {
		return null;
	}
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

//Calculates the Mean Center of a group of points
// Accepts an array of Person objects
// Returns a lat long pair as an array - access array properties using square brackets
function meanCenterPersonArray($personArray)
{
 //echo "<br>Attempt to solve for meanCenter Person (PointGeoStatistics.php)<br>";
	$LatitudeArray = array();
	$LongitudeArray = array();
	foreach($personArray as $person)
	{
		if ($person->getBirthPlace()->Lat() != -999 && $person->getBirthPlace()->Lon() != -999
			&& $person->getBirthPlace()->Lat() != -1 && $person->getBirthPlace()->Lon() != -1
			&& $person->getBirthPlace()->Lat() != 0 && $person->getBirthPlace()->Lon() != 0
			&& $person->getBirthPlace()->Lat() != -0 && $person->getBirthPlace()->Lon() != -0
			&& $person->getBirthPlace()->Lat() != null && $person->getBirthPlace()->Lon() != null
			//&& $person->getBirthPlace()->Lon() <= -50 && $person->getBirthPlace()->Lon() >= -160
			)
		{
			array_push($LatitudeArray, $person->getBirthPlace()->Lat());
			array_push($LongitudeArray, $person->getBirthPlace()->Lon());
		}
	}

	$latMean = mean($LatitudeArray);
	$lonMean = mean($LongitudeArray);
	
	if(count($personArray) == 1)
	{
		$latMean = $personArray[0]->getBirthPlace()->Lat();
		$lonMean = $personArray[0]->getBirthPlace()->Lon();
	}
	
	return array($latMean,$lonMean);
}

//Calculates the mean of an array of numbers
function mean($arr)
{
    if (count($arr) == 0) return -1;

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
		$dist = distVincenty($mLat, $mLon, $pLat, $pLon);
		$distSq = pow($dist, 2);
		$sum += $distSq;
	}
	$sumDivCount = $sum / count($pointsArray);
	
	return sqrt($sumDivCount);
}

//Measures Standard Distance
// Accepts lat long pair of mean center location and an array of
// type Person dispersed around the mean center
function standardDistancePersonArray($mLat, $mLon, $personArray)
{
	$sum = 0;
	$num = 0;
	foreach($personArray as $person)
	{
		if ($person->getBirthPlace()->Lat() != -999)
		{
			$dist = distVincenty($mLat, $mLon, $person->getBirthPlace()->Lat(), $person->getBirthPlace()->Lon());
			if($dist != null) {
				$distSq = pow($dist, 2);
				$sum += $distSq;
				$num++;
			}
		}
	}
	if ($num!=0)
	{
	$sumDivCount = $sum / $num;
	
	return sqrt($sumDivCount);
	}
	return 0;
}

?>
