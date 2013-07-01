<?php

if(!isset($_POST['action']))
    echo "";
else {
    if($_POST['action'] == 'set') {
        $result = setcookie("decobac", $_POST['data'], time()+3600, "/");
        echo $result;
    }

    if($_POST['action'] == 'get') {
        if(!isset($_COOKIE['decobac']))
            echo false;
        else
            echo $_COOKIE['decobac'];
    }

    if($_POST['action'] == 'reset') {
        $result = setcookie("decobac");
        echo $result;
    }
    
    if($_POST['action'] == 'remove') {
        $result = setcookie("decobac", "", time()-3600, "/");
        echo $result;
    }
}

?>
