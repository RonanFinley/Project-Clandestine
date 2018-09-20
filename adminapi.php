<?php
//ALL STATEMENTS PREPARED
session_start();

if($_SESSION['hp_login']!=true) {
    echo '<h1 class="primary">Paragon requires you to have <b>Admin</b> authorization to access</h1>';
    die();
}
if($_SESSION['auth']!="admin") {
    echo '<h1 class="primary">Paragon requires you to have <b>Admin</b> authorization to access</h1>';
    die();
}

//user has been authenticated

//get library
include getcwd().'/api.php';

$q = $_REQUEST['q'];

if($q == null) {
    die('q not defined');
}

//test for specific action
/*
 __      ___                           _   _      _           
 \ \    / (_)               /\        | | (_)    | |          
  \ \  / / _  _____      __/  \   _ __| |_ _  ___| | ___  ___ 
   \ \/ / | |/ _ \ \ /\ / / /\ \ | '__| __| |/ __| |/ _ \/ __|
    \  /  | |  __/\ V  V / ____ \| |  | |_| | (__| |  __/\__ \
     \/   |_|\___| \_/\_/_/    \_\_|   \__|_|\___|_|\___||___/
*/

if($q == "viewArticles") {
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
    
    $numOfPages = floor(floatval(mysqli_num_fields(mysqli_query($link, "SELECT id FROM `articles`"))) / floatval($limit));
    
    echo 'Page ' . ($page+1) . '/'.($numOfPages+1).' - Showing '.($minlimit+1).'/'.$maxlimit.' sorted by Identification<hr/>';
    
    while($row = $result->fetch_assoc()) {
        echo '<div class="card">';
        echo '<img src="'.getImage($row['id']).'" class="post-image"/>';
        echo '<div>';
        echo '<h1 class="primary cardTitle">'.$row['name'].'</h1>';
        echo '<p class="subTitle">ID: '.$row['id'].' - Written By '.$row['author'].' - Popularity: '.$row['popularity'].' - 
            TOOLS: <a href="?article&id='.$row[id].'">Read</a> - <a href="#" onclick="modify('.$row[id].')">Modify</a></p>';
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
/*
 __      ___               _    _                   
 \ \    / (_)             | |  | |                  
  \ \  / / _  _____      _| |  | |___  ___ _ __ ___ 
   \ \/ / | |/ _ \ \ /\ / / |  | / __|/ _ \ '__/ __|
    \  /  | |  __/\ V  V /| |__| \__ \  __/ |  \__ \
     \/   |_|\___| \_/\_/  \____/|___/\___|_|  |___/
                                                    
                                                    */
if($q == "viewUsers") {
    $page = $_REQUEST['page'];
    echo '<div class="article">';
    if($page<0) {
        die('page num too small');
        echo '</div>';
    }
    $link = connectToDatabase();
    if (!$link) {
        echo('could not connect');
        die();
        echo '</div>';
    }
    
    $page = mysqli_real_escape_string($link, $page);

    $limit = 25;
    
    $minlimit = $limit * $page;
    $maxlimit = $limit * ( $page + 1 );
    
    $sql = $link->prepare("SELECT * FROM `users` ORDER BY id DESC LIMIT ?, ?");
    $sql->bind_param("ii",$minlimit,$maxlimit);
    $sql->execute();
    $result = $sql->get_result();
    $sql->close();
    
    $numOfPages = floor(floatval(mysqli_num_fields(mysqli_query($link, "SELECT id FROM `users`"))) / floatval($limit));
    
    echo 'Page ' . ($page+1) . '/'.($numOfPages+1).' - Showing '.($minlimit+1).'/'.$maxlimit.' sorted by Identification<hr/>';
    echo '<p class="bad">Viewing an account is automatically logged as a possible breach of security, as well as anything you do inside the account. Any attempt to compromise any part of Huntington Post:
    Clandestine or a violation of any policies will result in a loss of Admin Authentication and will, at minimum, be reduced to Writer, with possible dismissal from the Huntington Post.</p>';
    echo '<table><tr><th>ID</th><th>Username</th><th>Password</th><th>Auth</th></tr>';
    
    while($row = $result->fetch_assoc()) {
        echo '<tr>';
        echo '<td>'.$row['id'].'</td>';
        echo '<td>'.$row['username'].'</td>';
        echo '<td>'.$row['password'].'</td>';
        echo '<td>'.$row['auth'].'</td>';
        if($row['auth']=='admin') {
            echo '<td class="bad">Denied</td>';
        } else {
            echo '<td><a href="#" onclick="modUser('.$row[id].')">View</a></td>';
        }
        echo '</tr>';
    }
    echo '</table>';
    if($page!=0) {
        echo '<button onclick="viewArticles('.($page-1).')" class="button">Previous</button>';
    }
    if($page!=$numOfPages) {
        echo '<button onclick="viewArticles('.($page+1).')" class="button">Next</button>';
    }
    echo '</div>';
    die();
}
/*
  __  __           _ _  __                       _   _      _      
 |  \/  |         | (_)/ _|           /\        | | (_)    | |     
 | \  / | ___   __| |_| |_ _   _     /  \   _ __| |_ _  ___| | ___ 
 | |\/| |/ _ \ / _` | |  _| | | |   / /\ \ | '__| __| |/ __| |/ _ \
 | |  | | (_) | (_| | | | | |_| |  / ____ \| |  | |_| | (__| |  __/
 |_|  |_|\___/ \__,_|_|_|  \__, | /_/    \_\_|   \__|_|\___|_|\___|
                            __/ |                                  
                           |___/
*/
if($q == "modify") {
    
}
/*
  __  __           _ _  __         _    _               
 |  \/  |         | (_)/ _|       | |  | |              
 | \  / | ___   __| |_| |_ _   _  | |  | |___  ___ _ __ 
 | |\/| |/ _ \ / _` | |  _| | | | | |  | / __|/ _ \ '__|
 | |  | | (_) | (_| | | | | |_| | | |__| \__ \  __/ |   
 |_|  |_|\___/ \__,_|_|_|  \__, |  \____/|___/\___|_|   
                            __/ |                       
                           |___/                        
*/
if($q == "modUser") {
    $id = $_REQUEST['id'];
    if($id<0) {
        die('page num too small');
    }
    $link = connectToDatabase();
    if (!$link) {
        echo('could not connect');
        die();
    }
    $id = mysqli_real_escape_string($link, $id);
    //test if user exists
    $sql = $link->prepare("SELECT * FROM users WHERE id = ?");
    $sql->bind_param("i", $id);
    $sql->execute();
    $search = getFirstRow($sql->get_result());
    $sql->close();
    if($search==null) {
        $sql = $link->prepare("INSERT INTO `logs` (username,action,time,type,significant) VALUES (?,'Failed to Modify User: ?','error','0')");
        $sql->bind_param("si",$_SESSION['username'],$id);
        mysqli_query($link, $sql);
        echo 'Fatal Error: User does not exist: '.$id;
        die();
    }
    if($search['auth']=="admin") {
        echo '<h1 class="bad">ATTEMPT TO TAKE CONTROL OF ADMIN ACCOUNT INTERCEPTED: THIS ACTION HAS BEEN LOGGED AND REPORTED</h1>';
        $sql = "INSERT INTO `logs` (username,action,time,type,significant) VALUES ('".$_SESSION['username']."','ATTEMPT AT MODIFYING ADMIN ACCOUNT: ".$search['username'].": INTERCEPTED', '".date("Y-m-d H:i:s")."','security','1')";
        mysqli_query($link, $sql);
        die();
    }
    //admin logging
    $sql = "INSERT INTO `logs` (username,action,time,type,significant) VALUES ('".$_SESSION['username']."','Modifying User: ".$search['username']."', '".date("Y-m-d H:i:s")."','security','1')";
    mysqli_query($link, $sql);
    echo '<h1 class="primary">Viewing Profile of '.$search['username'].'</h1>';
    echo '<div class="grid">';
        echo '<div class="grid-50"><h2 class="primary">Likes</h2>';
            $likes = mysqli_query($link, "SELECT * FROM `favs` WHERE userID = ".$id." ORDER BY date DESC");
            if($likes->num_rows == 0) {
                echo '<h3 class="primary center">No Liked Articles</h3>';
            } else {
                while($liked = $likes->fetch_assoc()) {
                    $row = getFirstRow(mysqli_query($link, "SELECT * FROM articles WHERE id = ".$liked['articleID']));
                    echo '<a href="?url=article&id='.$row['id'].'" class="artLink"><div class="card">';
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
        echo '</div>';
        echo '<div class="grid-50"><h2 class="primary">Tools</h2>';
        echo '<p class="bad">REMEMBER: ALL ACTIONS ARE LOGGED WITH YOUR USERNAME</p>';
        $_SESSION['viewingUser'] = $id; //extra security precaution: an admin may try to change the code to send the ID number of someone else
        echo '<button onclick="delUser('.$search['id'].',\'conf\')" class="badButton">Delete User</button><div id="delConf"></div>';
        echo '<button onclick="control('.$search['id'].', \'conf\')" class="button">Take Control of Account</button><div id="contconf"></div>';
        echo '</div>';
    echo '</div>';
}
/*
  _____       _      _         _    _               
 |  __ \     | |    | |       | |  | |              
 | |  | | ___| | ___| |_ ___  | |  | |___  ___ _ __ 
 | |  | |/ _ \ |/ _ \ __/ _ \ | |  | / __|/ _ \ '__|
 | |__| |  __/ |  __/ ||  __/ | |__| \__ \  __/ |   
 |_____/ \___|_|\___|\__\___|  \____/|___/\___|_|   
 THESE NEXT LINES ARE THE ABSOLUTE MOST SECURE POINT IN THE ENTIRE PARAGON
*/
if($q=="delUser") {
    //when we build the real paragon sooftware this will become obsolete and be removed.
    //MIGHT be replaced with "Ban User from Huntington Post"
    echo '<div class="article">';
    $link = connectToDatabase();
    if (!$link) {
        echo('<h1 class=\'bad\'>COULD NOT CONNECT TO SERVER: DUE TO ADDITIONAL SECURITY THIS IS BEING LOGGED IN AUXILLARY LOG</h1>');
        auxillaryLog("USER ".$SESSION['username']." ATTEMPTED TO DELETE USER ".$_REQUEST['id']." WITH REASON ".$_REQUEST['reason'].
" BUT SERVER CONNECTION COULD NOT BE INITIALIZED; Logged here because connection to primary database could not be established");
        die();
    }
    $deleteUser  = mysqli_real_escape_string($link, $_REQUEST['del']);
    $reason      = mysqli_real_escape_string($link, $_REQUEST['reason']);
    $recpassword = $_REQUEST['password'];
    //this is an extremely serious step. We need to make sure absolutely every precaution is taken.
    //preliminary checks
    if($_SESSION['auth']!='admin') { /*Probably overkill, but this is freaking important*/
        echo "<h1 class='bad'>BAD AUTHORIZATION: THIS INCIDENT HAS BEEN REPORTED</h1>";
        $sql = "INSERT INTO `logs` (username,action,time,type,significant) VALUES ('".$_SESSION['username']."','ATTEMPT TO DELETE ACCOUNT WITH BAD AUTH: ".$deleteUser." WITH REASON AS STATED- ".$reason.": INTERCEPTED', '".date("Y-m-d H:i:s")."','security','1')";
        mysqli_query($link, $sql);
        die();
    }
    //now we check if this user is actually an admin and did not somehow spoof the auth storage
    $sql = "SELECT * FROM `users` WHERE id = '".mysqli_real_escape_string($link, $_SESSION['del'])."'"; //extra escape, just in case
    $result = getFirstRow(mysqli_query($link, $sql));
    if($result==null) {
        echo "<h1 class='bad'>COULD NOT FIND USER; SESSION DESTROYED (This has not been reported)</h1>";
        
        session_unset();
        session_destroy();
        $_POST = array();
        die();
    }
    if($result['auth']!='admin') {
        echo "<h1 class='bad'>BAD AUTHORIZATION: THIS INCIDENT HAS BEEN REPORTED; USER SESSION DESTROYED</h1>";
        $sql = "INSERT INTO `logs` (username,action,time,type,significant) VALUES ('".$_SESSION['username']."','ATTEMPT TO DELETE ACCOUNT WITH BAD AUTH: ".$deleteUser." WITH REASON AS STATED- ".$reason.": INTERCEPTED', '".date("Y-m-d H:i:s")."','security','1')";
        mysqli_query($link, $sql);
        
        session_unset();
        session_destroy();
        $_POST = array();
        die();
    }
    //check confirmation password
    if(hashPass($recpassword)!=$result['password']) {
        echo "<h1 class='bad'>BAD AUTHORIZATION: INCORRECT PASSWORD; THIS INCIDENT HAS BEEN REPORTED; USER SESSION DESTROYED</h1>";
        echo '<p>Anticipated Question: Why log me out and report me if my my password was just wrong?<br/>Answer: This is a very sensitive area. The only way Paragon can know if an attacker has your account, '.$_SESSION['username'].', is if your password is incorrect. And, if it is, then you are logged out. Paragon figures that if you are the real you, you can log yourself back in.</p>';
        $sql = "INSERT INTO `logs` (username,action,time,type,significant) VALUES ('".$_SESSION['username']."','ATTEMPT TO DELETE ACCOUNT WITH BAD PASS: ".$deleteUser." WITH REASON AS STATED- ".$reason.": INTERCEPTED', '".date("Y-m-d H:i:s")."','security','1')";
        mysqli_query($link, $sql);
        
        session_unset();
        session_destroy();
        $_POST = array();
        die();
    }
    
    //I (Ronan) think that this is enough to see if the user login is legit.
    
    $sql = $link->prepare("SELECT * FROM `users` WHERE id = ?");
    $sql->bind_param("s", $deleteUser);
    $sql->execute();
    $victim = getFirstRow($sql->get_result());//for lack of a better variable name
    //are they trying to delete an admin? Lets find out!
    if($victim['auth']=="admin") {
        echo "<h1 class='bad'>ATTEMPT TO DELETE ADMIN ACCOUNT: THIS INCIDENT HAS BEEN REPORTED; USER SESSION DESTROYED; ADMIN PRIVILEDGES SUSPENDED</h1>";
        $sql = "INSERT INTO `logs` (username,action,time,type,significant) VALUES ('".$_SESSION['username']."','ATTEMPT TO DELETE ADMIN: ".$deleteUser." WITH REASON AS STATED- ".$reason.": INTERCEPTED; PRIVILEDGES SUSPENDED', '".date("Y-m-d H:i:s")."','security','1')";
        mysqli_query($link, $sql);
        
        //suspend admin priviledges
        $sql = "UPDATE `users` SET auth='user' WHERE id='".$_SESSION['id']."'";
        mysqli_query($link, $sql);
        
        session_unset();
        session_destroy();
        $_POST = array();
        echo '</div>';
        die();
    }
    //now we check if they are messing with the values
    if($_SESSION['viewingUser']!=$deleteUser) {
        echo "<h1 class='bad'>IDENTIFICATION MISMATCH: THIS INCIDENT HAS BEEN REPORTED</h1>";
        $sql = "INSERT INTO `logs` (username,action,time,type,significant) VALUES ('".$_SESSION['username']."','ATTEMPT TO DELETE ACCOUNT WITH BAD AUTH: ".$deleteUser." WITH REASON AS STATED- ".$reason.": INTERCEPTED', '".date("Y-m-d H:i:s")."','security','1')";
        mysqli_query($link, $sql);
        die();
    }
    //does this user even exist? 
    if($victim==null) {
        echo "<h1>(Reported as Deletable Error) Paragon could not find user. Sorry for the inconvienance. Perhaps they already have been deleted?</h1>"; 
        $sql = "INSERT INTO `logs` (username,action,time,type,significant) VALUES ('".$_SESSION['username']."','Tried to delete user: ".$deleteUser." with reason ".$reason." but could not find user', '".date("Y-m-d H:i:s")."','error','0')";
        mysqli_query($link, $sql);
        echo '</div>';
        die();
    }
    $sql = $link->prepare("DELETE FROM `users` WHERE id=?");
    $sql->bind_param("i", $deleteUser);
    $sql->execute();
    echo 'User Account Deleted. This has been pernamently logged.';
    echo '</div>';
}
/*
   _____            _             _   _    _               
  / ____|          | |           | | | |  | |              
 | |     ___  _ __ | |_ _ __ ___ | | | |  | |___  ___ _ __ 
 | |    / _ \| '_ \| __| '__/ _ \| | | |  | / __|/ _ \ '__|
 | |___| (_) | | | | |_| | | (_) | | | |__| \__ \  __/ |   
  \_____\___/|_| |_|\__|_|  \___/|_|  \____/|___/\___|_|   
                                                           
                                                           
*/
if($q=="contUser") {
    die("This function has been disabled");
    //no reason to at the moment to implement this.
    
    $link = connectToDatabase();
    
    $user = mysqli_real_escape_string($link, $_SESSION['id']);
    
    $sql = "SELECT * FROM `users` WHERE id = '".$user."'";
    $mask = getFirstRow(mysqli_query($link, $sql));
    
    $sql = "INSERT INTO `logs` (username,action,time,type,significant) VALUES ('".$_SESSION['username']."','User takes temporary control of user account: ".$mask['username']."', '".date("Y-m-d H:i:s")."','security','1')";
    mysqli_query($link, $sql);
    $_SESSION['security'] = "tight";
}
?>
