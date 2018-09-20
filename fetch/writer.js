/*global invokeEditor*/
var editInstance;
var output = document.getElementById("output");
function sendToServer(q,addt,invoke) {
    editInstance = null;
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            output.innerHTML = this.responseText;
            console.log(this.responseText);
            if(invoke) {editInstance = invokeEditor();}
        }
    }
    xmlhttp.open("POST", "fetch/writerAPI.php", true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlhttp.send("q=" + q + addt);
}
function viewArticles(page) {
    sendToServer("viewArticles","&page="+page,false);
}
/**
 * boolean draft: If the article being viewed is a draft and not published, true
 *      default: false
 */
function editArticle(id,draft) {
    //alert("editArticle"+",&id="+id+"&draft="+draft+",true");
    sendToServer("editArticle","&id="+id+"&draft="+draft,true);
}
function newArticle() {
    sendToServer("newArticle", "",true);
}
var response;
var page = 1;
var cache = [];
function searchPixabay(reset) {
    if(reset) {
        page = 1;
        cache = [];
    }
    if(page<cache.length) {
        renderPixabay(response);
        console.log("cached version of page "+page);
        return false;
    }
    console.log("retrieving page "+page);
    var search = document.getElementById("searchEntry");
    
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            response = JSON.parse(this.responseText);
            cache.push(response);
            renderPixabay(response);
        }
    };
    //we had to obfuscate this code to keep it safe. It basically sends a request to Pixabay API. The origional code is in WriterAPI in Pixabay Upload Section.
    var _$_7e77=["\x47\x45\x54","\x68\x74\x74\x70\x73\x3A\x2F\x2F\x70\x69\x78\x61\x62\x61\x79\x2E\x63\x6F\x6D\x2F\x61\x70\x69\x2F\x3F\x6B\x65\x79\x3D\x39\x37\x33\x31\x39\x37\x30\x2D\x38\x33\x38\x34\x38\x63\x61\x65\x65\x33\x38\x65\x38\x65\x36\x65\x63\x34\x39\x64\x31\x35\x39\x63\x33\x26\x71\x3D","\x76\x61\x6C\x75\x65","\x26\x69\x6D\x61\x67\x65\x5F\x74\x79\x70\x65\x3D\x61\x6C\x6C\x26\x70\x65\x72\x5F\x70\x61\x67\x65\x3D\x35\x26\x70\x61\x67\x65\x3D","\x26\x6F\x72\x69\x65\x6E\x74\x61\x74\x69\x6F\x6E\x3D\x68\x6F\x72\x69\x7A\x6F\x6E\x74\x61\x6C","\x6F\x70\x65\x6E"];xmlhttp[_$_7e77[5]](_$_7e77[0],_$_7e77[1]+ search[_$_7e77[2]]+ _$_7e77[3]+ page+ _$_7e77[4],true)
    xmlhttp.send();
}
function renderPixabay(response) {
    var size = response.totalHits<5?response.totalHits:5;
    for(var x=1;x<=size;x++) {
        var tmp = document.getElementById("pix-"+x);
        result = response;
        console.log(response);
        tmp.innerHTML = `
            <div class="pixabay-aspect-ratio">
                <img src="`+response.hits[x-1].webformatURL+`" class="pixabay-image">
            </div>
            <p>Likes: `+response.hits[x-1].likes+` <a href="`+response.hits[x-1].pageURL+`" target="_blank">Link</a></p>
            <p><img src="`+response.hits[x-1].userImageURL+`" class="pixabay-user" />`+response.hits[x-1].user+`</p>
            <a href="#" onclick="useImage('`+response.hits[x-1].webformatURL+`')">Use This</a>
        `;
        document.getElementById("pagenum").innerHTML = page;
        
    }
}
function useImage(link) {
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            var recieve = this.responseText;
            if(recieve=="link expired") {
                alert("Link Expired. Please save and reload");
                return false;
            }
            document.getElementById("image").src = "temp/" + recieve + ".jpg";
            document.getElementById("url-cont").value = recieve;
        }
    };
    xmlhttp.open("GET", "fetch/writerAPI.php?q=getImage&link="+link, true);
    //if for whatever reason this starts not working: try this:
    // 1) Replace "GET" with "POST"
    // 2) Cut and Paste the second parameter of xml.open as the first param of xml.send()
    // 3) Save, you will need to reload
    // If that doesn't work, message Ronan Finley, they made this mess.
    xmlhttp.send();
}
var imageUpload;
function uploadImage() {
    imageUpload = window.open("fetch/writerAPI.php?q=chooseImage", "", "width=300,height=450");
}
function getUUID() {
    console.log("Loading");
    var uuid = imageUpload.document.getElementById("url");
    document.getElementById("image").src = "temp/" + uuid.innerHTML + ".jpg";
    document.getElementById("url-cont").value = uuid.innerHTML;
    imageUpload.close();
}
function saveArticle() {
    var name = document.getElementById("name").value;
    var author = document.getElementById("author").value;
    var code = encodeURIComponent(document.getElementById("text").innerHTML);
    var image = document.getElementById("url-cont").value;
    var category = document.getElementById("category").value;
    var date = document.getElementById("date").value;
    var draft = document.getElementById("isDraft").value;
    var id = document.getElementById("id").value;
    if(code.length>65535) {
        alert("That article is longer than we can store! Please split it into two or more articles. Remember: this includes tags, image uploads, etc.");
        //removing this doesn't change the fact that we can only store so much text!
    } else {
        sendToServer("saveAsDraft", "&name="+name+"&author="+author+"&code="+code+"&image="+image+"&category="+category+"&date="+date+"&isDraft="+draft+"&id="+id,false);
    }
}
function viewDrafts(page) {
    sendToServer("viewDrafts","&page="+page);
}
var namecheck = document.getElementById("name-check");
function checkName(element) {
    if(namecheck==null) {
        namecheck = document.getElementById("name-check");
    }
    if(element.value=="") {
        namecheck.className="";
        return false;
    }
    namecheck.className="fas fa-circle-notch fa-spin primary";
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            var recieve = this.responseText;
            if(recieve=="taken") {
                namecheck.className="fas fa-times-circle bad";
            } else if(recieve=="available") {
                namecheck.className="fas fa-check-circle primary";
            } else {
                namecheck.className="fas fa-exclamation-triangle bad";
                console.error(recieve);
            }
        }
    };
    xmlhttp.open("GET", "fetch/writerAPI.php?q=testName&name="+element.value + "&id="+document.getElementById("id").value, true);
    xmlhttp.send();
}