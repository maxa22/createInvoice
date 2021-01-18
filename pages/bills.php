<!--  rendering user bills -->
<?php
if(!isset($_SESSION['id'])) {
    header('Location: login');
    exit();
}
?>

<main>
    <div class="hero">
    <div class="mt-s mb-s">
        <h1>Fiskalni računi</h1>
    </div>
<?php 
    require_once('include/autoloader.php');

    $id = $_SESSION['id'];
    $bills = Bill::findAllByQuery('userId', $id);
    if(count($bills) > 0) { ?>
        <div class="d-flex gap-m wrap s-flex-column">
        <?php
         foreach($bills as $bill) { 
        ?> 
            <div class="w-25-gap-m l-w-50-gap-m s-w-100 card">
            <form action="" method="POST">
                <div class="text-right d-flex jc-f-end">
                    <span class="edit info editing p-xs">
                        <i class="fas fa-edit d-iblock w-100"></i>
                    </span>
                </div>
                <div class="p-xs">
                    <div class="mb-xs">
                        <label for="<?php echo $bill['id'] . '-broj'; ?>">Broj računa</label>
                        <input type="text" class="form__input" disabled name="<?php echo $bill['id'] . '-broj'; ?>" id="<?php echo $bill['id'] . '-broj'; ?>" value="<?php echo $bill['broj']; ?>">
                        <span class="registration-form__error"></span>
                    </div>
                    <div class="mb-xs">
                        <label for="<?php echo $bill['id'] . '-datum'; ?>">Datum računa</label>
                        <input type="date" class="form__input" disabled name="datum" id="<?php echo $bill['id'] . '-datum'; ?>" value="<?php echo $bill['datum']; ?>">
                        <span class="registration-form__error"></span>
                    </div>
                    <div class="text-center">
                        <label for="<?php echo $bill['id'] . '-slika'; ?>" class="file-label mb-xs">Slika fiskalnog računa</label>
                        <input type="file" class="form__input-file" disabled name="slika" id="<?php echo $bill['id'] . '-slika'; ?>" value="<?php echo $bill['slika']; ?>">
                        <img src="<?php echo $bill['slika'] ? base() . 'images/' . $bill['slika'] : ''; ?>" class="w-100">
                        <span class="registration-form__error"></span>
                    </div>
                    <div>
                        <button class="btn btn-primary save w-100 mb-xs d-none" name="submit">Sačuvaj</button>
                        <span class="btn btn-secondary cancel w-100 text-center d-none">Poništi</span>
                    </div>
                </div>
            </form>
            </div>
    <?php } ?>
        </div>
    <?php } else { ?>
        <p>Niste dodali nijedan fiskalni račun...</p>
    <?php } ?>
    <div class="mt-m">
        <a href="<?php base(); ?>add_bill" class="btn btn-primary">Dodaj fiskalni račun</a>
    </div>
</div>
</main>

<script src="<?php base(); ?>javascript/bills.js"></script>
