<?php 
if(!isset($_SESSION['id'])) {
    header('Location: index');
    exit();
}
require_once('include/autoloader.php');
$id = $_SESSION['id'];
$articles = Article::findAllByQuery('userId', $id);
?>

<main>
<div class="wrapper d-flex jc-c ai-c">
    <div class="form-container m-auto mb-l">
    <h2 class="card__header text-center card__header-border weight-500 mb-xs">Dodaj Artikal</h2>
    <p class="success-message mb-xs text-center"></p>
    <form action="" id="newArticle" method="POST">
        <div class="card-container">
                <div class="card-body">
                    <div class="mb-xs">
                        <label for="<?php echo $id; ?>-id">Šifra</label>
                        <input type="text" name="idArtikla"  class="form__input">
                        <span class="registration-form__error"></span>
                    </div>
                    <div class="mb-xs">
                        <label for="ime">Naziv</label>
                        <input type="text" name="ime" id="ime" class="form__input">
                        <span class="registration-form__error"></span>
                    </div>
                    <div class="mb-xs">
                        <label for="cijena">Cijena</label>
                        <input type="number" step="0.01" name="cijena" id="cijena"  class="form__input">
                        <span class="registration-form__error"></span>
                    </div>
                    <div class="mb-xs">
                        <label for="opis">Opis</label>
                        <textarea rows="3" name="opis" id="opis"  class="form__input h-auto"></textarea>
                        <span class="registration-form__error"></span>
                    </div>  
                    <button name="submit" class="btn btn-primary">Sačuvaj</button>
                </div>
        </div>
    </form>
    </div>
    </div>
</main>
<script src="<?php base(); ?>javascript/add_article.js"></script>
