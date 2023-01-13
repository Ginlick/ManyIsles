<?php
if (!class_exists("parser")){
  require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/Parsedown.php");
  require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/allBase.php");

  class parser {
    use allBase;
    public $divTypes = [
      "gallery" => [
        "class" => "gallery"
      ],
      "wide" => [
        "class" => "wide"
      ],
      "quote" => [
        "class" => "quote",
        "classOptions" => ["note", "left"]
      ],
      "highlighted" => [
        "class" => "quote highlited",
        "classOptions" => ["note", "left"]
      ],
      "code" => [
        "class" => "code",
        "el_type" => "span",
        "innerType" => "line",
        "classOptions" => ["block"]
      ]
    ];

    function __construct($parseClear = false) {
      $this->Parsedown = new Parsedown();
      if ($parseClear) {$this->Parsedown->setSafeMode(false);} else {$this->Parsedown->setSafeMode(true);}
    }

    function parse($body, $extent = 0, $callback = null) {
      //extent: -1 no quotes, 0 basic parse, 1 with images&co

      //pre-Parsedown processing
      if ($extent > 0) {
        //images
        $this->insertArray = [];$lineMatches = [];
        if (preg_match_all("/^.*{([a-z]+\[.*\]).*}.*$/m", $body, $lineMatches) != false){$body = $this->parseImg($body, $this->keyTracker, $lineMatches[0]);}
      }

      if ($extent == 0){
        $body = $this->Parsedown->line($body);
      }
      else {
        $body = $this->Parsedown->text($body);
      }

      //post-Parsedown processing
      if (gettype($callback)=="object") {
        $callback($body);
      }
      if ($extent > 0){
        //images
        if ($this->insertArray != null){
          foreach ($this->insertArray as $key => $value) {
            $body = substr_replace($body, $value, strpos($body, "%key".$key."key%"), 0);
            $body = str_replace("%key".$key."key%", "", $body);
          }
        }

        //all kinds of divs
        $lineMatchesD = [];
        if (preg_match_all("/\[(([A-Za-z]+)[A-Za-z ]*)\]/", $body, $lineMatchesD, PREG_OFFSET_CAPTURE) != false){$body = $this->parseDivs($body, $lineMatchesD);}
      }

      $specCharLvl = 2;
      if ($extent < 0){$specCharLvl = 0;}
      $body = $this->placeSpecChar($body, $specCharLvl);
      //$body = utf8_decode($body);

      return $body;
    }

    function parseDivs($body, $lineMatches){
      $insertArray = [];
      for ($i = 0; $i < count($lineMatches[0]); $i++){
        $blockStart = $lineMatches[0][$i];
        $startMarker  = $blockStart[0];
        $startMarkerPos = $blockStart[1] + strlen($startMarker);
        $fullClass = $lineMatches[1][$i][0];
        $class = $lineMatches[2][$i][0];
        $endMarker = "[/".$class."]";

        $inBlock = substr($body, $startMarkerPos);
        if (($endMarkerPos = strpos($inBlock, $endMarker)) === false){continue;}//no end marker found
        $inBlock = substr($inBlock, 0, $endMarkerPos);

        if (!($officialClass = $this->checkDivClass($fullClass))){continue;}
        if ($officialClass[2] == "text"){$parsedInBlock = $this->Parsedown->text($inBlock);}
        else {$parsedInBlock = $this->Parsedown->line($inBlock);}

        $parsedBlock = "<".$officialClass[0]." class='".$officialClass[1]."'>".$parsedInBlock."</".$officialClass[0].">";
        $insertArray[$i] = [$startMarker.$inBlock.$endMarker, $parsedBlock];
      }
      foreach ($insertArray as $element){
        $body = str_replace($element[0], $element[1], $body);
      }
      return $body;
    }
    function checkDivClass($fullClass){
      $officialClass = [0 => "div", 1 => "", 2 => "text"];
      $classes = explode(" ", $fullClass);
      if (!isset($this->divTypes[$classes[0]])){ return false; }
      $officialClass[1] = $this->divTypes[$classes[0]]["class"];
      if (isset($this->divTypes[$classes[0]]["classOptions"])){
        foreach ($classes as $class){
          if (in_array($class, $this->divTypes[$classes[0]]["classOptions"])){
            $officialClass[1] .= " ".$class;
          }
        }
      }
      if (isset($this->divTypes[$classes[0]]["el_type"])){
        $officialClass[0] = $this->divTypes[$classes[0]]["el_type"];
      }
      if (isset($this->divTypes[$classes[0]]["innerType"])){
        $officialClass[2] = $this->divTypes[$classes[0]]["innerType"];
      }
      return $officialClass;
    }

    function parseImg($body, &$keyTracker, $lineMatches) {
        foreach($lineMatches as $line){
            $artId = substr($line, strpos($line, "{")+1);
            $stringDico = substr($artId, 0, strpos($artId, "}") - 1);
            $chunks = array_chunk(preg_split('/(\[|\])/', $stringDico), 2);

            if (count(array_column($chunks, 0)) > 0 AND count(array_column($chunks, 0)) == count(array_column($chunks, 1))){
                $img = array_combine(array_column($chunks, 0), array_column($chunks, 1));
            }
            else {$img = [];}

            if (!isset($img["class"])){$img["class"] = "sideimg";}
            else if ($img["class"]=="landscape"){$img["class"]="sideimg landscape";}
            if (!isset($img["caption"])){$img["caption"] = "";}
            if (!isset($img["src"])){$img["src"] = "";}
            if (!isset($img["style"])){$img["style"] = "";}
            $this->purify($img["class"]);$this->purify($img["caption"], "cleanText");$this->purify($img["src"], "cleanText");$this->purify($img["style"], "cleanText");
            $caption = $this->parse($this->placeSpecChar($img["caption"], 0), 0);

            $echoImg = '<div class="'.$img["class"].'" style="'.$img["style"].'"><a href="'.$img["src"].'" target="_blank"><img src="'.$img["src"].'" /></a><p>'.$caption.'</p></div>';

            $this->insertArray[$keyTracker] = $echoImg;
            $pos = strpos($body, "{");
            $body = str_replace($line, "%key".$keyTracker."key%", $body);
            $keyTracker++;
        }
        return $body;
    }
  }
}

?>
