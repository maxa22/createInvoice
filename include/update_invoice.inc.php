<?php
    // validate and sanitize user input and add invoice to table Fakture
    session_start();
    if(!isset($_SESSION['id'])) {
        header('Location: ../login');
        exit();
    }
 
    
    if(isset($_POST['submit'])) {
        
        require_once('autoloader.php');

        foreach($_POST as $key => $value) {
            if(strpos($key, 'broj')) {
                $args['id'] = explode('-', $key)[0];
                $args['broj'] = $value;
                break;
            }
        }
        
        $args['firmaId'] = $_POST['firma'];
        $args['mjesto'] = $_POST['mjesto'];
        $args['nacin'] = $_POST['nacin'];
        $args['fakturista'] = $_POST['fakturista'];
        if(count(explode(',', $_POST['kupac'])) > 1) {
            $kupacIme = explode(',', $_POST['kupac'])[0];
            $kupacMjesto = trim(explode(',', $_POST['kupac'])[1]);
            $kupac = Client::findAllByQuery3('ime', $kupacIme, 'mjesto', $kupacMjesto, 'userId', $_SESSION['id']);
        } else {
            $kupac = Client::findAllByQuery2('ime', trim($_POST['kupac']), 'userId', $_SESSION['id']);
        }
        $args['userId'] = $_SESSION['id'];
        $args['kupacId'] = $kupac['id'];
        foreach($args as $key => $value) {
            Validate::validateString($key, $args[$key]);
            $args[$key] = Sanitize::sanitizeString($value);
        }

        
        $args['rok'] = $_POST['rok'];
        if(!empty($_POST['rok'])) {
            list($year, $month, $day) = explode('-', $_POST['rok']); 
            if(!checkdate($month, $day, $year)) {
                Message::addError('rok', 'Insert valid date');
            }
        } else {
            Message::addError('rok', 'Date can\'t be empty');
        }

        
        $args['datum'] = $_POST['datum'];
        if(!empty($_POST['datum'])) {
            list($year, $month, $day) = explode('-', $_POST['datum']); 
            if(!checkdate($month, $day, $year)) {
                Message::addError('datum', 'Insert valid date');
            }
        } else {
            Message::addError('rok', 'Date can\'t be empty');
        }

        $i = 1;
        foreach($_POST as $key => $value) {
            if(strpos($key, 'imeArtikla')) {
                $id = explode('-', $key)[0];
                $articleArgs[$id]['ime'] = Sanitize::sanitizeString($value);
                Validate::validateString($key, $value);
                continue;
            }
            if(strpos($key, 'cijena')) {
                if(count(explode('-', $key)) > 2) {
                    $id = explode('-', $key)[0];
                    $articleId = explode('-', $key)[2];
                    $articleArgs[$id]['id'] = Sanitize::sanitizeString($articleId);
                } else {
                    $id = explode('-', $key)[0];
                }
                $articleArgs[$id]['cijena'] = Sanitize::sanitizeString($value);
                Validate::validateNumber($key, $value);
                Validate::validateNumber($key, $articleId);
                continue;
            }
            if(strpos($key, 'kolicina')) {
                $id = explode('-', $key)[0];
                $articleArgs[$id]['kolicina'] = Sanitize::sanitizeString($value);
                Validate::validateNumber($key, $value);
                continue;
            }
        }

        $error = Message::getError();

        if($error) {
            echo json_encode($error);
            exit();
        }
        $invoice = new Invoice($args);
        $invoice->save();
        foreach($articleArgs as $key => $value) {
            $articleArgs[$key]['fakturaId'] = $args['id'];
            $invoiceArticle = new InvoiceArticle($articleArgs[$key]);
            $invoiceArticle->save();
        }
        $error = Message::getError();
        echo json_encode($error);
        exit();
    } else {
        header('Location: ../index');
        exit();
    }
?>