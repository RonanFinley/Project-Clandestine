<?php
//STATEMENTS ARE PREPARED
session_start();

function hashPass($pass) {
    return crypt($pass,"jher");
}
function gen_uuid() {
    return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        // 32 bits for "time_low"
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

        // 16 bits for "time_mid"
        mt_rand( 0, 0xffff ),

        // 16 bits for "time_hi_and_version",
        // four most significant bits holds version number 4
        mt_rand( 0, 0x0fff ) | 0x4000,

        // 16 bits, 8 bits for "clk_seq_hi_res",
        // 8 bits for "clk_seq_low",
        // two most significant bits holds zero and one for variant DCE1.1
        mt_rand( 0, 0x3fff ) | 0x8000,

        // 48 bits for "node"
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
    );
}
function connectToDatabase() {
    //return mysqli_connect($database_host, $database_username, $database_password, $database_db);
    return mysqli_connect(
        'SERVER ADDR',
        'USERNAME',
        'PASSWORD',
        'DATABASE NAME');
    //When updating this data, remember to also change the values in chron.php
}
function clearData() {
    session_unset();
    session_destroy();
    $_POST = array();
    header("Location: index.php");
    die();
}
function getFirstRow($result) {
    if($result==null) return null;
    while($row = $result->fetch_assoc()) {
        return $row;
    }
    return null;
}
function auxillaryLog($text) {
    $aux = fopen("auxillary.txt", "a+");
    fwrite($aux, $text."\n");
    fclose($aux);
}
function getImageWriter($id) {
    $url = getcwd().'/../images/'.$id;
    $urlparse = 'images/'.$id;
    if(file_exists( $url.'.jpg')) {
        return $urlparse.'.jpg';
    } else if(file_exists( $url.'.png')) {
        return $urlparse.'.png';
    } else if(file_exists( $url.'.jpeg')) {
        return $urlparse.'.jpeg';
    } else if(file_exists( $url.'.gif')) {
        return $urlparse.'.gif';
    } else if(file_exists( $url.'.svg')) {
        return $urlparse.'.svg';
    } else {
        return 'images/noimage.jpg';
    }
}
function getImage($id) {
    $url = getcwd().'/images/'.$id;
    $urlparse = 'images/'.$id;
    if(file_exists( $url.'.jpg')) {
        return $urlparse.'.jpg';
    } else if(file_exists( $url.'.png')) {
        return $urlparse.'.png';
    } else if(file_exists( $url.'.jpeg')) {
        return $urlparse.'.jpeg';
    } else if(file_exists( $url.'.gif')) {
        return $urlparse.'.gif';
    } else if(file_exists( $url.'.svg')) {
        return $urlparse.'.svg';
    } else {
        return 'images/noimage.jpg';
    }
}
function getImageDraft($id) {
    $url = getcwd().'/../draftimages/'.$id;
    $urlparse = 'draftimages/'.$id;
    if(file_exists( $url.'.jpg')) {
        return $urlparse.'.jpg';
    } else if(file_exists( $url.'.png')) {
        return $urlparse.'.png';
    } else if(file_exists( $url.'.jpeg')) {
        return $urlparse.'.jpeg';
    } else if(file_exists( $url.'.gif')) {
        return $urlparse.'.gif';
    } else if(file_exists( $url.'.svg')) {
        return $urlparse.'.svg';
    } else {
        return 'images/noimage.jpg';
    }
}
function logAction($username, $action, $type, $significance) {
    $link = connectToDatabase();
    $sqllog = $link->prepare("INSERT INTO `logs` (username,action,time,type) VALUES (?,?,?,?)");
    $sqllog->bind_param("ssss",$username, $action, date('Y-m-d'), $type);
    $sqllog->execute();
    $sqllog->close();
}
function login() {
    $link = connectToDatabase(); 
    
    if (!$link) { 
        die('Could not connect: ' . mysqli_error() . ' - Please contact Admin'); //streamline in production
    }

    $username   = htmlspecialchars(mysqli_real_escape_string($link, $_POST['username']));
    $password   = mysqli_real_escape_string($link, $_POST['password']);
    $hash       = hashPass($password);
    
    $sql = $link->prepare("SELECT * FROM users WHERE username=? AND status <> 'deletion'");
    $sql->bind_param("s",$username);
    $sql->execute();
    $result = $sql->get_result();
    $sql->close();
    
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            //echo "id: " . $row["id"]. " - Name: " . $row["username"]. " Hash:" . $row["password"]. " Authorization: " . $row['auth'] . "<br>";
            if($hash==$row['password']) {
                //correct password
                if($row['status']=="suspended") {
                    echo '<div class="article-xxs">
                        <div class="body">
                            <h1 class="center primary">That account had been <b>suspended</b></h1>
                            <div class="smallMessage">
                                <p>Your account has been suspended by Paragon until further notice.</p>
                            </div>
                        </div>
                    </div>';
                    return "suspended";
                }
                $_SESSION['id'] = $row['id'];
                $_SESSION['username'] = $row['username'];
                $_SESSION['auth'] = $row['auth'];
                $_SESSION['hp_login'] = "true";
                return "true";
            } else {
                return "false";
            }
        }
    } else {
        echo '<div class="article-xxs">
            <div class="body">
                <h1 class="center primary">Paragon can not find that user</h1>
                <div class="smallMessage">
                    <p>Paragon cannot find your account in the databases. You\'re username must be incorrect</p>
                </div>
            </div>
        </div>';
        return "suspended";
    }
}
function listAsArticles($result) {
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
}
?>