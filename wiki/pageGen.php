<?php


class gen {
    public $conn;
    public $article;
    public $writingNew = false;
    public $parentWiki = 2;
    public $wikiName = "";
    public $page = 0;
    public $user = 0;
    public $signedIn = true;
    public $power = 1;
    public $canedit = true;
    public $mode = "view";

    public $artLink = "/home";
    public $prude = false;
    public $parentName = "";
    public $ediProblem = "Error";
    public $ediButton = "";
    public $wriButton = "";
    public $repButton = "";
    public $compButton = "";
    public $dateArray = [];
    public $parse = null;
    public $manySlot = true;
    public $slotAt = "a";
    public $WSet = "";
    public $autoLinkArr = [];
    public $domainSpecs = [];

    function __construct($mode = "view", $page = 0, $parentWiki = 2, $new = false, $domain = "fandom", $moreSpecs = []) {
        $this->writingNew = $new;
        $this->page = $page;
        $this->mode = $mode;

        $fillIt = false; $igRev = false;$notArticle = false;
        if (isset($moreSpecs["fillIt"])){$fillIt = $moreSpecs["fillIt"];}
        if (isset($moreSpecs["igRev"])){$igRev = $moreSpecs["igRev"];}
        if (isset($moreSpecs["notArticle"])){$notArticle = $moreSpecs["notArticle"];}

        require_once($_SERVER['DOCUMENT_ROOT']."/wiki/domInfo.php");
        equipDom($this, $domain);
        $conn = $this->conn;

        //base security
        if ($mode == "edit" OR $mode == "act"){
            $doSecurity = true;
        }
        else {
            $doSecurity = false;
            if (!isset($_COOKIE["loggedIn"])){$this->signedIn = false;$this->canedit = false;$this->ediProblem = "Not Signed In";}
            else {$doSecurity = true;}
        }

        require($_SERVER['DOCUMENT_ROOT']."/wiki/engine.php");
        require($_SERVER['DOCUMENT_ROOT']."/fandom/getWiki.php");

        // find wiki
        $this->parentWiki = $parentWiki;
        if (!$this->writingNew){
            if ($this->page != 0){
                $this->parentWiki = getWiki($this->page, $this->database, $this->dbconn);
            }
            if (!$this->parentWiki){$this->parentWiki = 1;}
        }
        else {
            if ($parentWiki == 0){$this->wikiName = "New";}
        }

        $query="SELECT name FROM $this->database WHERE id = $this->parentWiki ORDER BY v DESC LIMIT 0, 1";
        if ($result =  $this->dbconn->query($query)) {
            if (mysqli_num_rows($result) > 0){
                while ($row = $result->fetch_assoc()){
                    $this->wikiName = $row["name"];
                }
                if ($this->writingNew){$this->page = $this->parentWiki;}
            }
        }

        $this->article = new article($this);
        if ($this->article->revertees) {$this->canedit = false; $this->ediProblem = "Reverted";}
        $this->WSet = $this->baseWSet.$this->parentWiki;

        $query= "SELECT * FROM wiki_settings WHERE id = '$this->WSet' LIMIT 1";
        if ($result =  $this->dbconn->query($query)) {
            if (mysqli_num_rows($result) > 0){
                while ($row = $result->fetch_assoc()){
                    if ($row["defaultBanner"]!= null){
                        $this->defaultBanner = $row["defaultBanner"];
                        if($this->writingNew OR $this->article->banner == "default"){
                            $this->article->banner = $this->defaultBanner;
                        }
                    }
                    if ($row["banners"]!= null){
                        $this->article->banners = json_decode($row["banners"], true);
                    }
                    if ($row["styles"]!= null){
                        $this->style = $row["styles"];
                    }
                    if ($this->changeableGenre AND isset($row["genres"]) AND $row["genres"] != ""){
                        $this->cateoptions = json_decode($row["genres"], true);
                    }
                }
            }
        }
        $this->article->banner = banner($this->article->banner, $this);

        if ($this->domain == "mystral"){
            $query= "SELECT data FROM auto_links WHERE id = $this->user LIMIT 1";
            if ($result =  $this->dbconn->query($query)) {
                if (mysqli_num_rows($result) > 0){
                    while ($row = $result->fetch_assoc()){
                        $this->autoLinkArr = json_decode($row["data"], true);
                    }
                }
            }
            $query = "SELECT size FROM images WHERE user = $this->user";
            if ($result =  $this->dbconn->query($query)) {
                if (mysqli_num_rows($result) > 0){
                    while ($row = $result->fetch_assoc()){
                        $this->domainSpecs["totalImages"] += 1;
                        $this->domainSpecs["imageSpace"] += $row["size"];
                    }
                }
            }
            if ($this->domainSpecs["totalImages"]>=$this->mystData["images"] OR $this->domainSpecs["imageSpace"]>=$this->mystData["fullSpace"]){$this->domainSpecs["canImage"]=false; }
        }

        //power
        if ($this->domain != "mystral"){
            require_once($_SERVER['DOCUMENT_ROOT']."/fandom/accStat.php");
            $this->power = getAccStat($this->dbconn, $this->user, $this->parentWiki, false);
        }
        else {
            $this->power = 5;
        }

        if (!$this->signedIn){$this->power == 1;}
        else if ($this->domain == "fandom") {
            $setto = "a";
            $query = "SELECT * FROM slots WHERE id = ".$this->user;
            if ($result = $this->conn->query($query)) {
                while ($row = $result->fetch_assoc()){
                    if ($row["a"]==null){$setto="a";}
                    else if ($row["b"]==null){$setto="b";}
                    else if ($row["c"]==null){$setto="c";}
                    else if ($row["d"]==null){$setto="d";}
                    else if ($row["e"]==null){$setto="e";}
                    else if ($row["f"]==null){$setto="f";}
                    else if ($row["g"]==null){$setto="g";}
                    else if ($row["h"]==null){$setto="h";}
                    else if ($row["i"]==null){$setto="i";$this->manySlot = false;}
                    else if ($row["j"]==null){$setto="j";$this->manySlot = false;}
                    else {$setto = "";}
                }
            }
            $canedit = true;
            if ($setto == "") {$this->canedit = false;$this->ediProblem = "Slots";}
            $slotAt = $setto;
        }
        if ($this->power < $this->minPower){$this->canedit = false; $this->ediProblem = "Status";}

        //mystral limits
        if ($this->domain == "mystral"){
          if (!$notArticle){
            $query = "SELECT a.id
            FROM $this->database a
            LEFT OUTER JOIN $this->database b
            ON a.id = b.id AND a.v < b.v
            WHERE b.id IS NULL AND a.root = 0";
            if ($result = $this->dbconn->query($query)){
                if (mysqli_num_rows($result) >= $this->mystData["notebooks"]) {
                    $this->canedit = false; $this->ediProblem = "Full Notebooks";
                }
            }
            $query = "SELECT a.id
            FROM $this->database a
            LEFT OUTER JOIN $this->database b
                ON a.id = b.id AND a.v < b.v
            WHERE b.id IS NULL";
            if ($result = $this->dbconn->query($query)){
                if (mysqli_num_rows($result) >= $this->mystData["articles"]) {
                    $this->canedit = false; $this->ediProblem = "Full Notes";
                }
            }
            $query = "SELECT id FROM $this->database";
            if ($result = $this->dbconn->query($query)){
                if (mysqli_num_rows($result) >= $this->mystData["pages"]) {
                    $this->canedit = false; $this->ediProblem = "No Space";
                }
            }
          }
        }

        //interrupt
        if ($this->mode == "edit" OR $this->mode == "act"){
            if (($this->article->revertees AND !$igRev) OR (!$this->canedit AND $this->ediProblem != "Reverted")){echo "<script>window.location.replace('$this->homelink?i=cantedit');</script>";exit();}
        }
        if ($this->mode == "act" AND $this->domain == "fandom" AND $fillIt){
            $uid = $this->user;
            $power = $this->power;
            $conn = $this->conn;
            require($_SERVER['DOCUMENT_ROOT']."/fandom/slotChecker.php");
        }

        //miscellaneous
        $this->editLink = $this->baseLink.'edit?id='.$this->page.'&v='.$this->article->version;
        $this->writeLink = $this->baseLink.'edit?w='.$this->parentWiki;
        $this->wsettLink = "";$this->wsettCogLink = "";
        if ($this->article->root == 0 && $this->power > 2) {$this->wsettLink = $this->baseLink."wsettings?w=".$this->parentWiki;$this->wsettCogLink = " <a href='".$this->wsettLink."'><i class='fas fa-cog'></i></a>"; }


        $this->typeTab = "roundInfo title";
        if ($this->domain == "fandom"){
            $this->typeTab = "typeTab";
        }

        //set buttons
        if ($this->article->incomplete == 0){$nowText = "Incomplete";$incD=1;}else{$nowText = "Complete";$incD=0;}
        $this->ediButton = '<a href="'.$this->editLink.'" class="wikiButton">Edit</a>';
        $this->wriButton = '<a  href="'.$this->writeLink.'" ><button class="wikiButton">Write</button></a>';
        $this->repButton = '<a href="/fandom/report.php?id='.$this->page.'&v='.$this->article->version.'" class="wikiButton">Report</a>';
        $this->compButton = '<a href="/fandom/incomplete.php?v='.$this->article->version.'&name='.$this->page.'&d='.$incD.'&dom='.$this->domainnum.'" class="wikiButton">'.$nowText.'</a>';
        $this->revButtons = '<a href="/fandom/revert.php?dir=0&id='.$this->page.'&dom='.$this->domainnum.'&v='.$this->article->version.'" class="wikiButton">Revert</a>';
        if ($this->article->revertees){$this->revButtons .= '<a href="/fandom/revert.php?dir=1&id='.$this->page.'&dom='.$this->domainnum.'&v='.$this->article->version.'" class="wikiButton">Evolve</a>';
                                $this->revButtons .= '<a href="/fandom/revert.php?dir=2&id='.$this->page.'&dom='.$this->domainnum.'&v='.$this->article->version.'" class="wikiButton">Age</a>';
        }
        $this->filButton = "";
        $this->delButton = "";
        if ($this->power > 3 AND !$this->article->revertees){$this->filButton = '<a href="/fandom/filicide.php?id='.$this->page.'&v='.$this->article->version.'&dom='.$this->domainnum.'" class="wikiButton">Filicide</a>';}
        if ($this->article->root != 0 AND $this->power > 4) {$this->delButton = '<a href="/fandom/delete.php?id='.$this->page.'&dom='.$this->domainnum.'" class="wikiButton">Delete</a>';}

        if (!$this->canedit) {
            if ($this->ediProblem == "Reverted") {
                $this->ediButton = '<button class="wikiButton" onclick="nospace()">Edit</button></a>';
            }
            else {
                $this->ediButton = '<button class="wikiButton" onclick="nospace()">Edit</button></a>';
                $this->wriButton = '<button class="wikiButton" onclick="nospace()">Write</button></a>';
                $this->repButton = '<button class="wikiButton" onclick="nospace()">Report</button></a>';
                $this->compButton = '<button class="wikiButton" onclick="nospace()">'.$nowText.'</button></a>';
                $this->revButtons = '<button class="wikiButton" onclick="nospace()">Revert</button></a>';
                $this->filButton = '<button class="wikiButton" onclick="nospace()">Filicide</button></a>';
                $this->delButton = '<button class="wikiButton" onclick="nospace()">Delete</button></a>';
            }
        }

        require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/fileManager.php");
        $this->files = new fileEngine($this->user);

        //remove when mostly done
        if ($this->article->parentWiki != $this->parentWiki){
            $query = "UPDATE $this->database SET parentWiki = $this->parentWiki WHERE id = $this->page AND v = ".$this->article->version;
            $this->dbconn->query($query);
        }
    }

    function doFandWork() {
        if ($this->domain == "fandom"){
            $this->artRootLink = "/fandom/".parse2Url($this->wikiName)."/";
        }

        if ($this->domain == "mystral"){
            $this->artLink = artUrl($this->artRootLink, $this->page, parse2Url($this->article->shortName));
        }
        else {
            $this->artLink = artUrl("/fandom/".parse2Url($this->wikiName)."/", $this->page, $this->article->shortName);
        }
        if ($this->article->NSFW == 2 AND !isset($_COOKIE["clearNSFW"])){
            $this->prude = true;
        }
        $query = "UPDATE pages SET pop = pop + 1 WHERE id = $this->page AND v = ".$this->article->version;
        $this->dbconn->query($query);

        if ($this->article->status == "suspended" && !isset($_GET["clear"])){
            if ($this->page != $this->parentWiki){
                header("Location:$newArtRoot".$this->parentWiki."/home?i=susp");exit();
            }
            else {header("Location:/fandom/home?i=susp");exit();}
        }

        $settingsId = $this->parentWiki; if ($this->domain == "mystral"){$settingsId = $this->user."_".$this->parentWiki;}
        $this->dateArray = getDateArray($this->dbconn, $settingsId, $this->domain);
        $date_array = date_parse($this->article->regdate);
        $pubdate = $date_array["day"].".".$date_array["month"].".".$date_array["year"];

        if ($this->article->root != 0){
            $query = "SELECT name FROM $this->database WHERE id = ".$this->article->root." ORDER BY v DESC LIMIT 0, 1";
            if ($firstrow = $this->dbconn->query($query)) {
                while ($row = $firstrow->fetch_assoc()) {
                    $this->parentName = $row["name"];
                }
            }
        }
        else {
            $this->parentName = $this->domainName;
        }

        //cache delete
        if (isset($_GET["cache"]) OR isset($_GET["v"])){
            header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");
            header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
            header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
            header("Cache-Control: post-check=0, pre-check=0", false);
            header("Pragma: no-cache");
        }

        $this->prepareParse();
    }
    function prepareParse() {
        require($_SERVER['DOCUMENT_ROOT']."/wiki/parse.php");
        $this->parse = new parse($this->dbconn, $this->page, $this->article->parseClear, $this->domain);
    }

    function giveFavicon() {
        $main = ' <link rel="icon" href="/Imgs/FaviconWiki.png">';
        $main .= '  <link rel="stylesheet" type="text/css" href="/Code/CSS/Main.css">
        <link rel="stylesheet" type="text/css" href="/wiki/wik.css">
        <link rel="stylesheet" type="text/css" href="/Code/CSS/pop.css">';
        if ($this->mode == "edit"){
            $main .= ' <link rel="stylesheet" type="text/css" href="/Code/CSS/specs.css" >';
        }
        if (!in_array($this->style, $this->styles)){$this->style = "Mystral";}

        foreach ($this->styleInfo[$this->style] as $lib){
            $main .= '<link rel="stylesheet" type="text/css" href="'.$lib.'">';
        }

        return $main;
    }
    function giveScripts() {
        $main = '
        <div class="showBGer" onclick="showMenu(this)" id="barsBoi">
            <i class="fas fa-bars" > </i >
        </div >
        <div id="modal" class="modal" onclick="removePops()"></div>

        <script src="/Code/CSS/global.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/mousetrap/1.4.6/mousetrap.min.js"></script>
        <script src="/wiki/wiki.js"></script>
        <script>
            var power = '.$this->power.';
            var parentWiki = "'.$this->parentWiki.'";
            var sourceJSON = \''.$this->article->sources.'\';
            var domain = '.$this->domainnum.';
            var baseURL = "'.$this->artRootLink.'";
            var domInfos = {
                0 : {
                    "baseURL" : "/fandom/wiki/"
                },
                1 : {
                    "baseURL" : "/docs/"
                },
                2 : {
                    "baseURL" : "/5eS/"
                },
                3 : {
                    "baseURL" : "/mystral/"
                }
            }
        </script>
        ';
        if ($this->mode == "view"){
            $main .= '<script src="/wiki/write.js"></script>';
        }
        else if ($this->mode == "edit"){
            $main .= '<script src="/wiki/edit.js"></script>';
            $main .= '<script class="jsbin" src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>';
        }
        return $main;
    }
    function giveWikStyle() {
        $query = "SELECT * FROM wiki_settings WHERE id = '$this->WSet'";
        if ($firstrow = $this->dbconn->query($query)) {
            if (mysqli_num_rows($firstrow) != 0){
                while ($row = $firstrow->fetch_assoc()) {
                    $echo = "";
                    if ($row["backgroundColor"] != ""){$echo .= "background-color: ".$row["backgroundColor"].";";}
                    if ($row["backgroundImg"] != ""){$echo .= "background-image: url(".$row["backgroundImg"].");"; }
                    return $echo;
                }
            }
        }
    }

    function giveTopBar() {
        if (!$this->acceptsTopBar){return;}
        $main ='
        <div class="topBar '.$this->domain.'">
            <div class="topBarRight">
            <div class="roundImgCont">';
        if ($this->domain == "5eS"){
          $main .=' <img src="/Imgs/5eSlogo.png" />';
        }
        else if ($this->domain == "mystral") {
            $main .='<a href="'.$this->homelink.'" style="padding: 0;display:flex;align-items: center;"> <img src="'.$this->baseImage.'" /></a>';
        }
        else {
          $main .=' <img src="/Imgs/Favicon.png" />';
        }
        $main .='   </div>
            <div class="logoCont"><a href="/home" target="_self">'.$this->domainLogo.'</a></div>
            <div class="wikisbarcont">
                <input type="text" class="wikisearchbar serach" placeholder="Search '.$this->pagename.'s..." oninput="offerSuggestions(this, \'\', 0, \'link\', 1);" onfocus="offerSuggestions(this, \'\', 0, \'link\', 1);" autocomplete="off"></input>
                <div class="suggestions" ></div>
            </div>
        </div>
        <div class="topBarLeft">';
            if ($this->power >= $this->minPower AND $this->domain != "mystral") {
                $main .= $this->revButtons;
                $main .= '<a href="/docs/edit.php?v='.$this->article->version.'&id='.$this->page.'&domain='.$this->domain.'" target="_self">Edit</a>';
                $main .= '<a href="/docs/edit.php?w='.$this->parentWiki.'&domain='.$this->domain.'" target="_self">Write</a>';
            }

        $main .=' <a href="/account/SignedIn.php" target="_self">Account</a>
                <a href="/home" target="_self">Home</a>
            </div>
        </div>
        ';
    return $main;
    }
    function giveEditInfo() {
        $main = '
                    <div class="col-l">
                    <h2>'.$this->domainLogo.'</h2>
                    <div id="topLInfo">
        ';

            if ($this->writingNew) {
                $main .= " <p>You are creating a new $this->pagename in the $this->wikiName wiki.<br> <a href='/fandom/21/Edit_and_Write' target='_blank'>more info</a></p>";
                $main .= '               <div class="bottButtCon" style="display: table">
                    <div class="wikiButton" onclick="newWiki=true;switchSupport(0)">New Wiki</div>
                    </div>';
            }
            else {
                $main .= "                <p>You are editing a $this->wikiName $this->groupName $this->pagename.<br> <a href='/docs/20/Fandom' target='_blank'>more info</a></p>";

            }

            $main .= ' <label class="switch">
                <input type="checkbox" onchange="differComplic(this);" id="neatChecker"';
            if (isset($_COOKIE["preferFullEdit"]) AND $_COOKIE["preferFullEdit"] == 1) { $main .= "checked";}
            $main .= '>
                <span class="slider"></span>
            </label>';
            if ($this->domain == "mystral"){
                $main .= ' <div class="smoothSwitchCont" style="padding:11px 0 5px;text-align:center;"><label class="switch onoff">
                    <input type="checkbox" id="autoLinkDo" onchange="differComplic(this, \'doLinkage\');" ';
                    if (!isset($_COOKIE["doLinkage"]) OR $_COOKIE["doLinkage"] == 1) { $main .= "checked"; }
                    $main .= '>
                    <span class="slider"></span>
                </label> auto-link</div>';
            }
            $main .= '</div>
        </div>';
        return $main;
    }
    function giveLAuthors($comp = false) {
        $authorsArr = explode(", ", $this->article->authors);
        if (count($authorsArr)>0 AND $this->domain != "mystral"){
            $main = ' <div class="col-l">
                    <h2>Authors</h2>
                    <p id="authCont">';
            if ($comp){ $counter = 0; foreach($authorsArr as $author) {if ($counter != 0){$main .= ", ";} $counter++;$main .= "<span style='position:relative'>$author</span>"; } $main .="</p>"; }
            else {$main.= $this->article->authors;}
            $main .='    </div> ';
            return $main;
        }
        return "";
    }
    function giveCategs() {
        return <<<MAIN
            <div class="col-l" id="addCateColl">
                <h2>Create Category</h2>
                <p>Add a new category to the $this->groupName.</p>
                <div class="bottButtCon" style="display: table">
                    <div class="wikiButton" onclick="startCategging();">Add</div>
                </div>
                <p style="text-align:center;"><br><span class="typeTab tiny"  onclick="startCategging();">ctrl+shift+j</span></p>
            </div>
MAIN;
    }
    function giveOutstans() {
        $main = '
                    <div class="col-l">
                <h2>New '.ucwords($this->pagename).'</h2>
                <p>Create an outstanding '.$this->pagename.'.<br><a href="/docs/30/Articles" target="_blank">more info</a></p>
                <input type="text" id="articleName" placeholder="'.ucwords($this->pagename).' Name" style="margin:5px 0;width:100%;box-sizing:border-box;padding:5px"/>
                <div class="bottButtCon" style="display: table">
                    <div class="wikiButton" onclick="submitOutstander();">Create</div>
                </div>';

        if ($this->manySlot){
             $main .= '<p id="outstanderAlert" style="text-align:center;color:green;display:none;">'.ucwords($this->pagename).' Created!</p>';
        }
        else {
             $main .= '<p id="outstanderAlert" style="text-align:center;color:#A93226;">Warning: Only 1 Slot Left</p>';
        }

        $main .=" </div>";
        return $main;
    }
    function giveLHomer($text = "") {
        $main = '<div class="col-l">
                <h2>'.$this->wikiName.' Wiki</h2>';
        if ($text != ""){
        $main .= "<p>".$text." ".$this->wikiName." ".$this->groupName.".</p>";
        }
        $main .= '<div class="bottButtCon">
                    <a href="'.$this->artRootLink.$this->parentWiki.'/home"><button id="submitButton" class="wikiButton" >Home</button></a>
                </div>
        </div>';
        return $main;
    }
    function giveWSetHomer() {
        if ($this->domain == "mystral"){
            $docLink = "/docs/40/Mystral";
        }
        else {
            $docLink = "/docs/20/Fandom";
        }
        $main ='
            <div class="col-l">
                <h2>'.$this->wikiName." ".$this->groupName.'</h2>
                <p>'.$this->domainName.' <a href="'.$docLink.'" target="_blank">documentation</a></p>
                <div class="bottButtCon">
                    <a href="'.$this->artRootLink.$this->parentWiki.'/home"><button id="submitButton" class="wikiButton" >Home</button></a>
                </div>
            </div>';
        return $main;
    }
    function homeL() {
        if ($this->article->root == 0){
            return '<div class="col-l">
            <h2>'.$this->domainName.'</h2>
            <p>This is your '.$this->wikiName.' notebook\'s homepage.</p>
            <div class="bottButtCon">
                <a class="wikiButton" href="/mystral/hub">Home</button></a>
            </div>
            </div>
            ';
        }
    }
    function giveLWikinfo() {
        if ($this->article->root == 0){
            return '<div class="col-l">
            <h2>Many Isles Fandom</h2>
            <p>The '.$this->wikiName.' wiki was created in the Many Isles Fandom.</p>
            <div class="bottButtCon">
                <a class="wikiButton" href="'.$this->writeLink.'&new=1">Create Own</button></a>
            </div>
            </div>
            ';
        }
    }
    function giveLParwikinfo() {
        $main ='<div class="col-l">
            <h2>'.$this->wikiName.' '.ucwords($this->groupName).$this->wsettCogLink;
        $main .= '</h2>
            <p>This '.$this->pagename.' is part of the '.$this->wikiName.' '.$this->groupName.'.</p>
            <div class="bottButtCon">'.$this->ediButton.$this->wriButton."</div>";
            if ($this->domain == "mystral" && $this->wsettLink != ""){$main .= "<div class='bottButtCon'><a href='".$this->wsettLink."' class='wikiButton'>Notebook Setup</a></div>";}
            if ($this->power == 0) {
                $main.= "<p class='warning'>You are banned from this '.$this->groupName.' and cannot edit.</p>";
            }
        $main .= '</div>';

        return $main;
    }
    function giveLChildren() {
        $rootChildren = "";
        $rootChildLink = "<a href='".$this->artRootLink."COOLTEXT/COOLWORDS2'>COOLWORDS</a>";

        $query = "SELECT a.*
        FROM $this->database a
        LEFT OUTER JOIN $this->database b
            ON a.id = b.id AND a.v < b.v
        WHERE b.id IS NULL AND a.root = $this->page AND a.status = 'active' ORDER BY reg_date ASC LIMIT 0, 999";
        if ($firstrow = $this->dbconn->query($query)) {
            while ($row = $firstrow->fetch_assoc()) {
                $currentLink = str_replace("COOLTEXT", $row["id"], $rootChildLink);
                $currentLink = str_replace("COOLWORDS2", parse2Url($row["shortName"]), $currentLink);
                $currentLink = str_replace("COOLWORDS", $row["shortName"], $currentLink);
                if ($rootChildren == "") {
                    $rootChildren = $rootChildren.$currentLink ;
                }
                else {
                    $rootChildren = $rootChildren.", ".$currentLink ;
                }
            }
        }
        if ($rootChildren == "") {$rootChildren = "None as of yet. <a href='".$this->writeLink."'>write one</a>";}

        return '<div class="col-l">
            <h2>Child '.ucwords($this->pagename).'s</h2>
            <p>'.$rootChildren.'</p>
        </div>';
    }
    function giveLOutstanders() {
        $outstandingSidetab = "";
        if ($this->article->root == 0){
            $outstandingSidetab = '<div class="col-l"><h2>Outstanding '.ucwords($this->pagename).'s</h2><p>';
                if ($this->domain == "fandom") { $outstandingSidetab .= 'Help out by writing outstanding '.$this->pagename.'s and finishing incomplete ones!'; }
                else {$outstandingSidetab .= 'These '.$this->pagename.'s are outstanding or incomplete.';}
            $outstandingSidetab .= '</p><ul>OUTSTANDERS</ul> </div>';
            $query = "SELECT a.*
            FROM $this->database a
            LEFT OUTER JOIN $this->database b
                ON a.id = b.id AND a.v < b.v
            WHERE b.id IS NULL AND (a.status = 'outstanding' OR a.incomplete = 1) ORDER BY reg_date ASC LIMIT 0, 999";
            if ($firstrow = $this->dbconn->query($query)) {
                while ($row = $firstrow->fetch_assoc()) {
                    if (getWiki($row["id"], $this->database, $this->dbconn)!=$this->parentWiki) {continue;}
                    $pageName = $row["shortName"];
                    $pageStatus = $row["status"];
                    $outstandingSidetab = str_replace("OUTSTANDERS", "<a href='".artUrl($this->artRootLink, $row["id"], $pageName)."' style='display:block;'><li>$pageName</li></a>OUTSTANDERS", $outstandingSidetab);
                }
            }
            $outstandingSidetab = str_replace("OUTSTANDERS", "", $outstandingSidetab);
        }
        if (str_contains($outstandingSidetab, "<ul></ul>")) {$outstandingSidetab = "";}
        return $outstandingSidetab;
    }
    function giveLShare() {
        $main = '<div class="col-l">
            <h2>Share</h2>
            <div class="sharerCont">
                <a href="https://www.facebook.com/sharer/sharer.php?u=https://manyisles.ch'.$this->artLink.'" target="_blank" class="fa fa-facebook"></a>
                <a href="http://www.reddit.com/submit?title=Read up on '.$this->article->shortName.' lore on the Many Isles!&url=https://manyisles.ch'.$this->artLink.'" target="_blank" class="fa fa-reddit"></a>
                <a href="https://twitter.com/intent/tweet?text=Read up on '.$this->article->shortName.' lore on the Many Isles!%0A&url=https://manyisles.ch'. $this->artLink.'&hashtags=manyisles,lore" target="_blank" class="fa fa-twitter"></a>';
                if ($this->article->sidetabImg != "") {$main .= '<a href="http://pinterest.com/pin/create/button/?url=https://manyisles.ch'.$this->artLink.'&media='.$this->article->sidetabImg.'&description=Read up on '.$this->article->shortName.' lore on the Many Isles!" target="_blank" class="fa fa-pinterest"></a> '; }
               $main .= ' <a class="fa fa-link fancyjump" onclick="navigator.clipboard.writeText(\'https://manyisles.ch/'.$this->artLink.'\');createPopup(\'d:poet;txt:Link copied!\');"></a>
            </div>
        </div>';
        return $main;
    }
    function giveDocSide() {
        require_once($_SERVER['DOCUMENT_ROOT']."/docs/sideLoader.php");
        $sideLoader = new sideLoader($this->dbconn, $this->database);

        $sideArray = $sideLoader->load($this->article->root);
        $main = "";
        function addSide($parentArray, $level, $gen) {
            if ($parentArray["children"]==""){
                $main = "<p class='navLink a$level purelink' id='sidRow".$parentArray["id"]."'><a href='".$gen->artRootLink.$parentArray["id"]."/".parse2Url($parentArray["shortName"])."'>".$parentArray["shortName"]."</a></p>";
            }
            else {
            $toecholine = "<p class='navLink a$level' wiki-add-hidCont='0' id='sidRow".$parentArray["id"]."'><a href='".$gen->artRootLink.$parentArray["id"]."/".parse2Url($parentArray["shortName"])."'>".$parentArray["shortName"]."</a></p>";
                $toecholine = str_replace("wiki-add-hidCont='0'", "onclick='showBg(\"side".$parentArray["id"]."\");'", $toecholine);
                $main = str_replace("</p>", " <i class='fas fa-angle-down arrow' id='fside".$parentArray["id"]."'></i></p>", $toecholine);
                $main .= "<div class='hidhidCont' id='side".$parentArray["id"]."'><div class='hiddenCont'>";
                foreach ($parentArray["children"] as $child){
                    $main .= addSide($child, $level + 1, $gen);
                }
                $main .= "</div></div>";
            }
            return $main;
        }
        foreach ($sideArray as $parentArray){
            $main .= addSide($parentArray, 0, $this);
        }
        return $main;
    }
    function giveNotebooks() {
        $itemStencil = <<<NABSDAI
        <div class="artContainer">
        <div class="incontainer">
        <a href="MEGALINK">
            <div class="imagCont">
                <div class="artSquare">
                    <img src="MEGATHUMBNAIL" alt="Thumbnail" class="linkim">
                </div>
            </div>
            <div class='titling'>MEGANAME <hr class="solid"> <span class="date">MEGADATE</span><br></div>
        </a>
            <a href="MEGALINK"><button class="wikiButton homescreen"><i class="fas fa-arrow-right"></i><span> View</span></button></a>
        </div>
        </div>
NABSDAI;
        $newNB = "<a class='wikiButton' href='newNb.php' target='_self'><i class='fas fa-plus'></i> Create Notebook</a>";
        $main = "";
        $query = "SELECT a.*
        FROM $this->database a
        LEFT OUTER JOIN $this->database b
            ON a.id = b.id AND a.v < b.v
        WHERE b.id IS NULL AND a.root = 0 ORDER BY reg_date DESC LIMIT 0, 999";
        if ($result = $this->dbconn->query($query)){
            while ($row = $result->fetch_assoc()) {
                $image = banner($row["banner"]);
                if ($row["sidetabImg"]!= ""){$image = $row["sidetabImg"];}
                else if ($row["banner"] == "default"){$image = "/IndexImgs/squMyst.png";}
                $date_array = date_parse($row["reg_date"]);
                $nicedate = $date_array["day"].".".$date_array["month"].".".$date_array["year"];

                $itemTab = $itemStencil;
                $itemTab = str_replace("MEGALINK", $this->artRootLink.$row["id"]."/".parse2URL($row["shortName"]), $itemTab);
                $itemTab = str_replace("MEGATHUMBNAIL", $image, $itemTab);
                $itemTab = str_replace("MEGANAME", $row["name"], $itemTab);
                $itemTab = str_replace("MEGADATE", $nicedate, $itemTab);
                $main .= $itemTab;
            }
            if (mysqli_num_rows($result) >= $this->mystData["notebooks"]) {
                $main .= "<div class='starterCont'><button class='wikiButton' onclick='switchDis(\"sub\");'><i class='fas fa-plus'></i> More Notebooks</button></div>";
            }
            else {
                $main .= "<div class='starterCont'>$newNB</div>";
            }
        }
        if ($main == ""){$main = "<div class='starterCont'><p>Let's get started!</p>$newNB</div>";}
        return $main;
    }

    function giveREdit($modifier = 2) {
        $main =' <div class="col-r">
            <img src="'.banner($this->article->banner, $this).'" alt="oops" class="topBanner" />
            <p class="topinfo"><a href="'.$this->homelink.'">'.$this->domainName.'</a> - <a href="'.$this->artRootLink.$this->parentWiki."/home".'">'.$this->wikiName.'</a> - <a href="#">';
        if ($this->writingNew) {$main .= "Write"; } else {$main .= "Edit"; }
            $main .='</a> </p>
            <h1 id="coolInfoH1">';
        if ($this->writingNew) {$main .= "Write a New ".ucwords($this->pagename); } else { $main .= "Edit ".ucwords($this->pagename); }
            $main .= '<span class="'.$this->typeTab.'">'.$this->wikiName." ".$this->groupName;
        if ($this->article->root == 0) {$main .= " homepage";}
            $main .= '</span></h1>
            <form action="/fandom/ediPage.php" method="POST" class="pageForm">
                <input type="text" name="name" placeholder="Page Name" value="'.$this->article->name.'" pattern="'.jsReg("wikiName").'" required></input>
                <input class="complete" type="text" name="shortName" placeholder="Short Name" value="'.$this->article->shortName.'" pattern="'.jsReg("wikiName").'"></input>

                <div ';
        if ($this->article->root == 0){$main .= "style='display:none;'";}
        $main .= 'id ="rootChanger">
                    <p id="currentRoot" class="topinfo" style="padding-top:5px;"><a href="/fandom/home">Fandom</a></p>
                    <input type="text" placeholder="New Root"  oninput="offerSuggestions(this, \'findSuggestions\', 0, \'switchSupport\');" autocomplete="off" onfocus="offerSuggestions(this, \'findSuggestions\', 0, \'switchSupport\');this.value=\'\';"></input>
                    <div class="suggestions" style=""></div>
                    <input type="text" id="root" name="root" style="display:none;opacity:0;visibility:hidden;" value="'.$this->article->page.'"/>
                </div>';
        if ($modifier > 0){
            $main .='
                <div id="cateChanger">
                    <p id="currentCategs" class="topinfo" style="padding-top:5px;"></p>
                    <input type="text" id="viewRoot3" placeholder="Add Categories"  oninput="offerSuggestions(this, \'findCategSugg\', 0, \'addCategory\');" autocomplete="off" onfocus="offerSuggestions(this, \'findCategSugg\', 0, \'addCategory\');this.value=\'\';"></input>
                    <div class="suggestions" style=""></div>
                    <input type="text" id="categs" name="categories" style="display:none;opacity:0;visibility:hidden;" value="'.$this->article->page.'"/>
                </div>';
        }
            $main .= '
                <input type="text" name="id" value="'.$this->article->page.'"style="display:none;" required></input>
                <div class="selectCont">
                    <label for="cate">Choose a genre:</label>
                    <select id="cate" name="cate" >';
                    foreach ($this->cateoptions as $option){
                        $main .= '<option value="'.$option["value"].'">'.$option["name"].'</option>';
                    }
                    $main .= '
                    </select>
                </div>';
            if ($modifier > 0){
                $main .= '
                    <div class="selectCont">
                        <label for="banner">Choose a banner:</label>
                        <select id="banner" name="banner" onchange="newBanner()">
                            <option value="current">current</option>';
                                foreach ($this->article->banners as $banner){
                                    $main .= "<option value='".banner($banner["src"])."'>".$banner["name"]."</option>";
                                }
                    $main .= '</select>
                    </div>
                    <div class="selectCont complete">
                        <label for="NSFW">Set NSFW level:</label>
                        <select name="NSFW">
                            <option value="0"'; if ($this->article->NSFW == 0) { $main .=  "selected"; } $main .= '>SFW</option>
                            <option value="1"'; if ($this->article->NSFW == 1) { $main .=  "selected"; } $main .= '>Some graphic content</option>
                            <option value="2"'; if ($this->article->NSFW == 2) { $main .=  "selected"; } $main .= '>NSFW</option>
                        </select>
                    </div>

                    <img src="/Imgs/Bar2.png" class="separator"></img>

                    <h3>Sidetab<span class="roundInfo green">Optional</span><span class="roundInfo">Takes Markdown</span></h3>
                    <p>This is optional. If you leave all fields blank, the page will not have a sidetab.</p>
                    <textarea name="sidetabTitle" rows = "3" placeholder="Titling" onfocus="textareaToFill = this;" oninput="autoLinkage()">'.$this->article->sidetabTitle.'</textarea>
                    <input type="text" name="sidetabImg" placeholder="Image (direct link)"  value="'.$this->article->sidetabImg.'"></input>
                    <textarea name="sidetabText" rows = "5" placeholder="Sidetab  body text" onfocus="textareaToFill = this;" oninput="autoLinkage()">'.$this->article->sidetabText.'</textarea>
                    <div class="complete"><h4>Timeframe</h4>
                    <input name="timeStart" type="text" value ="'.$this->article->timeStart.'" placeholder="Starting Date" />
                    <input name="timeEnd" type="text" value ="'.$this->article->timeEnd.'" placeholder="Ending Date"  /></div>
                    ';
            }
            $main .='  <img src="/Imgs/Bar2.png" class="separator"></img>

                <h3>Body<span class="roundInfo">Takes Markdown</span></h3>
                <p>Write your page\'s body below using <a href="/docs/24/Markdown" target="_blank">Many Isles Markdown</a>. <br>
                    <span class="typeTab tiny" onclick="insLink();">ctrl+shift+k</span> insert link<br>
                    <span class="typeTab tiny" onclick="insThumb();">ctrl+shift+l</span> insert '.$this->pagename.' thumbnail<br>
                    <span class="typeTab tiny" onclick="insImg();">ctrl+shift+i</span> insert image<br>
                    <span class="complete" onclick="insFootnote();"><span class="typeTab tiny">ctrl+shift+o</span> insert footnote<br></span>
                </p>
                <textarea name="body" id="bodyFieldarea" rows = "40" placeholder="body in Many Isles markdown " onfocus="textareaToFill = this;" oninput="autoLinkage()" required>'.$this->article->body.'</textarea>
                <img src="/Imgs/Bar2.png" class="separator"></img> ';
            if ($modifier > 0){
                $main .= '                <div class="complete">
                    <h4>Sources (Footnotes)</h4>
                    <p>Put footnotes in your body text with the "[footnote:X]" syntax. Add sources here as references.</p>
                    <table id="gimmeBabes">
                        <tbody id="gimmeBabesTbody">
                        </tbody>
                    </table>
                    <input type="text" value="" id = "sources" name ="sources" style="display:none;" />

                    <div class="addSome" onclick="addSome(1);">
                        <i class="fas fa-plus"></i>
                    </div>
                    <div class="addSome" onclick="addSome(0);">
                        <i class="fas fa-minus"></i>
                    </div>
                    <img src="/Imgs/Bar2.png" class="separator"></img>
                    </div> ';
            }
                if ($this->power > 1) {
                    $main .= '<div class="complete">
                        <h2>Additional Details<span class="roundInfo green">Optional</span></h2>';
                    $main .= "<h4>Search Details</h4>";
                    $main .= "<input type='text' name='queryTags' placeholder='Tree,Brate'  value='".$this->article->queryTags."' pattern=\"".jsReg("basicList")."\" />";
                    $main .= "<input type='number' name='importance' placeholder='Importance'  value='".$this->article->importance."'/>
                        <img src='/Imgs/Bar2.png' class='separator'></img></div>";
                }

                $main .= '<div class="bottButtCon">
                    <button id="submitButton" class="wikiButton" type="submit" onclick="setFormSubmitting()">Submit</button>
                    <a href="'.$this->artRootLink;
                if ($this->writingNew) {$main .= $this->parentWiki."/home"; } else { $main .= $this->article->page."/".parse2Url($this->article->shortName); }
                    $main .= '" class="wikiButton" onclick="setFormSubmitting()">Cancel</a>
                </div>

                <input type="text" id="writeInfo" name="writeInfo" style="display:none;opacity:0;visibility:hidden;" value="';
                 if ($this->writingNew){$main .= "1"; } else {$main .= "0";}
                $main .= '"/>
                <input type="text" name="branch" style="display:none;opacity:0;visibility:hidden;" value="'.$this->domain.'"/>
                    </form>
                </div>
            </div>';
            $main .= '
                <div id="mod1" class="modCol">
                    <div class="modContent smol">
                        <h1>Insert Link</h1>
                        <div style="padding:0 10% 5%;">
                            <input type="checkbox" checked  id="linkFinderLocal">
                            <label for="artFinderLocal"> Only in this '.$this->groupName.'</label><br>

                            <select id="linkType">
                                <option value="false">External</option>';
                                if ($this->domain == "mystral"){
                                    $main .= '<option value="3" selected>Mystral</option>';
                                }
                                $main .= '<option value="0" '; if ($this->domainnum == 0){$main .= "selected";} $main .= '>Fandom</option>
                                <option value="1" '; if ($this->domainnum == 1){$main .= "selected";} $main .= '>Docs</option>
                                <option value="2" '; if ($this->domainnum == 2){$main .= "selected";} $main .= '>5eS</option>
                            </select>
                            <input type="text" placeholder="Shown Text" id="linkNameEr"></input>
                            <input type="text" placeholder="url" id="viewRoot2"  oninput="suggestLinks();" onfocus="suggestLinks();" autocomplete="off"></input>
                            <div class="suggestions" style="width:50%;"></div>
                            <div class="bottButtCon">
                                <button class="wikiButton" onclick="createLink(document.getElementById(\'viewRoot2\').value, \'href\', 0);">Insert</button>
                            </div>';
                        if ($this->domain == "mystral"){
                            $main .= ' <div class="smoothSwitchCont"><label class="switch onoff">
                                <input type="checkbox" id="autoLinkChecker" onchange="differComplic(this, \'preferLinkage\');" ';
            if (!isset($_COOKIE["preferLinkage"]) OR $_COOKIE["preferLinkage"] == 1) { $main .= "checked"; }
            $main .= '>
                                <span class="slider"></span>
                            </label> automatically link this word. more info</div>';
                        }
                      $main .= '   <p><span class="typeTab tiny" onclick="removePops()">esc</span> close</p>
                        </div>
                    </div>
                </div>
                <div id="mod2" class="modCol">
                    <div class="modContent smol">
                        <h1>Create Category</h1>
                        <p>for the '.$this->wikiName.' wiki</p>
                        <div style="padding:0 10% 5%;">
                            <input type="text" placeholder="Category Name" id="categInput" pattern="[A-Za-z0-9\',():\- ]{2,}"></input>
                            <p style="color:green;display:none;text-align:center;" id="madder">Done!</p>
                            <p><span class="typeTab tiny" onclick="removePops()">esc</span> close</p>
                            <div class="bottButtCon" style="display: table">
                                <button class="wikiButton" onclick="createCategory(document.getElementById(\'categInput\').value);">Create</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="mod3" class="modCol">
                    <div class="modContent smol">
                        <h1>Insert '.ucwords($this->pagename).' Thumbnail</h1>
                        <div style="padding:0 10% 5%;">
                            <input type="checkbox" checked id="artFinderLocal">
                            <label for="artFinderLocal"> Only in this wiki</label><br>
                            <input type="text" placeholder="Find '.ucwords($this->pagename).'" id="viewRoot4"  oninput="offerSuggestions(this, \'findSuggestions\', 0, \'wikthumb\');" onfocus="offerSuggestions(this, \'findSuggestions\', 0, \'wikthumb\');" autocomplete="off"></input>
                            <div class="suggestions" style="width:50%;z-index:5;"></div>
                            <p><span class="typeTab tiny" onclick="removePops()">esc</span> close</p>
                        </div>
                    </div>
                </div>
                <div id="mod5" class="modCol">
                    <div class="modContent">
                        <img src="/Imgs/PopPoet.png" alt="Hello There!" style="width: 100%; margin: 0; padding: 0; display: inline-block " />
                        <h1>Warning: One Slot Left </h1>
                            <p>You have one slot left and can only submit one new '.$this->pagename.'. If you create another outstanding '.$this->pagename.', you won\'t be able to submit your work on this page.</p>
                    </div>
                </div>
                <div id="mod6" class="modCol">
                    <div class="modContent smol">
                        <h1>Insert Image</h1>
                        <div style="padding:0 10% 5%;">
                            <select id="insImgClass">
                                <option value="sideimg">Side Image</option>
                                <option value="sideimg medium">Larger Side Image</option>
                                <option value="sideimg landscape">Landscape Side Image</option>
                                <option value="sideimg gallery">Gallery Image</option>
                            </select>
                            <input type="text" placeholder="Direct link to image" id="insImgSrc" />
                            <input type="text" placeholder="Caption (optional)" id="insImgCap"  />
                            <input type="text" placeholder="Style (css, optional)" id="insImgStyle"  />
                            <button class="wikiButton" style="margin: 30px auto 20px" onclick="insertImage();">Insert</button>
                            <p><span class="typeTab tiny" onclick="removePops()">esc</span> close</p>
                        </div>
                    </div>
                </div>';
        return $main;
    }
    function giveRArticle($parts = []){
        $main = '        <div class="col-r" style="margin-bottom:50px;">
            <input type="text" class="wikisearchbar" placeholder="Search '.$this->wikiName.' '.$this->groupName.'..." id="viewRoot1"  oninput="offerSuggestions(this, \'findSuggestions\', 1);" onfocus="offerSuggestions(this, \'findSuggestions\', 1);" autocomplete="off"></input>
            <div class="suggestions" style="transform: translate(0, 35px);"></div>

            <img src="'.banner($this->article->banner).'" alt="banner" class="topBanner" />';

        function root($gen) {
            $page = $gen->page;$dontEcho = true;$branch = $gen->domain;
            include($_SERVER['DOCUMENT_ROOT']."/fandom/getRoot.php");
            return '<div class="topinfo">'.$fullLine.'</div>';
        }
        function categLine($gen) {
            $fullCategLine = "";
            $categArray = explode(",", $gen->article->categories);
            foreach ($categArray as $catelink){
                $query = "SELECT name FROM wikicategories WHERE id = ".$catelink;
                if ($gen->domain == "mystral"){$query .= " AND user = $gen->user";}
                if ($result = $gen->dbconn->query($query)){
                    while ($row = $result->fetch_assoc()) {
                        if ($fullCategLine == ""){
                            $fullCategLine = "<a href='/".$gen->domain."/search.php?w=".$gen->parentWiki."&c=".$catelink."'>".$row["name"]."</a>";
                        }
                        else {
                            $fullCategLine = $fullCategLine.", <a href='/".$gen->domain."/search.php?w=".$gen->parentWiki."&c=".$catelink."'>".$row["name"]."</a>";
                        }
                    }
                }
            }
            if ($fullCategLine != ""){
                return '<div class="topinfo">In: <i>'.$fullCategLine.'</i></div>';
            }
        }
        function titleBlock($gen) {
            $main = '<h1>'.$gen->article->name.'<a href="/'.$gen->domain.'/search.php?g='.$gen->article->cate."&w=".$gen->parentWiki.'"><span class="'.$gen->typeTab.'">'.$gen->article->cate.'</span></a>';
            if ($gen->article->status == "outstanding" OR $gen->article->status == "suspended"){
                $main .=  '<span class="'.$gen->typeTab.' not">'.$gen->article->status.'</span>';
            }
            else if ($gen->domain == "fandom"){
                if ($gen->article->canon == 0) {
                    $main .=   '<span class="'.$gen->typeTab.' not">Not Canon</span>';
                }
                else if ($gen->article->canon == 1){
                    $main .=   '<span class="'.$gen->typeTab.' yes"><i class="fas fa-check"></i> Canon</span>';
                }
            }
            //NSFW typeTabs
            if ($gen->article->NSFW == 1){
                $main .=   '<span class="'.$gen->typeTab.' orange altStep">NSFW<span class="hoverinfo">This '.$gen->pagename.' may contain offensive content.</span></span>';
            }
            else if ($gen->article->NSFW == 2) {
                $main .=   '<span class="'.$gen->typeTab.' not">NSFW</span>';
            }
            return $main.'</h1>';
        }
        function body($gen){
            $sidetab = "<div class='sidetab'>".$gen->parse->bodyParser($gen->article->sidetabTitle, 0, $gen->database);
            if ($gen->article->timeStart != "" OR $gen->article->timeEnd != "") {
                $sidetab .= "<p><i>".parseIWDate($gen->article->timeStart, $gen->article->timeEnd, $gen->dateArray, true);
                $sidetab .="</i></p>";
            }
            if ($gen->article->sidetabImg != "") {$sidetab = $sidetab.'<a href="'.$gen->article->sidetabImg.'" target="_blank"><div class="sImage" load-image="'.$gen->article->sidetabImg.'"></div></a>';}
            $sidetab = $sidetab.$gen->parse->bodyParser($gen->article->sidetabText, 0, $gen->database)."</div>";

            $main = '<div style="'; if ($gen->prude) { $main .= "visibility: hidden";} $main .= '" id="actualNeatCont" >';
                if ($gen->article->sidetabTitle != ""  OR $gen->article->sidetabText != ""){
                    $main .= $sidetab;
                }

                if ($gen->article->incomplete == 1){
                    $main .= '<p class="warning">This '.$gen->pagename.' is <b>incomplete</b>. You can help by finishing it! <a href="'.$gen->editLink.'">Edit</a></p>';
                }
                else if ($gen->article->status == "outstanding"){
                    $main .= '<p class="warning">This page is <b>outstanding</b>. Be the one to write the '.$gen->pagename.'! <a href="'.$gen->editLink.'">Edit</a></p>';
                }

            $main .= $gen->parse->bodyParser($gen->article->body, 1, $gen->database);
            $main .='<div id="footnotes" style="display:none;">
                    <h2>Sources</h2>
                    <div id = "gimmeSources"></div>
                </div>
                <div class="backCont">
                    <a href="'.artUrl($gen->artRootLink, $gen->article->root, $gen->parentName).'">'.$gen->parentName.'</a> <i class="fas fa-arrow-up"></i>
                </div>';

            if ($gen->prude){
                $main .= '
                <div style="height: 300px" id="NSFWheightPadd"></div>
                <div class="overlayCont" id="NSFWoverlayCont">
                    <div class="contInfo">
                        <h3><i class="fas fa-exclamation-circle"></i> This '.$gen->pagename.' is marked NSFW</h3>
                        <p>This Not Safe For Work '.$gen->pagename.' may feature offensive content. You must be over 18 to proceed.</p>
                        <div style="padding: 40px 0 20px" >
                            <span class="roundInfo button grey" onclick="viewNSFW(1);">Always Ignore</span>
                            <span class="roundInfo button red" onclick="viewNSFW(0);">Proceed</span>
                        </div>
                    </div>
                </div>
            ';
            }
            return $main."</div>";
        }

        foreach ($parts as $part){
            $main .= call_user_func($part, $this);
        }
        $main .= "</div>";
        return $main;
    }
    function giveRAdmin() {
        $main = '
            <div class="col-r">
                <h3>Page Admin</h3>
                <p class="topinfo">v'.number_format($this->article->version, 0, ".", "'").', last edited: '.$this->article->nicedate.'<br>Views: '.number_format($this->article->pop, 0, ".", "'").'</p>
                <p>Edit information about this page below. For more information, check out the <a href="/docs/30/Articles" target="_blank">documentation</a>.</p>
                <div class="bottButtCon" style="display: table">';
                    $main .= $this->ediButton;
                    $main .= $this->repButton;
                    $main .= $this->wriButton;
                    $main .= $this->compButton;
            $main .= '   </div>
                <p class="topinfo">
                    This page was written by the Many Isles community and is moderated by the '.$this->wikiName.' wiki community. The Pantheon holds no guarantee against incorrect or offensive content.
                </p>
            </div>';
        return $main;
    }
    function giveRWiki() {
        if ($this->power < 3){return;}
        if ($this->article->status == "suspended"){$dir = 1;}else{$dir = 0;}
        $main = '
        <div class="col-r">
            <h3>Wiki Admin</h3>
            <p>Higher-Level administration. Be careful with the revert button, because it might be irreversible.</p>
            <div class="bottButtCon" style="display: table">';
                if ($this->article->root != 0 OR $this->power > 3){ $main .= '<a href="/fandom/suspend.php?w='.$dir.'&id='.$this->page.'" class="wikiButton">Suspend</a>'; }
                $main .= $this->delButton;
                $main .= $this->revButtons;
                if ($this->article->canon == 0 ){$main .= '<a href="/fandom/canonize.php?dir=1&id='.$this->page.'&v='.$this->article->version.'" class="wikiButton">Canonize</a>';}else if($this->article->canon == 1){$main .= '<a href="/fandom/canonize.php?dir=0&id='.$this->page.'&v='.$this->article->version.'" class="wikiButton">Decanonize</a>';}
                $main .= $this->filButton;
            $main .= ' </div>';
        $main .= ' </div>';
        return $main;
    }
    function giveRMAdmin() {
        $main = '
            <div class="col-r">
                <h3>Page Admin</h3>
                <p class="topinfo">v'.number_format($this->article->version, 0, ".", "'").', last edited: '.$this->article->nicedate.'</p>
                <p>Edit information about this page below. For more information, check out the <a href="/docs/30/Articles" target="_blank">documentation</a>.</p>
                <div class="bottButtCon" style="display: table">';
                    $main .= $this->ediButton;
                    $main .= $this->wriButton;
                    $main .= $this->compButton;
            $main .= '   </div> '; if ($this->article->root != 0) { $main .= '<p>Be <b>very</b> careful with the Delete button. This is absolutely permanent.</p>'; }
            $main .= '    <div class="bottButtCon" style="display: table">';
                    $main .= $this->revButtons;
                    $main .= $this->filButton;
                    $main .= $this->delButton;
            $main .= '   </div>
            </div>';
        return $main;
    }
    function giveRArticPops() {
        if (!$this->signedIn){$basetext = "<h1>Log In</h1>
                                <p>Sorry, you need to log in to edit the fandom.</p>
                                <div class='bottButtCon' style='display: table'>
                                <a href='/account/Account?error=signIn' target='_blank' class='wikiButton'>Account</a>
                                <a href='#' onclick='location.reload();' class='wikiButton'><i class='fas fa-redo'></i> Refresh</a></div>"; }
        else if ($this->power == 0) {
            $basetext = "<h1>Banned</h1><p>Sorry, you've been <a href='/docs/26/User_Roles'>banned</a> by the $this->wikiName wiki moderators or by the Pantheon. You can't participate to this wiki until you're cleared.</p>";
        }
        else if ($this->ediProblem == "Slots") {
            $basetext = "<h1>Slots Full</h1><p>Sorry, your slots are full - you'll have to wait a moment for the Pantheon to clear them. <a href='/docs/21/Edit_and_Write>more info</a><br>
                            You can <a href='/docs/37/more_slots'>ask</a> the $this->wikiName moderators to curate you, granting you unlimited editing powers.</p>";
        }
        else if ($this->ediProblem == "Reverted") {
            $basetext = "<h1>Error - Cannot Edit</h1><p>Sorry, this $this->pagename has reverted versions and cannot be edited, because this would overwrite them. Please wait until the $this->wikiName moderators age this $this->pagename.</p>";
        }
        else if ($this->ediProblem == "Full Notes") {
            $basetext = "<h1>Notes Full - Cannot Edit</h1><p>You're all out of notes. <a href='/mystral/hub?view=sub'>Buy a better plan</a> to write more.</p>";
        }
        else if ($this->ediProblem == "No Space") {
            $basetext = "<h1>No Space - Cannot Edit</h1><p>You've got too many pages. Delete some unneeded versions with the Filicide button, or <a href='/mystral/hub?view=sub'>uy a better plan</a>.</p>";
        }
        else if ($this->ediProblem == "Full Notebooks") {
            $basetext = "<h1>Notebooks Full - Cannot Edit</h1><p>You're all out of notebooks. <a href='/mystral/hub?view=sub'>Buy a better plan</a> to create more.</p>";
        }
        else {
            $basetext = "<h1>Error - Cannot Edit</h1><p>Sorry, you are forbidden of editing for this reason: $this->ediProblem.<br>Contact the Pantheon if you feel this is wrong.</p>";
        }

        $main = '
        <div id="mod" class="modCol">
            <div class="modContent" >
                <img src="/Imgs/PopPoet.png" alt="Hello There!" style="width: 100%; margin: 0; padding: 0; display: inline-block " />
                '.$basetext.'
            </div>
        </div>';
        return $main;
    }
    function giveRDocArticle() {
        $page = $this->page;$dontEcho = true;$branch = $this->domain;
        include($_SERVER['DOCUMENT_ROOT']."/fandom/getRoot.php");
        $main = '
            <div class="colrContent">
                <div class="topinfo">'.$fullLine.'</div>
                <h1>'.$this->article->name.'<span class="roundInfo title">'.$this->article->cate.'</span>
                <div class="topinfo">'.$this->article->nicedate;
        if ($this->power > 2) {$main .= ', v'.$this->article->version; }
        $main .= '</div></h1>
                <div>
                    '.$this->parse->bodyParser($this->article->body, 1, $this->domain).'
                </div>
            </div>';
        return $main;
    }
    function giveSubs() {
        if ($this->domain == "mystral"){
            $sub1button = '<a href="/ds/subs/createSubSesh?o=0"><button class="wikiButton homescreen"><i class="fas fa-arrow-right"></i><span> Subscribe</span></button></a>';
            $sub2button = '<a href="/ds/subs/createSubSesh?o=1"><button class="wikiButton homescreen"><i class="fas fa-arrow-right"></i><span> Subscribe</span></button></a>';
            $subs = '                <div class="tierCont">
                    <div class="tierblock incontainer subscribed">
                        <h2>Free Plan</h2>
                        <div class="img"><img src="/Imgs/Mystral.png"/></div>
                        <h2>$0 <span class="priceInfo">/ year</span></h2>
                        <ul>
                            <li>222 notes</li>
                            <li>2 notebooks</li>
                            <li>5 images</li>
                            <li>Default styles</li>
                        </ul>
                    </div>
                    <div class="tierblock incontainer SUBSCRIBABLE1">
                        <h2>Full Mystral</h2>
                        <div class="img"><img src="/mystral/mystral1.png"/></div>
                        <h2>$5 <span class="priceInfo">/ year</span></h2>
                        <ul>
                            <li>999 notes</li>
                            <li>10 notebooks</li>
                            <li>50 images</li>
                            <li>Great Gamemaster style</li>
                        </ul>
                        <h4>Permanent Benefits</h4>
                        <ul>
                            <li>Loremaster tier 2 title</li>
                        </ul>
                        SUB1BUTTON
                    </div>
                    <div class="tierblock incontainer SUBSCRIBABLE2">
                        <h2>Mystral Gold</h2>
                        <div class="img"><img src="/mystral/mystral2.png"/></div>
                        <h2>$10 <span class="priceInfo">/ year</span></h2>
                        <ul>
                            <li>9999 notes</li>
                            <li>Unlimited notebooks</li>
                            <li>222 images</li>
                            <li>Imperium style</li>
                        </ul>
                        <h4>Permanent Benefits</h4>
                        <ul>
                            <li>Grand Poet tier 3 title</li>
                        </ul>
                        SUB2BUTTON
                    </div>
                </div>';
            if ($this->premPower == 0){$title = "<h1>Purchase Subscription</h1><p>With a subscription, you gain much greater features in Mystral, as well as some permanent benefits.</p>";}
            else {$title = "<h1>Manage Subscriptions</h1><p>Your subscription gives you access to awesome features.<br>Manage your plan from the <a href='/ds/subs/hub' target='_blank'>hub</a>.</p>";
                for ($i = $this->premPower; $i > 0; $i--){
                    $subs = str_replace("SUBSCRIBABLE".$i, "subscribed", $subs);
                    $subs = str_replace("SUB".$i."BUTTON", '<button class="wikiButton homescreen"><i class="fas fa-check"></i><span> Subscribed</span></button>', $subs);
                }
            }
            $subs = str_replace("SUB1BUTTON", $sub1button, $subs);
            $subs = str_replace("SUB2BUTTON", $sub2button, $subs);
            return $title.$subs;
        }
    }
    function giveFooter($type = "fandom") {
        $classer = "wikiFooter"; if ($type == "doc"){$classer="footer";}
        if ($this->domain == "mystral") {
              return '  <footer class="'.$classer.' black">
                <div class="footerCont">
                    <ul class="dicolumn">

                    </ul>
                    <p>
                        <div class="roundImgCont mystral footerMyst"><a href="/docs/40/Mystral" style="display:flex;"><img src="'.$this->baseImage.'" alt="mystral" /></a></div>
                        <span>Powered by </span> <a href="/docs/5/Wiki" target="_blank"><span class="logoWiki">Many Isles Wiki</span></a><br>
                        © Many Isles 2021
                    </p>
                </div>
            </footer>';
        }
        else if ($this->domain == "fandom") {
          return '  <footer class="'.$classer.'">
            <div class="footerCont">
                <ul class="dicolumn">
                    <li><a href="/docs/20/fandom" target="_blank">Fandom</a></li>
                    <li><a href="/docs/24/Markdown" target="_blank">Many Isles Markdown</a></li>
                    <li><a href="/docs/22/Fandom_wikis" target="_blank">Fandom Wikis</a></li>
                    <li><a href="/docs/21/Edit_and_Write" target="_blank">How to Participate</a></li>
                    <li><a href="/docs/30/Articles" target="_blank">Fandom Articles</a></li>
                    <li><a href="/docs/26/User_roles" target="_blank">User Roles</a></li>
                    <li><a href="/docs/6/Accounts" target="_blank">Account</a></li>
                    <li><a href="/docs/28/Hosting_Images" target="_blank">Image Sources</a></li>
                </ul>
                <p>
                    <span>Powered by </span> <a href="/docs/5/Wiki" target="_blank"><span class="logoWiki">Many Isles Wiki</span></a><br>
                    © Many Isles 2021
                </p>
            </div>
        </footer>';
        }
    }

    function giveEditScript() {
        $main = '
            <script>
            $("option[value=\''.$this->article->cate.'\']").attr("selected", true);
            $("option[value=\''.$this->article->banner.'\']").attr("selected", true);

            var articleCategArray = "'.$this->article->categories.'".split(",");
            if (articleCategArray==""){
                articleCategArray = [];
            }
            var autoLinks = '.json_encode($this->autoLinkArr).';
            var newWiki = ';
             if (isset($_GET["new"])) {$main.= "true";} else {$main .= "false";}

            $main .= '; var sourceJSON = \''. $this->article->sources.'\';
            if (sourceJSON != "" && sourceJSON != \'""\'){
                sourceJSON = JSON.parse(sourceJSON);
                addSome(0);
                for (let key in sourceJSON){
                    addSome(1);
                    document.getElementById("gimmeBabesTbody").lastChild.children[1].children[0].value = sourceJSON[key];
                }
            }
            else {sourceJSON = {}};

            if (document.getElementById("currentCategs") != null) {
                getFile = "/fandom/giveEditCategRow.php?q=" + encodeURIComponent("'.$this->article->categories.'") + "&dom='.$this->domainnum.'";
                var xhttp = new XMLHttpRequest();
                xhttp.onreadystatechange = function () {
                    if (this.readyState == 4 && this.status == 200) {
                        document.getElementById("currentCategs").innerHTML = xhttp.responseText;
                    }
                };
                xhttp.open("GET", getFile, true);
                xhttp.send();
            }


            function newBanner() {
                var newImg = document.getElementById("banner").value;
                if (newImg == "current"){newImg = "'.$this->article->banner.'"};
                $(".topBanner").attr("src", newImg);
              }


            function doOnIncludeLoad(file) {
                if (file =="cateoptions.html"){
                    let optionArray = document.getElementById("cate").getElementsByTagName("option");
                    for (option of optionArray){
                        if (option.value == "'.$this->article->cate.'"){option.setAttribute("selected", "selected");}
                    }
                }
            }
            var formSubmitting = false;
            var setFormSubmitting = function() { formSubmitting = true; };
            window.onload = function() {
                window.addEventListener("beforeunload", function (e) {
                    if (formSubmitting) {
                        return undefined;
                    }
                    var confirmationMessage = "Warning: unsaved changes!";
                    (e || window.event).returnValue = confirmationMessage; //Gecko + IE
                    return confirmationMessage; //Gecko + Webkit, Safari, Chrome etc.
                });
            };

            ';

            if (isset($_GET["new"])) {
                $main .= "switchSupport(0);";
            }
            else {
                $main .= "switchSupport(".$this->article->root.");";
            }
            $main .= '</script>';

        return $main;
    }
    function giveArtScript() {
        $main ='
        <script>
        var urlParams = new URLSearchParams(window.location.search);
        var show = urlParams.get("i");
        if (show =="created"){
            createPopup("d:poet;txt:'.ucwords($this->groupName).' created");
        }
        else if (show =="completed"){
            createPopup("d:poet;txt:'.ucwords($this->pagename).' modified");
        }

        function nospace() {
            document.getElementById("modal").style.display = "block";
            document.getElementById("mod").style.display = "block";
        }

        fancyLinkage();
        showAuthors();
        addJSON(sourceJSON);

        </script>';
        return $main;
    }
    function giveDocScript() {
        $main = '
        <script>
        var urlParams = new URLSearchParams(window.location.search);
        var show = urlParams.get("show");
        var i = urlParams.get("i");
        if (show =="reverted"){
            createPopup("txt:Doc Reverted by 1");
        }
        else if (show =="nreverted"){
            createPopup("txt:Doc couldn\'t be reverted");
        }
        else if (show =="ureverted"){
            createPopup("txt:Doc was evolved");
        }
        else if (show =="aged"){
            createPopup("txt:Patricide committed successfully");
        }
        else if (show =="deleted"){
            createPopup("txt:'.ucwords($this->pagename).' sadly deleted");
        }
        if (i =="cantedit"){
            createPopup("d:poet;txt:Error. Could not perform action.;b:1;bTxt:more info;bHref:/docs/38/Debugging");
        }



        var allLinks = document.getElementsByTagName("a");

        for (let coollink of allLinks) {
            let chref = coollink.href;
            if (!/\/'.$this->domain.'\//.test(chref) && !/\/home/.test(chref) && !coollink.hasAttribute("target")) {
                coollink.setAttribute("target", "_blank");
            }
        }';

        if ($this->domain != "mystral"){
            $main .= 'document.getElementById("sidRow'.$this->page.'").classList.add("selected");';
            function doOpen($main, $gen, $root){
                $query = "SELECT root FROM $gen->database WHERE id = $root ORDER BY v DESC LIMIT 1";
                if ($max = $gen->dbconn->query($query)){
                    while ($gay = $max->fetch_row()){
                        $root = $gay[0];
                        $main .= "showBg('side".$root."');";
                        if ($root != 0){
                            return doOpen($main, $gen, $root);
                        }
                        else {
                            return $main;
                        }
                    }
                }
            }
            $main .=  doOpen("", $this, $this->page);
            $main .= "showBg('side$this->page')";
        }
        $main .= '</script>';
        return $main;
    }
    function giveSScript($genre = null, $categories = null) {
    $main = '
    <script>
        function getResults(genre, categories) {
            genre = document.getElementById("genre").value;
            categories = document.getElementById("cate").value;
            mode = document.getElementById("mode").value;

            getFile = "/fandom/giveResults.php?w='.$this->parentWiki.'&g="+genre+"&c="+categories+"&m="+mode+"&dom='.$this->domainnum.'";
            console.log(getFile);
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("resultDIV").innerHTML = xhttp.responseText;
                    getIndexImgs();
                }
            };
            xhttp.open("GET", getFile, true);
            xhttp.send();
        }

        let genreNode = document.createElement("OPTION");
        genreNode.setAttribute("value", "");
        let cateNode = document.createElement("OPTION");
        cateNode.setAttribute("value", "");';
        if (!$genre) { $main .= 'genreNode.setAttribute("selected", "selected");';}
        if (!$categories) { $main .= 'cateNode.setAttribute("selected", "selected");';}

        $main .= 'genreNode.innerHTML = "any";
        document.getElementById("genre").appendChild(genreNode);
        cateNode.innerHTML = "any";
        document.getElementById("cate").appendChild(cateNode);

        function killSugg(x) {
            window.setTimeout(function () { document.getElementById("suggestions".concat(x)).style.display = "none"; }, 300);
        }

        function sortSelect(selElem) {
            var tmpAry = new Array();
            for (var i=0;i<selElem.options.length;i++) {
                tmpAry[i] = new Array();
                tmpAry[i][0] = selElem.options[i].text;
                tmpAry[i][1] = selElem.options[i].value;
            }
            tmpAry.sort();
            while (selElem.options.length > 0) {
                selElem.options[0] = null;
            }
            for (var i=0;i<tmpAry.length;i++) {
                var op = new Option(tmpAry[i][0], tmpAry[i][1]);
                selElem.options[i] = op;
            }
            return;
        }
        sortSelect(document.getElementById("cate"));

        let optionArray = document.getElementById("cate").getElementsByTagName("option");
        for (option of optionArray){
            if (option.value == "'.$categories.'"){option.setAttribute("selected", "selected");}
        }
        optionArray = document.getElementById("genre").getElementsByTagName("option");
        for (option of optionArray){
            if (option.value == "'.$genre.'"){option.setAttribute("selected", "selected");}
        }

        fancyLinkage();
        getResults();

        </script>';
        return $main;
    }
}

class article {
    public $conn;
    public $page;
    public $root;
    public $writingNew;
    public $name = "";
    public $shortName = "";
    public $cate = "Lore";
    public $banner = "fandom.png";
    public $authors = "";
    public $body = "";
    public $status = "active";
    public $incomplete = 0;
    public $canon = 0;
    public $categories = "";
    public $sidetabTitle = "";
    public $sidetabImg = "";
    public $sidetabText = "";
    public $sources = "";
    public $NSFW = 0;
    public $timeStart = "";
    public $timeEnd = "";
    public $queryTags = "";
    public $importance = 0;
    public $version = 0;
    public $pop = 0;
    public $parseClear = 0;
    public $regdate = null;
    public $nicedate = "";
    public $parentWiki = 1;
    public $revertees = false;
    public $banners = [];
    public $gen = null;

    function __construct($gen) {
        $this->conn = $gen->dbconn;
        $this->page = $gen->page;
        $this->root = $gen->parentWiki;
        $this->writingNew = $gen->writingNew;
        $this->database = $gen->database;
        $this->banner = $gen->defaultBanner;
        $this->gen = $gen;
        $this->getInfo(0);
        $this->banners = json_decode('[{"src":"fandom.png","name":"Fandom"},{"src":"lore.png","name":"Lore default"},{"src":"manyisles.png","name":"Many Isles"},{"src":"starry.png","name":"Star Sky"},{"src":"icehall.jpg","name":"Ice Hall"},{"src":"snowycliff.jpg","name":"Snowy Cliff"},{"src":"mounts.png","name":"Mountains"},{"src":"stones.jpg","name":"Stone Mountains"},{"src":"desertcanyon.jpg","name":"Desert Canyon"},{"src":"dunes.png","name":"Dunes"},{"src":"lava.jpg","name":"Lava Landscape"},{"src":"fire.jpg","name":"Flames"},{"src":"caves.png","name":"Cave"},{"src":"dark.png","name":"Dark Woods"},{"src":"plains.png","name":"Plains"},{"src":"flowersvillage.jpg","name":"Flowers Village"},{"src":"waterfallforest.jpg","name":"Forest Waterfall"},{"src":"trees.png","name":"Trees"},{"src":"woodssunset.jpg","name":"Forest Sunset"},{"src":"goldleaves.jpg","name":"Sun and Leaves"},{"src":"swamphuts.jpg","name":"Swamp Huts"},{"src":"sunsetships.jpg","name":"Sunset Ships"},{"src":"coast.jpg","name":"Coast"},{"src":"sea.jpg","name":"Fantastic Sea"},{"src":"sailship.jpg","name":"Ship"},{"src":"city1.jpg","name":"City #1"},{"src":"city2.jpg","name":"City #2"},{"src":"battlefield.png","name":"Battlefield"},{"src":"war.png","name":"War"}]',
            true);
    }

    function getInfo($level) {
        if (!$this->writingNew) {
            $query = "SELECT * FROM $this->database WHERE id = $this->page ORDER BY v DESC LIMIT $level, 1";
            if ($firstrow = $this->conn->query($query)) {
                if (mysqli_num_rows($firstrow) > 0){
                    while ($row = $firstrow->fetch_assoc()) {
                        $this->status = $row["status"];
                        if ($this->status == "reverted"){$this->revertees = true; return $this->getInfo($level + 1);}
                        $this->name = $row["name"];
                        $this->shortName = $row["shortName"];
                        $this->cate = $row["cate"];
                        $this->banner = $row["banner"];
                        $this->authors = $row["authors"];
                        $this->body = $row["body"];
                        $this->root = $row["root"];
                        $this->canon = $row["canon"];
                        $this->incomplete = $row["incomplete"];
                        $this->categories = $row["categories"];
                        $this->sidetabTitle = $row["sidetabTitle"];
                        $this->sidetabImg = $row["sidetabImg"];
                        $this->sidetabText = $row["sidetabText"];
                        $this->sources = $row["sources"];
                        $this->NSFW = $row["NSFW"];
                        $this->timeStart = $row["timeStart"];
                        $this->timeEnd = $row["timeEnd"];
                        $this->queryTags = $row["queryTags"];
                        $this->importance = $row["importance"];
                        $this->version = $row["v"];
                        $this->pop = $row["pop"];
                        $this->parentWiki = $row["parentWiki"];
                        $this->parseClear = $row["parseClear"];
                        $this->regdate = $row["reg_date"];
                    }
                    $this->body = txtUnparse($this->body, 2);
                    $this->sidetabTitle = txtUnparse($this->sidetabTitle, 2);
                    $this->sidetabText = txtUnparse($this->sidetabText, 2);

                    $date_array = date_parse($this->regdate);
                    $this->nicedate = $date_array["day"].".".$date_array["month"].".".$date_array["year"];
                }
            }
        }
    }
}

?>
