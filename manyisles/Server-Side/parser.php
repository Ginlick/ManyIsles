<?php
//header('Content-Type: text/example');
if (!class_exists("parser")){
  require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/allBase.php");
  require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/datastructures/stack.php");

  class parser {
    use allBase;
    private $Parsedown = null;

    function __construct($parseClear = false) {
      $this->Parsedown = new Parsedown(!$parseClear);
    }

    function parse($body, $extent = 0, $callback = null) {
      //extent: -1 pure markdown, 0 markdown and special characters, 1 full html (with images&divs)
      $body = $this->Parsedown->parse($body, $extent, $callback);
      return $body;
    }
  }

  trait parseTypes {
    private $boxTypes = [
      "fullTable" => [
        "name" => "fullTable",
        "regex"=> ["o" => "/^\[fullTable\]/", "c" => "/\[\/fullTable\]/"],
        "syntax"=>'<div class="wide fullTable">%body%</div>',
        "nesting"=>["level"=>1, "maxParent" => 1],
      ],
      "wide" => [
        "name" => "wide",
        "regex"=> ["o" => "/^\[wide\]/", "c" => "/\[\/wide\]/"],
        "syntax"=>'<div class="wide">%body%</div>',
        "nesting"=>["level"=>1, "maxParent" => 1],
      ],
      "gallery" => [
        "name" => "gallery",
        "regex"=> ["o" => "/^\[gallery\]/", "c" => "/\[\/gallery\]/"],
        "syntax"=>'<div class="gallery">%body%</div>',
        "nesting"=>["level"=>1, "maxParent" => 1],
      ],
      "quote" => [
        "name" => "quote",
        "regex"=> ["o" => "/\[quote((?: note| left)*)\]/", "c" => "/\[\/quote\]/"],
        "syntax"=>'<div class="quote%1%">%body%</div>',
        "nesting"=>["level"=>2, "maxParent" => 1],
      ],
      "highlighted" => [
        "name" => "highlighted",
        "regex"=> ["o" => "/\[highlighted((?: note| left)*)\]/", "c" => "/\[\/highlighted\]/"],
        "syntax"=>'<div class="highlighted%1%">%body%</div>',
        "nesting"=>["level"=>2, "maxParent" => 1],
      ],
      "codeBlock" => [
          "name" => "codeBlock",
          "regex"=> ["o" => "/\[codeBlock\]/", "c" => "/\[\/codeBlock\]/"],
          "syntax"=>'<div class="code">%body%</div>',
          "nesting"=>["level"=>2, "maxParent" => 2]
      ],
      "image"=>[
        "name" => "image",
        "syntax"=>'%body%',
        "nesting"=>["level"=>1, "maxParent" => 1],
        "autoclose" => 1,
      ],
      "paragraph" => [
        "name" => "paragraph",
        "syntax" => "<p>%body%</p>",
        "nesting"=>["level"=>2,"maxParent"=>2],
        "autoclose" => 1,
      ],
      "table" => [
        "name" => "table",
        "syntax" => "<table><thead></thead><tbody>%body%</tbody></table>",
        "nesting"=>["level"=>2, "maxParent" => 1],
        "autoclose"=> 1
      ],
      "ul" => [
        "name" => "ul",
        "syntax" => "<ul>%body%</ul>",
        "nesting"=>["level"=>2,"maxParent"=>2],
        "autoclose" => 1,
      ],
      "base" => [
        "name" => "base",
        "nesting" => ["level"=>0]
      ]
    ];
    private $inlineTypes = [
      "bolditalic" => [
        "name"=>"bolditalic",
        "regex"=>"/\*\*\*(.+?)\*\*\*/",
        "syntax"=>"<i><b>%1%</b></i>",
      ],
      "bold" => [
        "name"=>"bold",
        "regex"=>"/\*\*(.+?)\*\*/",
        "syntax"=>"<b>%1%</b>"
      ],
      "italic" => [
        "name"=>"italic",
        "regex"=>"/\*(.+?)\*/",
        "syntax"=>"<i>%1%</i>"
      ],
      "link" => [
          "name" => "link",
          "regex"=>"/\[([^\[\]\(\)]+)\]\(([A-Za-z0-9\.\/:\-\?\= ]+)\)/",
          "syntax"=>'<a href="%2%">%1%</a>'
      ],
      "squote" => [
          "name" => "squote",
          "regex"=>"/\[squote\](.+?)\[\/squote\]/",
          "syntax"=>'<span class="squote">%1%</span>'
      ],
      "code" => [
          "name" => "code",
          "regex"=>"/\[code\](.+?)\[\/code\]/",
          "syntax"=>'<span class="code">%1%</span>'
      ],
      "linebreak" => [
          "name" => "linebreak",
          "regex"=>"/\[br\]/",
          "syntax"=>'<br />'
      ],
      //the line types
      "li" => [
        "name"=>"li",
        "regex"=>"/^\- *(.+)/",
        "syntax"=>"<li>%1%</li>",
        "requiredBox" => "ul",
      ],
      "h5" => [
        "name"=>"h5",
        "regex"=>"/^#####(.*)/",
        "syntax"=>"<h5>%1%</h5>",
        "requiredBox" => ""
      ],
      "h4" => [
        "name"=>"h4",
        "regex"=>"/^####(.*)/",
        "syntax"=>"<h4>%1%</h4>",
        "requiredBox" => ""
      ],
      "h3" => [
        "name"=>"h3",
        "regex"=>"/^###(.*)/",
        "syntax"=>"<h3>%1%</h3>",
        "requiredBox" => ""
      ],
      "h2" => [
        "name"=>"h2",
        "regex"=>"/^##(.*)/",
        "syntax"=>"<h2>%1%</h2>",
        "requiredBox" => ""
      ],
      "h1" => [
        "name"=>"h1",
        "regex"=>"/^#(.*)/",
        "syntax"=>"<h1>%1%</h1>",
        "requiredBox" => ""
      ],
      "tableLine" => [
        "name"=>"tableLine",
        "regex"=>"/((\|([^\|]*))+)\|?/",
        "syntax"=>"%1%",
        "requiredBox" => "table"
      ],
      "emptyline" => [
        "name" => "emptyline",
        "regex" => "/^$/",
        "syntax"=>"",
        "requiredBox" => ""
      ]
    ];
  }

  class Parsedown {
    use allBase; //purify, special characters parse required
    use parseTypes; //instructions for the syntax to parse

    public $boxes;
    public $currLine = [];
    public $safeMode = true;
    public $extent = 1;
    public $styles = "";
    public $idtracker = 0;
    public $callback = null;

    function __construct($safeMode = true) {
      if (!$safeMode){$this->safeMode = false;}
      $this->construct();
    }
    function construct(){
      $this->boxes = new Stack();
      $this->boxes->push(["type"=>"base", "text" => []]);
      $this->currLine = [];
      $this->styles = "";
      $this->idtracker = 0;
    }

    function parse($body, $extent = 1, $callback = null) {
      //extent: -1 pure markdown, 0 markdown and special characters but no elements (not even boxes), 1 full html (with images&divs)
      $this->construct();
      $this->extent = $extent;
      $this->callback = $callback;

      $bodyArr = explode(PHP_EOL, $body);
      foreach ($bodyArr as $i => $line){
        //print_r($this->boxes->stack);
        $this->newline($line);
      }
      return $this->plainBody();
    }

    //lines
    function newline(string $line) {
      $line = preg_replace("/[\r\n]/", "", $line);
      if (preg_match("/{(?:.+\[.+\])+}/", $line)){//image
        $this->openBox("image");
        $line = $this->parseImage($line);
        $this->currLine = ["text" => $line];
        $this->endline();
        return;
      }

      //explicit boxes: parse tags
      $closeBoxes = [];
      foreach ($this->boxTypes as $boxType){
        if (isset($boxType["regex"]["o"]) && preg_match_all($boxType["regex"]["o"], $line, $lineMatches)){
          foreach ($lineMatches[0] as $i => $match){
            $syntax = $boxType["syntax"];
            for ($j = 1; $j < count($lineMatches); $j++){
              $syntax = str_replace("%".$j."%", $lineMatches[$j][$i], $syntax);
            }
          }
          $this->openBox($boxType["name"], $syntax);
          $line = preg_replace($boxType["regex"]["o"], "", $line);
        }
        if (isset($boxType["regex"]["c"]) && preg_match($boxType["regex"]["c"], $line)){
          $closeBoxes[] = $boxType["name"];
          $line = preg_replace($boxType["regex"]["c"], "", $line);
        }
      }

      $this->currLine = ["text" => $line, "requiredBox" => "paragraph"];
      if ($this->safeMode){$this->linesafe();}
      else if (str_contains($line, "<")){$this->currLine["requiredBox"] = "";}
      $this->linemarkdown();

      //autoclose not required box, open required box
      while ($this->boxes->top > 0){
        $box = $this->boxTypes[$this->boxes->top()["type"]];
        if (isset($box["autoclose"]) && $box["autoclose"]==1 && $box["name"]!=$this->currLine["requiredBox"]){
          $this->closeBox();
        }
        else {
          break;
        }
      }
      if ($this->currLine["requiredBox"] != ""){
        if ($this->boxes->top()["type"]!=$this->currLine["requiredBox"]){
          $this->openBox($this->currLine["requiredBox"]);
        }
      }

      $this->endline($closeBoxes);
    }
    function linesafe(){ //make sure no user-inputted HTML is interpreted (safe mode)
      $this->currLine["text"] = htmlspecialchars($this->currLine["text"], ENT_NOQUOTES, 'UTF-8');
    }
    function linemarkdown() {
      $line = $this->currLine["text"];
      foreach ($this->inlineTypes as $inlineType){
        if (preg_match_all($inlineType["regex"], $line, $lineMatches)){
          foreach ($lineMatches[0] as $i => $match){
            $replacement = $inlineType["syntax"];
            for ($j = 1; $j < count($lineMatches); $j++){
              $replacement = str_replace("%".$j."%", $lineMatches[$j][$i], $replacement);
            }
            $line = str_replace($match, $replacement, $line);
          }
          if (isset($inlineType["requiredBox"])){
            $this->currLine["requiredBox"] = ($inlineType["requiredBox"]);
          }
        }
      }
      $this->currLine["text"] = $line;
    }
    function endline($closeBoxes = []){
      $line = $this->currLine["text"];
      $box = $this->boxes->pop();

      //special rules
      if (preg_match("/[ ]*/", $line)){
        if ($box["type"]=="table"){//table lines
          if (count($box["text"]) == 0){
            $box["syntax"] = str_replace("<thead></thead>", "<thead>".$this->parseTableLine($line, true)."</thead>", $box["syntax"]);
            $line = "";
          }
          else if (preg_match("/^(\|[:\-]+!?)+\|?$/", $line)){//special alignment
            $box["syntax"]=str_replace("<table>", "<table id='".$this->parseTableAlign($line, $box)."'>", $box["syntax"]);
            $line = "";
          }
          else {
            $line = $this->parseTableLine($line);
          }
        }
      }
      $func = $this->callback;
      if ($this->callback != null){$func($line);}
      if ($this->extent > -1){
        $line = $this->placeSpecChar($line, 2);
      }
      $box["text"][] = $line;
      $this->boxes->push($box);

      foreach ($closeBoxes as $closeBox){
        $inStack = false;
        foreach ($this->boxes->stack as $livebox){
          if ($livebox["type"] == $closeBox){
            $inStack = true; break;
          }
        }
        if ($inStack){
          while (count($this->boxes->stack) > 1){
            $box = $this->boxes->top();
            $this->closeBox();
            if ($box["type"]==$closeBox){
              break;
            }
          }
        }
      }
      $this->currLine = ["text" => ""];
    }

    //boxes
    function openBox(string $boxType, string $syntax = ""){
      //input checker
      if (!isset($this->boxTypes[$boxType])){
        return false;
      }
      if ($syntax == ""){
        $syntax = $this->boxTypes[$boxType]["syntax"];
      }

      //nesting checker
      while (count($this->boxes->stack) != 1){
        if ($this->boxTypes[$this->boxes->top()["type"]]["nesting"]["level"] > $this->boxTypes[$boxType]["nesting"]["maxParent"]){
          $this->closeBox();
        }
        else {
          break;
        }
      }

      $box = ["type" => $boxType, "text" => [], "syntax" => $syntax];
      $this->boxes->push($box);
    }
    function closeBox() {
      $box = $this->boxes->pop();
      $parentBox = $this->boxes->pop();
      $body = implode(PHP_EOL, $box["text"]);
      if ($this->extent < 1){
        $box["syntax"] = "%body%";
      }
      $fullText = $box["syntax"];
      $fullText = str_replace("%body%", $body, $fullText);
      $parentBox["text"][] = $fullText;
      $this->boxes->push($parentBox);
    }

    //other
    function plainBody(){
      $return = "";
      while (!$this->boxes->top == 0){
        $this->closeBox();
      }

      $return .= implode(PHP_EOL, $this->boxes->pop()["text"]);
      if ($this->styles != ""){$return .= PHP_EOL."<style>".$this->styles."</style>";}
      return $return;
    }
    function parseTableLine(string $body, $titlerow = false){
      $elements = explode("|", $body);
      $final = "<tr>";
      $inset = ""; if ($titlerow){$inset = "prepdwnid".$this->idtracker;}
      for ($i = 1; $i < count($elements); $i++){
        $final .= "<td $inset$i>".$elements[$i]."</td>";
      }
      $final .= "</tr>";
      return $final;
    }
    function parseTableAlign(string $body, &$box){
      $tid = "pdwnstyle".$this->idtracker++;
      $elements = explode("|", $body);
      $final = "";
      for ($i = 1; $i < count($elements); $i++){
        if ($elements[$i]==""){continue;}

        $sortable = false; //idk how to actually implement this
        if (preg_match("/\!$/", $elements[$i])){$sortable = true; $elements[$i]=substr($elements[$i], 0, strlen($elements[$i]) - 1);}
        if ($sortable){
            $box["syntax"] = str_replace("prepdwnid".($this->idtracker-1).$i, "class='sortable'", $box["syntax"]);
        }

        if (preg_match("/:\-+:/", $elements[$i])){$align="center";}
        else if (preg_match("/\-+:/", $elements[$i])){$align="right";}
        else {$align="left";}
        $final .= "#$tid td:nth-child($i) {text-align:".$align.";} ";
      }
      $this->styles .= $final;
      return $tid;
    }
    function parseImage(string $body){
      if (preg_match("/{(.*)}/", $body, $allmatches)){
        $stringDico = $allmatches[1];
        $chunks = array_chunk(preg_split('/(\[|\])/', $stringDico), 2);
        $img = [];
        if (count(array_column($chunks, 0)) > 0){
          foreach ($chunks as $chunk){
            if (isset($chunk[1])){
              $img[$chunk[0]]=$chunk[1];
            }
          }
        }
        if (!isset($img["src"])){return $body;}
        if (!isset($img["class"])){$img["class"] = "sideimg";}
        else if ($img["class"]=="landscape"){$img["class"]="sideimg landscape";}
        if (!isset($img["caption"])){$img["caption"] = "";}
        if (!isset($img["style"])){$img["style"] = "";}
        $this->purify($img["class"]);$this->purify($img["caption"], "cleanText");$this->purify($img["src"], "cleanText");$this->purify($img["style"], "cleanText");
        $parser = new Parsedown();
        $caption = $parser->parse($this->placeSpecChar($img["caption"], 0), 0);

        $echoImg = '<div class="'.$img["class"].'" style="'.$img["style"].'"><a href="'.$img["src"].'" target="_blank"><img src="'.$img["src"].'" /></a><p>'.$caption.'</p></div>';

        return $echoImg;
      }
      return $body;
    }
  }
}

?>
