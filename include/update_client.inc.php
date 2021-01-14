<?php
    session_start();
    if(!isset($_SESSION['id'])) {
        header('Location: ../login');
        exit();
    }
 
    require_once('autoloader.php');

    if(isset($_POST['submit'])) {
        foreach($_POST as $key => $value) {
            if(strpos($key, 'ime')) {
                $args['id'] = explode('-', $key)[0];
                $args['ime'] = $value;
                break;
            }
        }
        $args['logo'] = 'logo';
        $args['jib'] = $_POST['jib'];
        $args['pib'] = $_POST['pib'] ?? '-';
        $args['vlasnik'] = $_POST['vlasnik'];
        $args['adresa'] = $_POST['adresa'];
        $args['mjesto'] = $_POST['mjesto'];
        $args['telefon'] = $_POST['telefon'];
        $args['racun'] = $_POST['racun'];
        $args['userId'] = $_SESSION['id']; 

        foreach($args as $key => $value) {
            if(!empty($value) || $key == 'racun' || $key == 'ime' || $key == 'adresa') {
                Validate::validateString($key, $args[$key]);
                $args[$key] = Sanitize::sanitizeString($value);
            }
        }
        $args['pdv'] = $_POST['pdv'];
        Validate::validateNumber('pdv', $args['pdv']);

        $args['email'] = $_POST['email'];
        if($args['email']) {
            Validate::validateEmail($args['email']);
        }

        $oldClient = Client::findById($args['id']);

        if($_FILES['logo']['error'] == 4) {
            // if user doesen't upload image, don't update image path
            $args['logo'] = $oldClient['logo'];
        } else {
            if($oldClient['logo']) {
                $args['logo'] = 'logo';
                unlink('../images/' . $oldClient['logo']);
                Validate::validateFile($args['logo'], 'logo');
            }
        }

        
        $error = Message::getError();

        if($error) {
            echo json_encode($error);
            exit();
        }
        $client = new Client($args);
        $client->save();
        $error = Message::getError();
        echo json_encode($error);
        exit();
    } else {
        header('Location: ../index');
        exit();
    }
?>