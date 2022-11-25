//TODO: a css document for acp (the blocks, styling of inputs?, error notifiers)

class acp_builder{
  constructor(returnF = null, options = []) {
    this.returnF = returnF;
    this.parser = new DOMParser();
  }
  createPortal(parentEl, content = "signIn") {
    var el = this.giveHTMLel(content);
    for (let ele of el.getElementsByTagName("*")){
      if (ele.hasAttribute("acp-formtype")){
        if (ele.getAttribute("acp-formtype") == "signIn") {
          ele.addEventListener("submit", this.signIn.bind(this));
        }
      }
      if (ele.hasAttribute("acp-eltype")){
        if (ele.getAttribute("acp-eltype")=="switchCreate"){
          let object = this;
          ele.addEventListener("click", function () { object.createPortal(parentEl, "signCreate"); }, false);
        }
        else if (ele.getAttribute("acp-eltype")=="switchIn"){
          let object = this;
          ele.addEventListener("click", function () { object.createPortal(parentEl, "signIn"); }, false);
        }
      }
    }
    parentEl.replaceChildren(el);
  }
  signIn(e){
    e.preventDefault();
    var el = e.target;
    var file = "/account/portal/acp-api.php";
    var xhttp = new XMLHttpRequest();
    var formData = new FormData(el);
    xhttp.addEventListener("load", (event) => {
      var r = JSON.parse(event.target.responseText);
      console.log(r);
      if (!r.signedIn){return this.formError(el);}
      el.parentElement.replaceChildren(this.giveHTMLel("successSignHTML"));
      this.returnF(r);
    });
    xhttp.addEventListener("error", (event) => {
      return this.formError(el);
    });
    xhttp.open("POST", file, true);
    xhttp.send(formData);

    return false;
  }

  formError(el) {
    for (let element of el.getElementsByTagName("*")){
      if (element.tagName == "INPUT"){element.value = "";}
      if (element.hasAttribute("acp-eltype")){
        if (element.getAttribute("acp-eltype") == "errorTaker"){element.style.display = "block";}
      }
    }
    return false;
  }
  giveHTMLel(name) {
    let toParse = this.HTMLels[name];
    return this.parser.parseFromString(toParse, "text/html").firstChild.children[1];
  }
  HTMLels = {
    "signIn" : `
    <div class="acp-portal-cont">
      <h2>Sign In</h2>
      <form acp-formtype="signIn">
        <table>
            <tr>
                <td><label for="loguname"><b>Username</b></label></td>
                <td style="width:1000%"><input type="text" placeholder="Hansfried Dragonslayer" name="uname" id="loguname" oninput="inputGramm(this, 'u')" autocomplete="username" required></td>
            </tr>
            <tr>
                <td><label for="logpassword"><b>Password</b></label></td>
                <td><input type="password" placeholder="uniquePassword22" name="psw" id="logpassword" oninput="inputGramm(this, 'p')" autocomplete="current-password" required><br></td>
            </tr>
        </table>
        <p style="color:red;display:none;" acp-eltype="errorTaker">Sign in failed.</p>
        <button class="popupButton" type="submit">Log In</button>
      </form>
      <p>Don't have an account yet? <span class="fakelink" acp-eltype="switchCreate">Join us now!</span></p>
    </div>
    `,
    "signCreate" : `
    <div class="acp-portal-cont">
      <h2>Create Account</h2>
      <form acp-formtype="signCreate">
        <input name="wanttoPublish" id="wanttoPublish" type="text" style="display:none;" value="0" autocomplete="off" readonly />
          <table>
              <tr>
                  <td> <label for="uname"><b>Username</b></label></td>
                  <td style="width:1000%">  <input type="text" placeholder="Hansfried Dragonslayer" name="uname" id="uname" oninput="inputGramm(this, 'u')" autocomplete="username" required></td>
              </tr>
              <tr>
                  <td></td>
                  <td id="unameInputErr" class="inputErr">Incorrect input!</td>
              </tr>
              <tr>
                  <td> <label for="email"><b>Email</b></label></td>
                  <td> <input type="email" placeholder="godsofmanyisles@gmail.com" name="email" id="email" onfocusout="inputGramm(this, 'e')" required></td>
              </tr>
              <tr>
                  <td></td>
                  <td id="emailInputErr" class="inputErr">Incorrect input!</td>
              </tr>
              <tr>
                  <td> <label for="psw"><b>Password</b></label></td>
                  <td> <input type="password" placeholder="uniquePassword22" name="psw" oninput="inputGramm(this, 'p')" autocomplete="new-password" required></td>
              </tr>
              <tr>
                  <td></td>
                  <td id="pswInputErr" class="inputErr">Incorrect input!</td>
              </tr>
              <tr>
                  <td> <label for="region"><b>Region</b></label></td>
                  <td>
                      <select name="region" id="region" required>
                          <option value="1">1 (UTC)</option>
                          <option value="2">2 (UTC + 7)</option>
                          <option value="3">3 (UTC - 7)</option>
                      </select>
                  </td>
              </tr>
          </table>
          <p>By creating an account, you agree with the community's <a href="/docs/44/Conditions_and_Terms" target="_blank">Terms and Conditions</a>.</p>
          <p style="color:red;display:none;" acp-eltype="errorTaker">Account creation failed.</p>
          <button class="popupButton" type="submit">Sign Up</button>
      </form>
      <p>Already have an account? <span class="fakelink" acp-eltype="switchIn">Log in</span></p>
    </div>
    `,
    "successSignHTML" : `
      <div class="acp-portal-cont">
        <i class="fa-regular fa-circle-check"></i>
        <p>Successfully signed in!</p>
      </div>
    `
  };
}
