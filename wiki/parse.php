<?php
// engine.php, getWiki(), parseTxt.php (engine)

require_once($_SERVER['DOCUMENT_ROOT']."/wiki/Parsedown.php");
require_once($_SERVER['DOCUMENT_ROOT']."/wiki/domInfo.php");

class parse {
    public $conn = null;
    protected $page = "";
    protected $parseClear = false;

    public $insertArray = [];
    public $keyTracker = 0;
    public $database = "pages";
    public $parentWiki = 2;

    function __construct($conn, $page, $parseClear = 0, $domain = "fandom") {
        $this->page = $page;
        if ($parseClear == 1){$this->parseClear = true;}
        equipDom($this, $domain);
        $this->parentWiki = getWiki($page, $this->database, $this->dbconn);
    }

    function bodyParser($body, $extent = 0, $database = "pages") {
        $Parsedown = new Parsedown();
        $this->database = $database;
        if ($this->parseClear) {$Parsedown->setSafeMode(false);} else {$Parsedown->setSafeMode(true);}

        if ($extent > 0) {
            $lineMatches = [];
            if (preg_match_all("/^.*{([a-z]+\[.*\]).*}.*$/m", $body, $lineMatches) != false){$body = $this->parseImg($body, $this->keyTracker, $lineMatches[0]);}
        }

        $body = $Parsedown->text($body);

        //post-parse processing
        if (str_contains($body, "[footnote:")){$body = $this->doSources($body);}
        if ($extent > 0) {
            //if (preg_match_all("/^<h.+\[fold\].*$/m", $body, $newMatches) != false){$body = $this->parseFolds($body, $newMatches[0]);}
            if (str_contains($body, "[wiki:children]")){
                $body = $this->addFullChildLine($body, $this->page, "full");
            }
            if (str_contains($body, "[wiki:children")) {
                $artId = substr($body, strpos($body, "[wiki:children") + 14);
                $artId = substr($artId, 0, strpos($artId, "]"));
                $body = $this->addFullChildLine($body, $artId, "spec");
            }
            if (str_contains($body, "[wiki:new")){$body = $this->addRecents($body, "reg_date");}
            if (str_contains($body, "[wiki:pop")){$body = $this->addRecents($body, "pop");}
            if (str_contains($body, "[wiki:genre:")){$body = $this->addGenre($body);}
            if (str_contains($body, "[wiki:art")){$body = $this->addThumbnails($body);}

            if ($this->insertArray != null){
                foreach ($this->insertArray as $key => $value) {
                    $body = substr_replace($body, $value, strpos($body, "%key".$key."key%"), 0);
                    $body = str_replace("%key".$key."key%", "", $body);
                }
            }
            $body = str_replace("[gallery]", "<div class='gallery'>", $body);
            $body = str_replace("[wide]", "<div class='wide'>", $body);
            $body = preg_replace("/\[note([a-zA-Z ]*)\]/", "<div class='note $1'>", $body);
            $body = str_replace("[/gallery]", "</div>", $body);
            $body = preg_replace("/\[\/[a-z]+\]/", "</div>", $body);
            $body = str_replace("[/wide]", "</div>", $body);
        }

        $body = txtUnparse($body, 0);
        return $body;
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
        WHERE a.root = $page AND b.id IS NULL LIMIT 22";
        if ($firstrow = $this->dbconn->query($query)) {
            while ($row = $firstrow->fetch_assoc()) {
                $pageName = $row["name"];
                $pageShortName = $row["shortName"];
                $pageImg = $row["banner"];
                $thumbImg = $row["sidetabImg"];
                $pageStatus = $row["status"];
                $childPage = $row["id"];
                if ($pageShortName != ""){
                    $pageName = $pageShortName;
                }
                if ($pageStatus != "suspended" && $pageStatus != "outstanding"){
                    if ($thumbImg != null){$pageImg = $thumbImg;}else{$pageImg = banner($pageImg, $this);}
                    $fullChildLine .= " <div class='domCont'><a href='".artUrl($this->baseLink, $childPage, $pageName)."' load-image='".$pageImg."'> <h3>".$pageName."</h3><div class='overlay'></div></a></div>";
                }
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
                    $toInsertThumb = " <div class='domCont'><a href='".artUrl($this->baseLink, $childPage, $pageName)."' load-image='".$pageImg."'> <h3>".$pageName."</h3><div class='overlay'></div></a></div>";
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

    function addRecents($body, $sort){
        if ($sort == "reg_date") {$sortName = "new";}
        else {$sortName = "pop";}

        if (str_contains($body, "[wiki:$sortName]")){
            $age = 0;
            $fullRow = "";
        }
        else {
            $artId = substr($body, strpos($body, "[wiki:$sortName") + 9);
            $age = substr($artId, 0, strpos($artId, "]"));
        }

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
                    if (getWiki($childPage) != $this->parentWiki OR $row["status"] != "active") {continue;}
                    if ($age > 0) {
                        if ($age == $counter) {
                                $pageName = $row["shortName"];
                                $pageImg = $row["banner"];
                                $thumbImg = $row["sidetabImg"];
                                if ($thumbImg != null){$pageImg = $thumbImg;}else{$pageImg = banner($pageImg, $this);}
                                $toInsertThumb = $this->createThumbTab(artUrl($this->baseLink, $childPage, $pageName), $pageImg, $pageName);
                                $body = substr_replace($body, $toInsertThumb, strpos($body, "[wiki:$sortName".$age."]"), 0);
                                $body = str_replace("[wiki:$sortName".$age."]", "", $body);
                                if (str_contains($body, "[wiki:$sortName")){return $this->addRecents($body, $sort);}
                                else { return $body;}
                                break;
                        }
                        else {$counter++;}
                    }
                    else {
                        if ($counter > 8) {break;}
                        $pageName = $row["shortName"];
                        $pageImg = $row["banner"];
                        $thumbImg = $row["sidetabImg"];
                        if ($thumbImg != null){$pageImg = $thumbImg;}else{$pageImg = banner($pageImg, $this);}
                        $fullRow.= $this->createThumbTab(artUrl($this->baseLink, $childPage, $pageName), $pageImg, $pageName);
                        $counter++;
                    }
                }
            }
        }
        if ($age == 0){
            $body = substr_replace($body, $fullRow, strpos($body, "[wiki:$sortName]"), 0);
            $body = str_replace("[wiki:$sortName]", "", $body);

            if (str_contains($body, "[wiki:$sortName")){return $this->addRecents($body, $sort);}
            else { return $body;}
        }
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
                    $fullLine .= $this->createThumbTab(artUrl($this->baseLink, $childPage, $pageName), $pageImg, $pageName);

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
    function parseImg($body, &$keyTracker, $lineMatches) {
        foreach($lineMatches as $line){
            $artId = substr($line, strpos($line, "{")+1);
            $stringDico = substr($artId, 0, strpos($artId, "}") - 1);
            $chunks = array_chunk(preg_split('/(\[|\])/', $stringDico), 2);

            if (count(array_column($chunks, 0)) > 0 AND count(array_column($chunks, 0)) == count(array_column($chunks, 1))){
                $img = array_combine(array_column($chunks, 0), array_column($chunks, 1));
            }
            else {$img = [];}

            if (!isset($img["class"]) OR !checkRegger("basic", $img["class"])){$img["class"] = "sideimg";}
            if (!isset($img["caption"]) OR !checkRegger("cleanText", $img["caption"])){$img["caption"] = "";} 
            if (!isset($img["src"]) OR !checkRegger("cleanText", $img["src"])){$img["src"] = "";}
            if (!isset($img["style"]) OR !checkRegger("cleanText", $img["style"])){$img["style"] = "";}
            $caption = $this->bodyParser(txtUnparse($img["caption"], 0), 0, $this->database);

            $echoImg = '<div class="'.$img["class"].'" style="'.$img["style"].'"><img src="'.$img["src"].'" /><p>'.$caption.'</p></div>';

            $this->insertArray[$keyTracker] = $echoImg;
            $pos = strpos($body, "{");
            $body = str_replace($line, "%key".$keyTracker."key%", $body);
            $keyTracker++;
        }

        if (preg_match("/^{.*}$/", $body)){return $this->parseImg($body, $keyTracker);}
        else {return $body;}
    }
    /*function parseFolds($body, $lineMatches){
        foreach($lineMatches as $line){
            $header

            return $body;
        }
    }*/

    function doSources($body){
        $artId = substr($body, strpos($body, "[footnote:") + 10);
        $footNumber = substr($artId, 0, strpos($artId, "]"));

        $footnote = "<sup class='footnote' onclick='showFoot($footNumber);'>[$footNumber]</sup>";

        $body = substr_replace($body, $footnote, strpos($body, "[footnote:$footNumber]"), 0);
        $body = str_replace("[footnote:$footNumber]", "", $body);
        if (str_contains($body, "[footnote:")){return $this->doSources($body);}
        else { return $body;}
    }
}



?>