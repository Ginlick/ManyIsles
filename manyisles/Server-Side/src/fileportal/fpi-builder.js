class fpi_builder{
  root = "/Server-Side/src/fileportal/";

  constructor(code, returnF = null) {
    this.returnF = returnF;
    this.code = code;
    this.parser = new DOMParser();
    addCss(this.root+"fpi.css", "css");
  }

  createPortal(parentEl, content = "broad", inputMethod = 0) {
    var el = this.giveHTMLel(content);
    eleActions(el, this);
    for (let ele of el.getElementsByTagName("*")){
      eleActions(ele, this);
    }

    function eleActions(ele, thisClass) {
      if (ele.hasAttribute("fpi-inputtype")){
        if (ele.getAttribute("fpi-inputtype") == "uploader") {
          ele.parentEl = el;
          if (inputMethod == 1){ //use as form element
            ele.addEventListener("change", thisClass.edisplay.bind(thisClass));
          }
          else { //directly upload
            ele.addEventListener("input", thisClass.eupload.bind(thisClass));
          }
        }
      }
    }

    parentEl.replaceChildren(el);
  }

  eupload(e){
    let thisClass = this;
    let input = e.target;
    let parentEl = input.parentEl;
    if (input.files && input.files.length > 0) {
      thisClass.childVis(parentEl, "image-upload-wrap", "hide");
      thisClass.childVis(parentEl, "file-upload-content", "show");
      thisClass.childVis(parentEl, "uploaded-image-title", "change", input.files[0].name);

      var formData = new FormData();
      formData.append("intent", "upload");
      for (let i = 0; i < input.files.length; i++){
        formData.append(i, input.files[i]);
      }
      thisClass.removeUpload(parentEl);
      thisClass.makeRequest(formData, function (r) {
        createPopup("d:gen;txt:Image uploaded");
        if (thisClass.returnF != null){thisClass.returnF(r)};
      });

    }
  }
  edisplay(e){
    let thisClass = this;
    let input = e.target;
    let parentEl = input.parentEl;

    if (input.files && input.files[0]) {
      var reader = new FileReader();
      reader.onload = function(e) {
        console.log(input.files[0].webkitRelativePath);
        thisClass.childVis(parentEl, "imageShower", "src", input.files[0]);
      };
      reader.readAsDataURL(input.files[0]);
    }
  }
  removeUpload(input) {
    this.childVis(input, "image-upload-wrap", "show");
    this.childVis(input, "image-upload-content", "hide");
    this.childVis(input, "file-upload-input", "clear");
  }
  deleteImage(name){
    var formData = new FormData();
    formData.append("intent", "delete");
    formData.append("file", name);
    this.makeRequest(formData);
  }
  error(errorText = "An error occurred.") {
    createPopup("d:gen;txt:"+errorText);
    return false;
  }
  makeRequest(formData, response = null){
    formData.append("code", this.code);
    var getFile = this.root + "fpi-api.php";
    var xhttp = new XMLHttpRequest();
    xhttp.addEventListener("load", (event) => {
      console.log(event.target.responseText);
      if (!this.isJsonString(event.target.responseText)){return this.error();}
      let r = JSON.parse(event.target.responseText);
      if (r["error"] != undefined){return this.error(r["error"]);}
      if (response != null) {response(r);}
    });
    xhttp.addEventListener("error", (event) => {
      this.removeUpload(parentEl);
      return this.error();
    });
    xhttp.open("POST", getFile, true);
    xhttp.send(formData);
  }


  childVis(parentEl, targetClass, action, text = ""){
    for (let child of parentEl.getElementsByClassName(targetClass)){
      if (action == "change"){
        child.innerHTML = text;
      }
      else if (action == "hide"){
        child.style.display = "none";
      }
      else if (action == "show"){
        child.style.display = "block";
      }
      else if (action == "clear") {
        child.value = "";
      }
      else if (action == "src"){
        console.log(child);
        child.setAttribute("src", text);
      }
    }
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
  isJsonString(str) {
      try {
          JSON.parse(str);
      } catch (e) {
          return false;
      }
      return true;
  }
  HTMLels = {
    "broad" : `
      <div class="file-upload">
        <div class="image-upload-wrap">
          <input class="file-upload-input" type="file" name="file" id="file-upload-input" accept=".png, .jpg" multiple fpi-inputtype="uploader" />
          <div class="drag-text">
            <p><i class="fas fa-arrow-up"></i> Upload Image (max 2 mb)</p>
          </div>
        </div>
        <div class="file-upload-content">
          <p class="image-title"><i class="fas fa-spinner fa-spin"></i> Uploading <span class="uploaded-image-title">Uploaded Image</span></p>
        </div>
      </div>
    `,
    "wideDashed" : `
      <div>
        <div class="bannerBlock" >
          <img alt="banner image" class="inBanner imageShower" src=""/>
        </div>
        <div class="uploadable bannerUploadCont">
            <i class="fa-solid fa-arrow-up-from-bracket"></i>
             Select Banner (optional, max 2mb)
             <input type="file" class="fileInput" value="null" name = "banner" accept=".png, .jpg" multiple fpi-inputtype="uploader" />
        </div>
      </div>
    `
  };
}

if (typeof fpi_launcher !== "undefined"){
  fpi_launcher();
}
