var allFeeds = [];
function fillFeed(feed, offset = 0) {
  if (!feed.hasAttribute("blog-feed")){return;}
  let mode = "new";
  let buser = 0; let type = "posts"; let reference = ""; let moreinfo = "";
  if (feed.hasAttribute("blog-feed-user")){buser = feed.getAttribute("blog-feed-user");}
  if (feed.hasAttribute("blog-feed-sort")){mode = feed.getAttribute("blog-feed-sort");}
  if (feed.hasAttribute("blog-feed-type")){type = feed.getAttribute("blog-feed-type");}
  if (feed.hasAttribute("blog-feed-reference")){reference = feed.getAttribute("blog-feed-reference");}
  if (feed.hasAttribute("blog-feed-settings")){moreinfo = feed.getAttribute("blog-feed-settings");}
  let form = new FormData();
  form.append("u", buser); //string array of who to draw from
  form.append("m", mode); //sort mode (eg. new, liked... )
  form.append("t", type); //posts or comments
  form.append("r", reference); //for comments: which post to draw from
  form.append("o", offset); //where to start offset
  form.append("s", moreinfo); //string array with more settings
  let file = "/blog/g/fetchFeed.php";
  if (file) {
    xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
        if (this.readyState == 4) {
          if (this.status == 200) {
            console.log("Current offset: " + offset + ", Current mode: " + mode);
            let exhausted = false; let overwrite = false;
            feedId = feed.getAttribute("id");
            if (this.responseText.includes("No more posts")){exhausted = true;}
            if (feedId in allFeeds && allFeeds[feedId]["mode"]!=mode){overwrite = true;}
            allFeeds[feedId] = {"offset":offset, "exhausted":exhausted, "mode":mode};

            if (!exhausted) {
              if (overwrite){
                feed.innerHTML = this.responseText;
              }
              else {
                feed.innerHTML += this.responseText;
              }
              getIndexImgs();
            }
          }
          else {
            feed.innerHTML = this.status + ". Failed to load.";
          }
        }
    }
    xhttp.open("POST", file, true);
    xhttp.send(form);
  }
}
function fillFeeds() {
  var feedList = document.getElementsByTagName("*");
  for (let feed of feedList){
    fillFeed(feed);
  }
}
fillFeeds();
function expandFeed(){
  for (let feedId in allFeeds){
    if (allFeeds[feedId]["exhausted"]){continue;}
    feed = document.getElementById(feedId);
    offset = allFeeds[feedId]["offset"] + 8;
    fillFeed(feed, offset);
  }
}
window.onscroll = function(ev) {
    if (window.scrollY>3000){
      document.getElementById("backToTop").style.display = "block";
    }
    else {
      document.getElementById("backToTop").style.display = "none";
    }
    if ((window.innerHeight + window.scrollY) >= (document.body.offsetHeight - 1200)) {
      expandFeed("explore-feed");
    }
};

var allSortcons = document.getElementsByTagName("*");
for (let sortcon of allSortcons) {
  if (sortcon.hasAttribute("target-feed")){
    feed = sortcon.getAttribute("target-feed");
    var children = sortcon.children;
    for (let child of children){
      child.addEventListener("click", switchSort);
      child.pFeedid = feed;
    }
  }
  else if (sortcon.hasAttribute("profile-option")){
    selector = sortcon.getAttribute("profile-t-selector");
    sortcon.addEventListener("click", switchProfile);
    sortcon.selector = selector;
  }
}

function switchSort(evt) {
  var elmnt = evt.currentTarget;
  let feedid = elmnt.pFeedid;
  var feed = document.getElementById(feedid);
  var sort = elmnt.getAttribute("sort-by");
  feed.setAttribute("blog-feed-sort", sort);
  for (let child of elmnt.parentNode.children){
    child.classList.remove("selected");
  }
  elmnt.classList.add("selected");

  fillFeeds();
}

function switchProfile(evt) {
  var elmnt = evt.currentTarget;
  let selectorId = elmnt.selector;
  var switchTo = elmnt.getAttribute("profile-option");

  var selector = document.getElementById(selectorId);
  for (let child of selector.children){
    if (child.getAttribute("profile-inside")==switchTo){
      child.classList.add("visible");
      document.getElementById(selectorId+"input").value = switchTo;
      console.log(selectorId+"input");
    }
    else {
      child.classList.remove("visible");
    }
  }
}

function toggleLike(elmnt, postId) {
  var goal = 1;
  if (elmnt.classList.contains("active")){
    goal = 0;
  }
  let file = "/blog/g/like.php?p="+postId;
  if (file) {
    xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
      if (this.readyState == 4) {
        console.log(this.responseText);
        if (this.responseText.includes("error")){
          createPopup("d:gen;txt:Error. Could not like post.");
        }
        else {
          likeNum = parseInt(document.getElementById("likenumber"+postId).innerHTML);
          if (this.responseText.includes("success+")){
            elmnt.classList.add("active");
            elmnt.classList.remove("fa-regular");
            elmnt.classList.add("fa-solid");
            document.getElementById("likenumber"+postId).innerHTML = likeNum + 1;
          }
          else {
            elmnt.classList.remove("active");
            elmnt.classList.add("fa-regular");
            elmnt.classList.remove("fa-solid");
            document.getElementById("likenumber"+postId).innerHTML = likeNum - 1;
          }
        }
      }
    }
    xhttp.open("GET", file, true);
    xhttp.send();
  }
}


function suggestPosts(elmnt) {
  document.getElementById("suggest-this").style.display="block";
  query = elmnt.value;
  let file = "/blog/g/suggest.php?q="+query;
  console.log(file);
  if (file) {
    xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
      if (this.readyState == 4) {
        console.log(this.responseText);
        document.getElementById("suggest-this").innerHTML = this.responseText;
      }
    }
    xhttp.open("GET", file, true);
    xhttp.send();
  }
}
function hideSuggest() {
  setTimeout(function () {
    document.getElementById("suggest-this").style.display="none";
  }, 220);
}

function backToTop(id) {
  document.getElementById(id).scrollIntoView({ behavior: "smooth", block: "end" });
}
