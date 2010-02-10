<?php
/**
 * ToppaXMLParser class file.
 *
 * This file is part of Shashin. Please see the Shashin.php file for
 * copyright and license information.
 *
 * @author Michael Toppa
 * @version 2.6
 * @package Shashin
 * @subpackage Classes
 */

/**
 * We'll use Snoopy to fetch the RSS feed - this is a safer bet across various
 * server configurations than fopen or curl
 */
require_once(ABSPATH . WPINC . '/class-snoopy.php');

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
    var $feed;
    var $counter = 0;

    function ToppaXMLParser() {
        $this->parser = xml_parser_create();
        xml_parser_set_option($this->parser, XML_OPTION_CASE_FOLDING, false);
        xml_set_object($this->parser, $this);
        xml_set_element_handler($this->parser, "startElement", "endElement");
        xml_set_character_data_handler($this->parser, "characterData");
    }

    function fetch($url, $authCode = null,  $gsessionid = null, $recursedOnce = false) {
        if ($authCode) {
            if ($gsessionid) {
                $url .= "?gsessionid=$gsessionid";
            }

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: GoogleLogin auth=$authCode"));
            //curl_setopt($ch, CURLINFO_HEADER_OUT, true); // for debugging the request
            //var_dump(curl_getinfo($ch,CURLINFO_HEADER_OUT)); //for debugging the request
            $response = curl_exec($ch);
            curl_close($ch);

            if (strpos($response, '200 OK') !== false) {
                // get the feed without the http headers
                $pieces = explode("\r\n\r\n", $response);
                $this->feed = $pieces[1];
                return true;
            }

            else if (strpos($response, '302 Moved Temporarily') !== false) {
                // get the gsessionid
                preg_match("/(gsessionid=)([\w|-]+)/", $response, $matches);

                if (!$matches[2]) {
                    return false;
                }

                // we need to call the function again, this time with gsessionid;
                // but only try once, so we don't get caught in a loop if there's
                // a problem
                if ($recursedOnce === false) {
                    return $this->fetch($url, $authCode, $matches[2], true);
                }

                return false;
            }

            return false;
        }

        // if authentication isn't needed, use Snoopy
        $client = new Snoopy();
        $ret = $client->fetch($url);

        if ($ret === false) {
            return false;
        }

        $this->feed = $client->results;
        return true;
    }

    function parse() {
        $clean = str_replace('&', '%and%', $this->feed); // sucky
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
            $attrs = str_replace('%and%', '&', $attrs); // sucky
            $this->allTags[$this->counter][$name] = array();

            foreach ($attrs as $k=>$v) {
                $this->allTags[$this->counter][$name]['attrs'][$k] .= $v;
            }

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
            //    call this method again, so you need to concatenate the
            //    data from each call
            $this->allTags[$this->counter][$this->tag]['data'] .= $data;
        }
    }
}

?>
