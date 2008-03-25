<?php
/**
 * ToppaXMLParser class file.
 *
 * This file is part of Shashin. Please see the Shashin.php file for
 * copyright and license information.
 *
 * @author Michael Toppa
 * @version 2.0.3
 * @package Shashin
 * @subpackage Classes
 */

/**
 * We'll use Snoopy to fetch the RSS feed - this is a safer bet across various
 * server configurations than fopen
 */
require_once(ABSPATH . WPINC . '/class-snoopy.php');
//require_once('class-snoopy.php');

/**
 * A simple XML parser. Looks for "item" entries in the feed, and builds an
 * array of items. Captures item values and attributes.
 *
 * @author Michael Toppa
 * @package Shashin
 * @subpackage Classes
 */

class ToppaXMLParser {
    var $allTags = array();
    var $insideItem = false;
    var $tag;
    var $parser;
    var $counter = 0;

    function ToppaXMLParser() {
        $this->parser = xml_parser_create();
        xml_parser_set_option($this->parser, XML_OPTION_CASE_FOLDING, false);
        xml_set_object($this->parser, $this);
        xml_set_element_handler($this->parser, "startElement", "endElement");
        xml_set_character_data_handler($this->parser, "characterData");
    }

    function parse($url) {
        $client = new Snoopy();
	    $client->fetch($url);
        $clean = str_replace('&', '%and%', $client->results); // sucky
        $ret = xml_parse($this->parser, $clean);

        if (!$ret) {
            return(sprintf("XML error: %s at line %d",
    			xml_error_string(xml_get_error_code($this->parser)),
    			xml_get_current_line_number($this->parser)));
        }

        xml_parser_free($this->parser);
        return $this->allTags;
    }

    function startElement($parser, $name, $attrs) {
    	if ($this->insideItem === true) {
            $this->allTags[$this->counter][$name] = array();
            $this->allTags[$this->counter][$name]['attrs'] = $attrs;
            $this->tag = $name;
        }
    	if ($name == "item") {
    		$this->insideItem = true;
    	}
    }

    function endElement($parser, $name) {
    	if ($name == "item") {
            $this->counter++;
    		$this->insideItem = false;
    	}
    }

    function characterData($parser, $data) {
    	if ($this->insideItem === true) {
            $data = str_replace('%and%', '&', $data); // sucky

            // The use of .= here is crucial for two reasons:
            // 1. the php parser divides the data into 1024 byte chunks, if your
            //    data falls in the middle of chunk, this function is called
            //    again, so you need to concatenate the data
            // 2. entities, newlines, and tabs cause it to stop scanning and
            //    and call this method again, so you need to concatenate the
            //    data from each call
            $this->allTags[$this->counter][$this->tag]['data'] .= $data;
    	}
    }
}

?>
