<?php
//requires $conn, $page, $writingNew, ($database), $parentWiki, parseTxt.php;

class article {
    public $name = "";
    public $shortName = "";
    public $root;
    public $cate = "Lore";
    public $banner = "fandom.png";
    public $authors = "";
    public $body = "";
    public $status = "active";
    public $categories = "";
    public $sidetabTitle = "";
    public $sidetabImg = "";
    public $sidetabText = "";
    public $sources = "";
    public $page = 0;
    public $NSFW = 0;
    public $timeStart = "";
    public $timeEnd = "";
    public $queryTags = "";
    public $importance = 0;
    public $version = 0;
    public $regdate = null;
    public $nicedate = "";
    public $revertees = false;
    public $banners = [];

    function __construct() {
        $this->getInfo(0);
        $this->banners = json_decode('[{"src":"fandom.png","name":"Fandom"},{"src":"lore.png","name":"Lore default"},{"src":"manyisles.png","name":"Many Isles"},{"src":"starry.png","name":"Star Sky"},{"src":"icehall.jpg","name":"Ice Hall"},{"src":"snowycliff.jpg","name":"Snowy Cliff"},{"src":"mounts.png","name":"Mountains"},{"src":"stones.jpg","name":"Stone Mountains"},{"src":"desertcanyon.jpg","name":"Desert Canyon"},{"src":"dunes.png","name":"Dunes"},{"src":"lava.jpg","name":"Lava Landscape"},{"src":"fire.jpg","name":"Flames"},{"src":"caves.png","name":"Cave"},{"src":"dark.png","name":"Dark Woods"},{"src":"plains.png","name":"Plains"},{"src":"flowersvillage.jpg","name":"Flowers Village"},{"src":"waterfallforest.jpg","name":"Forest Waterfall"},{"src":"trees.png","name":"Trees"},{"src":"woodssunset.jpg","name":"Forest Sunset"},{"src":"goldleaves.jpg","name":"Sun and Leaves"},{"src":"swamphuts.jpg","name":"Swamp Huts"},{"src":"sunsetships.jpg","name":"Sunset Ships"},{"src":"coast.jpg","name":"Coast"},{"src":"sea.jpg","name":"Fantastic Sea"},{"src":"sailship.jpg","name":"Ship"},{"src":"city1.jpg","name":"City #1"},{"src":"city2.jpg","name":"City #2"},{"src":"battlefield.png","name":"Battlefield"},{"src":"war.png","name":"War"}]',
            true);
    }

    function getInfo($level) {
        global $conn, $page, $writingNew, $database, $parentWiki;
        if (!isset($database)){$database = "pages";}
        $this->root = $parentWiki;
        if (!$writingNew) {
            $query = "SELECT * FROM $database WHERE id = $page ORDER BY v DESC LIMIT $level, 1";
            $firstrow = $conn->query($query);
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


?>