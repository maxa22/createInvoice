<!--  rendering  invoices -->
<?php
if(!isset($_SESSION['id'])) {
    header('Location: login');
    exit();
}

require_once('include/autoloader.php');
$invoices = Invoice::findAllByQuery('userId', $_SESSION['id']);
?>

<main>
<div class="hero">
    <div class="mt-s mb-s">
        <h1>Fakture</h1>
    </div>
    <?php if(count($invoices) > 0) { ?>
        <div class="d-flex gap-m wrap s-flex-column mb-m">
    <?php foreach($invoices as $invoice) { 
            $rok = strtotime($invoice['rok']);
            $rok = date('d.m.Y', $rok);
            $datum = strtotime($invoice['datum']);
            $datum = date('d.m.Y', $datum);
            $client = Client::findById($invoice['kupacId']);
            $firm = Firm::findById($invoice['firmaId']);
    ?>
        <div class="w-25-gap-m l-w-50-gap-m s-w-100 card relative">
            <a href="<?php base(); ?>include/delete_invoice.inc.php?id=<?php echo $invoice['id']; ?>" class="p-xs danger delete__invoice">
                <i class="fas fa-trash d-iblock w-100"></i>
            </a>
            <div class="card-body">
                <span class="d-block">Firma: </span><span class="d-block mb-xs"> <?php echo $firm['ime'];?></span>
                <span class="d-block">Broj fakture: </span><span class="d-block mb-xs"> <?php echo $invoice['broj'];?></span>
                <span class="d-block">Klijent:</span> <span class="d-block mb-xs"> <?php echo $client['ime'];?></span>
                <span class="d-block">JIB klijenta: </span><span class="d-block mb-xs"> <?php echo $client['jib'];?></span>
                <span class="d-block">Adresa klijenta: </span><span class="d-block mb-xs"> <?php echo $client['adresa'];?></span>
                <span class="d-block">Mjesto izdavanja: </span><span class="d-block mb-xs"> <?php echo $invoice['mjesto'] ?? ''; ?></span>
                <span class="d-block">Datum izdavanja: </span><span class="d-block mb-xs"> <?php echo $datum; ?></span>
                <span class="d-block">Način plaćanja: </span><span class="d-block mb-xs"> <?php echo $invoice['nacin']; ?></span>
                <span class="d-block">Rok plaćanja: </span><span class="d-block mb-xs"> <?php echo $rok; ?></span>
                <span class="d-block">Fakturisao: <span class="d-block mb-xs"> <?php echo $invoice['fakturista']; ?></span></span>
                <a href="pdf_preview/<?php echo $invoice['id'] ?>" class="btn btn-info w-100 text-center mb-xs">Pregled</a>
                <a href="render_pdf/<?php echo $invoice['id'] ?>"  target="_blank" rel="nooklijenta" class="btn btn-info w-100 text-center mb-xs">Ispiši PDF</a>
                <a href="update_invoice/<?php echo $invoice['id'] ?>" class="btn btn-info w-100 text-center">Uredi</a>
            </div>
        </div>
    <?php } ?>
        </div>
    <?php } else { ?>
        <p>Niste kreirali nijednu fakturu...</p>
    <?php } ?>
    <a href="add_invoice" class="btn btn-primary mt-m">Kreiraj fakturu</a>
</div>
</main>