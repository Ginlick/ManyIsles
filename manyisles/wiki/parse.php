<?php
//generates thumbnails ([wiki:X]), [contentsTable:X], [footnote:X]

require_once($_SERVER['DOCUMENT_ROOT']."/wiki/engine.php");
require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/parser.php");
require_once($_SERVER['DOCUMENT_ROOT']."/wiki/domInfo.php");
require_once($_SERVER['DOCUMENT_ROOT']."/fandom/getWiki.php");

class parse {
    use allBase;
    public $conn = null;
    protected $page = "";
    protected $parseClear = false;

    public $keyTracker = 0;
    public $database = "pages";
    public $parentWiki = 2;

    protected $titleArray = [];

    function __construct($conn, $page, $parseClear = 0, $domain = "fandom") {
        $this->page = $page;
        if ($parseClear == 1){$this->parseClear = true;}
        equipDom($this, $domain);
        $this->parentWiki = getWiki($page, $this->database, $this->dbconn);

        $this->parser = new parser($parseClear);
    }

    function bodyParser($body, $extent = 0, $database = "pages") {
        $this->database = $database;

        if ($extent > 1){
          $wikiWork = function(&$body) {
            if (str_contains($body, "[footnote:")){$body = $this->doSources($body);}
              //if (preg_match_all("/^<h.+\[fold\].*$/m", $body, $newMatches) != false){$body = $this->parseFolds($body, $newMatches[0]);}
              if (str_contains($body, "[wiki:children]")){
                  $body = $this->addFullChildLine($body, $this->page, "full");
              }
              if (str_contains($body, "[wiki:children")) {
                  $artId = substr($body, strpos($body, "[wiki:children") + 14);
                  $artId = substr($artId, 0, strpos($artId, "]"));
                  $body = $this->addFullChildLine($body, $artId, "spec");
              }
              $this->idifyTitles($body);
              if (str_contains($body, "[wiki:new")){$body = $this->addRecents($body, "reg_date");}
              if (str_contains($body, "[wiki:rand")){$body = $this->addRecents($body, "RAND()");}
              if (str_contains($body, "[wiki:pop")){$body = $this->addRecents($body);}
              if (str_contains($body, "[wiki:genre:")){$body = $this->addGenre($body);}
              if (str_contains($body, "[wiki:art")){$body = $this->addThumbnails($body);}
              if (str_contains($body, "[contentsTable:")){$body = $this->addContentsTable($body);}

              // $body = preg_replace("/\[note([a-zA-Z ]*)\]/", "<div class='note $1'>", $body);
              // $body = str_replace("[/note]", "</div>", $body);
              //$body = preg_replace("/\[\/[a-z]+\]/", "</div>", $body);
          };
        }
        else {
          $wikiWork = function(&$body) {
            if (str_contains($body, "[footnote:")){$body = $this->doSources($body);}
          };
        }
        return $this->parser->parse($body, $extent, $wikiWork);
    }




    function createThumbTab($link, $img, $name) {
       return " <div class='domCont'><a href='".$link."' load-image='".$img."'><h3>".$name."</h3><div class='overlay'></div></a></div>";
    }

    function addFullChildLine($body, $page, $mode) {
        $fullChildLine = "";
        $query = "SELECT a.*
        FROM $this->database a
        LEFT OUTER JOIN $this->database b
            ON a.id = b.id AND a.v < b.v
        WHERE a.root = $page AND a.status != 'outstanding' AND b.id IS NULL LIMIT 22";
        if ($firstrow = $this->dbconn->query($query)) {
            while ($row = $firstrow->fetch_assoc()) {
                $pageName = $row["name"];
                $pageShortName = $row["shortName"];
                $pageImg = $row["banner"];
                $thumbImg = $row["sidetabImg"];
                $childPage = $row["id"];
                if ($pageShortName != ""){
                    $pageName = $pageShortName;
                }
                if ($thumbImg != null){$pageImg = $thumbImg;}else{$pageImg = banner($pageImg, $this);}
                $fullChildLine .= " <div class='domCont'><a href='".artUrl($this->artRootLink, $childPage, $pageName)."' load-image='".$pageImg."'> <h3>".$pageName."</h3><div class='overlay'></div></a></div>";
            }
        }
        if ($mode == "full"){
            $body = substr_replace($body, $fullChildLine, strpos($body, "[wiki:children]"), 0);
            $body = str_replace("[wiki:children]", "", $body);
        }
        else if ($mode == "spec"){
            $body = substr_replace($body, $fullChildLine, strpos($body, "[wiki:children$page]"), 0);
            $body = str_replace("[wiki:children$page]", "", $body);
        }

        return $body;
    }

    function addThumbnails($body){
        $startString = substr($body, stripos($body, "[wiki:art"));
        $startPos = stripos($startString, "art") + 3;
        $childPage = substr($startString, $startPos, stripos($startString, "]") - $startPos);
        $query = "SELECT * FROM $this->database WHERE id = ".$childPage."  ORDER BY v DESC LIMIT 0, 1 ";
        if ($firstrow = $this->dbconn->query($query)) {
            if ($firstrow->num_rows != 0){
                while ($row = $firstrow->fetch_assoc()) {
                    $pageName = $row["name"];
                    $pageShortName = $row["shortName"];
                    $pageImg = $row["banner"];
                    $thumbImg = $row["sidetabImg"];
                    $pageStatus = $row["status"];
                }
                if ($pageShortName != ""){
                    $pageName = $pageShortName;
                }
                if ($pageStatus != "suspended"){
                    if ($thumbImg != null){$pageImg = $thumbImg;}else{$pageImg = banner($pageImg, $this);}
                    $toInsertThumb = " <div class='domCont'><a href='".artUrl($this->artRootLink, $childPage, $pageName)."' load-image='".$pageImg."'> <h3>".$pageName."</h3><div class='overlay'></div></a></div>";
                    $body = substr_replace($body, $toInsertThumb, strpos($body, "[wiki:art".$childPage."]"), 0);
                }
            }
        }
        $body = str_replace("[wiki:art".$childPage."]", "", $body);
        if (str_contains($body, "[wiki:art")){return $this->addThumbnails($body);}
        else {
            return $body;
        }
    }

    function addRecents($body, $sort = "pop"){
        if ($sort == "reg_date") {$sortName = "new";}
        else if ($sort == "RAND()") {$sortName = "rand";}
        else {$sortName = "pop";}
        $age = 0;
        $fullRow = "";

        $artId = substr($body, strpos($body, "[wiki:$sortName") + strlen("[wiki:$sortName"));
        $age = substr($artId, 0, strpos($artId, "]"));
        if ($age == ""){$age = 0;}

        $query = "SELECT a.*
        FROM $this->database a
        LEFT OUTER JOIN $this->database b
            ON a.id = b.id AND a.v < b.v
        WHERE b.id IS NULL ORDER BY $sort DESC LIMIT 0, 222";
        if ($firstrow = $this->dbconn->query($query)) {
            if ($firstrow->num_rows != 0){
                $counter = 1;
                while ($row = $firstrow->fetch_assoc()) {
                    $childPage = $row["id"];
                    if (getWiki($childPage, $this->database, $this->dbconn) != $this->parentWiki OR $row["status"] != "active") {continue;}
                    if ($age > 0) {
                      if ($sortName == "rand"){
                        if ($age >= $counter) {
                          $pageName = $row["shortName"];
                          $pageImg = $row["banner"];
                          $thumbImg = $row["sidetabImg"];
                          if ($thumbImg != null){$pageImg = $thumbImg;}else{$pageImg = banner($pageImg, $this);}
                          $fullRow .= $this->createThumbTab(artUrl($this->artRootLink, $childPage, $pageName), $pageImg, $pageName);
                          $counter++;

                        }
                        else {
                          break;
                        }
                      }
                      else {
                        if ($age == $counter) {
                          $pageName = $row["shortName"];
                          $pageImg = $row["banner"];
                          $thumbImg = $row["sidetabImg"];
                          if ($thumbImg != null){$pageImg = $thumbImg;}else{$pageImg = banner($pageImg, $this);}
                          $toInsertThumb = $this->createThumbTab(artUrl($this->artRootLink, $childPage, $pageName), $pageImg, $pageName);
                          $body = str_replace("[wiki:$sortName".$age."]", $toInsertThumb, $body);
                          if (str_contains($body, "[wiki:$sortName")){return $this->addRecents($body, $sort);}
                          else { return $body;}
                          break;
                        }
                        else {$counter++;}
                      }
                    }
                    else {
                        if ($counter > 8) {break;}
                        $pageName = $row["shortName"];
                        $pageImg = $row["banner"];
                        $thumbImg = $row["sidetabImg"];
                        if ($thumbImg != null){$pageImg = $thumbImg;}else{$pageImg = banner($pageImg, $this);}
                        $fullRow.= $this->createThumbTab(artUrl($this->artRootLink, $childPage, $pageName), $pageImg, $pageName);
                        $counter++;
                    }
                }
            }
        }
        if ($age == 0){
          $body = substr_replace($body, $fullRow, strpos($body, "[wiki:$sortName]"), 0);
          $body = str_replace("[wiki:$sortName]", "", $body);
        }
        else {
          $body = str_replace("[wiki:$sortName".$age."]", $fullRow, $body);
          $body = str_replace("[wiki:$sortName".$age."]", "", $body);
        }

        if (str_contains($body, "[wiki:$sortName")){return $this->addRecents($body, $sort);}
        else { return $body;}
    }

    function addGenre($body){
        $artId = substr($body, strpos($body, "[wiki:genre:") + 12);
        $genre = substr($artId, 0, strpos($artId, "]"));
        $genre = preg_replace("/[^A-Za-z- ]/", "", $genre);
        $fullLine = "";

        $query = "SELECT a.*
        FROM $this->database a
        LEFT OUTER JOIN $this->database b
            ON a.id = b.id AND a.v < b.v
        WHERE a.cate LIKE '%{$genre}%' AND b.id IS NULL ORDER BY v DESC LIMIT 0, 222";
        if ($firstrow = $this->dbconn->query($query)) {
            if ($firstrow->num_rows != 0){
                $counter = 1;
                $fullLine = "";
                while ($row = $firstrow->fetch_assoc()) {
                    $childPage = $row["id"];
                    if (getWiki($childPage) != $this->parentWiki OR $row["status"] != "active") {continue;}

                    $pageName = $row["shortName"];
                    $pageImg = $row["banner"];
                    $thumbImg = $row["sidetabImg"];
                    if ($thumbImg != null){$pageImg = $thumbImg;}else{$pageImg = banner($pageImg, $this);}
                    $fullLine .= $this->createThumbTab(artUrl($this->artRootLink, $childPage, $pageName), $pageImg, $pageName);

                    $counter++;
                    if ($counter > 8){break;}
                }
            }
        }
        $body = substr_replace($body, $fullLine, strpos($body, "[wiki:genre:$genre]"), 0);
        $body = str_replace("[wiki:genre:$genre]", "", $body);

        if (str_contains($body, "[wiki:genre:")){return $this->addGenre($body);}
        else { return $body;}
    }

    function addContentsTable($body){
      if (preg_match_all("/\[contentsTable:([1-9])\]/", $body, $lineMatches, PREG_OFFSET_CAPTURE) != false){
        $degree = $lineMatches[1][0][0];
        $offset = $lineMatches[0][0][1];
        $index = 1;
        $result = "<div class='contentsTable'><h4>Contents</h4><ul>";
        foreach ($this->titleArray as $title){
          if ($title["magnitude"]>$degree){continue;}
          $inset = ""; if ($title["magnitude"]==2){$inset = $index.". ";$index++;}
          $result .= "<li class='contentEl m".$title["magnitude"]."'>".$inset."<span class='fakelink' onclick='showTitle(\"".$title["id"]."\")'>".$title["text"]."</span></li>";
        }
        $result .= "</ul></div>";
        $body = substr_replace($body, $result, $offset, 17);
      }
      return $body;
    }


    /*function parseFolds($body, $lineMatches){
        foreach($lineMatches as $line){
            $header

            return $body;
        }
    }*/

    function idifyTitles(&$body){
      $degree = 5;
      if (preg_match_all("/<h([1-".$degree."])>([^<\/>]+)<\/h[1-".$degree."]>/", $body, $titles) != false){
        for ($i = 0; $i < count($titles[0]); $i++){
          $titFull = $titles[0][$i];
          $titMagnitude = $titles[1][$i];
          $titText = $titles[2][$i];
          $titId = $this->purate($titText);
          $newTitFull = "<h$titMagnitude id='$titId'>$titText</h$titMagnitude>";
          $body = str_replace($titFull, $newTitFull, $body);
          $this->titleArray[] = ["id" => $titId, "magnitude" => $titMagnitude, "text" => $titText];
        }
      }
    }
    function doSources($body){
        $artId = substr($body, strpos($body, "[footnote:") + 10);
        $footNumber = substr($artId, 0, strpos($artId, "]"));

        $footnote = "<sup class='footnote' onclick='showFoot($footNumber);'>[$footNumber]</sup>";

        $body = str_replace("[footnote:$footNumber]", $footnote, $body);
        if (str_contains($body, "[footnote:")){return $this->doSources($body);}
        else { return $body;}
    }
}



?>
