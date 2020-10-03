<?php
require_once "pdo.php";
require_once "util.php";
session_start();
?>
<!DOCTYPE html>
<html>
<head>
<?php require_once "bootstrap.php";
?>
<title>Keya Bhadreshkumar Adhyaru's Resume Registry</title>
</head>
<body>


<div class="container">
<h1>Keya Bhadreshkumar Adhyaru's Resume Registry</h1>
<?php
flashmsg();
if(!isset ($_SESSION['success']))
{        
    echo('<p><a href="login.php">Please log in</a></p>');

}
else{
  echo('<p><a href="add.php">Add New Entry </a></p>');       
  echo('<p><a href="logout.php">Logout</a></p>');      

    echo('<table border="1">'."\n");
    $stmt = $pdo->query("SELECT first_name,last_name, headline, profile_id FROM profile1");
    echo("<tr><th>Name</th><th>Headline</th>");
    if (isset($_SESSION["user_id"])) {
        echo "<th>Action</th></tr>";
    }
       
    while( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
    echo "<tr><td>";
    echo("<a href='view.php?profile_id=" . $row["profile_id"] .
    "'>" . htmlentities($row['first_name'] . " " . $row['last_name']) .
    "</a>");
    echo("</td><td>");
    echo(htmlentities($row['headline']));
    echo("</td>");
    if (isset($_SESSION["user_id"])) {
    
    echo("<td>");
    echo('<a href="edit.php?profile_id='.$row['profile_id'].'">Edit</a> / ');
    echo('<a href="delete.php?profile_id='.$row['profile_id'].'">Delete</a>');
    echo("</td></tr>\n");
    }
}
    echo ("</table>"."\n");
}   
    return;
    unset($_SESSION['success']);

?>

</div>
</body>
</html>