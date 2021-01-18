<?php

if(!isset($_SESSION['id'])) {
    header('Location: ../index');
    exit();
}
// include autoloader
require_once 'dompdf/autoload.inc.php';

// reference the Dompdf namespace
use Dompdf\Dompdf;

// instantiate and use the dompdf class
$dompdf = new Dompdf();

require_once('include/autoloader.php');
//html
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
if($firm['logo']) {
    $path = 'images/' . $firm['logo'];
    $type = pathinfo($path, PATHINFO_EXTENSION);
    $data = file_get_contents($path);
    $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
}

$pdfName = $firm['ime'] . '-' . $invoice['broj'] . '-' . $datum;
$pdfName = str_replace(' ', '_', $pdfName); 
$pdfName = str_replace(['/', '?'], '', $pdfName);

$articles = InvoiceArticle::findAllByQuery('fakturaId', $id);

ob_start();
?>
<style>
* { 
    font-family: DejaVu Sans;
    font-size: 12px;
}
p, div {
    margin: 0px;
    padding: 0px;
}
.pdf-header__logo {
  float: left;
  height: 100px;
}
.pdf-header__logo img {
  height: 100%;
}
.pdf-header__info {
  float: right;
  font-size: 14px;
}

.pdf-line {
  clear: both;
  height: 2px;
  background-color: #007bff;
  margin-bottom: 25px;
}
.pdf-invoice-info {
  float: left;
  margin-bottom: 20px;
}
.pdf-wrapper p {
  margin-bottom: 5px;
}
.pdf-client-info {
  float: right;
}
.pdf-invoice-number {
  clear: both;
  background-color: #007bff;
  color: #fff;
  text-align: center;
  margin-bottom: 25px;
}
.pdf-invoice-number h2 {
    padding: 2px;
}
.pdf-invoice-articles {
  width: 100%;
  border-collapse: collapse;
  margin-bottom: 50px;
  text-align: right;
}
.pdf-invoice-articles td {
  border-bottom: 1px solid #000;
  padding: 0px 5px;
}
td.pdf-invoice-last {
  padding-right: 0px;
}
td.pdf-invoice-first {
  text-align: left;
  width: 10%;
}
.pdf-signature {
  float: left;
  width: 20%;
}
.pdf-signature__line {
  width: 100%;
  height: 1px;
  background-color: #000;
  margin-top: 25px;
}
.pdf-total-info {
  float: right;
  border: 1px solid #000;
  padding: 5px;
}
.pdf-total-info td {
  padding: 0 5px;
}
.text-left {
    text-align: left;
}
.text-right {
    text-align: right;
}
.w-40 {
    width: 30%;
}
</style>
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
        <td class="text-left w-40">Naziv robe</td>
        <td>Količina</td>
        <td>Cijena</td>
        <td>Popust</td>
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
        <td class="text-left w-40"><?php echo $article['ime']; ?></td>
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
        <td class="text-right" {><?php echo sprintf("%.2f", $ukupno) . 'KM'; ?></td>
    </tr>
</tab }le>
</div>
<?php 
$html = ob_get_clean();


$html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');

$dompdf->loadHtml($html);

// (Optional) Setup the paper size and orientation
$dompdf->setPaper('A4', 'portrait');

// Render the HTML as PDF
$dompdf->render();

// Output the generated PDF to Browser
$dompdf->stream($pdfName);