<?php

function equipDom($gen, $domain = "fandom"){
    require($_SERVER['DOCUMENT_ROOT']."/Server-Side/promote.php");
    $user = new adventurer;
    $gen->conn = $user->conn;
    $gen->user = $user->user;

    if (preg_match("/^[0-9]+$/", $domain)) {
        if ($domain == "3"){$domain = "mystral";}
        else if ($domain == "4"){$domain = "spells";}
        else if ($domain == "5"){$domain = "dic";}
        else if ($domain == "2"){$domain = "5eS";}
        else if ($domain == "1"){$domain = "docs";}
        else {$domain = "fandom";}
    }
    $gen->domainType = "fandom";
    $gen->luckying = false;
    $gen->acceptsTopBar = true;
    $gen->changeableGenre = false;
    $gen->canLocalAccStat = false;
    $gen->baseWSet = "";
    $gen->wsettingsdb = "wiki_settings";
    $gen->premPower = 0;
    $gen->domainSpecs = [];

    $gen->banners = json_decode('[{"src":"fandom.png","name":"Fandom"},{"src":"lore.png","name":"Lore default"},{"src":"starry.png","name":"Star Sky"},{"src":"clouds.jpg","name":"Clouds"},{"src":"icehall.jpg","name":"Ice Hall"},{"src":"river.jpg","name":"Snowy River"},{"src":"snowycliff.jpg","name":"Snowy Cliff"},{"src":"mounts.png","name":"Mountains"},{"src":"stones.jpg","name":"Stone Mountains"},{"src":"desertcanyon.jpg","name":"Desert Canyon"},{"src":"dunes.png","name":"Dunes"},{"src":"lava.jpg","name":"Lava Landscape"},{"src":"fire.jpg","name":"Flames"},{"src":"caves.png","name":"Cave"},{"src":"dark.png","name":"Dark Woods"},{"src":"plains.png","name":"Plains"},{"src":"flowersvillage.jpg","name":"Flowers Village"},{"src":"waterfallforest.jpg","name":"Forest Waterfall"},{"src":"trees.png","name":"Trees"},{"src":"woodssunset.jpg","name":"Forest Sunset"},{"src":"goldleaves.jpg","name":"Sun and Leaves"},{"src":"swamphuts.jpg","name":"Swamp Huts"},{"src":"sunsetships.jpg","name":"Sunset Ships"},{"src":"coast.jpg","name":"Coast"},{"src":"sea.jpg","name":"Fantastic Sea"},{"src":"sailship.jpg","name":"Ship"},{"src":"city1.jpg","name":"City #1"},{"src":"city2.jpg","name":"City #2"},{"src":"ochebana.png","name":"Ochebana Empire"},{"src":"battlefield.png","name":"Battlefield"},{"src":"war.png","name":"War"}]',true);
    $gen->cateoptions = json_decode('[{"name":"Lore","value":"Lore"},{"name":"Lore - Condition / Disease","value":"Lore - Universe"},{"name":"Lore - Conflict","value":"Lore - Conflict"},{"name":"Lore - Culture","value":"Lore - Culture"},{"name":"Lore - Document","value":"Lore - Document"},{"name":"Lore - Event / Legend","value":"Lore - Event"},{"name":"Lore - Geography","value":"Lore - Geography"},{"name":"Lore - Item","value":"Lore - Item"},{"name":"Lore - Language","value":"Lore - Language"},{"name":"Lore - Magic","value":"Lore - Magic"},{"name":"Lore - Organization / State","value":"Lore - Organization"},{"name":"Lore - Person / Deity","value":"Lore - Person"},{"name":"Lore - Politics","value":"Lore - Politics"},{"name":"Lore - Technology","value":"Lore - Technology"},{"name":"Lore - Race / Ethnicity","value":"Lore - Race"},{"name":"Lore - Relation / Treaty","value":"Lore - Relation"},{"name":"Lore - Religion","value":"Lore - Religion"},{"name":"Lore - Settlement / Location","value":"Lore - Settlement"},{"name":"Meta","value":"Meta"}]', true);
    $gen->defaultdateB = "";
    $gen->defaultdateA = "";
    $gen->defaultBanner = "fandom.png";
    $gen->defaultbackgroundImg = '/Imgs/OshBacc.png';
    $gen->defaultbackgroundColor = "var(--gen-color-bblue)";
    $gen->defaultauths = "";
    $gen->defaultmods = "";
    $gen->defaultbanned = "";
    $gen->styleInfo = ["Fandom" => [],
                "Docs" => ["/docs/docs.css", "https://fonts.googleapis.com/css2?family=Paytone+One&display=swap"],
                "5eS" => ["/docs/docs.css", "/5eS/5eS.css", "https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Lobster&family=Patua+One&family=Alfa+Slab+One&display=swap"],
                "Spells" => ["/docs/docs.css", "/5eS/5eS.css", "https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Lobster&family=Patua+One&family=Alfa+Slab+One&display=swap", "/spells/spells.css"],
                "Mystral" => ["/docs/docs.css", "/mystral/myst.css", "https://fonts.googleapis.com/css2?family=PT+Serif&display=swap"],
                "Great Gamemaster" => ["/docs/docs.css", "/mystral/GGM.css", "https://fonts.googleapis.com/css2?family=PT+Serif&display=swap"],
                "Imperium" => ["/docs/docs.css", "/mystral/Imperium.css", "https://fonts.googleapis.com/css2?family=PT+Serif&family=Lora&display=swap"]
    ];
    $gen->styleDefaults = [
        "Fandom" => ["banner" => "fandom.png", "backgroundColor" => "var(--gen-color-bblue)", "backgroundImg" => '/Imgs/OshBacc.png'],
        "Docs" => ["banner" => "manyisles.png", "backgroundColor" => "var(--doc-base-color)", "backgroundImg" => ''],
        "5eS" => ["banner" => "fesban.jpg", "backgroundColor" => "var(--doc-base-color)", "backgroundImg" => ''],
        "Spells" => ["banner" => "starry.png", "backgroundColor" => "var(--doc-base-color)", "backgroundImg" => ''],
        "Mystral" => ["banner" => "notes.png", "backgroundColor" => "var(--doc-accent-color)", "backgroundImg" => '/Imgs/metal.jpg'],
        "Great Gamemaster" => ["banner" => "plains.png", "backgroundColor" => "#B9BAA3", "backgroundImg" => ''],
        "Imperium" => ["banner" => "imperium.png", "backgroundColor" => "var(--ds-gold)", "backgroundImg" => '/Imgs/gold.jpg']
    ];
    $gen->styles = ["Fandom"];
    $gen->defaultStyle = "Fandom";
    $gen->baseImage = "/IndexImgs/GMTips.png";

    $gen->editable = [];

    if ($domain == "docs"){
        $gen->dbconn = $gen->conn;
        $gen->domain = "docs";
        $gen->domainnum = 1;
        $gen->database = "docs";
        $gen->pagename = "doc";
        $gen->groupName = "documentation";
        $gen->homelink = "/docs/1/home";
        $gen->minPower = 3;
        $gen->domainName = "Docs";
        $gen->domainLogo = 'Many Isles <a href="/docs/1/home"><span class="fakelink docsLogo">Docs</span></a>';
        $gen->defaultBanner = "manyisles.png";
        $gen->cateoptions =  json_decode('[{"name":"Guide","value":"Guide"},{"name":"Documentation","value":"Documentation"},{"name":"Manual","value":"Manual"}]', true);
        $gen->defaultGenre = "Guide";
        $gen->artRootLink = "/docs/";
        $gen->baseLink = "/docs/";

        $gen->styles = ["Docs"];
        $gen->defaultStyle = "Docs";
        $gen->domainType = "docs";
    }
    else if ($domain == "5eS"){
        $gen->dbconn = $gen->conn;
        $gen->domain = "5eS";
        $gen->domainnum = 2;
        $gen->database = "rules";
        $gen->pagename = "rule";
        $gen->groupName = "rulebook";
        $gen->homelink = "/5eS/1/home";
        $gen->minPower = 3;
        $gen->domainName = "5eS Rulebook";
        $gen->domainLogo = '<a href="/5eS/home"><span class="fakelink fesLogo">Rulebook</span></a>';
        $gen->defaultBanner = "fesban.jpg";
        $gen->cateoptions =   json_decode('[{"name":"Rule","value":"Rule"},{"name":"Race","value":"Race"},{"name":"Subrace","value":"Subrace"},{"name":"Class","value":"Class"},{"name":"Subclass","value":"Subclass"},{"name":"Prestige Class","value":"Prestige Class"},{"name":"Abilities","value":"Abilities"},{"name":"Spell","value":"Spell"},{"name":"Item","value":"Item"},{"name":"Feat","value":"Feat"},{"name":"Skill","value":"Skill"}]', true);
        $gen->defaultGenre = "Rule";
        $gen->artRootLink = "/5eS/";
        $gen->baseLink = "/5eS/";
        $gen->canLocalAccStat = true;

        $gen->styles = ["5eS"];
        $gen->defaultStyle = "5eS";
        $gen->baseImage == "/Imgs/5eSlogo.png";
        $gen->domainType = "docs";
    }
    else if ($domain == "spells"){
        $gen->dbconn = $user->addConn("spells");
        $gen->domain = "spells";
        $gen->domainnum = 4;
        $gen->database = "spells";
        $gen->pagename = "spell";
        $gen->groupName = "spell index";
        $gen->listName = "spell list";
        $gen->homelink = "/spells/index";
        $gen->minPower = 2;
        $gen->domainName = "Spells";
        $gen->domainLogo = '<a href="/5eS/home"><span class="fakelink fesLogo">Spells</span></a>';
        $gen->canLocalAccStat = true;

        $gen->defaultBanner = "starry.png";
        $gen->cateoptions =   json_decode('[{"name":"Spell","value":"Spell"},{"name":"Ritual","value":"Ritual"},{"name":"Ceremony","value":"Ceremony"}]', true);
        $gen->defaultGenre = "Spell";
        $gen->artRootLink = "/spells/";
        $gen->baseLink = "/spells/";

        $gen->styles = ["Spells"];
        $gen->defaultStyle = "Spells";
        $gen->baseImage = "/Imgs/ManySpells.png";
        $gen->domainType = "spells";

        $gen->domainSpecs = ["totalIndexes"=>0, "totalLists" => 0];
        $gen->mystData = [
            "indexes" => 2, "lists" => 6
        ];

        $gen->editable = ["Name" => "string", "Level"=>"int", "School"=>"string", "Element"=>"string", "Class"=>"string", "CastingTime"=>"string", "Range"=>"string", "Components"=>"string", "Duration"=>"string", "FullDesc"=>"text", "Direct_Image_Link"=>"url","Source"=>"string"];
        $gen->modules = ["Seas"=>["code"=>"4251","codeName"=>"Seas","fullName"=>"Adventurer's Guide to the Seas"],"DarkSecrets"=>["code"=>"6660","codeName"=>"DarkSecrets","fullName"=>"Handbook of Dark Secrets"]];
    }
    else if ($domain == "mystral") {
        $gen->dbconn = $user->addConn("notes");
        $gen->domain = "mystral";
        $gen->domainnum = 3;
        $gen->database = "notes_$gen->user";
        $gen->domainName = "Mystral";
        $gen->pagename = "note";
        $gen->groupName = "notebook";
        $gen->homelink = "/mystral/hub";
        $gen->minPower = 2;
        $gen->domainLogo = '<span class="mysLogo">Mystral</span>';
        $gen->defaultBanner = "notes.png";
        $gen->cateoptions = json_decode('[{"name":"Generic Note","value":"Note"},{"name":"File","value":"File"},{"name":"Condition / Disease","value":"Condition"},{"name":"Conflict","value":"Conflict"},{"name":"Culture","value":"Culture"},{"name":"Document","value":"Document"},{"name":"Event / Legend","value":"Event"},{"name":"Geography","value":"Geography"},{"name":"Item","value":"Item"},{"name":"Language","value":"Language"},{"name":"Magic","value":"Magic"},{"name":"Organization / State","value":"Organization"},{"name":"Person / Deity","value":"Person"},{"name":"Politics","value":"Politics"},{"name":"Technology","value":"Technology"},{"name":"Race / Ethnicity","value":"Race"},{"name":"Relation / Treaty","value":"Relation"},{"name":"Religion","value":"Religion"},{"name":"Settlement / Location","value":"Settlement"}]', true);
        $gen->defaultGenre = "Note";
        $gen->artRootLink = "/mystral/".$gen->user."/";
        $gen->baseLink = "/mystral/";
        $gen->luckying = true;
        $gen->baseWSet = $gen->user."_";
        $gen->changeableGenre = true;
        $gen->domainSpecs = ["totalImages"=>0, "imageSpace" => 0, "canImage" => true];

        $gen->banners = json_decode('[{"src":"notes.png","name":"Mystral"},{"src":"lore.png","name":"Lore default"},{"src":"starry.png","name":"Star Sky"},{"src":"icehall.jpg","name":"Ice Hall"},{"src":"snowycliff.jpg","name":"Snowy Cliff"},{"src":"mounts.png","name":"Mountains"},{"src":"stones.jpg","name":"Stone Mountains"},{"src":"desertcanyon.jpg","name":"Desert Canyon"},{"src":"dunes.png","name":"Dunes"},{"src":"lava.jpg","name":"Lava Landscape"},{"src":"fire.jpg","name":"Flames"},{"src":"caves.png","name":"Cave"},{"src":"dark.png","name":"Dark Woods"},{"src":"plains.png","name":"Plains"},{"src":"flowersvillage.jpg","name":"Flowers Village"},{"src":"waterfallforest.jpg","name":"Forest Waterfall"},{"src":"trees.png","name":"Trees"},{"src":"woodssunset.jpg","name":"Forest Sunset"},{"src":"goldleaves.jpg","name":"Sun and Leaves"},{"src":"swamphuts.jpg","name":"Swamp Huts"},{"src":"sunsetships.jpg","name":"Sunset Ships"},{"src":"coast.jpg","name":"Coast"},{"src":"sea.jpg","name":"Fantastic Sea"},{"src":"sailship.jpg","name":"Ship"},{"src":"city1.jpg","name":"City #1"},{"src":"city2.jpg","name":"City #2"},{"src":"ochebana.png","name":"Ochebana Empire"},{"src":"battlefield.png","name":"Battlefield"},{"src":"war.png","name":"War"}]',true);
        $gen->defaultdateB = "BC";
        $gen->defaultdateA = "AD";
        $gen->defaultbackgroundImg = '/Imgs/metal.jpg';
        $gen->defaultbackgroundColor = "var(--doc-accent-color)";

        $gen->styles = ["Mystral", "Fandom", "5eS", "Docs"];
        $gen->defaultStyle = "Mystral";
        $gen->mystData = [
            "notebooks" => 2, "articles" => 222, "pages" => 999, "images" => 22, "fullSpace" => 10000001
        ];

        require_once($_SERVER['DOCUMENT_ROOT']."/ds/subs/subHandler.php");
        $gen->premPower = subPower("mystral", $gen->user);
        if ($gen->premPower > 0){
            $gen->baseImage = "/mystral/mystral".$gen->premPower.".png";
            $gen->styles[] = "Great Gamemaster";
            if ($gen->premPower > 1){
                $gen->mystData = [
                    "notebooks" => 99, "articles" => 9999, "pages" => 22222, "images" => 222, "fullSpace" => 444000001
                ];
                $gen->styles[] = "Imperium";
                $gen->defaultBanner = "mystral2.png";
                $gen->styleInfo["Mystral"] = ["/docs/docs.css", "/mystral/myst.css", "/mystral/myst2.css", "https://fonts.googleapis.com/css2?family=PT+Serif&display=swap"];
                $gen->defaultbackgroundImg = '/Imgs/gold.jpg';
            }
            else {
                $gen->mystData = [
                    "notebooks" => 10, "articles" => 222, "pages" => 2222, "images" => 50, "fullSpace" => 100000001
                ];
                $gen->defaultBanner = "mystral1.png";
            }
            $gen->banners[0] = ["src"=>$gen->defaultBanner, "name"=>"Mystral"];
        }
        else {
            $gen->baseImage = "/Imgs/Mystral.png";
        }
    }
    else if ($domain == "dic"){
        $gen->dbconn = $user->addConn("dic");
        $gen->domain = "dic";
        $gen->domainnum = 5;
        $gen->database = "words";
        $gen->pagename = "word";
        $gen->groupName = "dictionary";
        $gen->homelink = "/dic/home";
        $gen->minPower = 2;
        $gen->domainName = "Dictionary";
        $gen->domainLogo = 'Dictionary';
        $gen->domainType = "dic";
        $gen->acceptsTopBar = false;
        $gen->wsettingsdb = "languages";
        $gen->canLocalAccStat = true;

        $gen->artRootLink = "/dic/word/";
        $gen->baseLink = "/dic/";
    }
    else {
        $gen->dbconn = $gen->conn;
        $gen->domain = "fandom";
        $gen->domainnum = 0;
        $gen->database = "pages";
        $gen->pagename = "article";
        $gen->groupName = "wiki";
        $gen->homelink = "/fandom/home";
        $gen->minPower = 1;
        $gen->domainName = "Fandom";
        $gen->domainLogo = "Fandom Wiki";
        $gen->defaultBanner = "fandom.png";
        $gen->defaultGenre = "Lore";
        $gen->artRootLink = "/fandom/wiki/";
        $gen->baseLink = "/fandom/";
        $gen->canLocalAccStat = true;

        $gen->luckying = true;
        $gen->acceptsTopBar = false;
    }
    if (!isset($gen->listName)){$gen->listName = $gen->groupName;}
    $gen->style = $gen->defaultStyle;
}


?>
