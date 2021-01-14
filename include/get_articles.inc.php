<?php
    session_start();
    if(!isset($_SESSION['id'])) {
        header('Location: ../login');
        exit();
    }

    if(isset($_POST['submit'])) {
        require_once('autoloader.php');
        $id = $_SESSION['id'];
        Validate::validateString('id', $id);
        if(Message::getError()) {
            header('Location: ../index');
            exit();
        }
        $id = Sanitize::sanitizeString($id);
        $articles = Article::findAllByQuery('userId', $id);
        echo json_encode($articles);
        exit();
    } else {
        header('Location: ../index');
        exit();
    }
?>