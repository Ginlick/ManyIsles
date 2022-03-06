var feedList = document.getElementsByTagName("*");
for (let feed of feedList){
  if (!feed.hasAttribute("blog-feed")){continue;}
  let mode = "chronology";
  let buser = 0;
  if (feed.hasAttribute("blog-feed-user")){buser = feed.getAttribute("blog-feed-user");}
  let file = "/blog/g/fetchFeed.php?m="+mode+"&u="+buser;
  if (file) {
      xhttp = new XMLHttpRequest();
      xhttp.onreadystatechange = function () {
          if (this.readyState == 4) {
            feed.innerHTML = this.status + ". Failed to load.";
            if (this.status == 200) {
              feed.innerHTML = this.responseText;
              getIndexImgs();
            }
          }
      }
      xhttp.open("GET", file, true);
      xhttp.send();
  }
}
