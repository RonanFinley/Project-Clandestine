function like(id, call) {
    var xmlhttp = new XMLHttpRequest();
    console.log("The function has been bloody called - " + xmlhttp);
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            console.log("Response: " + this.responseText);
            if(this.responseText=="not signed in") {
                call.innerHTML = "You need to be signed in to add this article to your likes";
                call.onclick = "window.location.assign('?url=login')";
            } else if(this.responseText=="could not connect") {
                call.innerHTML = "There was an error connecting to the server; pleases try again later";
                call.onclick = "";
            } else {
                call.innerHTML = "Successfully added to your Likes!";
                call.onclick = "window.location.assign('?url=profile')";
            }
        }
    };
    xmlhttp.open("GET", "fetch/like.php?article=" + id, true);
    console.log("Sending: fetch/like.php?article=" + id)
    xmlhttp.send();
    console.log("sent");
    call.innerHTML = "...";
    call.onclick = "";
}