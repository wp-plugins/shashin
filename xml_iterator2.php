<?php
function xmlToArray($xml,$ns=null){
  $a = array();
  for($xml->rewind(); $xml->valid(); $xml->next()) {
    $key = $xml->key();
    if(!isset($a[$key])) { $a[$key] = array(); $i=0; }
    else $i = count($a[$key]);
    $simple = true;
    foreach($xml->current()->attributes() as $k=>$v) {
        $a[$key][$i][$k]=(string)$v;
        $simple = false;
    }
    if($ns) foreach($ns as $nid=>$name) {
      foreach($xml->current()->attributes($name) as $k=>$v) {
         $a[$key][$i][$nid.':'.$k]=(string)$v;
         $simple = false;
      }
    }
    if($xml->hasChildren()) {
        if($simple) $a[$key][$i] = xmlToArray($xml->current(), $ns);
        else $a[$key][$i]['content'] = xmlToArray($xml->current(), $ns);
    } else {
        if($simple) $a[$key][$i] = strval($xml->current());
        else $a[$key][$i]['content'] = strval($xml->current());
    }
    $i++;
  }
  return $a;
}

$xml = new SimpleXmlIterator('flickr_feed.rss', null, true);
//$namespaces = $xml->getNamespaces(true);
//print_r(xmlToArray($xml,$namespaces));
  //foreach ($namespaces as $prefix => $ns) {
    $xml->registerXPathNamespace('media', "http://search.yahoo.com/mrss/");
    $result = $xml->xpath("//media:content");
    //var_dump($result);
    //exit;
      foreach($result[0]->attributes() as $k=>$v) {
        echo $k . " -- " . $v . "\n";
      }


