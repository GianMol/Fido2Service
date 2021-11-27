<?php
session_start();
if(isset($_SESSION["username"])){
    header("location: resource.php");
    exit;
}

include_once("../constants.php");

if(isset($_POST["firstname"]) && $_POST["firstname"] !== "" &&
isset($_POST["lastname"]) && $_POST["lastname"] !== "" &&
isset($_POST["username"]) && $_POST["username"] !== "" &&
isset($_POST["displayname"]) && $_POST["displayname"] !== ""){

    $conn = mysqli_connect("localhost", "root", "", "fido2service");
    mysqli_query($conn, "set character set 'utf8'");

    $username = mysqli_real_escape_string($conn, $_POST["username"]);

    $query = "SELECT * FROM users WHERE username = '".$username."'";
    $res = mysqli_query($conn, $query);
    if(mysqli_num_rows($res) !== 0){
        $err = true;
        mysqli_free_result($res);
        mysqli_close($conn);
        exit;
    }
    else{
        $firstname = mysqli_real_escape_string($conn, $_POST["firstname"]);
        $lastname = mysqli_real_escape_string($conn, $_POST["lastname"]);
        $displayname = mysqli_real_escape_string($conn, $_POST["displayname"]);

        $empty_obj = new stdClass();

        $data = array(
            'svcinfo' => SVCINFO,
            'payload' => array(
                'username' => $username,
                'displayname' => $displayname,
                'options' => array(
                    'attestation' => 'direct',
                ),
                'extensions' => $empty_obj,
            ),
        );
        $post_data = json_encode($data);
        $url = SKFS_HOSTNAME . SKFS_PREREGISTRATION_PATH . ":" . SKFS_PORT;
        
        $crl = curl_init($url);
        curl_setopt($crl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($crl, CURLINFO_HEADER_OUT, true);
        curl_setopt($crl, CURLOPT_POST, true);
        curl_setopt($crl, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($crl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($post_data)
        ));
        $result = curl_exec($crl);
        if($result === false){ //err
            console_log("false");
        }
        else{ //not err
            console_log($result);
        }


        /**here */






    }

    /*mysqli_free_result($res);
    mysqli_close($conn);


    
    $_SESSION["username"] = $_POST["username"];
    header("location: resource.php");
    exit;*/
}
?>

<html>

    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Fido2 Service Registration</title>
        <!--<link rel="icon" type="image/png" href="./Images/libro-stilizzato.png" sizes="16x16" /> -->
        <script src = "../js/registration.js" defer = "true"></script>
        <link rel="stylesheet" href="../css/registration.css">
        <link rel="stylesheet" href="../css/layout.css">
        <link href="https://fonts.googleapis.com/css?family=Merriweather" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css?family=Playfair+Display" rel="stylesheet">
    </head>

    <body>
    <div id="nav-bar">
            <a href="./registration.php">Register</a>
            <a href="https://www.google.com">Login</a>
            <a href="https://www.google.com">Resource</a>
            <a href="../index.php">Home</a>
        </div>
        <div id="body-layout">
            <h3>Registration</h3>
            <form method="post" id="register-form">
                <input id="firstname" name="firstname" class="input-item" type="text" placeholder="First Name">
                <input id="lastname" name="lastname" class="input-item" type="text" placeholder="Last Name">
                <input id="username" name="username" class="input-item" type="text" placeholder="Username">
                <?php
                    if(isset($err)){
                        if($err === true){
                            echo "<div id='error'>Username already in use</div>";
                            $err = false;
                        }
                    }
                    if(isset($res)){
                        if(mysqli_num_rows($res) !== 0){
                            echo "<div id='error'>".$res."</div>";
                            echo "<div id='error'>".$conn."</div>";
                            $err = false;
                        }
                    }
                ?>
                <input id="displayname" name="displayname" class="input-item" type="text" placeholder="Display Name">
                <input id="submit-button" type="submit" >Sign Up</button>
            </form>
        </div>
    </body>

</html>