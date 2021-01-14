<?php 
    if(!isset($_SESSION['id'])) { 
        header('Location: login');
        exit();
    }
?>
<main>
<div class="wrapper d-flex jc-c ai-c">
<div class="form-container m-auto">
<form action="include/add_bill.inc.php" enctype="multipart/form-data"  method="POST">
    <h2 class="card__header text-center card__header-border weight-500 mb-xs">Dodaj fiskalni račun</h2>
    <p class="success-message mb-xs text-center"></p>
    <div class="card-body">
        <div class="mb-xs">
            <label for="broj">Broj fiskalnog računa</label>
            <input type="text" name="broj" id="broj"  class="form__input">
            <span class="registration-form__error"></span>
        </div>
        <div class="mb-xs">
            <label for="datum">Datum izdavanja računa</label>
            <input type="date" name="datum" id="datum"  class="form__input" placeholder="datum.mjesec.godina npr. 02.07.2020">
            <span class="registration-form__error"></span>
        </div>
        <div class="mb-xs">
            <label for="logo" class="file-label mb-xs">Slika računa</label>
            <input type="file" name="slika" id="logo" class="form__input-file">
            <img src="" alt="" class="firm-logo d-block m-auto">
            <span class="registration-form__error"></span>
        </div>
        <button class="btn btn-primary" name="submit">Potvrdi</button>
    </form>
</div>
</div>
</main>

<script src="<?php base(); ?>javascript/add_bill.js"></script>
