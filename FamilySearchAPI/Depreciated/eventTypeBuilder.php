<?
// BYUFMR Event Type Builder
// Author: Brian Bunker
// Project: Geography Research - Modeling Large-Scale Historical Migration Patterns Using Family History Records
// Supervisor: Dr. Samuel Otterstrom
// Title: eventTypeBuilder.php
// Purpose: Creates basic XML skeleton for life events of extracted people.

// Builds base XML structure for event(s)
// Calls eventPlaceFMRXMLBuilder()
// Sorts events by astro date
function eventTypeBuilder($credentials, $personReadXML, $personID)
{
	// Variables to be used in the function
	$name = "";
	$gender = "";	
	$eventXMLString = "";
	$eventArray[] = array();
	$eventPlaceCount = array();
	$eventsRecorded = array();
	// Iterate through each person to find the person specified in function parameter $personID
	foreach($personReadXML->persons->person as $person)
	{
		// Once person is found, organize and record individual data in a string formatted as XML
		if((string)$person['id'] == (string)$personID)
		{
			$name .= '<name>'.$person->assertions->names->name->value->forms->form->fullText.'</name>';
			$gender .= '<gender>'.$person->assertions->genders->gender->value->type.'</gender>';
			// Retrieve the correct events and event places for the individual
			if(isset($person->assertions->events->event))
			{
				// Find out which places are the most repeated for a person's events
				// Purpose is to find the most common place id for each event assertion
				foreach($person->assertions->events->event as $event)
				{
					$eventType = (string)strtolower($event->value['type']);
					$eventPlaceId = (int)$event->value->place->normalized['id'];
					// Count and record the number of event assertions for each event type
					// Create a new new event array if not yet made
					if(!isset($eventPlaceCount[$eventType]))
					{
						// Create a new array within $eventPlaceCount array for new event type
						$eventPlaceCount[$eventType] = array();
						$eventPlaceCount[$eventType][$eventPlaceId] = 1;
					}
					// For each place id, add 1 to the current count
					else
					{
						if(!isset($eventPlaceCount[$eventType][$eventPlaceId]))
						{
							$eventPlaceCount[$eventType][$eventPlaceId] = 1;
						}
						else
						{
							$eventPlaceCount[$eventType][$eventPlaceId]++;
						}
					}
					// Sort the places using value(a) rsort to get descending values
					arsort($eventPlaceCount[$eventType]);
				}
				// Re-iterate through events to extract the most common places
				// Record the information in a string formatted as XML
				foreach($person->assertions->events->event as $event)
				{
					// Check to see if the normalized place id is available
					if(isset($event->value->place->normalized['id']))
					{
						// Record the event type and place id
						$eventType = (string)strtolower($event->value['type']);
						$eventPlaceId = (int)$event->value->place->normalized['id'];
						// Check to see if the place count array has any counts for the current event type
						if(isset($eventPlaceCount[$eventType]))
						{
							// Extract the most common place id for the event as recorded from the above foreach loop
							$eventTypeKeys = array_keys($eventPlaceCount[$eventType]);
							$highKeyArray = array_slice($eventTypeKeys, 0, 1);
							$highKey = $highKeyArray[0];
							// Check to see if the current place is the most common assertion place for the event
							if($eventPlaceId == $highKey)
							{
								// If we found the correct event and place, call the eventPlaceFSXMLBuilder to return a correctly formatted xml string
								if(!isset($eventsRecorded[$eventType]) || $eventType == 'Other')
								{
									echo '<br />'.$event->value['type'].': '.$event->value->date->astro->earliest.'. Place: '.$event->value->place->normalized;
									$xfmrCurrentEvent = eventPlaceFMRXMLBuilder($credentials, $event);
									$eventXML = simplexml_load_string($xfmrCurrentEvent);
									$astroKey = (int)$eventXML->date->astro;
									if(isset($eventArray[$astroKey]))
									{
										$eventArray[$astroKey+1] = $xfmrCurrentEvent;
									}
									else
									{
										$eventArray[$astroKey] = $xfmrCurrentEvent;
									}
									$eventsRecorded[$eventType] = $eventType;
								}
							}
						}
					}
				}
				// build $eventXMLString by using the astro date to sort the events (using key sort)
				ksort($eventArray);
				foreach($eventArray as $events)
				{
					$eventXMLString .= $events;
				}
				$eventXMLString = str_replace('Array', '', $eventXMLString);
			}
		}
	}
	return $name.$gender.$eventXMLString;
}

?>