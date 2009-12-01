<?php

function xml2array($fname){
  $sxi = new SimpleXmlIterator($fname, null, true);
  $namespaces = $sxi->getNamespaces(true);

  $result = array();
  $results[] = sxiToArray($sxi);

  foreach ($namespaces as $prefix => $ns) {
    $sxi->registerXPathNamespace($prefix, $ns);
    $result[] = sxiToArray($sxi);
  }

 return $result;
}

function sxiToArray($sxi){
  $a = array();
  for( $sxi->rewind(); $sxi->valid(); $sxi->next() ) {
    if(!array_key_exists($sxi->key(), $a)){
      $a[$sxi->key()] = array();
    }

    $va = array('values' => array());
    $attrs = $sxi->{$sxi->key()}->attributes();
        if (!empty($attrs)) {
            $va['attrs'] = array();
            foreach ($attrs as $k=>$v) {
                $va['attrs'][$k] = strval($v);
            }
        }

    if($sxi->hasChildren()){
      $va['values'] = sxiToArray($sxi->current());
    }
    else{
        $va['values'] = strval($sxi->current());
    }

      $a[$sxi->key()][] = $va;

  }
  return $a;
}


// Read cats.xml and print the results:
//$feed = file_get_contents('flickr_feed.rss');
//$feed = preg_replace("/(<|<\/)(\w*)(\W*)(\w*)(>|\/>)/", "$1${2}_$3$4$5", $feed);
//var_dump($feed);
//exit;
$catArray = xml2array('flickr_feed.rss');
print_r($catArray);
?>