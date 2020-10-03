<?php
require_once "pdo.php";
require_once "util.php";
session_start();

if ( !isset($_SESSION["user_id"]) ) {
    die('ACCESS DENIED');
}
if(isset($_POST['cancel']))
{
    header('Location: index.php');
    return;
}
if ( ! isset($_REQUEST['profile_id']) ) {
    $_SESSION['error'] = "Missing profile_id";
    header('Location: index.php');
    return;
}            
  $stmt=$pdo->prepare("select * from profile1 where profile_id=:prof and user_id=:uid");
  $stmt->execute(array(':prof'=>$_REQUEST['profile_id'],
                ':uid'=>$_SESSION['user_id']));
                $profile= $stmt->fetch(PDO::FETCH_ASSOC);


                if($profile===false)
                {
                    $_SESSION['error']="could not load profile";
                    header('Location:index.php');
                    return;
                }  
 
if ( isset($_POST['first_name']) && isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email'])
&& isset($_POST['headline']) && isset($_POST['summary']))                                                                                                                                                             
{
    if((strlen($_POST['first_name'])==0 || strlen($_POST['last_name'])==0 || strlen($_POST['email'])==0 || strlen($_POST['headline'])==0 || strlen($_POST['summary'])==0))
    {
        $_SESSION['error']= "All values are required";
        header('Location:add.php');
        return;
    }
    if((strpos($_POST['email'], '@') === false)) 
    {
        $_SESSION['error']= "Email address must contain @";
        header('Location:add.php');
        return;

    }
    for($i=1; $i<=9; $i++) {
        if ( ! isset($_POST['year'.$i]) ) continue;
        if ( ! isset($_POST['desc'.$i]) ) continue;
        $year = $_POST['year'.$i];
        $desc = $_POST['desc'.$i];
        if ( strlen($year) == 0 || strlen($desc) == 0 ) {
            $_SESSION['error']= "All fields are required";
            header('Location:add.php');
        return;
        }
 
        if ( ! is_numeric($year) ) {
            $_SESSION['error']= "Position year must be numeric";
            header('Location:add.php');
        return;
        }
    }
    

        $sql= "UPDATE profile1 SET first_name= :fn,
            last_name=:ln,
            email=:e,headline=:h,
            summary=:s
            WHERE  profile_id=:pid and user_id= :uid";
            $stmt = $pdo->prepare($sql);
        
            $stmt->execute(array(
            ':fn' => $_POST['first_name'],
            ':ln' => $_POST['last_name'],
            ':e' => $_POST['email'],
            ':h' => $_POST['headline'],
            ':s'  => $_POST['summary'],
            ':uid'=> $_SESSION['user_id'],
            ':pid' => $_REQUEST ['profile_id']));
            
            $stmt= $pdo->prepare("delete from position where profile_id=:pid");
            $stmt->execute(array(':pid'=>$_REQUEST['profile_id']));

            $rank=1;
            for($i=1; $i<=9; $i++) {
                if ( ! isset($_POST['year'.$i]) ) continue;
                if ( ! isset($_POST['desc'.$i]) ) continue;
                $year = $_POST['year'.$i];
                $desc = $_POST['desc'.$i];
    
            $stmt = $pdo->prepare('INSERT INTO position
                (profile_id, rank, year, description) 
            VALUES ( :pid, :rank, :year, :desc)');
            $stmt->execute(array(
                ':pid' => $_REQUEST['profile_id'],
                ':rank' => $rank,
                ':year' => $year,
                ':desc' => $desc)
            );
            $rank++;
        }
            
                $_SESSION['success'] = 'Profile updated';
                header('Location: index.php');
                return;
                                  
             
}             
$positions= loadpos($pdo,$_REQUEST['profile_id']);             

?>
<html>
<head>
<title>Keya Bhadreshkumar Adhyaru profile Edit </title>
<?php require_once "bootstrap.php"; ?>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">
  <script src="https://code.jquery.com/jquery-3.2.1.js" integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE=" crossorigin="anonymous">
  </script>
<script type="text/javascript" src="jquery.min.js" >
</script>
</head>
<body>
<div class= "container">
<h1>Editing Profile for<?= htmlentities($_SESSION['name']); ?> </h1>
<?php flashmsg();?>
<form method="post" action="edit.php">
<input type="hidden" name="profile_id" value="<?= htmlentities($_GET['profile_id']);?>">
<p>First Name:
<input type="text" name="first_name" value="<?= htmlentities($profile['first_name']);?>" size="50"></p>
<p>Last Name:
<input type="text" name="last_name" value="<?= htmlentities($profile['last_name']);?>" size="50"></p>
<p>Email:
<input type="text" name="email" value="<?= htmlentities($profile['email']);?>" size="50"></p>
<p>Headline:
<input type="text" name="headline" value="<?=  htmlentities($profile['headline']);?>" size="50"></p>
<p>Summary:
<textarea name="summary" rows="8"cols="80"><?=  htmlentities($profile['summary']);?></textarea></p>
<?php
$pos=0;
echo('<p>position: <input type="button" id="addpos" value=" + ">'."\n");
echo('<div id="position_fields">'."\n");
foreach($positions as $position)
{
    $pos++;
    echo('<div id="position'.$pos.'">'."\n");
    echo('<p>Year: <input type="text" name="year'.$pos.'"');
    echo(' value="'.$position['year'].'"/>'."\n");
    echo('<input type="button" value="-"');
    echo('onclick="$(\'#position'.$pos.'\').remove();return false;">'."\n");
    echo("</p>\n");
    echo('<textarea name="desc'.$pos.'" rows="8" cols="80">'."\n");
    echo(htmlentities($position['description'])."\n");
    echo("\n</textarea>\n</div>\n"); 

}

echo("</div></p>\n");
?>
<p><input type="submit" value="Save" name="Save"/>
<input type="submit" value="cancel" name="cancel"/></p>
</form>
<script>
countpos=<?= $pos ?>;
    $(document).ready(function(){
        window.console && console.log('document ready called');
        $('#addpos').click(function(event){
            event.preventDefault();
            if(countpos>=9){
                alert("Maximum of nine position entries exceeded");
                return;
            }
            countpos++;
            window.console && console.log("adding position"+countpos);
            $('#position_fields').append( 
                '<div id="position'+countpos+'">\
                <p>Year: <input type="text" name="year'+countpos+'" value=""/ >\
                <input type="button" value="-" onclick="$(\'#position'+countpos+'\').remove();return false;"></p>\
                <textarea name="description'+countpos+'" rows="8" cols="80">\
                </textarea>\
                </div>');
            
        });
    });
    </script>
</body>
</html>
