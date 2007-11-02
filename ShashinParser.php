<?php
/**
 * ShashinParser class file.
 *
 * @author Michael Toppa
 * @version 1.0.6
 * @package Shashin
 * @subpackage Classes
 *
 * Copyright 2007 Michael Toppa
 * 
 * This file is part of Shashin.
 *
 * Shashin is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * Shashin is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 *
 * @author Michael Toppa
 * @package Shashin
 * @subpackage Classes
 */
class ShashinParser {
    var $allTags = array();
    var $insideItem = false;
    var $tag;
    var $parser;
    var $counter = 0;
    
    function ShashinParser() {
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
    	if ($this->insideItem) {
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

$parser = new ShashinParser();
print "<pre>";
print_r($parser->parse("http://picasaweb.google.com/data/feed/api/user/michaeltoppa?kind=album&alt=rss&hl=en_US"));
print "</pre>";
?>