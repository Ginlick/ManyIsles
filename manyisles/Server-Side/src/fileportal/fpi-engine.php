<?php
require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/fileManager.php");
require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/promote.php");

class fpi {
  private $imageStencil = <<<NABSDAI
  <div class="imageContainer" id="IMAGESRC2">
  <div class="sidenav">
    <div class="shown fancyjump" style="top:10px" onclick="copyLink('IMAGESRC')"><i class="fas fa-link"></i></div>
    <div class="trans" style="top:75px" onclick="renameImage('IMAGESRC2')"><i class="fas fa-pen"></i></div>
    <div class="trans" style="top:140px" onclick="deleteImage('IMAGESRC2')"><i class="fas fa-trash"></i></div>
  </div>
  <div load-image="IMAGESRC"></div>
  <div class="titleCont">
  <h3 id="titleIMAGESRC2">IMAGENAME</h3>
  </div>
  </div>
  NABSDAI;
  public $codes = [
    221 => ["name" => "mystralImage", "filetype" => "mystimg", "max" => 11],
    250 => ["name" => "fandomSource", "filetype" => "wikiSrc", "max" => 1],
    301 => ["name" => "blogBanner", "filetype" => "mystimg", "max" => 1]
  ];
  private $code;

  function __construct($code, $defaultAllowed = false) {
    $this->user = new adventurer();
    $this->files = new fileengine($this->user->user);
    $this->files->killCache();
    if (!isset($this->codes[$code])){return false;}
    $this->code = $code;
    $this->defaultAllowed = $defaultAllowed;
  }

  function handle($intent, $inFiles) {
    $returnObj = ["error"=>"Error, $intent failed."]; $canUpload = $this->defaultAllowed;

    //check basic credentials
    if ($this->code == 221){ //mystral image
      require($_SERVER['DOCUMENT_ROOT']."/wiki/pageGen.php");
      $mgen = new gen("act", 0, 0, false, "mystral", ["notArticle"=>true]);
      if ($mgen->power >= 3){$canUpload = true;}else{$returnObj["error"]="Image cap reached.";}
    }


    //execute
    if ($intent == "upload"){
      $fileNum = min($this->codes[$this->code]["max"], count($inFiles));
      //check upload credentials
      if ($this->code == 221){
        if (!$mgen->domainSpecs["canImage"]){$canUpload = false;}
      }
      //upload file
      if ($canUpload){
        $count = 0;
        foreach ($inFiles as $file){
          $count++; if ($count > $fileNum){break;}
          if ($fileTitle = $this->files->new($file, $this->files->generateRandomString(22), $this->code)) { //some codes will require different naming (pre-set, not random)
            if ($this->files->check($file, $this->codes[$this->code]["filetype"])){ //maybe: a better image checker too; see if it's inappropriate, virus...
              if ($placed = $this->files->add($file["tmp_name"], $fileTitle)) {
                $toInsert = ["name" => $fileTitle, "dir" => $placed, "url" => $this->files->clearmage($placed)];
                //custom post-processing
                if ($this->code == 221){
                  $firstName = $this->files->purate(basename($file["name"]), "quotes");
                  $query = "INSERT INTO images (title, name, size, user) VALUES ('$firstName', '$placed', ".$file["size"].", ".$this->user->user.")";
                  $mgen->dbconn->query($query);
                  $insert = str_replace("IMAGESRC2", $fileTitle, $this->imageStencil);
                  $insert = str_replace("IMAGESRC", $toInsert["url"], $insert);
                  $insert = str_replace("IMAGENAME", $firstName, $insert);
                  $toInsert["galleryHTML"] = $insert;
                }
                $this->extendObj($returnObj, $toInsert);
              }
            }
          }
        }
      }
    }
    else if ($intent == "delete"){
      foreach ($inFiles as $file){
        $name = $this->files->purify($file, "quotes");
        if ($canUpload){
          if ($this->files->deleteFull($name)){
            $toInsert = ["name"=> $name, "url" => ""];
            //custom post-processing
            if ($this->code == 221){
              $query = "DELETE FROM images WHERE user = ".$this->user->user." AND name = '$name'";
              $mgen->dbconn->query($query);
            }
            $this->extendObj($returnObj, $toInsert);
          }
        }
      }
    }
    return $returnObj;
  }

  //utilities
  function extendObj(&$extendObj, $toInsert){
    if (isset($extendObj["error"])){unset($extendObj["error"]);$extendObj["files"] = [];}
    $extendObj["files"][] = $toInsert;
  }
}
?>
