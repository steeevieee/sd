<?php
  $loc = dirname(__FILE__);

  include_once "utils.php";

  // LOAD SOURCE XML AND CONFIG
  $file = simplexml_load_file("$loc/xmltv.xml");
  $lines = file("$loc/my.channels", FILE_IGNORE_NEW_LINES);

  // CREATE NEW XML STRUCTURE
  $newdoc = new SimpleXMLElement('<!DOCTYPE tv SYSTEM "xmltv.dtd"><tv></tv>');
  foreach ($file->attributes() as $a => $b)
  {
    $newdoc->addAttribute($a, $b);
  }

  // MUNGE CHANNELS
  foreach ($file->channel as $channel)
  {
    if (in_array($channel->attributes()->{'id'}, $lines))
    {
      $newchan = $newdoc->addChild('channel');
      $newchan->addAttribute('id', $channel->attributes()->{'id'});
      $newchan->addChild('display-name', $channel->{'display-name'});
    }
  }

  // MUNGE PROGRAMMES
  foreach ($file->programme as $programme)
  {
    if (strcasecmp($programme->{'title'}, "To Be Announced") == 0)
//    if ($programme->{'title'} == "To Be Announced")
    {
      continue;
    }
    if (in_array($programme->attributes()->{'channel'}, $lines))
    {
      $newprog = $newdoc->addChild('programme');
      foreach ($programme->attributes() as $a => $b)
      {
        $newprog->addAttribute($a, $b);
      }
      $category = best_category(categories($programme, 0));
      if ($category != "UNDEF")
      {
        $newprog->addChild("category", $category);
      }
      foreach ($programme->children() as $nextgen)
      {
        if ($nextgen->getName() == "episode-num" && $nextgen->attributes()->{'system'} == "xmltv_ns")
        {
          $child = $newprog->addChild($nextgen->getName(), htmlspecialchars($nextgen));
          foreach ($nextgen->attributes() as $a => $b)
          {
            $child->addAttribute($a, $b);
          }
        }
        if ($nextgen->getName() == "desc" && $nextgen != "")
        {
          $cats = categories($programme, 0);
          if (count($cats) > 0)
          {
            $catstring = " [".implode("][", $cats)."]";
          }
          $child = $newprog->addChild("desc", htmlspecialchars($nextgen.$catstring));
        }
        if ($nextgen->getName() == "title" || $nextgen->getName() == "sub-title")
        {
          $child = $newprog->addChild($nextgen->getName(), htmlspecialchars($nextgen));
          //foreach ($nextgen->attributes() as $a => $b)
          //{
          //  $child->addAttribute($a, $b);
          //}
        }
      }
    }
  }

  $dom = new DOMDocument("1.0");
  $dom->preserveWhiteSpace = false;
  $dom->formatOutput = true;
  $dom->loadXML($newdoc->asXML());
  $dom->saveXML();
  $dom->save("$loc/new.xml");
//  $socket = "unix:///root/tvheadend/epggrab/xmltv.sock";
//  $sock = stream_socket_client($socket, $errno, $errst);
//  fwrite($sock,  $dom->saveXml());
//  fclose($sock);
?>

