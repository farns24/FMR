<?php
// BYUFMR prettyPrintXML
// Author: Brian Bunker
// Project: Geography Research - Modeling Large-Scale Historical Migration Patterns Using Family History Records
// Supervisor: Dr. Samuel Otterstrom
// Title: prettyPrintXML.php
// Purpose: Format a simpleXML object for web viewing.

/** Prettifies an XML string into a human-readable and indented work of art
 *  @param string $xml The XML as a string
 *  @param boolean $html_output True if the output should be escaped (for use in HTML)
 */
function xmlpp($fileName, $html_output=false) {
    $xml_obj = simplexml_load_file ("http://ivins.geog.byu.edu/fmr/data/$fileName");
    $level = 4;
    $indent = 0; // current indentation level
    $pretty = array();
    
    // get an array containing each XML element
    $xml = explode("\n", preg_replace('/>\s*</', ">\n<", $xml_obj->asXML()));

    // shift off opening XML tag if present
    if (count($xml) && preg_match('/^<\?\s*xml/', $xml[0])) {
      $pretty[] = array_shift($xml);
    }

    foreach ($xml as $el) {
      if (preg_match('/^<([\w])+[^>\/]*>$/U', $el)) {
          // opening tag, increase indent
          $pretty[] = str_repeat(' ', $indent) . $el;
          $indent += $level;
      } else {
        if (preg_match('/^<\/.+>$/', $el)) {            
          $indent -= $level;  // closing tag, decrease indent
        }
        if ($indent < 0) {
          $indent += $level;
        }
        $pretty[] = str_repeat(' ', $indent) . $el;
      }
    }   
    $xml = implode("\n", $pretty);   
    return ($html_output) ? htmlentities($xml) : $xml;
}

?>