<?php // Do not put any HTML above this line

// Redirect the browser to view.php

session_start();
require_once "pdo.php";

#$_SESSION['name'] = $_POST['email'];
#header("Location: view.php");
#return;


$salt = 'XyZzy12*_';
$md5= hash('md5', $salt);
$stored_hash = '1a52e17fa899cf40fb04cfc42e6352f1';  // Pw is meow123
$failure = false;  // If we have no POST data
// Check to see if we have some POST data, if we do process it
if (isset($_POST["cancel"])) {
    header("Location: index.php");
    die();
}
if ( isset($_POST['email']) && isset($_POST['pass']) )
 {
    unset($SESSION["name"]);
    unset($SESSION["user_id"]);
    
        $pass = htmlentities($_POST['pass']);
        $email = htmlentities($_POST['email']);

       
            $check = hash('md5', $salt.$pass);
            $stmt = $pdo->prepare('SELECT user_id, name FROM user2 WHERE email = :em AND password = :pw');

            $stmt->execute(array( ':em' => $_POST['email'], ':pw' => $check));
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ( $check == $stored_hash ) 
            {
                if ( $row !== false ) {

                    $_SESSION['name'] = $row['name'];
                    $_SESSION['user_id'] = $row['user_id'];
                    #$_SESSION['email']=$email ;
                error_log("Login success ".$email);
                $_SESSION['success'] = "you are logged in";
                header("Location: index.php?name=".urlencode($email));
                return;
                }
            
            } 
              else 
            {
                error_log("Login fail ".$pass." $check");
                $_SESSION['error'] = "Incorrect password";
                header("Location: login.php");
                return;
            }  
        
    
    }
 

// Fall through into the View
?>
<!DOCTYPE html>
<html>
<head>
<?php require_once "bootstrap.php"; ?>
<title>Keya Bhadreshkumar Adhyaru Login Page</title>
</head>
<body>
<div class="container">
<h1>Please Log In</h1>
<?php
// Note triple not equals and think how badly double
// not equals would work here...
if ( isset ($_SESSION['error']))
{
    echo ('<p style="color:red">'.$_SESSION['error']."</p>\n");
    unset($_SESSION['error']);

}


?>

<form method="POST">
<label for="nam">User Name</label>
<input type="text" name="email" id="id_email"><br/>
<label for="id_1723">Password</label>
<input type="text" name="pass" id="id_1723"><br/>
<input type="submit" onclick="return doValidate();" value="Log In" name="login">
<input type="submit" name="cancel" value="Cancel">
</form>


<script type="text/javascript">

function doValidate() {
    console.log('Validating...');
    try {
        email = document.getElementById("id_email").value;
        pw = document.getElementById('id_1723').value;
        console.log("Validating pw="+pw);
        console.log("Validating email="+email);

        if (pw == null || pw == "" || email==null || email=="") {
            alert("Both fields must be filled out");
            return false;
        }
        if(email.search("@") === -1)
        {
            alert("Email address must contain @");
             return false;
        }
        return true;
        }
    catch(e) {
        return false;
    }
    return false;
    }
    </script>

</body>
