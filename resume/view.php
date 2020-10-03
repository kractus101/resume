<?php
require_once "pdo.php";
require_once "util.php";
session_start();

if (! isset($_GET['profile_id']) ) {
    $_SESSION['error'] = "Missing profile_id";
    header("Location: index.php");
    die();
}

$sql = "SELECT * FROM profile1 WHERE profile_id = :pid";
$stmt = $pdo->prepare($sql);
$stmt->execute(array(":pid" => $_GET['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);

$sql1 = "SELECT year,description FROM position WHERE profile_id = :pid";
$stmt1 = $pdo->prepare($sql1);
$stmt1->execute(array(":pid" => $_GET['profile_id']));
$rows = $stmt1->fetch(PDO::FETCH_ASSOC);


if ($row === false ) {
    $_SESSION['error'] = 'Bad value for profile_id';
    header('Location: index.php');
    die();
}

$fn = htmlentities($row["first_name"]);
$ln = htmlentities($row["last_name"]);
$e = htmlentities($row["email"]);
$h = htmlentities($row["headline"]);
$s = htmlentities($row["summary"]);
#$y= htmlentities($rows["year"]);
#$d=htmlentities($rows["description"]);

$positions= loadpos($pdo,$_REQUEST['profile_id']);             


?>
<!DOCTYPE html>
<html>
<head>

    <title>Keya Adhyaru</title>
</head>
<body style="font-family: Helvetica, sans-serif">
    <h1>Profile information</h1>
    <p>First Name: <?php echo $fn ?></p>
    <p>Last Name: <?php echo $ln ?></p>
    <p>Email: <?php echo $e ?></p>
    <p>
        Headline:
        <br>
        <?php echo $h ?>
    </p>
    <p>
        Summary:
        <br>
        <?php echo $s ?>
    </p>
    <p>Position: <br/><ul>
        <?php
        foreach ($positions as $r) {
            echo('<li>'.htmlentities($r['year']).':'.htmlentities($r['description']).'</li>');
        } ?>
        </ul></p>
            <a href="index.php">Done</a>
</body>
</html>