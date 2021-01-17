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
        $args['tip'] = $_POST['tip'];
        $args['fakturista'] = $_POST['fakturista'];
        $args['userId'] = $_SESSION['id'];
        $args['kupacId'] = $_POST['kupac'];
        foreach($args as $key => $value) {
            Validate::validateString($key, $args[$key]);
            $args[$key] = Sanitize::sanitizeString($value);
        }

        $args['nacin'] = $_POST['nacin'];
        if($args['nacin']) {
            Validate::validateString('nacin', $args['nacin']);
            $args['nacin'] = Sanitize::sanitizeString($args['nacin']);
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
            $articleArgs[$i]['kolicina'] = Sanitize::sanitizeString($_POST[$i . '-kolicina']);
            Validate::validateNumber($i . '-kolicina',$_POST[$i . '-kolicina']);
            $articleArgs[$i]['rabat'] = Sanitize::sanitizeString($_POST[$i . '-rabat']);
            Validate::validateNumber($i . '-rabat',$_POST[$i . '-rabat']);
            $articleArgs[$i]['ukupno'] = Sanitize::sanitizeString(substr($_POST[$i . '-ukupno'], 0, -2));
            Validate::validateNumber($i . '-ukupno', $articleArgs[$i]['ukupno']);
            if(isset($_POST[$i . '-pdv'])) { 
                $articleArgs[$i]['pdv'] = Sanitize::sanitizeString(substr($_POST[$i . '-pdv'], 0, -2));
                Validate::validateNumber($i . '-pdv',$articleArgs[$i]['pdv']);
                $articleArgs[$i]['bezPdv'] = Sanitize::sanitizeString(substr($_POST[$i . '-bezPdv'], 0, -2));
                Validate::validateNumber($i . '-bezPdv',$articleArgs[$i]['bezPdv']);
            }
            if(isset($_POST[$i . '-idArtikla'])) {
                $newArticleArgs[$i]['ime'] = Sanitize::sanitizeString($_POST[$i . '-imeArtikla']);
                $newArticleArgs[$i]['idArtikla'] = Sanitize::sanitizeString($_POST[$i . '-idArtikla']);
                $newArticleArgs[$i]['firmaId'] = Sanitize::sanitizeString($args['firmaId']);
                $newArticleArgs[$i]['opis'] = '';
                $newArticleArgs[$i]['userId'] = Sanitize::sanitizeString($_SESSION['id']);
            }
            $i++;
        }

        foreach($_POST as $key => $value) {
            if(strpos($key, 'cijena')) {
                if(count(explode('-', $key)) > 2) {
                    $id = explode('-', $key)[0];
                    $articleId = explode('-', $key)[2];
                    $articleArgs[$id]['id'] = Sanitize::sanitizeString($articleId);
                } else {
                    $id = explode('-', $key)[0];
                }
                if(isset($_POST[$id . '-idArtikla'])) {
                    $newArticleArgs[$id]['cijena'] = Sanitize::sanitizeString($value);
                }
                $articleArgs[$id]['cijena'] = Sanitize::sanitizeString($value);
                Validate::validateNumber($key, $value);
                Validate::validateNumber($key, $articleId);
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
        if(isset($newArticleArgs)) {
            foreach($newArticleArgs as $key => $value) {
                $article = new Article($newArticleArgs[$key]);
                $article->save();
            }
        }
        $error = Message::getError();
        echo json_encode($error);
        exit();
    } else {
        header('Location: ../index');
        exit();
    }
?>