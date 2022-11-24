
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <link rel="icon" href="Imgs/Favicon.png">
    <title>Many Isles</title>
    <link rel="stylesheet" type="text/css" href="/Code/CSS/Main.css">
    <link rel="stylesheet" type="text/css" href="/Code/CSS/sleek.css">
    <link rel="stylesheet" type="text/css" href="/Code/CSS/pop.css">
    <link rel="stylesheet" type="text/css" href="/tools/imgTabs.css"> <!--remove-->
    <style>
    .m-topnav {
        position: sticky;
        top: 0;
        background-color: var(--gen-color-mellowite);
        height: 100px;
        box-shadow: var(--doc-neatshadow);
        z-index: 15;
    }
    .m-topnav-wrapper {
      display: flex;
      flex-direction: row;
      padding: 8px 16px;
      height: 100%;
    }

    .m-topnav-left, .m-topnav-right {
      display: flex;
      text-align: left;
      align-items: center;
    }

    .m-topnav-left {
        flex-grow: 3;
    }

    .m-topnav-logo {
        height: 50px;
        width: 50px;
        border-radius: 500px;
        overflow: hidden;
    }

        .m-topnav-logo img {
            width: 100%;
            object-fit: contain;
        }

        .m-topnav-logocont {
          padding: 0 10px;
        }
        .m-topnav-logocont a {
          color: var(--doc-text-color);
        }

        .m-topnav-a {
          color: var(--doc-text-color);
          padding: 0 10px;
        }


        .m-topnav-right {
          flex-grow: 1;
          justify-content: flex-end;
        }

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
          color: var(--gen-color-mellowite);
        }
        .firstview-texter h1 {
          font-size: 6em;
          margin-bottom: 0.5em;
        }
        .firstview-texter .slogan {
          font-size: 2em;
        }

        .content {
          background-color: var(--gen-color-mellowite);
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
          position: relative;
          justify-content: center;
          max-width: 1000px;
          margin: auto;
        }
        .im-box {
          width: 300px;
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

        /* .im-text {
          color: white;
          text-align: center;
          position: absolute;
          top: 50%;left: 50%;
          transform: translate(-50%, -65%);
          -ms-transform: translate(-50%, -50%);
        }

        .im-buttcont {
          position: absolute;
          bottom: 10px;
          width: 100%;
          text-align: center;
        }
        .im-button {
          background-color: white;
          color: var(--gen-color-link);
        }*/
    </style>
</head>
<body>

    <div class="m-topnav">
      <div class="contcol-wrapper m-topnav-wrapper">
        <div class="m-topnav-left">
          <div class="m-topnav-logo"> <img src="/IndexImgs/GMTips.png" />   </div>
          <div class="m-topnav-logocont"><a href="/home" class="m-topnav-a" target="_self"><h3>Many Isles</h3></a></div>
        </div>
        <div class="m-topnav-right">
          <a href="" target="_self" class="m-topnav-a">Revert</a>
          <a href="" target="_self" class="m-topnav-a">Revert</a>
          <a href="" target="_self" class="m-topnav-a">Revert</a>
          <a href="" target="_self" class="m-topnav-a">Revert</a>
          <a href="" target="_self" class="m-topnav-a">Revert</a>
          <a href="" target="_self" class="m-topnav-a">Revert</a>
        </div>
      </div>
    </div>

    <section class="allcontent">
      <section class="firstview">
        <div class="firstview-cont contcol-wrapper">
          <div class="firstview-texter">
            <h1>Many Isles</h1>
            <p class="slogan">A world of creation.</p>
          </div>
        </div>


      </section>
      <section class="content">
        <div class="contcol-wrapper">
          <p class="homep">Explore the awesome lore, RPG content, and tools of the fantasy community.</p>

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
              <a class="im-wrapA" href="/account/home"></a>
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
                  <p>
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
                  <p>
                    <span class="fakelink">Join</span>
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>

      </section>




    </section>


    <div class="contentBlock">

        <div class="banner">
            <picture>
                <source srcset="/Imgs/Banner.png" media="(max-width: 1400px)">
                <source srcset="/Imgs/BigBanner.png">
                <img src="/Imgs/BigBanner.png" alt="Banner" style='width:100%;display:block'>
            </picture>
        </div>

        <h1 style="
        color: RGB(130,0,0);
        font-size: 4em;
        margin-bottom: 0.1vw">
            Many Isles
        </h1>
        <div style="width:80%;background-color:RGB(149,96,50);height:3px;margin:auto;"></div>
        <div style="text-align:center;margin:0px 0.3vw 3vw;font-size:2vw">
            <p style="color: rgb(76, 54, 47);font-size: var(--all-fonts-larg);">
                A homebrew hub with lore, cool rules, and epic tools.<br />
            </p>
        </div>
        <div class="contCont">


            <div class="container">
                <!-- introduction-link -->

                <div class="imgCont" load-image="/Imgs/tools.png">
                </div>

                <div class="overlay">
                    <div class="text">Awesome community-created wikis.</div>
                </div>
                <div class='partinimg'><a href='/fandom/home' class='button fandom' target='_self'>Fandom</a></div>

            </div>

            <div class="container">
                <!-- accounts-link -->

                <div class="imgCont" load-image="../Imgs/Acc.png">
                </div>

                <div class="overlay">
                    <div class="text">Your account. Be an adventurer of the Many Isles!</div>
                </div>
                <div class='partinimg'><a href='/account/Account' class='button' target='_self'>Account</a></div>

            </div>

            <div class="container">
                <div class="imgCont" load-image="/IndexImgs/squMyst.png">
                </div>

                <div class="overlay">
                    <div class="text">Easily create notebooks for campaigns and worldbuilding.</div>
                </div>

                <div class='partinimg'><a href='/mystral/hub' class='button mystral' target='_self'>Notes</a></div>

            </div>


            <div class="container">
                <div class="imgCont" load-image="/Imgs/5eSlogo.png" style="background-color:black;">
                </div>

                <div class="overlay">
                    <div class="text">A variant D&D system.</div>
                </div>
                <div class='partinimg'><a href='/5eS/home' class='button FeS' target='_self'>5eS System</a></div>

            </div>

            <div class="container">
                <!-- tools-link -->

                <div class="imgCont" load-image="/IndexImgs/Gear.png">
                </div>

                <div class="overlay">
                    <div class="text">A ton of extra tools for the adventurer!</div>
                </div>

                <div class='partinimg'>
                    <a href='/tools/tools' class='button ' target='_self'>Tools</a>
                </div>
            </div>


        </div>



        <div>
            <img src="/Imgs/Bar2.png" alt="RedBar" class='separator'>
        </div>


        <h2>Join Us</h2>

        <div class="contCont">
            <div class='container'>

                <div class="imgCont" load-image="/Imgs/disct.png">
                </div>

                <div class="overlay">
                    <div class="text">Join our discord server and become part of the growing community!</div>
                </div>

                <div class='partinimg'><a href='https://discord.gg/XTQnR7mS3D' class='button' target='_blank'>Join!</a></div>

            </div>

            <div class="container">
                <div class="imgCont" load-image="/Imgs/docs.png">
                </div>
                <div class="overlay">
                    <div class="text">View and post awesome content!</div>
                </div>
                <div class='partinimg'><a href='/blog/explore' class='button blog' target='_self'>Blogs</a></div>
            </div>

            <div class='container'>

                <div class="imgCont" load-image="/Imgs/Reddit.png">
                </div>

                <div class="overlay">
                    <div class="text">Find us on reddit!</div>
                </div>

                <div class='partinimg'><a href='https://www.reddit.com/r/ManyIsles/' class='button' target='_blank'>Join!</a></div>
            </div>
        </div>
    </div>
    <div class="contentBlock">
      <p>
        The Many Isles are a free and open-source community devoted to all things fantasy.<br>
        <ul>
          <li><b>Website Version:</b> Armuria<a href="/Code/Changelog" style="padding-left:5px;" w3-include-html="/Code/CSS/v.html"></a></li>
          <li><b>Source Code:</b> <a href="https://github.com/Ginlick/ManyIsles" target"_blank">Github</a></li>
          <li><b>Documentation:</b> <a href="/docs/1/Many_Isles">Many Isles Docs</a></li>
        </ul>
      </p>
    </div>
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
    }
</script>
