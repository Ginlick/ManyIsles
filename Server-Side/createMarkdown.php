<?php
//usage: call markdownTabs() and markdownScript() in your html document, and add the "markdownable" tag to all markdown input elements

function markdownInfos() {
  //give the shortcut + text info
}
function markdownTabs() {
  //link
  $tab = '
  <div id="markdown-mods-link" class="modCol">
      <div class="modContent smol">
          <h1>Insert Link</h1>
          <div class="nmodBody">
              <input type="text" placeholder="Shown Text" id="markdown-mods-link-name"></input>
              <input type="text" placeholder="url" id="markdown-mods-link-url"></input>
              <button class="wikiButton" onclick="markdownCreateLink();">Insert</button>
              <p><span class="typeTab tiny" onclick="newpop(\'ded\')">esc</span> close</p>
          </div>
      </div>
  </div>';
  //image
  $image = '
  <div id="markdown-mods-img" class="modCol">
      <div class="modContent smol">
          <h1>Insert Image</h1>
          <div class="nmodBody">
              <select id="markdown-mods-img-class">
                  <option value="sideimg">Side Image</option>
                  <option value="sideimg medium">Larger Side Image</option>
                  <option value="sideimg landscape">Landscape Side Image</option>
              </select>
              <input type="text" placeholder="Direct link to image" id="markdown-mods-img-src" />
              <input type="text" placeholder="Caption (optional)" id="markdown-mods-img-caption"  />
              <input type="text" placeholder="Style (css, optional)" id="markdown-mods-img-style"  />
              <button class="wikiButton" onclick="markdownCreateImg();">Insert</button>
              <p><span class="typeTab tiny" onclick="newpop()">esc</span> close</p>
          </div>
      </div>
  </div>';

  return $tab.$image;
}
function markdownScript() {
  $script = <<<HELAI
    <script>
    var myField = null;
    fillableAreas = document.getElementsByTagName("*");
    for (let area of fillableAreas){
      if (!area.hasAttribute("markdownable")){continue;}
      area.addEventListener("focus", markdownTarget);
      myField = area;
    }
    function markdownTarget(evt) {
      myField = evt.srcElement;
    }

    function markdownInsLink() {
      if (myField == null){throw "no markdownable input found";}
      let selectedText = getSelectionText();
      document.getElementById("markdown-mods-link-name").value = selectedText;
      document.getElementById("markdown-mods-link-url").value = "";
      newpop("markdown-mods-link");
      if (selectedText == "") {
          document.getElementById("markdown-mods-link-name").focus();
      }
      else {
          document.getElementById("markdown-mods-link-url").focus();
      }
      return false;
    }
    function markdownCreateLink() {
      var name = document.getElementById("markdown-mods-link-name").value;
      var url = document.getElementById("markdown-mods-link-url").value;

      linkName = name.replace(/"/g, '“');
      link = url.replace(/"/g, '');
      var myText = "[" + linkName + "](" + link + ")";

      newpop("ded");
      insertText(myField, myText);
    }
    function markdownInsImg() {
      if (myField == null){throw "no markdownable input found";}
      document.getElementById("markdown-mods-img-src").value = "";
      document.getElementById("markdown-mods-img-caption").value = "";
      document.getElementById("markdown-mods-img-style").value = "";
      newpop("markdown-mods-img");
      document.getElementById("markdown-mods-img-src").focus();
      return false;
    }
    function markdownCreateImg() {
      var details = [];
      details["class"] = document.getElementById("markdown-mods-img-class").value;
      details["style"] = document.getElementById("markdown-mods-img-style").value;
      details["caption"] = document.getElementById("markdown-mods-img-caption").value;
      details["src"] = document.getElementById("markdown-mods-img-src").value;
      if (details["src"]==""){return;}
      var text = "{";
      for (let key in details){
        if (details[key] != ""){
          text += key + "[" + details[key] + "]";
        }
      }
      text +="}";
      newpop("ded");
      insertText(myField, text);
    }
    function markdownBolden() {
      resultText = "*" + getSelectionText() + "*";
      insertText(myField, resultText);
    }

    whenAvailable("Mousetrap", function () {
      Mousetrap.bind("ctrl+shift+k", function (e) {
        e.preventDefault();
        markdownInsLink();
        return false;
      });
      Mousetrap.bind("ctrl+shift+i", function (e) {
        e.preventDefault();
        markdownInsImg();
        return false;
      });
      Mousetrap.bind("command+b", function (e) {
        e.preventDefault();
        markdownBolden();
        return false;
      });
    });

    </script>
  HELAI;
  return $script;
}
?>
