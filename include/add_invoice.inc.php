<?php
    // validate and sanitize user input and add invoice to table Fakture
    session_start();
    if(!isset($_SESSION['id'])) {
        header('Location: ../login');
        exit();
    }
 
    
    if(isset($_POST['submit'])) {
        
        require_once('autoloader.php');
        
        $args['firmaId'] = $_POST['firma'];
        $args['broj'] = $_POST['broj'];
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
        while(isset($_POST[$i . '-imeArtikla'])) {
            $articleArgs[$i]['ime'] = Sanitize::sanitizeString($_POST[$i . '-imeArtikla']);
            Validate::validateString($i . '-imeArtikla',$_POST[$i . '-imeArtikla']);
            $articleArgs[$i]['cijena'] = Sanitize::sanitizeString($_POST[$i . '-cijena']);
            Validate::validateNumber($i . '-cijena',$_POST[$i . '-cijena']);
            $articleArgs[$i]['kolicina'] = Sanitize::sanitizeString($_POST[$i . '-kolicina']);
            Validate::validateNumber($i . '-kolicina',$_POST[$i . '-kolicina']);
            $i++;
        }

        $error = Message::getError();

        if($error) {
            echo json_encode($error);
            exit();
        }
        $invoice = new Invoice($args);
        $invoice->save();
        foreach($articleArgs as $key => $value) {
            $articleArgs[$key]['fakturaId'] = $invoice->getId();
            $invoiceArticle = new InvoiceArticle($articleArgs[$key]);
            $invoiceArticle->save();
        }
        $error = Message::getError();
        if(!$error) {
            $error['success'] = $invoice->getId();
        }
        echo json_encode($error);
        exit();
    } else {
        header('Location: ../index');
        exit();
    }
?>