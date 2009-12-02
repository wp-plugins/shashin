<?php
$feed = file_get_contents('flickr_feed.rss');
$xml = new SimpleXmlElement($feed);

foreach ($xml->channel->item as $entry){
echo $entry->title  . "\n";
  echo $entry->guid . "\n";

  //Use that namespace
  $namespaces = $entry->getNameSpaces(true);
  //Now we don't have the URL hard-coded
  $media = $entry->children($namespaces['media']);
  echo $media->title . "\n";
  echo $media->content->attributes()->width . "\n";
}

/*
$xml = simplexml_load_file('flickr_feed.rss');

var_dump(xml2array_parse($xml));

function xml2array_parse($xml){
     while (list($parent, $child) = each($xml->children())) {
         var_dump($parent);
         var_dump($child);
         $return["$parent"] = xml2array_parse($child)?xml2array_parse($child):"$child";
     }
     //return $return;
 }
$result = $sxi->xpath('//spec:name');
foreach ($result as $k=>$v)
{
echo $v.'<br />';
}


function xml2array($fname){
  $sxi = new SimpleXmlIterator($fname, null, true);
  $namespaces = $sxi->getNamespaces(true);

  $results = array();
  $results[] = sxiToArray($sxi);

  foreach ($namespaces as $prefix => $ns) {
    $sxi->registerXPathNamespace($prefix, $ns);
    $results[] = sxiToArray($sxi);
  }

 return $results;
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

$catArray = xml2array('flickr_feed.rss');
print_r($catArray);
*/
?>