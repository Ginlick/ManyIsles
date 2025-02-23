<?php
//  ALL USES OF THIS ENGINE SHOULD GO THROUGH fpi!
/*
sample usage ($image being a php post file):

  if ($realpath = $filing->new($image, $prodId, "461")) {
    if ($filing->check($image, "standImg")){
      $placedI = $filing->add($image["tmp_name"], $realpath);
    }
  }


*/

if (!trait_exists("fundamentals")){
  require($_SERVER['DOCUMENT_ROOT']."/Server-Side/allBase.php");
  trait fundamentals {
    use allBase;
    public $root = ""; public $mediaDir = "";
    public $accentedCharacters = "àèìòùÀÈÌÒÙáéíóúýÁÉÍÓÚÝâêîôûÂÊÎÔÛãñõÃÑÕäëïöüÿÄËÏÖÜŸçÇßØøÅåÆæœĀāŌо̄Ūū";

    public function giveBasis() {
      $this->root = dirname(dirname($_SERVER['DOCUMENT_ROOT']));
      $this->mediaDir = $this->root."/media";
    }

    public $fileRequs = [
      "standImg" => ["size"=>500000, "types"=>["jpg", "png", "jpeg"], "likesImg"=>true],
      "mystimg" => ["size"=>2200000, "types"=>["jpg", "png", "jpeg", "gif"], "likesImg"=>true],
      "dlPdf" => ["size"=>35000000, "types"=>["pdf"], "likesImg"=>false],
      "wikiSrc" => ["size"=>22000000, "types"=>["jpg", "png", "jpeg", "svg", "pdf"], "likesImg"=>false],
      "dlArt" => ["size"=>12000000, "types"=>["jpg", "png", "jpeg", "gif"], "likesImg"=>true],
      "dl3d" => ["size"=>22000000, "types"=>["stl", "fbx", "obj", "usd", "usdz"], "likesImg"=>false],
      "bigAudio" => ["size"=>1200000, "types"=>["wav", "mp3", "pcm"], "likesImg"=>false]
    ];
    function clearmage($file, $oldOption = "") {
      if ($oldOption != "" AND !preg_match("/^\/.*/", $file)){
        $file = $oldOption.$file;
      }
      $this->checkServerInfo();

      if (!preg_match("/^http/", $file)){
        $file = $this->giveServerInfo("servername_media").$file;
      }
      return $file;
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
        while (ob_get_level()) {
          ob_end_clean();
        }
        readfile($target);
        exit();
      }
    }
    function deleteD($filepath) {
      //less safe than $fileengine->delete(): not user-specifc!
      if (file_exists($this->mediaDir.$filepath)){
        if(unlink($this->mediaDir.$filepath)){
          return true;
        }
      }
      return false;
    }
  }

  class fileengine {
    use fundamentals;

    function __construct(int $user) {
      $this->giveBasis();
      $this->user = $user;
      $this->outsideDir = "/priv/".$this->user."/";
      $this->userDir = $this->root."/media".$this->outsideDir;

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
          return false;
        }
      }
      if ($file["size"] > $this->fileRequs[$mode]["size"]) {
        return false;
      }
      if(!in_array($imageFileType, $this->fileRequs[$mode]["types"])){
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
    function deleteFull($filename){
      if (file_exists($this->userDir.$filename)){
        unlink($this->userDir.$filename);
      }
      return true;
    }
  }

  class smolengine {
    use fundamentals;
    function __construct() {
      $this->giveBasis();
    }
  }
}


?>
