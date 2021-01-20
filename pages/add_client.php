<?php 
    if(!isset($_SESSION['id'])) { 
        header('Location: login');
        exit();
    }
    require_once('section/bank_list.php');
    require_once('section/town_array.php');
?>
<main>
<div class="wrapper">
<div class="form-container m-auto">
<form action="include/add_client.inc.php" enctype="multipart/form-data" method="POST">
    <h2 class="card__header text-center card__header-border weight-500 mb-xs">Dodaj klijenta</h2>
    <div class="card-body">
        <div class="mb-xs">
            <label for="ime">Ime klijenta(firme ili fizičkog lica)</label>
            <input type="text" name="ime" id="ime"  class="form__input">
            <span class="registration-form__error"></span>
        </div>
        <div class="mb-xs">
            <label for="logo" class="file-label mb-xs">Dodaj logotip</label>
            <input type="file" name="logo" id="logo" class="form__input-file">
            <img src="" alt="" class="firm-logo d-block m-auto">
            <span class="registration-form__error"></span>
        </div>
        <div class="mb-xs">
            <label for="jib">Jedinstveni identifikacioni broj/JMBG</label>
            <input type="text" name="jib" id="jib"  class="form__input">
            <span class="registration-form__error"></span>
        </div>
        <div class="mb-xs">
            <p class="mb-xs">Da li je firma u sistemu PDV-a?</p>
            <div class="d-flex">
                <div class="mb-xs w-100">
                    <input type="radio" name="pdv" id="pdv-da" value="1">
                    <label for="pdv-da">Da</label>
                </div>
                <div class="w-100">
                    <input type="radio" checked name="pdv" id="pdv-ne" value="0">
                    <label for="pdv-ne">Ne</label>
                </div>
            </div>
            <span class="registration-form__error"></span>
        </div>
        <div class="mb-xs d-none">
            <label for="pib">Poreski identifikacioni broj</label>
            <input type="text" name="pib" id="pib" disabled class="form__input pib">
            <span class="registration-form__error"></span>
        </div>
        <div class="mb-xs">
            <label for="vlasnik">Ime vlasnika</label>
            <input type="text" name="vlasnik" id="vlasnik" class="form__input">
            <span class="registration-form__error"></span>
        </div>
        <div class="mb-xs">
            <label for="adresa">Adresa firme</label>
            <input type="text" name="adresa" id="adresa" class="form__input">
            <span class="registration-form__error"></span>
        </div>
        <div class="mb-xs">
            <label for="mjesto">Mjesto</label>
            <input type="text" name="mjesto" id="mjesto" class="form__input">
            <span class="registration-form__error"></span>
        </div>
        <div class="mb-xs">
            <label for="broj">Broj telefona</label>
            <input type="text" name="telefon" id="broj" class="form__input">
            <span class="registration-form__error"></span>
        </div>
        <div class="mb-xs">
            <label for="email">Email adresa</label>
            <input type="text" name="email" id="email" class="form__input">
            <span class="registration-form__error"></span>
        </div>
        <div class="mb-s">
            <label for="racun">Žiro račun</label>
            <input type="text" name="racun" id="racun" class="form__input">
            <span class="registration-form__error"></span>
        </div>
        <div class="mb-s">
            <label for="banka">Banka</label>
            <select name="banka" id="banka" class="form__input">
                <option value=""></option>
                <?php foreach($bankArray as $bank) { ?>
                    <option value="<?php echo $bank ?>"><?php echo $bank ?></option>
                <?php } ?>
            </select>
            <span class="registration-form__error"></span>
        </div>
        <p class="success-message mb-xs text-center"></p>
        <button class="btn btn-primary" name="submit">Potvrdi</button>
    </form>
</div>
</div>
</main>

