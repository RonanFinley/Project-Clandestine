<?php
//ALL STATEMENTS IN HERE ARE PREPARED
session_start();

//get library
include getcwd().'/../api.php';

if($_SESSION['hp_login']!=true) {
    echo '<h1 class="primary">Paragon requires you to have <b>Writer or better</b> authorization to access</h1>';
    logAction("Unknown User ", "Attempt to access writer API without login", "general", "0");
    die();
}
if($_SESSION['auth']!="writer"&&$_SESSION['auth']!="admin") {
    echo '<h1 class="primary">Paragon requires you to have <b>Writer or better</b> authorization to access</h1>';
    logAction($_SESSION['username'], "Attempt to access writer API without writer or better auth", "general", "0");
    die();
}

//user has been authenticated


$q = $_REQUEST['q'];

if($q == null) {
    die('q not defined');
}

if($q=="editArticle") {
    $id = $_REQUEST['id'];
    $draft = $_REQUEST['draft'];
    if($draft==null) {
        $draft = false;
    }
    $link = connectToDatabase();
    if (!$link) {
        echo('could not connect');
        echo '</div>';
        die();
    }
    
    logAction($_SESSION['username'], "Editing article id: ".$id, "modify", "0");
    
    $id      = mysqli_real_escape_string($link, $id);
    $draft   = mysqli_real_escape_string($link, $draft);
    
    $sql=$link->stmt_init();
    
    if($draft=="true") {
        $sql->prepare("SELECT * FROM `drafts` WHERE id=?");
        $sql->bind_param("i",$id);
    } else {
        $sql->prepare("SELECT * FROM `articles` WHERE id=?");
        $sql->bind_param("i",$id);
    }
    $sql->execute();
    $result = getFirstRow($sql->get_result());
    $sql->close();
    
    if($result==null) {
        echo 'Paragon could not find the article. Please contact webmaster, specify "a possible draft error".';
        echo '</div>';
        die();
    }
    loadArticle($result['code'],$result['name'],$result['author'],$result['date'],$result['category'],-1,$result['id'],$draft);
}
if($q=="newArticle") {
    loadArticle("<p>Hello World!</p>", "", "", "", "Science", "noimage", -1, "true");
}
function loadArticle($code, $name, $author, $date, $category, $image, $id, $isDraft){
    //if ID = -1, then it's a new article
    echo '<p>Please don\'t write entire articles in here. This area is for fixing errors, adding custom code, and testing. The best place to write articles is in Google Drive</p>';
    echo '<button class="main-button" onclick="saveArticle()">Save as '.($isDraft=="true"?"Draft":"Published Article").'</button>';
    echo '<div class="grid">
    <div class="grid-50">
        <div id="editor">'.$code.'</div>
    </div>
    <div class="grid-50" >
        <pre id="codeeditor">'.htmlspecialchars($code).'</pre>
    </div>
</div>
<div class="grid">
    <div class="grid-75 grid-gutter-small pixabay">
        <div class="pixabay-header">
            <a href="https://pixabay.com/" target="_blank">
                <img src="fetch/pixabay.png" class="pixabay-logo">
            </a>
            <input class="pixabay-search" placeholder="Search Pixabay" id="searchEntry"/>
            <button class="pixabay-search-button" onclick="searchPixabay(true)">Search</button>
        </div>
        <div class="div-sm"></div>
        <div class="grid pixabay-results" id="pixabay">
            <div class="grid-20 pixabay-display" id="pix-1">
            </div>
            <div class="grid-20 pixabay-display" id="pix-2">
            </div>
            <div class="grid-20 pixabay-display" id="pix-3">
            </div>
            <div class="grid-20 pixabay-display" id="pix-4">
            </div>
            <div class="grid-20 pixabay-display" id="pix-5">
            </div>
        </div>
        <div class="center pixabay-nav">
            <i class="fas fa-chevron-left" onclick="if(page>0){page--;searchPixabay(false)}"></i><span id="pagenum"></span>
            <i class="fas fa-chevron-right" onclick="if(result.totalHits>page*5){page++;searchPixabay(false)}"></i>
        </div>
    </div>
    <input type="hidden" id="isDraft" value="'.$isDraft.'">
    <input type="hidden" id="id" value="'.$id.'">
    <input type="hidden" id="url-cont" value="'.$image.'">
    <div class="grid-25 grid-gutter-small upload-image center" id="upload">
        <h1>Upload</h1>
        <button class="button" onclick="uploadImage()">Choose an Image</button>
    </div>
</div>
<div class="showArticle">
    <select id="category">
        <option value="Science" '.($category=="Science"?"selected":"").'        >Category Science</option>
        <option value="Technology"'.($category=="Technology"?"selected":"").'   >Category Technology</option>
        <option value="Engineering"'.($category=="Engineering"?"selected":"").' >Category Engineering</option>
        <option value="Mathematics"'.($category=="Mathematics"?"selected":"").' >Category Mathematics</option>
        <option value="Intra"'.($category=="Intra"?"selected":"").'             >Category Intra-Post</option>
        <option value="Other"'.($category=="Other"?"selected":"").'             >Category Other</option>
    </select>

    <p><i class="" id="name-check"></i><input type="input" id="name" class="makeArticleTitle" placeholder="Article Title" onkeyup="checkName(this)" value="'.$name.'"></p>
    <p>By <input type="input" id="author" class="makeArticleAuthor" placeholder="Editorial Board" value="'.$author.'"> - Published <input type="date" id="date" value="'.$date.'"></p>
    
    <img src="'.($id==-1?"images/noimage.jpg":($isDraft=="true"?getImageDraft($id):getImageWriter($id))).'" id="image"/>
    <span id="text">'.$code.'</span>
</div>
';
    die();
}
if($q=="getImage") {
    //this is an unobfuscated version of the ajax code in writer.js: DO NOT DELETE
    //xmlhttp.open("GET", "https://pixabay.com/api/?key=9731970-83848caee38e8e6ec49d159c3&q="+search.value+"&image_type=all&per_page=5&page="+page+"&orientation=horizontal", true);
    //SERIOUSLY IF SOMETHING GOES WRONG WE NEED THIS DON'T DELETE
    $pixabay = $_REQUEST['link'];
    $url = gen_uuid();
    $suffix = getcwd()."/../temp/";
    $filename = $suffix.$url.".jpg";
    
    $image = file_get_contents($pixabay);
    file_put_contents($filename, $image);
    
    $source_image_tmp = imagecreatefromjpeg($filename);
    $source_image = imagecreatetruecolor(imagesx($source_image_tmp),imagesy($source_image_tmp));
    imagecopy($source_image,$source_image_tmp,0,0,0,0,imagesx($source_image_tmp),imagesy($source_image_tmp));
    
    $origx = imagesx($source_image);
    $origy = imagesy($source_image);
    
    $dest_imagex = 640;
    $dest_imagey = 420;
    
    $dest_image = imagecreatetruecolor($dest_imagex, $dest_imagey);
    
    imagecopyresampled($dest_image, $source_image, 0, 0, 0, 0, $dest_imagex, 
    $dest_imagey, $origx, $origy);
    
    imagejpeg($dest_image, $filename, 40);  
    
    die($url);
}
if($q=="chooseImage") {
    echo '
    <html>
        <head>
            <style>
                @import url(\'https://fonts.googleapis.com/css?family=Eczar\');
                body {
                    background-color: #2d6fd8;
                    color: white;
                    font-family: \'Eczar\', serif;
                    padding:15px;
                    font-size:30px;
                    border:0;
                    outline:0;
                }
                .button {
                    border: 1px solid #2d6fd8;
                    background-color:white;
                    color:#2d6fd8;
                    padding:5px;
                    border-radius: 2px;
                    font-size:20px;
                }
                .button-file {
                    border: 1px solid #2d6fd8;
                    background-color:white;
                    color:#2d6fd8;
                    padding:5px;
                    border-radius: 2px;
                    font-size:20px;
                    width:100%;
                }
                p {
                    font-size: 14px;
                }
            </style>
        </head>
        <body class="center">
            <h1>Upload</h1>
            <form action="?q=uploadImage" method="post" enctype="multipart/form-data">
                <input class="button-file" type="file" accept=".png, .jpg, .jpeg .gif .svg" id="image-upload" name="files"/>
                <button class="button" type="submit">Save and Upload</button>
            </form>
            <p>NOTE: Uploaded images are resized to a maximum of 640x420 and are converted to 4:3 aspect ratio. HP resesrves the right to replace low-quality images.</p>
        </body>
    </html>
    
    ';
}
if($q=="uploadImage") {
    $files = $_FILES["files"];
    //die( var_dump($files));
    //check if the file is in actuality an image
    $check = getimagesize($_FILES["files"]["tmp_name"]);
    if($check == false) {
        die( "not image" );
    }
    
    $imageFileType = strtolower(pathinfo("temp/".basename($_FILES["files"]["name"]),PATHINFO_EXTENSION));
    //echo( $imageFileType );
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
        && $imageFileType != "gif") {
        die( "not allowed" );
    }
    //die( "TEMP: ".$_FILES["files"]["tmp_name"] );
    $upload = basename($_FILES["files"]["tmp_name"]);
    
    $url = gen_uuid();
    $suffix = getcwd()."/../temp/";
    $filename = $suffix.$url.".jpg";
    
    $target_file = getcwd() . "/../temp/" . $url . "." . $imageFileType;
    //die(var_dump($target_file));
    move_uploaded_file($_FILES["files"]["tmp_name"], $target_file);
    //move_uploaded_file($_FILES["files"]["tmp_name"], getcwd()."/../temp/greg.jpg");
    
    //die();
    $image = file_get_contents($target_file);
    file_put_contents($filename, $image);
    
    $source_image_tmp;
    if($imageFileType=="jpg"||$imageFileType=="jpeg") {
        $source_image_tmp = imagecreatefromjpeg($filename);
    } else if($imageFileType=="png") {
        $source_image_tmp = imagecreatefrompng($filename);
    } else if($imageFileType=="gif") {
        $source_image_tmp = imagecreatefromgif($filename);
    }
    $source_image = imagecreatetruecolor(imagesx($source_image_tmp),imagesy($source_image_tmp));
    imagecopy($source_image,$source_image_tmp,0,0,0,0,imagesx($source_image_tmp),imagesy($source_image_tmp));
    
    $origx = imagesx($source_image);
    $origy = imagesy($source_image);
    
    $dest_imagex = 640;
    $dest_imagey = 420;
    
    $dest_image = imagecreatetruecolor($dest_imagex, $dest_imagey);
    imagecopyresampled($dest_image, $source_image, 0, 0, 0, 0, $dest_imagex,$dest_imagey, $origx, $origy);
    
    imagejpeg($dest_image, $filename, 100);  
    
    die("<span id='url'>$url</span><script>window.opener.getUUID();</script>");
}
if($q=="testName") {
    $link = connectToDatabase();
    if (!$link) {
        echo('could not connect');
        echo '</div>';
        die();
    }
    $name = mysqli_real_escape_string($link, $_REQUEST['name']);
    $id = mysqli_real_escape_string($link, $_REQUEST['id']);
    
    if(strlen($name)>255) {
        die("taken");//this will jsut return an "X"
    }
    
    if($id=="") {
        $id=-1;
    }
    $sql = $link->prepare("SELECT name FROM `drafts` WHERE name = ? UNION SELECT name FROM `articles` WHERE name = ? AND id!=?;");
    $sql->bind_param("ssi",$name,$name,$id);
    $sql->execute();
    $result = $sql->get_result();
    $sql->close();
    
    if(getFirstRow($result)==null) {
        //so what we just did is check if the name is available in both drafts and articles databases.
        die("available");
    }
    die("taken");
}
if($q == "saveAsDraft") {
    //sendToServer("saveAsDraft", "&name="+name+"&author="+author+"&code="+code+"&image="+image,true);
    
    $link = connectToDatabase();
    if (!$link) {
        echo('could not connect');
        echo '</div>';
        die();
    }
    
    $name = $_REQUEST['name'];
    $auth = $_REQUEST['author'];
    $code = $_REQUEST['code'];
    $image = $_REQUEST['image'];
    $category = $_REQUEST['category'];
    $date = $_REQUEST['date'];
    $isDraft = $_REQUEST['isDraft'];
    $id = $_REQUEST['id'];
    $preview = strip_tags($_REQUEST['code']);

    logAction($_SESSION['username'], "Saving article: ".$name, "important", "1");
    
    $tmp = $link->prepare("SELECT name FROM `drafts` WHERE name = ? AND id!=? UNION SELECT name FROM `articles` WHERE name = ? AND id!=?;");
    $tmp->bind_param('sisi', $name, $id, $name, $id);
    $tmp->execute();
    $tmpName = $tmp->get_result();
    $tmp->close();
    //these lines explained:
    //we are preparing a statement, so as to prevent sql injection because amazon web services.
    //we start by creating a prepared statement, as on line 292
    //we then bind parameters. The first input is datatypes in the order they are inputted
    //we are putting in a string, itn, string, then an int in that order
    //then we specify the variables to oshove in there.
    //then we run it. Don't forget to close.
    // https://www.w3schools.com/php/php_mysql_prepared_statements.asp
    
    if(getFirstRow($tmpName)!=null) {
        //so what we just did is check if the name is available in both drafts and articles databases.
        $name.="_".uniqid();
    }
    
    //echo var_dump("5 : ".$name);
    if(strlen($name)==0) {
        $name = "Untitled_".uniqid();
    }
    //echo var_dump("6 : ".$name);
    if($auth=="") {
        $auth = "Editorial Board";
    }
    //echo var_dump("7 : ".$name);
    if($code=="") {
        $code="There is no text for this article, yet!";
    }
    //echo var_dump("8 : ".$name);
    if($date=="") {
        $date = date("Y-m-d");
    }
    //echo var_dump("9 : ".$name);
    if(strlen($preview)>255) {//if the article is larger than 255 characters (sql limit), then we truncate it to 252 characters then add an ellipses (3 characters)
        $preview = substr($preview,0,252)."...";
    }
    $sql = $link->stmt_init();
    if($id==-1) {
        $sql = $link->prepare("INSERT INTO `drafts` (name, author, code, preview, category, date) VALUES (?, ?, ?, ?, ?, ?);");
        $sql->bind_param("ssssss",$name,$auth,$code,$preview,$category,$date);
        $sql->execute();
        if($image!=-1) {
            $largestNumber = 0;
            $sqlrows = "SELECT id FROM `drafts` WHERE name='$name';";
            $result = mysqli_query($link, $sqlrows);
            $largestNumber = getFirstRow($result)['id'];
            rename(getcwd()."/../temp/".$image.".jpg", getcwd()."/../draftimages/".$largestNumber.".jpg");
        }
    } else if($isDraft=="true") {
        $sql = $link->prepare("UPDATE `drafts` SET name=?,author=?,code=?,preview=?,category=?,date=? WHERE id=?");
        $sql->bind_param("ssssssi", $name,$auth,$code,$preview,$category,$date,$id);
        $sql->execute();
        if($image!=-1) { //$image!=-1 will ONLY be true if the image changes
            $largestNumber = intval($id);
            if (file_exists("/../draftimages/".$id)) { unlink ("/../draftimages/".$id); }
            rename(getcwd()."/../temp/".$image.".jpg", getcwd()."/../draftimages/".$largestNumber.".jpg");
        }
    } else if($isDraft=="false") {
        $url = strtolower($name);
        $url = preg_replace("/[^a-z0-9]/", "_", $url);
        $sql->prepare("UPDATE `articles` SET name=?,author=?,code=?,preview=?,category=?,date=?,link=? WHERE id=?");
        $sql->bind_param("sssssssi", $name,$auth,$code,$preview,$category,$date,$url,$id);
        $sql->execute();
        if($image!=-1) {
            $largestNumber = $id;
            if (file_exists("/../images/".$id)) { unlink ("/../images/".$id); }
            rename(getcwd()."/../temp/".$image.".jpg", getcwd()."/../images/".$largestNumber.".jpg");
        }
    } else {
        die("Fatal Error in Draft Boolean");
    }
    $result = $sql->get_result();
    $sql->close();
    $sql = $link->prepare("SELECT MAX(id) FROM `drafts`");
    $last = getFirstRow($sql->get_result());
    $sql->close();
    
    die(substr(strpos($image,"/temp/"),strlen($image)));
    
    //$image = substr(strpos("/temp/")+6,strlen($image)-4);
    if(!$result) echo mysqli_error();
    else {
        echo '<h1 class="primary">Saved</h1>';
    }
    die();
}
if($q=="viewArticles") {
    $page = $_REQUEST['page'];
    if($page<0) {
        die('page num too small');
    }
    $link = connectToDatabase();
    if (!$link) {
        echo('could not connect');
        die();
    }
    $page = mysqli_real_escape_string($link, $page);

    $limit = 25;
    
    $minlimit = $limit * $page;
    $maxlimit = $limit * ( $page + 1 );
    
    $sql = $link->prepare("SELECT * FROM `articles` ORDER BY id DESC LIMIT ?, ?");
    $sql->bind_param("ii",$minlimit,$maxlimit);
    $sql->execute();
    $result = $sql->get_result();
    $sql->close();
    
    $numOfPages = floor(floatval(mysqli_fetch_assoc(mysqli_query($link, "SELECT id FROM `articles`"))["num_rows"]) / floatval($limit));
    echo var_dump(mysqli_query($link, "SELECT id FROM `articles`"));
    //we do not need to prepare the statement immidiiatly preceding this because it doesn't accept any user input
    
    echo 'Page ' . ($page+1) . '/'.($numOfPages+1).' - Showing '.($minlimit+1).'/'.$maxlimit.' sorted by Identification<hr/>';
    
    while($row = $result->fetch_assoc()) {
        echo '<div class="card">';
        echo '<img src="'.getImageWriter($row['id']).'" class="post-image"/>';
        echo '<div>';
        echo '<h1 class="primary cardTitle">'.$row['name'].'</h1>';
        echo '<p class="subTitle">ID: '.$row['id'].' - Written By '.$row['author'].' - Popularity: '.$row['popularity'].' - 
            TOOLS: <a href="?url=article&link='.$row['link'].'">Read</a> - <a href="javascript:editArticle('.$row['id'].',\'false\')" >Modify</a> -
 <a href="fetch/writerAPI.php?q=rescind&id='.$row['id'].'">Rescind</a></p>';
        echo "<p>".$row['preview']."</br/>";
        echo $row['date']."</p>";
        echo '</div>';
        echo '</div>';
    }
    if($page!=0) {
        echo '<button onclick="viewArticles('.($page-1).')" class="button">Previous</button>';
    }
    if($page!=$numOfPages) {
        echo '<button onclick="viewArticles('.($page+1).')" class="button">Next</button>';
    }
    die();
}
if($q=="viewDrafts") {
    $page = $_REQUEST['page'];
    if($page<0) {
        die('page num too small');
    }
    $link = connectToDatabase();
    if (!$link) {
        echo('could not connect');
        die();
    }
    $page = mysqli_real_escape_string($link, $page);

    $limit = 25;
    
    $minlimit = $limit * $page;
    $maxlimit = $limit * ( $page + 1 );
    
    $sql = $link->prepare("SELECT * FROM `drafts` ORDER BY id DESC LIMIT ?, ?");
    $sql->bind_param("ii",$minlimit,$maxlimit);
    $sql->execute();
    $result = $sql->get_result();
    $sql->close();
    
    $numOfPages = floor(floatval(mysqli_num_fields(mysqli_query($link, "SELECT id FROM `articles`"))) / floatval($limit));
    //we do not need to prepare the statement immidiiatly preceding this because it doesn't accept any user input
    
    echo 'Page ' . ($page+1) . '/'.($numOfPages+1).' - Showing '.($minlimit+1).'/'.$maxlimit.' sorted by Identification<hr/>';
    
    while($row = $result->fetch_assoc()) {
        echo '<div class="card">';
        echo '<img src="'.getImageDraft($row['id']).'" class="post-image"/>';
        echo '<div>';
        echo '<h1 class="primary cardTitle">'.$row['name'].'</h1>';
        echo '<p class="subTitle">ID: '.$row['id'].' - Written By '.$row['author'].' - Popularity: '.$row['popularity'].' - 
            TOOLS: <a href="javascript:editArticle(\''.$row['id'].'\',true)">Modify</a> - <a href="fetch/writerAPI.php?q=publish&id='.$row['id'].'">Schedule to Publish @ '.$row['date'].'</a> - 
<a href="fetch/writerAPI.php?q=delete&id='.$row['id'].'" class="bad">Delete Draft (LOGGED)</a></p>';
        echo "<p>".$row['preview']."</br/>";
        echo $row['date']."</p>";
        echo '</div>';
        echo '</div>';
    }
    if($page!=0) {
        echo '<button onclick="viewDrafts('.($page-1).')" class="button">Previous</button>';
    }
    if($page!=$numOfPages) {
        echo '<button onclick="viewDrafts('.($page+1).')" class="button">Next</button>';
    }
    die();
}
if($q=="publish") {
    //well well well. Time to publish!
    
    //retrieve draft
    //generate link
    //prepare insert article
    //execute
    //move & rename image
    //print success page
    
    $pub = $_REQUEST['id'];
    //sleep(5); //5 seconds //remove this when users complain of slow publishing and then say you worked tirelessly to revamp the program
    $link = connectToDatabase();
    if (!$link) {
        echo('could not connect to database; try again later');
        die();
    }
    $sql = $link->prepare("SELECT * FROM `drafts` WHERE id = ?;");
    $sql->bind_param("i", $pub);
    $sql->execute();
    $result = getFirstRow($sql->get_result());
    $sql->close();
    
    if($result==null) {
        die("Sorry! We are having a tinsey bit of trouble getting that article, please retry that or contact webmasters");
        logAction($_SESSION['username'], "Error in publishing article: ".$pub, "error", "0");
    }
    logAction($_SESSION['username'], "Publishing Article: ".$result['name'], "important", "1");
    
    $url = strtolower($result['name']);
    $url = preg_replace("/[^a-z0-9]/", "_", $url); //we are having a problem where this returns null but no error
    if($url==null) {
        echo array_flip(get_defined_constants(true)['pcre'])[preg_last_error()];
        die(' - error occured in URL generation: refer webmasters to publishing function');
        logAction($_SESSION['username'], "Error encountered in URL Generation: ".array_flip(get_defined_constants(true)['pcre'])[preg_last_error()], "error", "0");
    }
    
    $sql = $link->prepare("INSERT INTO `articles`(`name`, `author`, `code`, `link`, `preview`, `category`, `date`, `popularity`) VALUES (?, ?, ?, ?, ?, ?, ?, ?);");
    $zero = 0;//because prepared statements hate integer literals
    $sql->bind_param("ssssssss", $result['name'], $result['author'], $result['code'], $url, $result['preview'], $result['category'], $result['date'], $zero);
    $sql->execute();
    $sql->close();
    $zero = null;//kill it as to not take up memory
    
    //success, now we move the image
    if (file_exists(getcwd()."/../draftimages/".$result['id'].".jpg")) {
        $sqlrows = "SELECT MAX( id ) AS max FROM `articles`;";
        $drafttmp = mysqli_query($link, $sqlrows);
        $draft = getFirstRow($drafttmp);
        $largestNumber = $draft['max'];
        rename(getcwd()."/../draftimages/".$result['id'].".jpg", getcwd()."/../images/".$largestNumber.".jpg");
        if(file_exists(getcwd()."/../images/".$largestNumber.".jpg")) {
            echo 'image successfully moved';
        } else {
            echo 'Image Not Moved: You may need to manually add the image back in, or you may contact a web master to have it moved manually';
        }
    } else {
        echo 'no image to move; skipping';
    }
     
    $sql = $link->prepare("DELETE FROM `drafts` WHERE id=?");
    $sql->bind_param("i", $result['id']);
    $sql->execute();
    $sql->close();
    
    $_POST = array(); //so "Refresh" works*/
    header("Location: ../index.php?url=article&link=".$url);
    die("success; redirecting");
}

//RESCIND

if($q=="rescind") {
    //so something has gone wrong and somebody needs to rescind an article. No big deal!
    //this is basically the publication process but reversed
    $pub = $_REQUEST['id'];
    $link = connectToDatabase();
    if (!$link) {
        echo('could not connect to database; try again later');
        die();
    }
    $sql = $link->prepare("SELECT * FROM `articles` WHERE id = ?;");
    $sql->bind_param("i", $pub);
    $sql->execute();
    $result = getFirstRow($sql->get_result());
    $sql->close();
    
    if($result==null) {
        die("Sorry! We are having a tinsey bit of trouble getting that article, please retry that or contact webmasters");
    }
    logAction($_SESSION['username'], "Rescinding article: ".$result['name'], "security", "1");
    
    $sql = $link->prepare("INSERT INTO `drafts`(`name`, `author`, `code`, `preview`, `category`, `date`) VALUES (?, ?, ?, ?, ?, ?);");
    $sql->bind_param("ssssss", $result['name'], $result['author'], $result['code'], $result['preview'], $result['category'], $result['date']);
    $sql->execute();
    $sql->close();
    
    //success, now we move the image
    if (file_exists(getcwd()."/../images/".$result['id'].".jpg")) {
        $sqlrows = "SELECT MAX( id ) AS max FROM `drafts`;";
        $drafttmp = mysqli_query($link, $sqlrows);
        $draft = getFirstRow($drafttmp);
        $largestNumber = $draft['max'];
        rename(getcwd()."/../images/".$result['id'].".jpg", getcwd()."/../draftimages/".$largestNumber.".jpg");
        if(file_exists(getcwd()."/../draftimages/".$largestNumber.".jpg")) {
            echo 'image successfully moved';
        } else {
            echo 'Image Not Moved: You may need to manually add the image back in, or you may contact a web master to have it moved manually';
        }
    } else {
        echo 'no image to move; skipping';
    }
     
    $sql = $link->prepare("DELETE FROM `articles` WHERE id=?");
    $sql->bind_param("i", $result['id']);
    $sql->execute();
    $sql->close();
    
    $_POST = array(); //so "Refresh" works*/
    echo $url . " - ";
    header("Location: ../index.php?write".$url);
    die("success; redirecting");
}
if($q=="delete") {
    $id = $_REQUEST['id'];
    $link = connectToDatabase();
    if (!$link) {
        echo('could not connect to database; try again later');
        die();
    }
    
    logAction($_SESSION['username'], "Deleting Draft: ".$id, "important", "1");
    
    $sql = $link->prepare("DELETE FROM `drafts` WHERE id = ?;");
    $sql->bind_param("i", $id);
    $sql->execute();
    $result = getFirstRow($sql->get_result());
    $sql->close();
    
    
    $_POST = array(); //so "Refresh" works*/
    header("Location: ../index.php?write".$url);
    die("success; redirecting");
}
die($q." not found");
?>