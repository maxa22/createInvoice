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
$path = 'images/' . $firm['logo'];
$type = pathinfo($path, PATHINFO_EXTENSION);
$data = file_get_contents($path);
$base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);

$articles = InvoiceArticle::findAllByQuery('fakturaId', $id);

$output = '
<style>

    * { 
        font-family: DejaVu Sans;
        font-size: 12px;
    }
    p {
        margin: 0;
        margin-bottom: 5px;
        padding: 0;
    }
    div {
        margin:0;
        margin-bottom: 20px;
        padding: 0;
    }

    table {
        border-collapse: collapse;
        width: 100%;
    }
    .ukupno td {
        border: none;
    }
    td {
        border-bottom: 1px solid black;
        padding: 0 5px;
    }
</style>
<div style= margin-bottom: 0;">
    <div style="float:left; margin-bottom: 0;">
        <img src="' . $base64 .'" style=" height: 100px;">
    </div>
    <div style="float:right;">
    <p style="font-size: 14px;">' . $firm['ime'] . '</p>
    <p style="font-size: 14px;">' . $firm['adresa'] . '</p>
    <p style="font-size: 14px;">JIB: ' . $firm['jib'] . '</p>';
    if($firm['pib'] !== '-') {
        $output .=  '<p style="font-size: 14px;"> PIB: ' . $firm['pib'] . '</p>';
    }
    $output .= '<p style="font-size: 14px; margin-bottom: 0;">Telefon: ' . $firm['telefon'] . '</p>
    </div>
</div>
<div style="clear:both; height: 2px; background-color: #007bff;">
</div>
<div style="float:left; width=40%; margin-top: 25px;">
    <p>Datum izdavanja: ' . $datum .'</p>
    <p>Mjesto izdavanja: ' . $invoice['mjesto'] . '</p>
    <p>Način plaćanja: ' . $invoice['nacin'] . '</p>
    <p>Rok plaćanja: ' . $rok . '</p>
</div>
<div style="float:right; width=40%;"> 
    <p>Kupac: ' . $client['ime'] .'</p>
    <p>Adresa: ' . $client['adresa'] . '</p>
    <p>ID: ' . $client['jib'] . '</p>
    <p>Telefon: ' . $client['telefon'] . '</p>
</div>

<div style="clear:both; background-color: #007bff; text-align: center; color: #fff; padding: 2px;">
    <h2 style="padding: 0px; margin: 0px;">Račun broj: ' . $invoice['broj'] . '</h2>
</div>

<table style="margin-bottom: 50px;">
    <tr>
        <td>Redni broj</td>
        <td style="width: 40%">Naziv robe</td>
        <td>Količina</td>
        <td>Cijena</td>
        <td>Ukupno bez PDV</td>
        <td>PDV</td>
        <td>Ukupno sa PDV</td>
    </tr>';
    $i = 1;
    $ukupno = 0;
    $ukupnoPDV = 0;
    $ukupnoBezPDV = 0;
    foreach($articles as $article) {
        $pdv = sprintf("%.2f", $article['kolicina'] * $article['cijena'] * 14.53 / 100);
        $ukupnoPDV += $pdv;
        $cijena = sprintf("%.2f", $article['kolicina'] * $article['cijena']);
        $ukupno += $cijena;
        $bezPDV = sprintf("%.2f", $cijena - $pdv);
        $ukupnoBezPDV += $bezPDV;
        $output .= '
        <tr>
            <td>' . $i . '</td>
            <td>' . $article['ime'] . '</td>
            <td>' . $article['kolicina'] . '</td>
            <td>' . $article['cijena'] . '</td>
            <td>' . $bezPDV . 'km</td>
            <td>' . $pdv . 'km</td>
            <td>' . $cijena . 'km</td>
        </tr>
        ';
        $i++;
    }
$output .= '</table>';

$output .= '
<div style="float:right; width: 40%; border: 1px solid black; padding: 3px; margin: 20px 0 0 0;" class="ukupno">
    <table>
    <tr>
        <td>Ukupno bez PDV: </td>
        <td>' . sprintf("%.2f", $ukupnoBezPDV) . '</td>
    </tr>
    <tr>
        <td>Ukupno PDV: </td>
        <td>' . sprintf("%.2f", $ukupnoPDV) . '</td>
    </tr>
    <tr>
        <td>Ukupno: </td>
        <td>' . sprintf("%.2f", $ukupno) . '</td>
    
    </tr>
    </table>
</div>
<div style="width:30%;">
    <p>Fakturisao: </p>
    <p style="margin-bottom: 25px;"> ' . $invoice['fakturista'] . '</p>
    <p style="height: 1px; background-color: #000;"></p>
</div>
';

$html = mb_convert_encoding($output, 'HTML-ENTITIES', 'UTF-8');

$dompdf->loadHtml($output);

// (Optional) Setup the paper size and orientation
$dompdf->setPaper('A4', 'portrait');

// Render the HTML as PDF
$dompdf->render();

// Output the generated PDF to Browser
$dompdf->stream('result.pdf', array('Attachment' => 0));