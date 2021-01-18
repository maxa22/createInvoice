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
    
    $clients = Client::findAllByQuery('userid', $_SESSION['id']);
    $invoiceClient = Client::findById($invoice['kupacId']);
    $firms = Firm::findAllByQuery('userId', $_SESSION['id']);
    $invoiceFirm = Firm::findById($invoice['firmaId']);
    $articles = InvoiceArticle::findAllByQuery('fakturaId', $id);
?>
<main>
<div class="wrapper">
<div class="card m-auto visible">
<form action="../include/update_invoice.inc.php" autocomplete="off"  method="POST">
    <h2 class="card__header text-center card__header-border weight-500 mb-xs">Uredi fakturu</h2>
    <div class="card-body">
    <p class="success-message mb-xs text-center"></p>
        <div class="d-flex gap-s mb-s m-flex-column">
            <div class="w-100">
                <label for="firma">Naziv firme</label>
                <select class="form__input required" name="firma" id="firma">
                        <option selected value="<?php echo $invoiceFirm['id']; ?>"><?php echo $invoiceFirm['ime']; ?></option>
                    <?php foreach($firms as $firm) { ?>
                        <option value="<?php echo $firm['id']; ?>"><?php echo $firm['ime'] ?></option>
                    <?php } ?>
                </select>
                <span class="registration-form__error"></span>
            </div>
            <div class="w-100">
                <label for="tip">Tip fakture</label>
                <select name="tip" id="tip" class="form__input required">
                    <option value="<?php echo $invoice['tip']; ?>" selected><?php echo $invoice['tip']; ?></option>
                    <option value="faktura">Faktura</option>
                    <option value="predracun">Predračun</option>
                    <option value="avansni">Avansni predračun</option>
                </select>
                <span class="registration-form__error"></span>
            </div>
            <div class="w-100">
                <label for="broj">Broj fakture</label>
                <input type="text" name="<?php echo $invoice['id']; ?>-broj" id="broj"  class="form__input required" value="<?php echo $invoice['broj']; ?>">
                <span class="registration-form__error"></span>
            </div>
            <div class="w-100">
                <label for="datum">Datum izdavanja</label>
                <input type="date" name="datum" id="datum" class="form__input required" value="<?php echo $invoice['datum']; ?>">
                <span class="registration-form__error"></span>
            </div>
        </div>
        <div class="d-flex gap-s mb-s m-flex-column">
            <div class="w-100">
                <label for="mjesto">Mjesto izdvananja</label>
                <input type="text" name="mjesto" id="mjesto" class="form__input required" value="<?php echo $invoice['mjesto']; ?>">
                <span class="registration-form__error"></span>
            </div>
            <div class="w-100 relative">
                <label for="kupac">Ime klijenta</label>
                <select name="kupac" id="kupac" class="form__input required">
                    <?php if(count($clients) > 0) {
                        foreach($clients as $client) {?>
                            <option value="<?php echo $client['id']; ?>"><?php echo $client['ime'] . ' ' . $client['mjesto']; ?></option>
                    <?php  }
                    } ?>
                </select>
                <span class="registration-form__error"></span>
            </div>
            <div class="w-100">
                <label for="nacin">Način plaćanja</label>
                <select name="nacin" class="form__input" id="nacin">
                    <option value="<?php echo $invoice['nacin']; ?>" selected><?php echo $invoice['nacin']; ?></option>
                    <option value="Virman">Virman</option>
                    <option value="Gotovina">Gotovina</option>
                    <option value="Ček">Ček</option>
                    <option value="Kartica">Kartica</option>
                    <option value="">Drugo</option>
                </select>
                <span class="registration-form__error"></span>
            </div>
            
        </div>
        <div class="d-flex gap-s mb-s m-flex-column">
            <div class="w-100">
                <label for="fakturista">Fakturisao</label>
                <input type="text" name="fakturista" id="fakturista" class="form__input required" value="<?php echo $invoice['fakturista']; ?>">
                <span class="registration-form__error"></span>
            </div>
            <div class="w-100">
                <label for="rok">Rok plaćanja</label>
                <input type="date" name="rok" id="rok" class="form__input required" value="<?php echo $invoice['rok']; ?>">
                <span class="registration-form__error"></span>
            </div>
            <div class="w-100"></div>
        </div>
        <div id="articles">
        <?php if(count($articles) > 0) { 
                $i = 0;
        ?>

            <div class="d-flex btn-primary">
                <span class="w-30 p-x border weight-500 hidden mm-d-none">Naziv</span>
                <span class="w-10 p-x border weight-500 hidden mm-d-none">Jed. cijena</span>
                <span class="w-10 p-x border weight-500 hidden mm-d-none">Količina</span>
                <span class="w-10 p-x border weight-500 hidden mm-d-none">Popust</span>
                <span class="w-15 p-x border weight-500 hidden mm-d-none">Cijena bez PDV</span>
                <span class="w-10 p-x border weight-500 hidden mm-d-none">PDV</span>
                <span class="w-15 p-x border weight-500 hidden mm-d-none">Ukupno</span>
            </div>
            <?php foreach($articles as $article) { 
                $i++;    
            ?>
            <div class="d-flex articlesNumber m-w-100 m-flex-column m-card m-mb-m">
                <div class="w-30 border relative m-d-flex m-w-100">
                    <span class="w-100 p-x btn-primary weight-600 d-none m-d-block">Naziv</span>
                    <div class="d-flex">
                        <div>
                            <input type="text" name="<?php echo $i; ?>-idArtikla" disabled class="w-100 p-xs border-none border-right form__input h-100 imeArtikla d-none" placeholder="Šifra">
                            <span class="registration-form__error"></span>
                        </div>
                        <div>
                            <input type="text" name="<?php echo $i; ?>-imeArtikla" disabled class="w-100 p-xs border-none form__input h-100 imeArtikla d-none " placeholder="Naziv">
                            <span class="registration-form__error"></span>
                        </div>
                    </div>
                    <select id="<?php echo $i; ?>-artikli" name="<?php echo $i; ?>-imeArtikla" class="w-100 p-xs border-none form__input h-100 imeArtikla dropdown ">
                        <option value="<?php echo $article['ime']; ?>" selected><?php echo $article['ime']; ?></option>
                    </select>
                    <span class="registration-form__error"></span>
                </div>
                <div class="w-10 border relative m-d-flex m-w-100">
                    <span class="w-100 p-x btn-primary weight-600 d-none m-d-block">Cijena</span>
                    <input type="number" step="0.01" name="<?php echo $i; ?>-cijena-<?php echo $article['id']; ?>" class="w-100 p-xs form__input border-none h-100 cijena" value="<?php echo $article['cijena']; ?>">
                    <span class="registration-form__error"></span>
                </div>
                <div class="w-10 border relative m-d-flex m-w-100">
                    <span class="w-100 p-x btn-primary weight-600 d-none m-d-block">Količina</span>
                    <input type="number" step="0.01" name="<?php echo $i; ?>-kolicina" class="w-100 p-xs form__input border-none h-100  kolicina" value="<?php echo $article['kolicina']; ?>">
                    <span class="registration-form__error"></span>
                </div>
                <div class="w-10 border relative m-d-flex m-w-100">
                    <span class="w-100 p-x btn-primary weight-600 d-none m-d-block">Popust</span>
                    <select name="<?php echo $i; ?>-rabat" class="w-100 p-xs border-none h-100 rabat form__input rabat required">
                        <option value="<?php echo $article['rabat']; ?>" selected><?php echo $article['rabat']; ?>%</option>
                        <?php for($j = 0; $j <= 100; $j++ ) { ?>
                            <option value="<?php echo $j ?>"><?php echo $j . '%'; ?></option>
                        <?php } ?>
                    </select>
                    <span class="registration-form__error"></span>
                </div>
                <div class="w-15 border m-d-flex m-w-100">
                    <span class="w-100 p-x btn-primary weight-600 d-none m-d-block">Cijena bez PDV</span>
                    <input type="text" name="<?php echo $i; ?>-bezPdv" <?php echo $invoiceFirm['pdv'] !== '0' ? '' : 'disabled' ?> class="w-100 p-xs form__input border-none h-100 bezPDV" value="<?php echo $invoiceFirm['pdv'] !== '0' ? $article['bezPdv'] . 'KM' : ''; ?>">
                    <span class="registration-form__error"></span>
                </div>
                <div class="w-10 border m-d-flex m-w-100">
                    <span class="w-100 p-x btn-primary weight-600 d-none m-d-block">PDV</span>
                    <input type="text" name="<?php echo $i; ?>-pdv" <?php echo $invoiceFirm['pdv'] !== '0' ? '' : 'disabled' ?> class="w-100 p-xs form__input border-none h-100 PDV" value="<?php echo $invoiceFirm['pdv'] !== '0' ? $article['pdv'] . 'KM' : ''; ?>">
                    <span class="registration-form__error"></span>
                </div>
                <div class="w-10 border m-d-flex m-w-100">
                    <span class="w-100 p-x btn-primary weight-600 d-none m-d-block">Ukupno</span>
                    <input type="text" name="<?php echo $i; ?>-ukupno" class="w-100 p-xs form__input border-none h-100 ukupno" value="<?php echo $article['ukupno'] . 'KM'; ?>">
                    <span class="registration-form__error"></span>
                </div>
                <div class="w-5 border remove d-flex jc-c ai-c m-d-flex pointer m-p-xs m-w-100">
                    <i class="fas fa-times remove-icon"></i>
                </div>
            </div>
            <?php } ?>
            
        <?php } ?>
        </div>
        <div class="w-40 m-w-100 mt-s card ml-auto">
            <div class="card-body">
                <h4 class="d-flex jc-sb ai-c <?php echo $invoiceFirm['pdv'] !== '0' ? '' : 'd-none'; ?>"><span>Ukupno bez PDV:</span> <span class="ukupnoBezPdv"></span> </h4>
                <h4 class="d-flex jc-sb ai-c <?php echo $invoiceFirm['pdv'] !== '0' ? '' : 'd-none'; ?>"><span>PDV:</span> <span class="ukupnoPDV"></span> </h4>
                <h4 class="d-flex jc-sb ai-c"><span>Ukupno:</span> <span class="ukupnoSve"></span> </h4>
            </div>
        </div>
      
        <div class="mt-m">
            <button class="btn btn-primary add">Dodaj artikal</button>
            <button class="btn btn-primary" name="submit">Potvrdi</button>
        </div>
    </form>
</div>
</div>
</main>

<script src="<?php base(); ?>javascript/update_invoice.js"></script>
