<?php
require_once "pdo.php";
require_once "util.php";
session_start();
#$name = htmlentities($_GET['name']);

if (isset($_POST['cancel'])) {
    header('Location: index.php');
    return;
}
if (!isset($_SESSION["user_id"])) {
    die('ACCESS DENIED');
}
if (isset($_POST['add'])) {
    if ((strlen($_POST['first_name']) == 0 || strlen($_POST['last_name']) == 0 || strlen($_POST['email']) == 0 || strlen($_POST['headline']) == 0 || strlen($_POST['summary']) == 0)) {
        $_SESSION['error'] = "All values are required";
        header('Location:add.php');
        return;
    }
    if ((strpos($_POST['email'], '@') === false)) {
        $_SESSION['error'] = "Email address must contain @";
        header('Location:add.php');
        return;
    }
    for ($i = 1; $i <= 9; $i++) {
        if (!isset($_POST['year' . $i])) continue;
        if (!isset($_POST['desc' . $i])) continue;
        $year = $_POST['year' . $i];
        $desc = $_POST['desc' . $i];
        if (strlen($year) == 0 || strlen($desc) == 0) {
            $_SESSION['error'] = "All fields are required";
            header('Location:add.php');
            return;
        }

        if (!is_numeric($year)) {
            $_SESSION['error'] = "Position year must be numeric";
            header('Location:add.php');
            return;
        }
    }



    $sql = "INSERT INTO profile1 (user_id,first_name,last_name, email, headline,summary) VALUES (:uid, :fn,:ln, :e, :h, :s)";
    $stmt = $pdo->prepare($sql);

    $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->execute([
        ':uid' => $_SESSION['user_id'],
        ':fn' => $_POST['first_name'],
        ':ln' => $_POST['last_name'],
        ':e' => $_POST['email'],
        ':h' => $_POST['headline'],
        ':s' => $_POST['summary']
    ]);
    $profile_id = $pdo->lastInsertId();
    $rank = 1;
    for ($i = 1; $i <= 9; $i++) {
        if (!isset($_POST['year' . $i])) continue;
        if (!isset($_POST['desc' . $i])) continue;
        $year = $_POST['year' . $i];
        $desc = $_POST['desc' . $i];

        $stmt = $pdo->prepare('INSERT INTO position
            (profile_id, rank, year, description) 
        VALUES ( :pid, :rank, :year, :desc)');
        $stmt->execute(array(
            ':pid' => $profile_id,
            ':rank' => $rank,
            ':year' => $year,
            ':desc' => $desc
        ));
        $rank++;
    }
    $_SESSION['success'] = 'Profile Added';
    header('Location:index.php');
    return;
    $positions = loadpos($pdo, $_REQUEST['profile_id']);

    if (isset($_SESSION['error'])) {
        echo ('<p style="color:red;">' . htmlentities($_SESSION['error']) . "</p>\n");
        unset($_SESSION['error']);
    }

    if (isset($_SESSION['success'])) {
        echo ('<p style="color:green;">' . htmlentities($_SESSION['success']) . "</p>\n");
    }
}

?>
<html>

<head>
    <title>Keya Bhadreshkumar Adhyaru profile Add</title>

    <?php require_once "bootstrap.php"; ?>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.2.1.js" integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE=" crossorigin="anonymous">
    </script>
    <script type="text/javascript" src="jquery.min.js">
    </script>
</head>

<body>
    <div class="container">
        <h1>Adding Profile for <?= htmlentities($_SESSION["name"]); ?> </h1>
        <?php flashmsg(); ?>
        <form method="post">
            <p>First Name:
                <input type="text" name="first_name" size="50"></p>
            <p>Last Name:
                <input type="text" name="last_name" size="50"></p>
            <p>Email:
                <input type="text" name="email" size="20"></p>
            <p>Headline:
                <input type="text" name="headline" size="50"></p>
            <p>Summary:
                <textarea name="summary" rows="8" cols="80"></textarea></p>
            <p>position: <input type="button" id="addpos" value=" + " />

                <div id="position_fields">
                </div>
            </p>
            <br>
            <p><input type="submit" value="Add New" name="add" />
                <input type="submit" value="cancel" name="cancel" /></p>
        </form>
        <script>
            countpos = 0;
            $(document).ready(function() {
                window.console && console.log('document ready called');
                $('#addpos').click(function(event) {
                    event.preventDefault();
                    if (countpos >= 9) {
                        alert("Maximum of nine position entries exceeded");
                        return;
                    }
                    countpos++;
                    window.console && console.log("adding position" + countpos);
                    $('#position_fields').append(
                        '<div id="position' + countpos + '">\
                <p>Year: <input type="text" name="year' + countpos + '" value=""/ >\
                <input type="button" value="-" onclick="$(\'#position' + countpos + '\').remove();return false;"></p>\
                <textarea name="desc' + countpos + '" rows="8" cols="80">\
                </textarea>\
                </div>');

                });
            });
        </script>
</body>

</html>