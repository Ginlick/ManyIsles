<?php
/*
sample usage ($image being a php post file):

  if ($realpath = $filing->new($image, $prodId, "461")) {
    if ($filing->check($image, "standImg")){
      $placedI = $filing->add($image["tmp_name"], $realpath);
    }
  }


*/


class fileengine {
  use fundamentals;

  function __construct($user) {
    $this->root = dirname($_SERVER['DOCUMENT_ROOT']);
    $this->user = $user;
    $this->outsideDir = "/priv/".$this->user."/";
    $this->userDir = $this->root."/media".$this->outsideDir;
    $this->mediaDir = $this->root."/media";

    if (!is_dir($this->userDir)){
      mkdir($this->userDir, 0777, true);
    }
  }

  function add($input, $target) {
    $targuet = $this->userDir.$target;
    if (file_exists($target)){unlink($targuet);}
    if (MOVE_UPLOADED_FILE($input, $targuet)){
      return $this->outsideDir.$target;
    }
    return false;
  }
  function new ($file, $name, $code) {
    $fileType = strtolower(pathinfo($file["name"],PATHINFO_EXTENSION));
    if ($fileType != "") {
      return "MI22_".$code."_".$this->purate($name).".".$fileType;
    }
    return false;
  }
  function check($file, $mode = "standImg"){
    if (!$file){return false;}
    $imageFileType = strtolower(pathinfo($file["name"],PATHINFO_EXTENSION));
    if ($this->fileRequs[$mode]["likesImg"]){
      $check = getimagesize($file["tmp_name"]);
      if($check === false) {
        echo "Not file";
        return false;
      }
    }
    if ($file["size"] > $this->fileRequs[$mode]["size"]) {
      echo $file["size"];
      echo "too large";
      return false;
    }
    if(!in_array($imageFileType, $this->fileRequs[$mode]["types"])){
      echo "bad type";
      return false;
    }
    return true;
  }
  function delete($name, $code) {
    foreach (glob($this->userDir."MI22_".$code."_".$this->purate($name)."*") as $filename) {
      echo $filename;
      unlink($filename);
    }
    return true;
  }
  function deleteD($filepath) {
    //less safe: not user-specifc!
    if (file_exists($this->mediaDir.$filepath)){
      if(unlink($this->mediaDir.$filepath)){
        return true;

        //STILL ERRROR ON DELETING IMMEDIATELY (VIA JS)

      }
    }
    return false;
  }
}

class smolengine {
  use fundamentals;
  function __construct() {
    $this->root = dirname($_SERVER['DOCUMENT_ROOT']);
  }

  function download($file, $basename = "") {
    $fileType = strtolower(pathinfo($file,PATHINFO_EXTENSION));
    $target = $this->root."/media/".$file;
    $basename = $this->purate($basename).".".$fileType;
    if ($basename == ""){$basename = basename($target);}
    if (file_exists($target)){
      header('Content-Type: application/octet-stream');
      header('Content-Disposition: attachment; filename="'.$basename.'"');
      header('Expires: 0');
      header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
      header('Pragma: public');
      header('Content-Length: ' . filesize($target));
      readfile($target);
      exit();
    }
  }
}

trait fundamentals {
  public $fileRequs = [
    "standImg" => ["size"=>330000, "types"=>["jpg", "png", "jpeg"], "likesImg"=>true],
    "mystimg" => ["size"=>2200000, "types"=>["jpg", "png", "jpeg"], "likesImg"=>true],
    "dlPdf" => ["size"=>35000000, "types"=>["pdf"], "likesImg"=>false],
    "dlArt" => ["size"=>35000000, "types"=>["jpg", "png", "jpeg"], "likesImg"=>true]
  ];
  public $regArrayR = [
      "basic" => "/[^A-Za-z0-9]/",
  ];
  function purate($input, $regex = "basic") {
    return preg_replace($this->regArrayR[$regex], "", $input);
  }
  function clearmage($file, $oldOption = "") {
    if ($oldOption != "" AND !preg_match("/^\/.*/", $file)){
      $file = $oldOption.$file;
    }

    if ($_SERVER['DOCUMENT_ROOT'] == "/var/www/vhosts/manyisles.firestorm.swiss/manyisles.ch") {
      $file = "https://media.manyisles.ch".$file;
    }
    else {
      $file = "http://25.36.111.17:8080".$file;
    }
    return $file;
  }
}


?>