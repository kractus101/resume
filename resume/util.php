<?php
function flashmsg()
{
    if ( isset($_SESSION['error']) ) {
    echo ('<p style="color:red;">'.htmlentities($_SESSION['error'])."</p>\n");
    unset($_SESSION['error']);
    }
    
    if ( isset($_SESSION['success']) ) {
    echo ('<p style="color:green;">'.htmlentities($_SESSION['success'])."</p>\n");
    #unset($_SESSION['success']);
    }
}
function validateprofile()
{
    if((strlen($_POST['first_name'])==0 || strlen($_POST['last_name'])==0 || strlen($_POST['email'])==0 || strlen($_POST['headline'])==0 || strlen($_POST['summary'])==0))
            {
                return "All fields are requeried";
    }
    if((strpos($_POST['email'], '@') === false)) 
    {
        return "Email address must contain @";
    }
    return true;
}
//look through POST data and return true or eror message
function validatepos() {
   for($i=1; $i<=9; $i++) {
       if ( ! isset($_POST['year'.$i]) ) continue;
       if ( ! isset($_POST['desc'.$i]) ) continue;
       $year = $_POST['year'.$i];
       $desc = $_POST['desc'.$i];
       if ( strlen($year) == 0 || strlen($desc) == 0 ) {
           return "All fields are required";
       }

       if ( ! is_numeric($year) ) {
           return "Position year must be numeric";
       }
   }
   return true;
}
function loadpos($pdo,$profile_id)
{
    $stmt= $pdo->prepare('select * from position where profile_id=:prof order by rank');
    $stmt->execute(array(':prof'=> $profile_id));
    $positions=array();
    while($row=$stmt->fetch(PDO::FETCH_ASSOC))
    {
        $positions[]= $row;
    }
    return $positions;

}



?>