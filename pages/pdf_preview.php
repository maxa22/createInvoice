<?php
if(!isset($_SESSION['id'])) {
    header('Location: login');
    exit();
}

require_once('include/autoloader.php');
if(!isset($_GET['id'])) {
    header('Location: index');
    exit();
}

$id = $_GET['id'];
Validate::validateString('id', $id);
if(Message::getError()) {
    header('Location: ../index');
    exit();
}
$invoice = Invoice::findById($id);
if($invoice['userId'] !== $_SESSION['id']) {
    header('Location: ../index');
    exit();
}
$firm = Firm::findById($invoice['firmaId']);
$client = Client::findById($invoice['kupacId']);

$date = strtotime($invoice['datum']);
$datum = date('d.m.Y', $date);
$rok = strtotime($invoice['rok']);
$rok = date('d.m.Y', $rok);
$articles = InvoiceArticle::findAllByQuery('fakturaId', $id);
?>

<main>
<div class="mt-s mb-s">
    <h1>Pregled fakture</h1>
</div>
    <div class="pdf-wrapper">
        <div class="pdf-header">
            <div class="pdf-header__logo">
                <?php if($firm['logo']) { ?>
                    <img src="<?php base(); ?>images/<?php echo $firm['logo']; ?>" alt="">
                <?php } ?>
            </div>
            <div class="pdf-header__info">
                <p><?php echo $firm['ime']; ?></p>
                <?php if($firm['adresa']) { ?>
                    <p><?php echo $firm['adresa']; ?></p>
                <?php } ?>
                <?php if($firm['jib']) { ?>
                    <p><?php echo $firm['jib']; ?></p>
                <?php } ?>
                <?php if($firm['telefon']) { ?>
                    <p><?php echo $firm['telefon']; ?></p>
                <?php } ?>
            </div>
        </div>
        <div class="pdf-line"></div>
        <div class="pdf-invoice-info">
            <p>Datum izdavanja: <?php echo $datum ?></p>
            <p>Mjesto izdavanja: <?php echo $invoice['mjesto'] ?></p>
            <p>Način plaćanja: <?php echo $invoice['nacin'] ?></p>
            <p>Rok plaćanja: <?php echo $rok ?></p>
        </div>
        <div class="pdf-client-info">
            <p>Klijent: <?php echo $client['ime'] ?></p>
            <?php if($client['adresa']) ?>
                <p><?php echo $client['adresa']; ?></p>
            <?php ?>
            <?php if($client['jib']) ?>
                <p><?php echo $client['jib']; ?></p>
            <?php ?>
            <?php if($client['telefon']) ?>
                <p><?php echo $client['telefon']; ?></p>
            <?php ?>
        </div>
        <div class="pdf-invoice-number">
            <h2><?php echo $invoice['tip'] . ' broj: ' . $invoice['broj']; ?></h2>
        </div>
        <table class="pdf-invoice-articles">
            <tr>
                <td class="pdf-invoice-first">Redni broj</td>
                <td class="text-left">Naziv robe</td>
                <td>Količina</td>
                <td>Cijena</td>
                <td>Rabat</td>
                <?php if($firm['pdv'] == '1') { ?>
                    <td>Ukupno bez PDV</td>
                    <td>PDV</td>
                <?php } ?>
                <td class="pdf-invoice-last">Ukupno</td>
            </tr>
            <?php
                $i = 1;
                $ukupno = 0;
                $ukupnoPdv = 0;
                $ukupnoBezPdv = 0;
                foreach($articles as $article) {
            ?>
            <tr>
                <td class="pdf-invoice-first"><?php echo $i; ?></td>
                <td class="text-left"><?php echo $article['ime']; ?></td>
                <td><?php echo $article['kolicina']; ?></td>
                <td><?php echo $article['cijena']; ?>KM</td>
                <td><?php echo $article['rabat']; ?>%</td>
                <?php if($firm['pdv'] == '1') { ?>
                    <td><?php echo $article['bezPdv']; ?>KM</td>
                    <td><?php echo $article['pdv']; ?>KM</td>
                <?php
                 $ukupnoPdv += $article['pdv'];
                 $ukupnoBezPdv += $article['bezPdv'];
                } ?>
                <td class="pdf-invoice-last"><?php echo $article['ukupno']; ?>KM</td>
                <?php $ukupno += $article['ukupno']; $i++; ?>
            </tr>
            <?php } ?>
        </table>
        <div class="pdf-signature">
            <p>Fakturisao:</p>
            <p><?php echo $invoice['fakturista']; ?></p>
            <p class="pdf-signature__line"></p>
        </div>
        <table class="pdf-total-info">
            <?php if($firm['pdv'] == '1') { ?>
                <tr>
                    <td>Ukupno bez PDV: </td>
                    <td class="text-right"><?php echo sprintf("%.2f", $ukupnoBezPdv) . 'KM'; ?></td>
                </tr>
                <tr>
                    <td>Ukupno PDV:</td>
                    <td class="text-right"><?php echo sprintf("%.2f", $ukupnoPdv) . 'KM'; ?></td>
                </tr>
            <?php }?>
            <tr>
                <td>Ukupno:</td>
                <td class="text-right"><?php echo sprintf("%.2f", $ukupno) . 'KM'; ?></td>
            </tr>
        </table>
        <div class="clear-both"></div>
        <div class="mt-m">
            <a href="<?php base(); ?>render_pdf/<?php echo $id; ?>"  target="_blank" rel="nooklijenta" class="btn btn-primary">Ispiši PDF</a>
            <a href="<?php base(); ?>update_invoice/<?php echo $id; ?>" class="btn btn-primary">Uredi fakturu</a>
        </div>
    </div>
</main>