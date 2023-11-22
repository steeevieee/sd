<?php

function categories($node, $mode = 0)
{
  $cats = [];
  $bad_cats = array("Movie", "Episode", "Series", "Feature Film", "TV Movie");
  foreach ($node->children() as $nextgen)
  {
    if ($nextgen->getName() == "category")
    {
      if ($mode == 1 && in_array($nextgen, $bad_cats))
      {
        continue;
      }
      $cats[] = $nextgen;
    }
  }
  return $cats;
}

function in_cats($source, $dest)
{
  foreach ($source as $cat)
  {
    if (in_array($cat, $dest))
      return true;
  }
  return false;
}

function best_category($cats)
{
  if (in_cats(array("Movie", "Feature Film", "TV Movie", "Action", "Sitcom", "Drama", "Crime drama", "Science fiction"), $cats))
    return "Movie / Drama";
  if (in_cats(array("Agriculture", "Documentary", "Politics", "Public affairs", "Technology", "History"), $cats))
    return "Education / Science / Factual Topics";
  if (in_cats(array("Game show", "Comedy", "Reality", "Comedy drama"), $cats))
    return "Show / Game Show";
  if (in_cats(array("Cooking", "Consumer", "House/garden", "Art"), $cats))
    return "Leisure hobbies";
  if (in_cats(array("Children"), $cats))
    return "Children's / Youth Programmes";
  if (in_cats(array("Music", "Musical"), $cats))
    return "Music / Ballet / Dance";
  if (in_cats(array("Special"), $cats))
    return "Special";
  if (in_cats(array("Sports", "Sports non-event"), $cats))
    return "Sports";
  if (in_cats(array("News"), $cats))
    return "News / Current Affairs";
  return "UNDEF";
}

?>
