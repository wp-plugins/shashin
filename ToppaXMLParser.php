<?php
/**
 * ToppaXMLParser class file.
 *
 * This file is part of Shashin. Please see the Shashin.php file for
 * copyright and license information.
 *
 * @author Michael Toppa
 * @version 1.2
 * @package Shashin
 * @subpackage Classes
 */
 
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
        $fp = fopen($url, "r");
        
        if ($fp === false) {
            return "Failed to open URL: $url";
        }
        
        while ($data = fread($fp, 4096)) {
        	$ret = xml_parse($this->parser, $data, feof($fp));
            
            if (!$ret) {
                return(sprintf("XML error: %s at line %d", 
        			xml_error_string(xml_get_error_code($this->parser)), 
        			xml_get_current_line_number($this->parser)));
            }
        }
        
        fclose($fp);
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
            $this->allTags[$this->counter][$this->tag]['data'] = $data;

    	}
    }
}

$parser = new ToppaXMLParser();
print "<pre>";
print_r($parser->parse("http://picasaweb.google.com/data/feed/api/user/michaeltoppa?kind=album&alt=rss&hl=en_US"));
print "</pre>";
?>