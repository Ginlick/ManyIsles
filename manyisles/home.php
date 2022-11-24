
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <link rel="icon" href="Imgs/Favicon.png">
    <title>Many Isles</title>
    <link rel="stylesheet" type="text/css" href="/Code/CSS/Main.css">
    <link rel="stylesheet" type="text/css" href="/Code/CSS/pop.css">
    <style>
        .firstview {
          width: 100%;
          height: calc(100vh - 100px);
          background-image: linear-gradient(to bottom, rgba(0, 0, 0, 0.05), rgba(0, 0, 0, 0.5));
        }
        .firstview-cont {
          height: 100%;
          display: flex;
          align-items: center;
        }
        .firstview-texter {
          color: var(--g-bground);
        }
        .firstview-texter h1 {
          font-size: 6em;
          margin-bottom: 0.5em;
        }
        .firstview-texter .slogan {
          font-size: 2em;
        }

        .content {
          background-color: var(--g-bground);
          box-sizing: border-box;
          padding: 20px 0;
        }

        .homep, h1 {
          text-align: center;
        }
        h1 {
          padding-top: 50px;
        }
        .im-ccontainer {
          display: flex;
          flex-direction: row;
          flex-wrap: wrap;
          position: relative;
          justify-content: center;
          max-width: max(1000px, 80%);
          margin: auto;
        }
        .im-box {
          width: 300px;
          min-height: 400px;
          margin: 20px;
          box-shadow: 0 0 2px rgba(0, 0, 0, 0.2);
          transition: .2s ease;
          border-radius: 5px;
          overflow: hidden;
          position: relative;
        }
        .im-box:hover {
          transform: translateY(-2px);
          background-color: #fcfcfc;
        }
        .im-wrapA {
          position: absolute; width: 100%; height: 100%;
        }
        .im-topcontainer {
          width: 100%;
          height: 200px;
        }
        .im-intopcontainer {
          height: 100%; width: 100%;
          overflow: hidden;
        }
        .im-imgCont {
          width: 100%; height: 100%;
        }
        .im-imgCont img {
          height: 100%; width: 100%;
          object-fit: cover;
          object-position: 50% 50%;
        }
        .im-botcontainer {
          padding: 10px;
        }
        .im-text h3 {
          margin: 15px 0 0;
          display: flex;
          justify-content: space-between;
        }
        .im-text p {
          padding: 10px 0;
        }
    </style>
</head>
<body>
  <div w3-include-html="/Code/CSS/GTopnav.html" w3-create-newEl = "true"></div>
    <section class="ccont-cont">
      <section class="firstview">
        <div class="firstview-cont contcol-wrapper">
          <div class="firstview-texter">
            <h1>Many Isles</h1>
            <p class="slogan">A world of creation.</p>
          </div>
        </div>


      </section>
      <section class="cont-cont" style="padding-bottom: 200px;">
        <div class="contcol-wrapper">
          <p class="homep" id="firstTitle">Explore the awesome lore, RPG content, and tools of the fantasy community.</p>

          <h1>Publications</h1>
          <div class="im-ccontainer">
            <div class="im-box">
              <a class="im-wrapA" href="/dl/home"></a>
              <div class="im-topcontainer">
                <div class="im-intopcontainer">
                  <div class="im-imgCont" load-image="/Imgs/slides/dl.png"></div>
                  <div class="im-overlay"></div>
                </div>
              </div>
              <div class="im-botcontainer">
                <div class="im-text">
                  <h3>Digital Library</h3>
                  <p>
                    A gallery full of jewels from the Many Isles. What we're all about!
                  </p>
                  <p class="explorelink">
                    <span class="fakelink">Explore</span>
                  </p>
                </div>
              </div>
            </div>
            <div class="im-box">
              <a class="im-wrapA" href="/ds/store"></a>
              <div class="im-topcontainer">
                <div class="im-intopcontainer">
                  <div class="im-imgCont" load-image="/Imgs/slides/ds.png"></div>
                  <div class="im-overlay"></div>
                </div>
              </div>
              <div class="im-botcontainer">
                <div class="im-text">
                  <h3>Digital Store</h3>
                  <p>
                    A selection of physical products you can buy.
                  </p>
                  <p class="explorelink">
                    <span class="fakelink">Explore</span>
                  </p>
                </div>
              </div>
            </div>
          </div>

          <h1>Worldbuilding</h1>
          <div class="im-ccontainer">
            <div class="im-box">
              <a class="im-wrapA" href="/fandom/home"></a>
              <div class="im-topcontainer">
                <div class="im-intopcontainer">
                  <div class="im-imgCont" load-image="/Imgs/slides/fandom.png"></div>
                  <div class="im-overlay"></div>
                </div>
              </div>
              <div class="im-botcontainer">
                <div class="im-text">
                  <h3>Fandom Wiki</h3>
                  <p>
                    Awesome community-created wikis.
                  </p>
                  <p class="explorelink">
                    <span class="fakelink">Explore</span>
                  </p>
                </div>
              </div>
            </div>
            <div class="im-box">
              <a class="im-wrapA" href="/mystral/hub"></a>
              <div class="im-topcontainer">
                <div class="im-intopcontainer">
                  <div class="im-imgCont" load-image="/Imgs/slides/mystral.png"></div>
                  <div class="im-overlay"></div>
                </div>
              </div>
              <div class="im-botcontainer">
                <div class="im-text">
                  <h3>Mystral</h3>
                  <p>
                    Easily create notebooks for campaigns and worldbuilding.
                  </p>
                  <p class="explorelink">
                    <span class="fakelink">Explore</span>
                  </p>
                </div>
              </div>
            </div>
          </div>
          <h1>Community</h1>
          <div class="im-ccontainer">
            <div class="im-box">
              <a class="im-wrapA" href="/account/home"></a>
              <div class="im-topcontainer">
                <div class="im-intopcontainer">
                  <div class="im-imgCont" load-image="/Imgs/slides/account.png"></div>
                  <div class="im-overlay"></div>
                </div>
              </div>
              <div class="im-botcontainer">
                <div class="im-text">
                  <h3>Account</h3>
                  <p>
                    Your Many Isles account.
                  </p>
                  <p class="explorelink">
                    <span class="fakelink">Explore</span>
                  </p>
                </div>
              </div>
            </div>
            <div class="im-box">
              <a class="im-wrapA" href="/blog/explore"></a>
              <div class="im-topcontainer">
                <div class="im-intopcontainer">
                  <div class="im-imgCont" load-image="/Imgs/slides/blogs.png"></div>
                  <div class="im-overlay"></div>
                </div>
              </div>
              <div class="im-botcontainer">
                <div class="im-text">
                  <h3>Blogs</h3>
                  <p>
                    View and publish posts about fantasy.
                  </p>
                  <p class="explorelink">
                    <span class="fakelink">Explore</span>
                  </p>
                </div>
              </div>
            </div>
            <div class="im-box">
              <a class="im-wrapA" href="https://discord.gg/XTQnR7mS3D"></a>
              <div class="im-topcontainer">
                <div class="im-intopcontainer">
                  <div class="im-imgCont" load-image="/Imgs/slides/discord.png"></div>
                  <div class="im-overlay"></div>
                </div>
              </div>
              <div class="im-botcontainer">
                <div class="im-text">
                  <h3>Discord</h3>
                  <p>
                    Join our discord server and become part of our growing community.
                  </p>
                  <p class="explorelink">
                    <span class="fakelink">Join</span>
                  </p>
                </div>
              </div>
            </div>
          </div>
          <h1>Tools</h1>
          <div class="im-ccontainer">
            <div class="im-box">
              <a class="im-wrapA" href="/spells/index"></a>
              <div class="im-topcontainer">
                <div class="im-intopcontainer">
                  <div class="im-imgCont" load-image="/Imgs/slides/spells.png"></div>
                  <div class="im-overlay"></div>
                </div>
              </div>
              <div class="im-botcontainer">
                <div class="im-text">
                  <h3>Spells</h3>
                  <p>
                    A tool to create spell lists and custom spells for D&D.
                  </p>
                  <p class="explorelink">
                    <span class="fakelink">Explore</span>
                  </p>
                </div>
              </div>
            </div>
            <div class="im-box">
              <a class="im-wrapA" href="/5eS/home"></a>
              <div class="im-topcontainer">
                <div class="im-intopcontainer">
                  <div class="im-imgCont" load-image="/Imgs/slides/5eS.png"></div>
                  <div class="im-overlay"></div>
                </div>
              </div>
              <div class="im-botcontainer">
                <div class="im-text">
                  <h3>5eS System</h3>
                  <p>
                    The rules for 5eS, a D&D variant.
                  </p>
                  <p>
                    <span class="fakelink">Explore</span>
                  </p>
                </div>
              </div>
            </div>
            <div class="im-box">
              <a class="im-wrapA" href="/tools/tools"></a>
              <div class="im-topcontainer">
                <div class="im-intopcontainer">
                  <div class="im-imgCont" load-image="/Imgs/slides/tools.png"></div>
                  <div class="im-overlay"></div>
                </div>
              </div>
              <div class="im-botcontainer">
                <div class="im-text">
                  <h3>More Tools</h3>
                  <p>
                    A ton of other tools and features!
                  </p>
                  <p>
                    <span class="fakelink">Explore</span>
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>

      </section>
    </section>

<div w3-include-html="/Code/CSS/genericFooter.html" w3-create-newEl="true"></div>
</body>
</html>
<script src="/Code/CSS/global.js"></script>
<script>
    var urlParams = new URLSearchParams(window.location.search);
    var show = urlParams.get('show');

    onload= function hey() {
        if (!localStorage["alertdisplayed"]) {
            createPopup("d:gen;txt:Welcome to the Many Isles!;b:1;bTxt:take a tour;bHref:/docs/32/Welcome;dur:22000");
            localStorage["alertdisplayed"] = true;
        }
        else {
          setTimeout(function () {
            document.getElementById("firstTitle").scrollIntoView({ behavior: "smooth", block: "start" });
          }, 900);
          /*generate new slogan*/
        }
    }

</script>
