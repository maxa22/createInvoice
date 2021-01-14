<!--  rendering user firms -->
<?php
if(!isset($_SESSION['id'])) {
    header('Location: login');
    exit();
}
?>

<main>
<div class="hero">
<div class="mt-s mb-s">
    <h1>Firme</h1>
</div>
<?php 
    require_once('include/autoloader.php');

    $id = $_SESSION['id'];
    $firms = Firm::findAllByQuery('userId', $id);
    if(count($firms) > 0) { ?>
        <div class="d-flex gap-m wrap s-flex-column">
        <?php
         foreach($firms as $firm) { 
        ?> 
            <div class="w-25-gap-m l-w-50-gap-m s-w-100 card">
                <div class="card-body text-center">
                <img src="images/<?php echo $firm['logo'] ?>" alt="business photo" class="d-block w-100 mb-s">

                <h3 class="calculator__heading uppercase mb-s">
                    <?php echo $firm['ime']; ?>
                </h3>
                <div class="text-left">
                    <span class="d-block mb-xs">JIB: <?php echo $firm['jib'];?></span>
                    <span class="d-block mb-xs">PIB: <?php echo $firm['pib'] ?? ''; ?></span>
                    <span class="d-block mb-xs">Vlasnik: <?php echo $firm['vlasnik']; ?></span>
                    <span class="d-block mb-xs">Adresa: <?php echo $firm['adresa']; ?></span>
                    <span class="d-block mb-xs">Email: <?php echo $firm['email']; ?></span>
                    <span class="d-block mb-xs">Telefon: <?php echo $firm['telefon']; ?></span>
                </div>
                <a href="<?php base(); ?>update_firm/<?php echo $firm['id']; ?>" class="btn btn-info d-block w-100 mb-xs">Uredi</a>
            </div>
            </div>
    <?php } ?>
        </div>
    <?php } else { ?>
        <p>Niste dodali nijednu firmu...</p>
    <?php } ?>
    <div class="mt-m">
        <a href="<?php base(); ?>add_firm" class="btn btn-primary">Dodaj firmu</a>
    </div>
</div>
</div>
</main>


