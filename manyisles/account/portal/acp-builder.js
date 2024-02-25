addCss("/account/portal/acp.css", "css");
addCss("https://www.google.com/recaptcha/api.js?render=6LeFhjkjAAAAANXrjzjnSCGlhHg9qYYkkqx2bD3A", "js"); //somehow get the key (another api?)
addCss("")

class acp_builder{
  constructor(returnF = null, options = []) {
    this.returnF = returnF;
    this.parser = new DOMParser();
  }
  createPortal(parentEl, content = "signIn") {
    var el = this.giveHTMLel(content);
    eleActions(el, this);
    for (let ele of el.getElementsByTagName("*")){
      eleActions(ele, this);
    }
    function eleActions(ele, thisClass) {
      if (ele.hasAttribute("acp-formtype")){
        if (ele.getAttribute("acp-formtype") == "signIn") {
          ele.addEventListener("submit", thisClass.signIn.bind(thisClass));
        }
        else if (ele.getAttribute("acp-formtype") == "directSignIn") {
          ele.addEventListener("submit", thisClass.directSignIn.bind(thisClass));
        }
      }
      if (ele.hasAttribute("acp-eltype")){
        if (ele.getAttribute("acp-eltype")=="switchCreate"){
          ele.addEventListener("click", function () { thisClass.createPortal(parentEl, "signCreate"); }, false);
        }
        else if (ele.getAttribute("acp-eltype")=="switchIn"){
          ele.addEventListener("click", function () { thisClass.createPortal(parentEl, "signIn"); }, false);
        }
      }
    }
    parentEl.replaceChildren(el);
  }
  directSignIn(e) {
    //initial stuff, deal with captcha
    e.preventDefault(); 
    var el = e.target;
    //make request to acp-api (log in)
    var file = "/account/portal/acp-api.php";
    var xhttp = new XMLHttpRequest();
    var formData = new FormData(el);
    var r = false;
    xhttp.addEventListener("load", (event) => {
      r = JSON.parse(event.target.responseText);
      if (!r.signedIn){
        if (r.issues != undefined){
          if (r.issues.madeReturn != undefined){
            if (r.issues.madeReturn == "captcha"){ //replace this with captcha
              return this.formError(el, "Our spam block interrupted you. Please try again later.", true);
            }
            else if (r.issues.madeReturn == "userinput"){
              return this.formError(el, "", {"uname": {"errorLabel" : "Incorrect username or password."}});
            }
          }
        }
        return this.formError(el);
      }
      el.parentElement.replaceChildren(this.giveHTMLel("successSignHTML"));
      if (this.returnF == null) {location.reload();}
      else {this.returnF(r);}
    });
    xhttp.addEventListener("error", (event) => {
      return this.formError(el);
    });
    xhttp.open("POST", file, true);
    xhttp.send(formData);
  }
  signIn(e){
    e.preventDefault();
    var el = e.target;
    let thisClass = this;
    grecaptcha.ready(function() {
      grecaptcha.execute('6LeFhjkjAAAAANXrjzjnSCGlhHg9qYYkkqx2bD3A', {action: 'submit'}).then(function(token) {
        thisClass.actSignIn(el, token);
      });
    });
    return false;
  }
  signIn(e) {
    e.preventDefault();
    window.location.href = "/account/api/login";
  }

  formError(el, errorMessage = "An error occured.", resetForm = true) {
    console.log(errorMessage);
    for (let element of el.getElementsByTagName("*")){
      if (element.tagName == "INPUT"){
        if (resetForm === true){
          element.value = "";
        }
        else if (typeof resetForm == "object"){
          if (resetForm[element.getAttribute("name")] != null){
            element.value = "";
            element.style.backgroundColor = "var(--gen-color-lightred)";
            let id = element.id + "InputErr";
            console.log(id);
            document.getElementById(id).innerHTML = resetForm[element.getAttribute("name")]["errorLabel"];
            document.getElementById(id).style.display = "block";
            window.setTimeout(function () {
              element.style.backgroundColor = "var(--g-bground)";
            }, 2200);
          }
        }
      }
      if (element.hasAttribute("acp-eltype")){
        if (element.getAttribute("acp-eltype") == "errorTaker"){element.style.display = "block";}
        if (errorMessage !== null){element.innerHTML = errorMessage;}
      }
    }
    return false;
  }
  giveHTMLel(name, insertable) {
    let toParse = this.HTMLels[name];
    let parsed = this.insertInsert(toParse);
    return this.parser.parseFromString(parsed, "text/html").firstChild.children[1].firstChild;
  }
  insertInsert(text) {
    let matches = text.match(/\[insert:([a-zA-Z]+)\]/);
    if (matches == null){return text;}
    let match = matches[1];
    text = text.replace("[insert:"+match+"]", this.HTMLels[match]);
    return this.insertInsert(text);
  }
  HTMLels = {
    "signIn": `
    <div class="acp-portal-cont">
      <h2>Sign In</h2>
      [insert:signInMed]
    </div>
    `,
    "signInMed" : `
      [insert:signInBasic]
      <p>Don't have an account yet? <span class="fakelink" acp-eltype="switchCreate">Join us now!</span></p>
    `,
    "signInBasic" : `
      <form acp-formtype="signIn" class="acp-form">
        <p style="color:red;display:none;" acp-eltype="errorTaker">Sign in failed.</p>
        <button class="acp-button" type="submit">Log In</button>
    </form>
    `,
    "signInSmall" : `
      [insert:signInBasic]
      <p>Don't have an account yet? <span class="fakelink" acp-eltype="switchCreate">Join us now!</span></p>
    `,
    "signInLine" : `
      <form acp-formtype="directSignIn" class="acp-form line">
        <label for="loguname">Username</label>
        <input type="text" placeholder="Hansfried Dragonslayer" name="uname" id="loguname" oninput="inputGramm(this, 'u')" autocomplete="nickname" required>
        <div id="logunameInputErr" class="inputErr" default="Incorrect input!"></div>
        <label for="logpassword">Password</label>
        <input type="password" placeholder="uniquePassword22" name="psw" id="logpassword" oninput="inputGramm(this, 'p')" autocomplete="current-password" required>
        <div id="logpasswordInputErr" class="inputErr" default="Incorrect input!"></div>
        <p style="color:red;display:none;" acp-eltype="errorTaker">Sign in failed.</p>
        <button class="acp-button" type="submit">Log In</button>
    </form>
    `,
    "signCreate": `
    <div class="acp-portal-cont">
      <h2>Create Account</h2>
      [insert:signCreateBasic]
      <p>Already have an account? <span class="fakelink" acp-eltype="switchIn">Log in</span></p>
    </div>
    `,
    "signCreateBasic" : `
      <form acp-formtype="signIn" class="acp-form">
          <p>Sign in or create an account to join our marvelous community!</p>
          <p style="color:red;display:none;" acp-eltype="errorTaker">Account creation failed.</p>
          <button class="acp-button" type="submit">Sign In</button>
      </form>
    `,
    "successCreateHTML" : `
      <div class="acp-portal-cont">
        <i class="acp-successor fa-regular fa-circle-check"></i>
        <p>Account successfully created!<br>
          Confirm your email address to finish setting up your account.<br>
        </p>
        <p>
          <span style='color:#c2c2c2;'>Note that the confirmation email might be in your spam folder. You can also always resend the link from your account page.</span>
        </p>
        <a href="/account/home"><button class="acp-button">Continue</button></a>
      </div>
    `,
    "successCreateWanttopublishHTML" : `
      <div class="acp-portal-cont">
        <i class="acp-successor fa-regular fa-circle-check"></i>
        <p>Account successfully created!<br>
          Once you've confirmed your email, go to "Become Partner" on your account page.<br>
        </p>
        <p>
          <span style='color:#c2c2c2;'>Note that the confirmation email might be in your spam folder. You can also always resend the link from your account page.</span>
        </p>
        <a href="/account/home"><button class="acp-button">Continue</button></a>
      </div>
    `,
    "successSignHTML" : `
      <div class="acp-portal-cont">
        <i class="acp-successor fa-regular fa-circle-check"></i>
        <p>Successfully signed in!</p>
      </div>
    `
  };
}

function inputGramm(x, y) {
    var input = x.value;
    var target = x.id;
    var patt = new RegExp("^[A-Za-z0-9 ]+$");
    if (y == "e") { patt = new RegExp("[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]$");}
    else if (y == "p") { patt = new RegExp("^[A-Za-z0-9!.\-_ ]+$");}
    target = document.getElementById(target + "InputErr");
    target.style = "";
    target.innerHTML = target.getAttribute("default");
    if (!patt.test(input) && input.length != 0) { target.style.display = "block"; }
}
function inputLength(x, n) {
  if (x.value.length < n){
    target = document.getElementById(x.id + "InputErr");
    target.innerHTML = "Input too short!";
    target.style.display = "block";
  }
}
if (typeof acp_launcher !== "undefined"){
  acp_launcher();
}
