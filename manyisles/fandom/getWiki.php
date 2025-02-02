<?php
if (!function_exists("getWiki")){
  function getWiki($oldroot, $database = "pages", $conn = null, $seen = [], $specs = []){
      if ($conn == null){global $conn;}
      $return = "parentWiki";
      if (isset($specs["return"])){$return = $specs["return"];}
      if ($return == "seen"){
          $returnDefault = [];
      }
      else {
          $returnDefault = false;
      }
      if ($oldroot == 0){
          return $returnDefault;
      }
      $bquery = "SELECT root FROM $database WHERE id = ".$oldroot." ORDER BY v DESC LIMIT 1";
      if ($max = $conn->query($bquery)){
          while ($gay = $max->fetch_row()){
              $root = intval($gay[0]);
              if (!in_array($root, $seen)){
                  $seen[] = $root;
              }
              else {
                  return $returnDefault;
              }
              if ($root == 0){
                  if ($return == "seen"){
                      return $seen;
                  }
                  else {
                      return $oldroot;
                  }
              }
              else {
                  return getWiki($root, $database, $conn, $seen, $specs);
              }
          }
      }
      return $returnDefault;
  }
  function getWikiName($id, $database, $conn){
    $wikiName = "wiki";
    $id = getWiki($id, $database, $conn);
    $bquery = "SELECT shortName FROM $database WHERE id = ".$id." ORDER BY v DESC LIMIT 1";
    if ($max = $conn->query($bquery)){
      while ($gay = $max->fetch_row()){
        $wikiName = $gay[0];
      }
    }
    return $wikiName;
  }
}


?>
