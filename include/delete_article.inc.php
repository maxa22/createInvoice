<?php
    session_start();
    if(!isset($_SESSION['id'])) {
        header('Location: ../login');
        exit();
    }
    if(isset($_GET['id'])) {
        require_once('autoloader.php');

        $id = $_GET['id'];
        Validate::validateString('id', $id);
        $id = Sanitize::sanitizeString($id);
        $error = Message::getError();
        if($error) {
            header('Location: ../articles/' . $firmId);
            exit();
        }
        $articleArguments = Article::findById($id);
        if($articleArguments['userId'] != $_SESSION['id']) {
            header('Location: ../firms');
            exit();
        }
        $article = new Article($articleArguments);
        $article->delete();
        header('Location: ../articles');
        exit();
    } else {
        header('Location: ../index');
        exit();
}