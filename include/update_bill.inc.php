<?php
    session_start();
    if(!isset($_SESSION['id'])) {
        header('Location: ../login');
        exit();
    }
 
    require_once('autoloader.php');

    if(isset($_POST['submit'])) {
        foreach($_POST as $key => $value) {
            if(strpos($key, 'broj')) {
                $args['id'] = explode('-', $key)[0];
                $args['broj'] = $value;
            }
        }

        $args['userId'] = $_SESSION['id'];
        
        
        foreach($args as $key => $value) {
            Validate::validateString($key, $args[$key]);
            $args[$key] = Sanitize::sanitizeString($value);
        }

        $args['datum'] = $_POST['datum'];
        list($year, $month, $day) = explode('-',$_POST['datum']);
        if(!checkdate($month, $day, $year)) {
            Message::addError('datum', 'Please provide valid date');
        }

        $oldBill = Bill::findById($args['id']);

        if($_FILES['slika']['error'] == 4) {
            // if user doesen't upload image, don't update image path
            $args['slika'] = $oldBill['slika'];
        } else {
            if($oldBill['slika']) {
                $args['slika'] = 'slika';
                unlink('../images/' . $oldBill['slika']);
                Validate::validateFile('slika', $args['slika']);
            }
        }

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