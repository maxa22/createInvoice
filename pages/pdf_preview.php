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
$popust = 0;
foreach($articles as $article) {
    if($article['rabat'] != '0') {
        $popust += 1;
        break;
    }
}
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
                <p>Firma: <?php echo $firm['ime']; ?></p>
                
                <?php if($firm['adresa']) { ?>
                    <p>Adresa: <?php echo $firm['adresa']; ?>
                    <?php if($firm['mjesto']) { ?>
                        <?php echo ', ' . $firm['mjesto']; ?>
                    <?php } ?>
                </p>
                <?php } ?>
                <?php if($firm['telefon']) { ?>
                    <p>Telefon: <?php echo $firm['telefon']; ?></p>
                <?php } ?>
                <?php if($firm['email']) { ?>
                    <p>Email: <?php echo $firm['email']; ?></p>
                <?php } ?>
                <?php if($firm['jib']) { ?>
                    <p>JIB: <?php echo $firm['jib']; ?></p>
                <?php } ?>
                <?php if($firm['pib']) { ?>
                    <p>PIB: <?php echo $firm['pib']; ?></p>
                <?php } ?>
                <?php if($firm['racun']) { ?>
                    <p>TR: <?php echo $firm['racun'] ?></p>
                    <?php if($firm['banka']) { ?>
                        <p>Banka: <?php echo $firm['banka']; ?></p>
                    <?php } ?>
                <?php } ?>
            </div>
        </div>
        <div class="pdf-line"></div>
        <div class="pdf-invoice-info">
            <p>Datum izdavanja: <?php echo $datum ?></p>
            <p>Mjesto izdavanja: <?php echo $invoice['mjesto'] ?></p>
            <p>Način plaćanja: <?php echo $invoice['nacin'] ?></p>
            <p>Rok za plaćanje: <?php echo $rok ?></p>
        </div>
        <div class="pdf-client-info">
            <p>Klijent: <?php echo $client['ime'] ?></p>
            <?php if($client['adresa']) { ?>
                <p>Adresa: <?php echo $client['adresa'] ?>
                <?php if($client['mjesto']) { ?>
                    <?php echo ', ' . $client['mjesto']; ?>
                <?php } ?>
                </p>
            <?php } ?>
            <?php if($client['telefon']) { ?>
                <p>Telefon: <?php echo $client['telefon']; ?></p>
            <?php } ?>
            <?php if($client['email']) { ?>
                <p>Email: <?php echo $client['email']; ?></p>
            <?php } ?>
            <?php if($client['jib']) { ?>
                <p>JIB: <?php echo $client['jib']; ?></p>
            <?php } ?>
            <?php if($client['pib']) { ?>
                <p>PIB: <?php echo $client['pib']; ?></p>
            <?php } ?>
            <?php if($client['racun']) { ?>
                <p>Račun: <?php echo $client['racun']; ?></p>
                <?php if($client['banka']) { ?>
                    <p>Banka: <?php echo $firm['banka']; ?></p>
                <?php } ?>
            <?php } ?>
        </div>
        <div class="pdf-invoice-number">
            <h2><?php echo $invoice['tip'] . ' broj: ' . $invoice['broj']; ?></h2>
        </div>
        <table class="pdf-invoice-articles">
            <tr>
                <td class="pdf-invoice-first">Redni broj</td>
                <td class="text-left w-30">Naziv robe</td>
                <td>Količina</td>
                <td>Cijena</td>
                <?php if($popust > 0) { ?>
                    <td>Popust</td>
                <?php } ?>
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
                <td class="text-left w-30"><?php echo $article['ime']; ?></td>
                <td><?php echo $article['kolicina']; ?></td>
                <td><?php echo $article['cijena']; ?>KM</td>
                <?php if($popust > 0) { ?>
                    <td><?php echo $article['rabat']; ?>%</td>
                <?php } ?>
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