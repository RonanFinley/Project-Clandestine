<?php
//All statements are prepared
    session_start();
    include getcwd().'/api.php';
?> 
<!DOCTYPE html>
<html>
  <head>
      <?php include getcwd().'/css.php' ?>
      <title>Huntington Post - Paragon</title>
  </head>
  <body class="">
          
        <div id="background">
            
        </div>
        <div id="navbar">
            <h1 class="primary">Huntington Post</h1>
            <div class="right primary">
                <a href="?url=">Home</a>
                <?php
                    if($_SESSION['hp_login']!=true) {
                        echo '<a href="?url=login">Login</a>';
                        echo '<a href="?url=signup">Get a Free Account</a>';
                    } else {
                        echo '<a href="?url=profile">'.$_SESSION['username'].'</a><a href="?url=logout">Log Out</a>';
                        if($_SESSION['auth']=="admin") {
                            echo '<a href="?url=admin">Admin</a><a href="?url=write">Write</a>';
                        } else if($_SESSION['auth']=="insider") {
                            echo '<a href="?url=insider">Insider</a>';
                        } else if($_SESSION['auth']=="writer") {
                            echo '<a href="?url=write">Write</a>';
                        }
                    }
                ?>
            </div>
            
        </div>
        <div class="div-lg"></div>
        <?php
            //is it blank? if so, home.
            if(strlen($_REQUEST['url'])==0) {
                //home
                
                //POPULAR ARTICLES
                echo '<div class="article"><div class="body"><h1 class="primary secTitle">Popular Articles</h1>';
                $link = connectToDatabase();
                if (!$link) { 
                    die('Could not connect: ' . mysqli_error() . ' - Please contact Admin'); //streamline in production
                }
                $sql = $link->prepare("SELECT * FROM `articles` WHERE date <= ? ORDER BY popularity DESC LIMIT 0 , 3");
                $sql->bind_param("s", date("Y-m-d"));
                $sql->execute();
                $result = $sql->get_result();
                if($result->num_rows == 0) {
                    echo '<h1 class="primary center">No Articles Found, Please wait for admins to add articles</h1>';
                } else {
                    while($row = $result->fetch_assoc()) {
                        echo '<a href="?url=article&link='.$row['link'].'" class="artLink"><div class="card">';
                        echo '<img src="'.getImage($row['id']).'" class="post-image"/>';
                        echo '<div>';
                        echo '<h1 class="primary cardTitle">'.$row['name'].'</h1>';
                        echo '<p class="subTitle">Written By '.$row['author'].' - Category: '.$row['category'].' - Likes: '.$row['popularity'].'</p>';
                        echo "<p>".$row['preview']."</br/>";
                        echo $row['date']."</p>";
                        echo '</div>';
                        echo '</div></a>';
                    }
                }
                echo '<a href="?url=all&sort=Popular">All Articles sorted by Most Popular</a><div class="div-lg"></div></div></div><div class="div-lg"></div>';
                
                //ALL NEW
                echo '<div class="article"><div class="body"><h1 class="primary secTitle">Recent Articles</h1>';
                $sql->prepare("SELECT * FROM `articles` WHERE date <= ? ORDER BY date DESC LIMIT 0 , 3");
                $sql->bind_param("s", date("Y-m-d"));
                $sql->execute();
                $result = $sql->get_result();
                listAsArticles($result);
                echo '<a href="?url=all&sort=Recent">All Articles sorted by Most Recent</a><div class="div-lg"></div></div></div><div class="div-lg"></div>';
                
                //SCIENCE
                echo '<div class="article"><div class="body"><h1 class="primary secTitle">Science Articles</h1>';
                $sql->prepare("SELECT * FROM `articles` WHERE date <= ? AND category='Science' ORDER BY popularity DESC LIMIT 0 , 3");
                $sql->bind_param("s", date("Y-m-d"));
                $sql->execute();
                $result = $sql->get_result();
                listAsArticles($result);
                echo '<a href="?url=all&sort=Science">All Science Articles</a><div class="div-lg"></div></div></div><div class="div-lg"></div>';
                
                //TECHNOLOGY
                echo '<div class="article"><div class="body"><h1 class="primary secTitle">Technology Articles</h1>';
                $sql->prepare("SELECT * FROM `articles` WHERE date <= ? AND category='Technology' ORDER BY popularity DESC LIMIT 0 , 3");
                $sql->bind_param("s", date("Y-m-d"));
                $sql->execute();
                $result = $sql->get_result();
                listAsArticles($result);
                echo '<a href="?url=all&sort=Technology">All Technology Articles</a><div class="div-lg"></div></div></div><div class="div-lg"></div>';
                
                //Engineering
                echo '<div class="article"><div class="body"><h1 class="primary secTitle">Engineering Articles</h1>';
                $sql->prepare("SELECT * FROM `articles` WHERE date <= ? AND category='Engineering' ORDER BY popularity DESC LIMIT 0 , 3");
                $sql->bind_param("s", date("Y-m-d"));
                $sql->execute();
                $result = $sql->get_result();
                listAsArticles($result);
                echo '<a href="?url=all&sort=Engineering">All Engineering Articles</a><div class="div-lg"></div></div></div><div class="div-lg"></div>';
                
                //Mathematics
                echo '<div class="article"><div class="body"><h1 class="primary secTitle">Mathematics Articles</h1>';
                $sql->prepare("SELECT * FROM `articles` WHERE date <= ? AND category='Mathematics' ORDER BY popularity DESC LIMIT 0 , 3");
                $sql->bind_param("s", date("Y-m-d"));
                $sql->execute();
                $result = $sql->get_result();
                listAsArticles($result);
                echo '<a href="?url=all&sort=Mathematics">All Mathematics Articles</a><div class="div-lg"></div></div></div><div class="div-lg"></div>';
                
                //INTRA
                echo '<div class="article"><div class="body"><h1 class="primary secTitle">Intra-Post Articles</h1>';
                $sql->prepare("SELECT * FROM `articles` WHERE date <= ? AND category='Intra' ORDER BY popularity DESC LIMIT 0 , 3");
                $sql->bind_param("s", date("Y-m-d"));
                $sql->execute();
                $result = $sql->get_result();
                listAsArticles($result);
                echo '<a href="?url=all&sort=Intra">All Intra-Post Articles</a><div class="div-lg"></div></div></div><div class="div-lg"></div>';
                
                //OTHER
                echo '<div class="article"><div class="body"><h1 class="primary secTitle">Other Articles</h1>';
                $sql->prepare("SELECT * FROM `articles` WHERE date <= ? AND category='EOther' ORDER BY popularity DESC LIMIT 0 , 3");
                $sql->bind_param("s", date("Y-m-d"));
                $sql->execute();
                $result = $sql->get_result();
                listAsArticles($result);
                echo '<a href="?url=all&sort=Other">All Other Articles</a><div class="div-lg"></div></div></div><div class="div-lg"></div>';
                
                echo '</div>';
                //END
                $sql->close();
            } else {
                switch ($_REQUEST['url']) { //if there is not an "else" statement above this, you've dearly screwed up
                    case "article":
                        $link = connectToDatabase();
                        $articleID = $_REQUEST['link'];
                        if (!$link) { 
                            die('Could not connect: ' . mysqli_error() . ' - Please contact Admin'); //streamline in production //lol never mind
                        }
                        echo '<div class="article"><div class="body showArticle">';
                        $sql = $link->prepare("SELECT * FROM `articles` WHERE link=?;");
                        $sql->bind_param("s", $articleID);
                        $sql->execute();
                        $result = $sql->get_result();
                        $sql->close();
                        
                        $id = -1;
                        
                        if($result->num_rows != 1) {
                            echo '<h1 class="primary">Could not find article: ID name '.$articleID.'</h1>';
                        } else {
                            while($row = $result->fetch_assoc()) {
                                $id = $row['id'];
                                echo '<h1 class="primary">'.$row['name'].'</h1>';
                                echo '<p>By '.$row['author'].' - Published '.$row['date'].'</p>';
                                echo '<img src="'.getImage($row['id']).'"/>';
                                echo '<span>'.$row['code'].'</span>';
                                echo '<div class="div-sm"></div>';
                                echo '<div class="spacer">';
                                if($_SESSION['hp_login']==true) {
                                    $sqllike = "SELECT * FROM favs WHERE userID = ".$_SESSION['id']." AND articleID = ".$row['id'];
                                        //no need to prepare this sql statement, does not get user input
                                    $likes = mysqli_query($link, $sqllike);
                                    
                                    if ($likes->num_rows > 0) {
                                        echo '<a href="?url=profile">You liked this Article on '.getFirstRow($likes)['date'].'</a>';
                                    } else {
                                        echo '<button class="button" onclick="like('.$row['id'].', this)">Add to Liked Articles</button>';
                                        echo '<script>';
                                        include getcwd().'/fetch/like.js';
                                        echo '</script>';
                                    }
                                } else {
                                    echo '<a href="?url=login">Login to Like this Article</a>';
                                }
                                echo '</div>';//end spacer
                                echo '<div class="div-lg"></div>';
                                echo '</div></div><div class="div-lg"></div>';
                                
                                echo '<div class="article"><div class="body"><h1 class="primary secTitle">Recent Articles</h1>';
                                $sql = $link->prepare("SELECT * FROM `articles` WHERE date <= ? AND id != ".$id." ORDER BY date DESC LIMIT 0 , 5");
                                $sql->bind_param("s", date("Y-m-d"));
                                $sql->execute();
                                $result = $sql->get_result();
                                listAsArticles($result);
                                echo '<div class="div-lg"></div></div></div><div class="div-lg"></div>';
                                
                                echo '<div class="article"><div class="body"><h1 class="primary secTitle">Popular Articles</h1>';
                                $sql = $link->prepare("SELECT * FROM `articles` WHERE date <= ? AND id != ".$id."  ORDER BY popularity DESC LIMIT 0 , 5");
                                $sql->bind_param("s", date("Y-m-d"));
                                $sql->execute();
                                $result = $sql->get_result();
                                listAsArticles($result);
                                echo '<div class="div-lg"></div></div></div><div class="div-lg"></div>';
                                
                                echo '<div class="article"><div class="body"><h1 class="primary secTitle">Hidden Gems</h1>';
                                $sql = $link->prepare("SELECT * FROM `articles` WHERE date <= ? AND id != ".$id."  ORDER BY date ASC LIMIT 0 , 5");
                                $sql->bind_param("s", date("Y-m-d"));
                                $sql->execute();
                                $result = $sql->get_result();
                                listAsArticles($result);
                                echo '<div class="div-lg"></div></div></div><div class="div-lg"></div>';
                                
                            }
                        }
                        break;
                    case "all":
                        $sortIn = $_REQUEST['sort'];
                        $link = connectToDatabase();
                        echo '<div class="article"><div class="body"><h1 class="primary secTitle">'.$sortIn.' Articles</h1>';
                        $sql = $link->stmt_init();
                        if($sortIn=="Recent") {
                            $sql->prepare("SELECT * FROM `articles` WHERE date <=? ORDER BY date ASC");
                        } else if($sortIn=="Popular") {
                            $sql->prepare("SELECT * FROM `articles` WHERE date <=? ORDER BY popularity DESC");
                        } else if($sortIn=="Science") {
                            $sql->prepare("SELECT * FROM `articles` WHERE date <=? AND category='Science' ORDER BY popularity DESC");
                        } else if($sortIn=="Technology") {
                            $sql->prepare("SELECT * FROM `articles` WHERE date <=? AND category='Technology' ORDER BY popularity DESC");
                        } else if($sortIn=="Engineering") {
                            $sql->prepare("SELECT * FROM `articles` WHERE date <=? AND category='Engineering' ORDER BY popularity DESC");
                        } else if($sortIn=="Mathematics") {
                            $sql->prepare("SELECT * FROM `articles` WHERE date <=? AND category='Mathematics' ORDER BY popularity DESC");
                        } else if($sortIn=="Other") {
                            $sql->prepare("SELECT * FROM `articles` WHERE date <=? AND category='Other' ORDER BY popularity DESC");
                        } else if($sortIn=="Intra") {
                            $sql->prepare("SELECT * FROM `articles` WHERE date <=? AND category='Intra' ORDER BY popularity DESC");
                        } else {
                            $sql->prepare("SELECT * FROM `articles` WHERE date <=? ORDER BY date ASC");
                        }
                        $sortIn = null;

                        $sql->bind_param("s", date("Y-m-d"));
                        $sql->execute();
                        $result = $sql->get_result();
                        listAsArticles($result);
                        $sql->close();
                        echo '<div class="div-lg"></div></div></div>';
                        break;
                    case "profile":
                        if($_REQUEST['username']==null) { //not a specific user
                            //we are not going to operate this on a auto incrementing basis, because that presents a security risk
                            if($_SESSION['hp_login']!=true) {
                                echo '<div class="article-xxs">
                                <div class="body">
                                    <h1 class="center primary">You need to be logged in to view your profile</h1>
                                    <a href="?url=" class="center">Home</a><a href="?url=login" class="center">Login</a>
                                </div>
                            </div>';
                            } else {
                                //they are logged in
                                echo '<div class="article"><div class="body"><h1 class="primary secTitle">Profile: '.$_SESSION['username'].'</h1>';
                                echo '<div class="border-bottom article"><p>Your liked Articles!</p>';
                                echo '<p>To share your liked articles, copy this link: <a href="https://'.$_SERVER['SERVER_NAME']."/?url=profile&username=".$_SESSION['username'].'">
                                    '.$_SERVER['SERVER_NAME']."/?url=profile&username=".$_SESSION['username'].'</a></p></div>';
                                $link = connectToDatabase();
                                    if (!$link) { 
                                        die('Could not connect: ' . mysqli_error() . ' - Please contact Admin');
                                    }
                                
                                //retrieve liked articles
                                    $likes = mysqli_query($link, "SELECT * FROM `favs` WHERE userID = ".$_SESSION['id']." ORDER BY date DESC");
                                    //no need to prepare; no user input
                                    if($likes->num_rows == 0) {
                                        echo '<h1 class="primary center">No Liked Articles! Go find some interesting stuff!</h1>';
                                    } else {
                                        while($liked = $likes->fetch_assoc()) {
                                            $row = getFirstRow(mysqli_query($link, "SELECT * FROM articles WHERE id = ".$liked['articleID']));
                                            if($row==null) {
                                                mysqli_query($link, "DELETE FROM `favs` WHERE id=".$liked['id']);
                                            }
                                            //no need to prepare; no user input
                                            echo '<a href="?url=article&link='.$row['link'].'" class="artLink"><div class="card">';
                                            echo '<img src="'.getImage($row['id']).'" class="post-image"/>';
                                            echo '<div>';
                                            echo '<h1 class="primary cardTitle">'.$row['name'].'</h1>';
                                            echo '<p class="subTitle">Written By '.$row['author'].' - Liked on: '.$liked['date'].'</p>';
                                            echo "<p>".$row['preview']."</br/>";
                                            echo $row['date']."</p>";
                                            echo '</div>';
                                            echo '</div></a>';
                                        }
                                    }
                                echo '<div class="div-lg"></div></div>';
                            }
                        } else {
                            //we have a specific user in mind
                            echo '<div class="article"><div class="body"><h1 class="primary secTitle">Profile: '.htmlspecialchars($_REQUEST['username']).'</h1>';
                            echo '<div class="border-bottom article"><p>'.htmlspecialchars($_REQUEST['username']).'\'s liked Articles!</p></div>';
                            $link = connectToDatabase();
                            //retrieve liked articles
                                if (!$link) { 
                                    die('Could not connect: ' . mysqli_error() . ' - Please contact Admin');
                                }
                                //get user's id number
                                $sql = $link->prepare("SELECT * FROM users WHERE username = ?;");
                                $sql->bind_param("s", htmlspecialchars($_REQUEST['username']));
                                $sql->execute();
                                $userID = getFirstRow($sql->get_result());
                                $userID = $userID['id'];
                                if($userID == null) {
                                    echo '<h1 class="center bad">Cannot find Profile! Make sure you copied the link correctly!</h1>';
                                }
                                
                                $likes = mysqli_query($link, "SELECT * FROM `favs` WHERE userID = ".$userID." ORDER BY date DESC");
                                if($likes->num_rows == 0) {
                                    echo '<h1 class="primary center">No Liked Articles! Go find some interesting stuff!</h1>';
                                } else {
                                    while($liked = $likes->fetch_assoc()) {
                                        $row = getFirstRow(mysqli_query($link, "SELECT * FROM articles WHERE id = ".$liked['articleID']));
                                        //no need to prepare; no user input
                                        echo '<a href="?url=article&link='.$row['link'].'" class="artLink"><div class="card">';
                                        echo '<img src="'.getImage($row['id']).'" class="post-image"/>';
                                        echo '<div>';
                                        echo '<h1 class="primary cardTitle">'.$row['name'].'</h1>';
                                        echo '<p class="subTitle">Written By '.$row['author'].' - Liked on: '.$liked['date'].'</p>';
                                        echo "<p>".$row['preview']."</br/>";
                                        echo $row['date']."</p>";
                                        echo '</div>';
                                        echo '</div></a>';
                                    }
                                }
                            echo '<div class="div-lg"></div></div>';
                        }
                        break;
                    case "login":
                        if($_SESSION['hp_login']==true) {
                            $_POST = array(); //so "Refresh" works*/
                            header("Location: index.php?url=");
                            die();
                        }
                        if($_POST['username']!=null) {
                            $login = login();
                            if($login=="true") { //in api.php in root folder
                                header("Location: index.php?url=");
                                /*echo '<script type="text/javascript">
                                window.location.href = \'http://www.google.com.au/\';
                                </script>';*/
                                die();
                            } else if($login=="false") {
                                echo '<div class="article-xxs">
                                <div class="body">
                                    <h1 class="center bad">Wrong Username or Password!</h1>
                                    <a href="?url=" class="center">Cancel</a>
                                    <a href="?url=signup" class="center">Sign Up</a>
                                    <form method="post" action="?url=login">
                                        <div>
                                            <input type="text" placeholder="Username" name="username">
                                            <input type="password" placeholder="Password" name="password">
                                        </div>
                                        <button type="submit" id="submit" border="0">Enter</button>
                                    </form>
                                </div>
                            </div>';
                            $_POST = array(); //so "Refresh" works*/
                            }
                        } else {
                            echo '<div class="article-xxs">
                                <div class="body">
                                    <h1 class="center primary">Log In</h1>
                                    <a href="?url=" class="center">Cancel</a>
                                    <a href="?url=signup" class="center">Sign Up</a>
                                    <form method="post" action="?url=login">
                                        <div>
                                            <input type="text" placeholder="Username" name="username">
                                            <input type="password" placeholder="Password" name="password">
                                        </div>
                                        <button type="submit" id="submit" border="0">Enter</button>
                                    </form>
                                </div>
                            </div>';
                        }
                        $_POST = array(); //so "Refresh" works*/
                        break;
                    case "signup":
                        $errors = "";
                        if($_REQUEST['username']!=null) {
                            $cont = true;
                            $link            = connectToDatabase();
                            $username        = htmlspecialchars(mysqli_real_escape_string($link, $_REQUEST['username']));
                            $password        = mysqli_real_escape_string($link, $_REQUEST['password']);
                            $repeatpassword  = mysqli_real_escape_string($link, $_REQUEST['repeatpassword']);
                            
                            $sql = $link->prepare("SELECT * FROM users WHERE username=?");
                            $sql->bind_param("s", $username);
                            $sql->execute();
                            $result = $sql->get_result();
                            $sql->close();
                            
                            $row = getFirstRow($result);
                            if(strlen($username)<5) {
                                $errors.="Username not long enough<br/>";
                                $cont = false;
                            }
                            if($row!=null) {
                                $errors.="Username Taken!<br/>";
                                $cont = false;
                            }
                            if(strlen($password)<8) {
                                $errors.="Password must be at least 8 characters long!<br/>";
                                $cont = false;
                            }
                            if($password!=$repeatpassword) {
                                $errors.="Passwords do not match<br/>";
                                $cont = false;
                            }
                            if($cont==true) {//if there are no errors...
                                $sql     = $link->prepare("INSERT INTO users (username,password,auth) VALUES (?,?,'user')");
                                $sql->bind_param("ss", $username, hashPass($password));
                                $sql->execute();
                                $result  = $sql->get_result();
                                $sql->close();
                                $_POST   = array(); //so "Refresh" works
                                header_remove();
                                header("Location: index.php?url=login");
                                die();
                            }
                            $_POST = array();
                        }
                        echo '<div class="article-xxs">
                            <div class="body">
                                <h1 class="center primary">Sign Up</h1>
                                <a href="?url=" class="center">Cancel</a>
                                <a href="?url=login" class="center">Log In</a>
                                <form method="post" action="?url=signup">
                                    <div>
                                        <p class="bad">'.$errors.'</p>
                                        <input type="text" placeholder="Username" name="username" onkeyup="testUsername(this)">
                                        <p id="output"></p>
                                        <input type="password" placeholder="Password" name="password" id="pass" onkeyup="passwords()">
                                        <input type="password" placeholder="Repeat Password" name="repeatpassword" id="reppass" onkeyup="passwords()">
                                        <p id="passout"></p>
                                    </div>
                                    <p>By signing up you agree to our <a href="?url=privacy">Privacy Policy</a> and <a href="?url=terms">Terms of Service</a></p>
                                    <button type="submit" id="submit" border="0">Enter</button>
                                </form>
                            </div>
                        </div>';
                        
                        echo '
                            <script>
                                var output = document.getElementById("output");
                                function testUsername(obj) {
                                    if(obj.value=="") {
                                        output.innerHTML = "Enter Username";
                                    } else if(obj.value.length<5) {
                                        output.innerHTML = "Username must be 5 or more Characters";
                                    } else {
                                        var xmlhttp = new XMLHttpRequest();
                                        xmlhttp.onreadystatechange = function() {
                                            if (this.readyState == 4 && this.status == 200) {
                                                if(this.responseText=="available") {
                                                    output.innerHTML = "Username Available!";
                                                } else {
                                                    output.innerHTML = "That is Taken";
                                                }
                                            }
                                        }
                                        xmlhttp.open("GET", "fetch/username.php?name=" + obj.value, true);
                                        xmlhttp.send();
                                    }
                                }
                                var pass1 = document.getElementById("pass");
                                var pass2 = document.getElementById("reppass");
                                var passout = document.getElementById("passout");
                                function passwords() {
                                    passout.innerHTML = ""
                                    if(pass1.length<8) {
                                        passout.innerHTML += "Your password must be 8+ characters";
                                    }
                                    if(pass1.value != pass2.value) {
                                        passout.innerHTML += "Passwords must Match!";
                                    }
                                }
                            </script>
                        ';
                        break;
                    case "privacy":
                        echo '<div class="article"><div class="body"><h1 class="primary secTitle">Privacy Policy</h1>';
                        echo 'Welcome to the Huntington post! We are extremely lax on our data collection, but apparently this is required by law to have. So, here we go!';
                        echo ' Huntington Post does not sell your data. Huntington Post does not rent your data to anyone. Huntington post does not actually allow humans to see the data it gets.
 The Lead Editor really hates data collection for profit, so that\'s how we built it. We do, however, have to collect data for the service to function properly. For example, if you create an
 account, we store that data. Well, it\'s self explanatory, but we might as well include it. We store all of your "likes", or "favourites", on the server, so that you can log on with any device.
 We also allow people to view other\'s profiles, and you are given a unique link to do so with your friends. You do not need to use this feature, however, and make it impossible for others to
 view your profile. It is really easy: don\'t share the link. No personally identifiable information is recorded unless you choose to do so by making your username something personal. We no
 longer use Google Analytics. We do not use Facebook "like" buttons that like to track you. Really, we hate trackers, and we will not become that.';
                        echo '<div class="div-lg"></div></div></div>';
                        break;
                    case "terms":
                    echo '<div class="article"><div class="body"><h1 class="primary secTitle">Privacy Poolicy</h1>';
                    echo 'Welcome to the Huntington Post! We are extremely lax on our terms, so you can do almost whatever and we will not require you to purchase a lemur named george baskin.
We literally only have this because of legal issues. We reserve the right to store information you <i>Explicitly</i> provide to us (i.e. your username). You are not allowed to break our
website which we have worked so hard to make for you. Please do not extract our code. Feel free to reverse engineer it (That is how little we care). We reserve the right to rescind articles
without notice (Sometimes articles just become outdated or were never meant to be published.) We reserve the right to remove your account and/or your likes, but will only be done so in
accordance with these terms. Do not use our name in a similar service or simply by reference without attribution. You agree to show at least one article to a friend (or not). Yea, that about covers it.
Have a good day!';
                    echo '<div class="div-lg"></div></div></div>';
                        break;
                    case "logout":
                        clearData();
                        break;
                    case "admin":
                        if($_SESSION['auth']!="admin") {
                            //if they are not signed into an admin account, kick them out.
                            $_POST   = array(); //so "Refresh" works
                            header("Location: index.php?url=login");
                            die();
                        }
                        echo '<div class="article"><div class="body"><h1 class="primary secTitle">Administrative Centre</h1>';
                        echo '<div class="spacer article-sm admin-toolbar">';
                            echo '<button onclick="viewArticles(0)" >View/Edit Articles</button>';
                            echo '<button onclick="viewUsers(0)">User Settings</button>';
                        echo '</div>';
                        //echo '<div class="div-lg"></div>';
                        echo '<div id="output"></div>';
                        echo '<div class="div-lg"></div></div></div>';
                        
                        echo '<script src="admin.js"></script>';
                        break;
                    case "write":
                        if($_SESSION['auth']!="admin"&&$_SESSION['auth']!="writer") {
                            //if they are not signed into an writer or admin account, kick them out.
                            $_POST   = array(); //so "Refresh" works
                            header("Location: index.php?url=login");
                            die();
                        }
                        echo '<script src="libraries/quill/quill.js"></script>';
                        echo '<div class="article"><div class="body"><h1 class="primary secTitle">Writer\'s Centre</h1>';
                        echo '<div class="spacer article admin-toolbar">';
                            echo '<button onclick="viewArticles(0)" >All Articles</button>';
                            echo '<button onclick="viewDrafts(0)">View Drafts</button>';
                            echo '<button onclick="newArticle()">New Article</button>';
                        echo '</div>';
                        echo '<p class="primary" style="display:block;">Your editor is our top priority. No kidding. Need anything done? Post it in the Web Development Channel in Slack or email support@incode-labs.com. We will do pretty much anything you need, because this is your editor.</p>';
                        echo '<div id="output"></div>';
                        echo '<div class="div-sm"></div>
                        <div class="div-lg"></div></div></div>';
                        
                        echo '<script src="fetch/writer.js"></script>';
                        /*This script HAS to be included natively*/
                        echo '<script src="https://caitfinley.ipage.com/incode-labs.com-redirect/splicer/src-noconflict/ace.js" type="text/javascript" charset="utf-8"></script>
<script>/*global Quill editor ace*/
var result;
function invokeEditor() {
    var outtext = document.getElementById("text");
	var editor = ace.edit("codeeditor");
	editor.setTheme("ace/theme/chrome");
	editor.session.setMode("ace/mode/php");
    editor.getSession().setUseWrapMode(true);

	editor.$blockScrolling = Infinity;
	editor.setOptions({
      fontSize: "12pt"
    });
	
    var quill = new Quill(\'#editor\', {
        modules: {
            toolbar: [
                [\'bold\', \'italic\', \'underline\', \'strike\'],        // toggled buttons
                [\'blockquote\', \'code-block\'],

                [{ \'header\': 1 }, { \'header\': 2 }],               // custom button values
                [{ \'list\': \'ordered\'}, { \'list\': \'bullet\' }],
                [{ \'script\': \'sub\'}, { \'script\': \'super\' }],      // superscript/subscript
                [{ \'indent\': \'-1\'}, { \'indent\': \'+1\' }],          // outdent/indent
                [{ \'direction\': \'rtl\' }],                         // text direction

                [{ \'size\': [\'small\', false, \'large\', \'huge\'] }],  // custom dropdown
                [{ \'header\': [1, 2, 3, 4, 5, 6, false] }],
                [ \'link\', \'image\', \'video\', \'formula\' ],          // add\'s image support
                [{ \'color\': [] }, { \'background\': [] }],          // dropdown with defaults from theme
                [{ \'font\': [] }],
                [{ \'align\': [] }],
            ],
        },
        theme: \'snow\',
    });
    quill.on(\'text-change\', function(delta, oldDelta, source) {
        if(editor.isFocused()==false) {
            var prs = quill.container.firstChild.innerHTML;
            prs = prs.replace(/><\//g,">\n</");
            editor.blur();
            editor.setValue(prs);
            editor.clearSelection();
            outtext.innerHTML = quill.container.firstChild.innerHTML;
        }
    });
    editor.on("change", function(e) {
        if(editor.isFocused()) {
            quill.blur();
            quill.container.firstChild.innerHTML = editor.getValue();
            outtext.innerHTML = editor.getValue();
        }
    });
    $("#pixabay").change(function() {
        $("#upload").css("height", $("#pixabay").height());
    });
}
</script>';
                        break;
                    case "insider":
                        /**
                         *The insider program would be for specific people who
                         * are writing /about/ the huntington post for whatever 
                         * reason. This would grant them early access to
                         * unpublished articles, character notes, etc. to write
                         * about in their own journals, or for people who we are
                         * specifically good friends with or are thinking about
                         * joining HP
                         * 
                         *This is not to be implemented unless very bored. This
                         * would be implemented if we did get a request for
                         * access.
                         **/
                        break;
                    default:
                        echo '<div class="article-xxs">
                            <div class="body">
                                <h1 class="center primary">What are you on about?</h1>
                                <div class="smallMessage">
                                    <p>Your lost. 404, not found. Space monkeys. Whatever. - Web Dev Board</p>
                                </div>
                            </div>
                        </div>';
                        //404
                    }
                }
        ?>
        
        <div class="div-lg"></div>
        <div class="body stickybottom">www.Incode-Labs.com - www.HuntingtonPost.org - Paragon - COMPLEX- TESTING BRANCH: Do Not Distribute</div>
        <?php /*include getcwd().'/background-old.php'*/ ?>
  </body>
</html>