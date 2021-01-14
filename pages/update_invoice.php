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
    <h2 class="card__header text-center card__header-border weight-500 mb-xs">Kreiraj fakturu</h2>
    <div class="card-body">
    <p class="success-message mb-xs text-center"></p>
        <div class="d-flex gap-s mb-s m-flex-column">
            <div class="w-100">
                <label for="firma">Naziv firme</label>
                <select class="form__input" name="firma" id="firma">
                        <option selected value="<?php echo $invoiceFirm['id']; ?>"><?php echo $invoiceFirm['ime']; ?></option>
                    <?php foreach($firms as $firm) { ?>
                        <option value="<?php echo $firm['id']; ?>"><?php echo $firm['ime'] ?></option>
                    <?php } ?>
                </select>
                <span class="registration-form__error"></span>
            </div>
            <div class="w-100">
                <label for="broj">Broj računa</label>
                <input type="text" name="<?php echo $invoice['id']; ?>-broj" id="broj"  class="form__input" value="<?php echo $invoice['broj']; ?>">
                <span class="registration-form__error"></span>
            </div>
            <div class="w-100">
                <label for="datum">Datum izdavanja</label>
                <input type="date" name="datum" id="datum" class="form__input" value="<?php echo $invoice['datum']; ?>">
                <span class="registration-form__error"></span>
            </div>
        </div>
        <div class="d-flex gap-s mb-s m-flex-column">
            <div class="w-100">
                <label for="mjesto">Mjesto izdvananja</label>
                <input type="text" name="mjesto" id="mjesto" class="form__input" value="<?php echo $invoice['mjesto']; ?>">
                <span class="registration-form__error"></span>
            </div>
            <div class="w-100 relative">
                <label for="kupac">Ime kupca</label>
                <input type="text" name="kupac" id="kupac" class="form__input" list="kupac-dropdown" value="<?php echo $invoiceClient['ime']; ?>">
                <span class="registration-form__error"></span>
                <datalist id="kupac-dropdown">
                <?php if(count($clients) > 0) {
                    foreach($clients as $client) {?>
                        <option value="<?php echo $client['ime'] . ', ' . $client['mjesto']; ?>"><?php echo $client['ime'] . ', ' . $client['mjesto']; ?></option>
                <?php  }
                } ?>
                </datalist>
            </div>
            <div class="w-100">
                <label for="nacin">Način plaćanja</label>
                <input type="text" name="nacin" id="nacin" class="form__input" value="<?php echo $invoice['nacin']; ?>">
                <span class="registration-form__error"></span>
            </div>
            
        </div>
        <div class="d-flex gap-s mb-s m-flex-column">
            <div class="w-100">
                <label for="fakturista">Fakturisao</label>
                <input type="text" name="fakturista" id="fakturista" class="form__input" value="<?php echo $invoice['fakturista']; ?>">
                <span class="registration-form__error"></span>
            </div>
            <div class="w-100">
                <label for="rok">Rok plaćanja</label>
                <input type="date" name="rok" id="rok" class="form__input" value="<?php echo $invoice['rok']; ?>">
                <span class="registration-form__error"></span>
            </div>
            <div class="w-100"></div>
        </div>
        <div id="articles">
        <?php if(count($articles) > 0) { ?>

            <div class="d-flex btn-primary">
                <span class="w-60 p-x border weight-500 hidden m-d-none">Naziv</span>
                <span class="w-20 p-x border weight-500 hidden m-d-none">Jed. cijena</span>
                <span class="w-20 p-x border weight-500 hidden m-d-none">Količina</span>
            </div>
            <?php foreach($articles as $article) { ?>
            <div class="d-flex articlesNumber">
                <div class="w-60 border relative">
                    <span class="w-100 p-x border weight-600 d-none m-d-block">Naziv</span>
                    <input type="text" name="1-imeArtikla" class="w-100 p-xs border-none h-100 imeArtikla" list="1-artikli" value="<?php echo $article['ime']; ?>">
                    <span class="registration-form__error"></span>
                    <datalist id="1-artikli" class="dropdown"></datalist>
                </div>
                <div class="w-20 border">
                    <span class="w-100 d-none p-x border weight-600 d-none m-d-block">Cijena</span>
                    <input type="number" step="0.01" name="1-cijena-<?php echo $article['id']; ?>" class="w-100 p-xs border-none h-100" value="<?php echo $article['cijena']; ?>">
                    <span class="registration-form__error"></span>
                </div>
                <div class="w-20 border">
                    <span class="w-10 p-x border weight-600 d-none m-d-block">Količina</span>
                    <input type="number" step="0.01" name="1-kolicina" class="w-100 p-xs border-none h-100" value="<?php echo $article['kolicina']; ?>">
                    <span class="registration-form__error"></span>
                </div>
            </div>
            <?php } ?>

        <?php } ?>

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
