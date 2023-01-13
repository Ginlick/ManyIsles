<html>
<head>
  <link rel="icon" href="/Imgs/FaviconWiki.png">  <link rel="stylesheet" type="text/css" href="/Code/CSS/Main.css">
   <link rel="stylesheet" type="text/css" href="/wiki/wik.css">
<style>
  p {
    padding-left: 22px;
  }

  .wide {
    min-width: 200px;
    background-color: red;
    padding: 20px;
  }
  .fullTable {
    background-color: green;
  }

</style>
</head>
<body>
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
      $body = $this->Parsedown->parse($body, $extent);
      $callback($body);
      return $body;
    }
  }

  class Parsedown {
    use allBase; //purify, special characters parse required

    private $boxTypes = [ //change this to be like InlineTypes
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
        "name" => "wide",
        "regex"=> ["o" => "/^\[gallery\]/", "c" => "/\[\/gallery\]/"],
        "syntax"=>'<div class="gallery">%body%</div>',
        "nesting"=>["level"=>1, "maxParent" => 1],
      ],
      "quote" => [
        "name" => "wide",
        "regex"=> ["o" => "/\[quote((?: note| left)*)\]/", "c" => "/\[\/quote\]/"],
        "syntax"=>'<div class="quote%1%">%body%</div>',
        "nesting"=>["level"=>2, "maxParent" => 1],
      ],
      "highlighted" => [
        "name" => "wide",
        "regex"=> ["o" => "/\[highlighted((?: note| left)*)\]/", "c" => "/\[\/highlighted\]/"],
        "syntax"=>'<div class="highlighted%1%">%body%</div>',
        "nesting"=>["level"=>2, "maxParent" => 1],
      ],
      "paragraph" => [
        "name" => "paragraph",
        "syntax" => "<p>%body%</p>",
        "nesting"=>["level"=>2,"maxParent"=>1],
        "autoclose" => 1,
      ],
      "ul" => [
        "name" => "ul",
        "syntax" => "<ul>%body%</ul>",
        "nesting"=>["level"=>2,"maxParent"=>1],
        "autoclose" => 1,
      ],
      "base" => [
        "name" => "base",
        "nesting" => ["level"=>0]
      ]
    ];
    private $inlineTypes = [ //I probably can delete all the "name" attributes (unused)
      "bolditalic" => [
        "name"=>"bolditalic",
        "regex"=>"/\*\*\*(.+)\*\*\*/",
        "syntax"=>"<i><b>%1%</b></i>",
      ],
      "bold" => [
        "name"=>"bold",
        "regex"=>"/\*\*(.+)\*\*/",
        "syntax"=>"<i><b>%1%</b></i>"
      ],
      "italic" => [
        "name"=>"italic",
        "regex"=>"/\*(.+)\*/",
        "syntax"=>"<i>%1%</i>"
      ],
      "link" => [
          "name" => "link",
          "regex"=>"/\[(.+)\]\(([A-Za-z0-9\.\/: ]+)\)/",
          "syntax"=>'<a href="%2%">%1%</a>'
      ],
      "squote" => [
          "name" => "squote",
          "regex"=>"/\[squote\](.+)\[\/squote\]/",
          "syntax"=>'<span class="squote">%1%</span>'
      ],
      "code" => [
          "name" => "code",
          "regex"=>"/\[code\](.+)\[\/code\]/",
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
      "emptyline" => [
        "regex" => "/^$/",
        "syntax"=>"",
        "requiredBox" => ""
      ]
    ];

    public $boxes;
    public $currLine = [];
    public $safeMode = true;
    public $extent = 1;

    function __construct($safeMode = true) {
      if (!$safeMode){$this->safeMode = false;}
      $this->construct();
    }
    function construct(){
      $this->boxes = new Stack();
      $this->boxes->push(["type"=>"base", "text" => []]);
      $this->currLine = [];
    }

    function parse($body, $extent = 1) {
      //extent: -1 pure markdown, 0 markdown and special characters, 1 full html (with images&divs)
      $this->construct();
      $this->extent = $extent;

      $bodyArr = explode(PHP_EOL, $body);
      foreach ($bodyArr as $i => $line){
        $this->newline($line);
      }
      return $this->plainBody();
    }

    //lines
    function newline(string $line) {
      if (preg_match("/^{(?:.+\[.+\])+}$/", $line)){//image
        while (count($this->boxes->stack) > 1){
          $box = $this->boxes->top();
          if ($this->boxTypes[$box["type"]]["nesting"]["level"]!=1){
            $this->closeBox();
          }
          else {
            break;
          }
        }
        $line = $this->parseImage($line);
        $this->currLine = ["text" => $line];
        $this->endline();
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
            if (isset($inlineType["requiredBox"])){
              $this->currLine["requiredBox"] = ($inlineType["requiredBox"]);
            }
          }
        }
      }
      $this->currLine["text"] = $line;
    }
    function endline($closeBoxes = []){
      $line = $this->currLine["text"];
      $box = $this->boxes->pop();

      //special rules
      if ($box["type"]=="paragraph" && count($box["text"]) > 0 && $line != ""){
        $line = "<br />".$line;
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
      $fullText = $box["syntax"];
      $fullText = str_replace("%body%", $body, $fullText);
      $parentBox["text"][] = $fullText;
      $this->boxes->push($parentBox);
    }

    //other
    function plainBody(){
      echo "<br><br>BEGIN RETURN<br>";
      //print_r($this->boxes);
      $return = "";
      while (!$this->boxes->top == 0){
        $this->closeBox();
      }

      $return .= implode(PHP_EOL, $this->boxes->pop()["text"]);
      //parse all special characters now (%yomama% and company)
      return $return;
    }
    function parseImage(string $body){
      preg_match("/{(.*)}/", $body, $allmatches);
      $stringDico = $allmatches[1];
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

      return $echoImg;
    }
  }
}

$input = <<<helle
## I'm a title
what's up?
-
list e*lement*
-another lis*t [squote]element[/squote]
- hail

[quote]
hi
[/quote]

[wide]
{src[https://i.pinimg.com/564x/1f/1c/8f/1f1c8f39f8950afa1f5c7dcd79541a99.jpg]caption[this]}
the *sky*, lol
###Subtitle
[fullTable]
more ***Tex***t
and a line break <script>alert("hehe!");</script>
and another!
[/fullTable]
[/wide]
wh**at [happens](e)[br] h**ere?
#####Loooooo*ong loooo*oong tiiitle
helle;

$parser = new parser();
echo $parser->parse($input, 0);

?>
</body>
