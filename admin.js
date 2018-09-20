var output = document.getElementById("output");
function sendToServer(q,addt) {
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            output.innerHTML = this.responseText;
        }
    }
    xmlhttp.open("GET", "adminapi.php?q=" + q + addt, true);
    xmlhttp.send();
}
function viewArticles(page) {
    sendToServer("viewArticles","&page="+page);
}
function viewUsers(page) {
    sendToServer("viewUsers","&page="+page);
}
function viewLogs(page) {
    sendToServer("viewLogs","&page="+page);
}
function modUser(id) {/*this is called by a return alue from viewUsers(page)*/
    sendToServer("modUser","&id="+id);
}
function delUser(id, act) {
    var conf = document.getElementById("delConf");
    if(act=="conf") {
        conf.innerHTML = "<p class='bad'>Are you <b>ABSOLUTELY SURE</b>? <a href='#' onclick='delUser("+id+",\"reason\")'>Yes, I am sure.</a></p>";
    } else if(act=="reason") {
        conf.innerHTML = `<p class='bad'>Please type a reason to be appended to 
        the end of the undeletable log and re-enter your password</p><input type='text' id='reason' placeholder="Reason for Deletion" autocomplete="off"/>
        <input type='password' id='password' placeholder="Confirm Password" autocomplete="off"/><button class='badButton' onclick='delUser(`+id+`,"submit")'>Submit Deletion</button>`;
    } else if(act=="submit") {
        var reason   = document.getElementById("reason");
        var password = document.getElementById("password");
        sendToServer("delUser", "&del="+id+"&reason="+reason.value+"&password="+password.value);
    }
}
function control(id, act) {
    var conf = document.getElementById("contconf");
    if(act=="conf") {
        conf.innerHTML = `<p class="bad">Are you sure you want to take control of this account? You will not be able to make significant changes and will be logged as security</p>
        <a href="#" onclick="control(`+id+`, 'go')">Yes, I am sure</a> and I accept all the risks and consequences, and I understand that after this any actions made under this account
        could make me a suspect in any internal investigation.`;
    } else if(act == "go") {
        sendToServer("contUser", "&id="+id);
    }
}
function securityToInt($status) {
    if($status == "minimal") {
        return 0;
    } else if($status == "curious") {
        return 1;
    } else if($status == "suspicous") {
        return 2;
    } else if($status == "tight") {
        return 3;
    } else {
        return -1;
    }
}
function viewTools(page) {
    //page is not being used right now
    sendToServer("tools","");
}