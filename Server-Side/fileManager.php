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
  public $fileRequs = [
    "standImg" => ["size"=>330000, "types"=>["jpg", "png", "jpeg"], "likesImg"=>true],
    "dlPdf" => ["size"=>35000000, "types"=>["pdf"], "likesImg"=>false],
    "dlArt" => ["size"=>35000000, "types"=>["jpg", "png", "jpeg"], "likesImg"=>true]
  ];
  public $regArrayR = [
      "basic" => "/[^A-Za-z0-9]/",
  ];

  function __construct($user) {
    $realpath = realpath("SubPar.php");
    $realpath = dirname($realpath);
    $realpath = dirname($realpath);
    $realpath = dirname($realpath);
    $this->root = $realpath;
    $this->user = $user;
    $this->outsideDir = "/priv/".$this->user."/";
    $this->userDir = $this->root."/media".$this->outsideDir;

    require($_SERVER['DOCUMENT_ROOT']."/wiki/expressions.php");

    if (!is_dir($this->userDir)){
      mkdir($this->userDir, 0777, true);
    }
  }
  function purate($input, $regex = "basic") {
    return preg_replace($this->regArrayR[$regex], "", $input);
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
      unlink($filename);
    }
    return true;
  }
}


?>
