<!--  rendering  clients -->
<?php
if(!isset($_SESSION['id'])) {
    header('Location: login');
    exit();
}
?>

<main>
<div class="hero">
<div class="mt-s mb-s">
    <h1>Klijenti</h1>
</div>
<?php 
    require_once('include/autoloader.php');

    $id = $_SESSION['id'];
    $clients = Client::findAllByQuery('userId', $id);
    if(count($clients) > 0) { ?>
        <div class="d-flex gap-m wrap s-flex-column">
        <?php
         foreach($clients as $client) { 
        ?> 
            <div class="w-25-gap-m l-w-50-gap-m s-w-100 card">
                <div class="card-body text-center">
                <!-- promijeniti src slike da bude zapravo logo -->
                <?php if($client['logo']) { ?>
                    <img src="images/<?php echo $client['logo']; ?>"  class="d-block w-100 mb-s">
                <?php } ?>

                <h3 class="calculator__heading uppercase mb-s">
                    <?php echo $client['ime']; ?>
                </h3>
                <div class="text-left">
                    <span class="d-block mb-xs">JIB: <?php echo $client['jib']; ?></span>
                    <span class="d-block mb-xs">PIB: <?php echo $client['pib'] ?? ''; ?></span>
                    <span class="d-block mb-xs">Vlasnik: <?php echo $client['vlasnik']; ?></span>
                    <span class="d-block mb-xs">Adresa: <?php echo $client['adresa']; ?></span>
                    <span class="d-block mb-xs">Email: <?php echo $client['email']; ?></span>
                    <span class="d-block mb-xs">Telefon: <?php echo $client['telefon']; ?></span>
                </div>
                <a href="<?php base(); ?>update_client/<?php echo $client['id']; ?>" class="btn btn-info w-100">Uredi</a>
            </div>
            </div>
    <?php } ?>
        </div>
    <?php } else { ?>
        <p>Niste dodali nijednu klijenta...</p>
    <?php } ?>
    <div class="mt-m">
        <a href="<?php base(); ?>add_client" class="btn btn-primary">Dodaj klijenta</a>
    </div>
</div>
</div>
</main>


