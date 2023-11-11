<?php
  $loc = dirname(__FILE__);

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
    if (in_array($programme->attributes()->{'channel'}, $lines))
    {
      $newprog = $newdoc->addChild('programme');
      $skip_cat = 0;
      foreach ($programme->attributes() as $a => $b)
      {
        $newprog->addAttribute($a, $b);
      }
      foreach ($programme->children() as $nextgen)
      {
        if ($skip_cat == 0 && $nextgen->getName() == "category")
	{
	  switch ($nextgen)
          {
            case "movie":
            case "Sitcom":
            case "Adventure":
            case "Mystery":
              $newprog->addChild("category", "Movie / Drama");
              $skip_cat = 1;
              break;
            case "News":
            case "Weather":
              $newprog->addChild("category", "News / Current Affairs");
              $skip_cat = 1;
              break;
            case "Musical":
            case "Music":
              $newprog->addChild("category", "Music / Ballet / Dance");
              $skip_cat = 1;
              break;
            case "sports":
            case "Cricket":
              $newprog->addChild("category", "Sports");
              $skip_cat = 1;
              break;
            case "Children":
            case "Anime":
              $newprog->addChild("category", "Children's / Youth Programmes");
              $skip_cat = 1;
              break;
            case "Competition reality":
            case "Reality":
            case "Cooking":
            case "Game show":
            case "Animated":
            case "Auction":
            case "Card games":
            case "Interview":
              $newprog->addChild("category", "Show / Game Show");
              $skip_cat = 1;
              break;
            case "Documentary":
            case "Public affairs":
            case "Travel":
            case "History":
              $newprog->addChild("category", "Education / Science / Factual Topics");
              $skip_cat = 1;
              break;
            case "Special":
              $newprog->addChild("category", "Special");
              $skip_cat = 1;
              break;
          }
	}
        if ($nextgen->getName() == "episode-num" && $nextgen->attributes()->{'system'} == "xmltv_ns")
        {
          $child = $newprog->addChild($nextgen->getName(), htmlspecialchars($nextgen));
          foreach ($nextgen->attributes() as $a => $b)
          {
            $child->addAttribute($a, $b);
          }
        }
        if ($nextgen->getName() == "title" || $nextgen->getName() == "sub-title" || $nextgen->getName() == "desc")
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

