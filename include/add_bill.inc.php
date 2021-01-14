<?php
    // validate and sanitize user input, adding bill to fiskalniRacun table
    session_start();
    if(!isset($_SESSION['id'])) {
        header('Location: ../login');
        exit();
    }
 
    require_once('autoloader.php');

    if(isset($_POST['submit'])) {
        $args['broj'] = $_POST['broj'];
        $args['userId'] = $_SESSION['id'];
        
        
        foreach($args as $key => $value) {
            Validate::validateString($key, $args[$key]);
            $args[$key] = Sanitize::sanitizeString($value);
        }

        $args['datum'] = $_POST['datum'];
        list($year, $month, $day) = explode('-',$_POST['datum']);
        if(!checkdate($month, $day, $year)) {
            // Message::addError('datum', 'Please provide valid date');
            Message::addError('datum', $_POST['datum']);
        }

        $args['slika'] = 'slika';
        Validate::validateFile('slika', 'slika');
        $error = Message::getError();

        if($error) {
            echo json_encode($error);
            exit();
        }
        $bill = new Bill($args);
        $bill->save();
        $error = Message::getError(); 
        echo json_encode($error);
        exit();
    } else {
        header('Location: ../index');
        exit();
    }
?>