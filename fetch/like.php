<?php
//STATEMENTS PREPARED!!!
/*
 *like.php is queried by an ajax script client side
 *
 *it checks if their signed in, then if they are, it adds it as a favourite
 * to the "favs" database. this will appear in the users account and is queried
 * on the article itself to prevent double favs
 *
 *This will also add one to the "popularity" column in the "articles" table
 **/

session_start();
include getcwd().'/../api.php';

if($_SESSION['hp_login']!=true) {//Should be signed in to see the like button anyway, but just in case
    echo 'not signed in';
    die();
}//they ARE logged in.

$article = $_REQUEST['article'];//get the article id

$link = connectToDatabase();//create a link to the database

if (!$link) { //if we could not connect to the database, then we send an error to the client. Of course, this is hidiously unlikely because they need to access the database to get to this page.
    echo('could not connect');
    die();
}

$article = mysqli_real_escape_string($link, $article);//sanitize the data input because for whatever reason it requires a link

$sql = $link->prepare("SELECT * FROM favs WHERE userID = ? AND articleID = ?;");
$sql->bind_param("si", $_SESSION['id'], $article);
$sql->execute();
$result = $sql->get_result();
$sql->close();

if ($result->num_rows > 0) {
    echo('already liked');//we arn't going to do anything with this, at least not yet. We will just display "Success!" instead
    die();
}

$sql = $link->prepare("INSERT INTO favs (userID,articleID,date) VALUES (?,?,?);");
$sql->bind_param("sis", $_SESSION['id'], $article, date('Y-m-d'));
$sql->execute();

$result = $sql->get_result();
$sql->close();

$sql = $link->prepare("UPDATE articles SET popularity=popularity+1 WHERE id=?;");
$sql->bind_param("i", $article);
$sql->execute();
$sql->close();

echo 'success';

?>