<?php

function xml2array($fname){
  $sxi = new SimpleXmlIterator($fname, null, true);
  return sxiToArray($sxi);
}

function sxiToArray($sxi){
  $a = array();
  for( $sxi->rewind(); $sxi->valid(); $sxi->next() ) {
    if(!array_key_exists($sxi->key(), $a)){
      $a[$sxi->key()] = array();
    }
    if($sxi->hasChildren()){
      $a[$sxi->key()][] = sxiToArray($sxi->current());
    }
    else{
      $a[$sxi->key()][] = strval($sxi->current());
    }

    $temp = $sxi->{$sxi->key()}->attributes();

    if (!empty($temp)) {
        foreach ($temp as $k=>$v) {
            var_dump($k);
            var_dump(strval($v));
        }
    }
  }
  return $a;
}

// Read cats.xml and print the results:
$catArray = xml2array('picasa_album_feed.rss');
print_r($catArray);
?>