<?php 
    if(!isset($_SESSION['id'])) { 
        header('Location: login');
        exit();
    }
    require_once('include/autoloader.php');
    $clients = Client::findAllByQuery('userId', $_SESSION['id']);
    $firms = Firm::findAllByQuery('userId', $_SESSION['id']);
    $user = User::findById($_SESSION['id']);
?>
<main>
<div class="wrapper">
<div class="card m-auto visible">
<form action="include/add_invoice.inc.php" autocomplete="off"  method="POST">
    <h2 class="card__header text-center card__header-border weight-500 mb-xs">Kreiraj fakturu</h2>
    <div class="card-body">
        <div class="d-flex gap-s mb-s m-flex-column">
            <div class="w-100">
                <label for="firma">Naziv firme</label>
                <select class="form__input required" name="firma" id="firma">
                        <option value="">Izaberite Vašu firme</option>
                        <option value="dodajFirmu">Dodaj novu firmu</option>
                    <?php foreach($firms as $firm) { ?>
                        <option value="<?php echo $firm['id']; ?>"><?php echo $firm['ime'] ?></option>
                    <?php } ?>
                </select>
                <span class="registration-form__error"></span>
            </div>
            <div class="w-100">
                <label for="tip">Tip fakture</label>
                <select name="tip" id="tip" class="form__input required">
                    <option value=""></option>
                    <option value="Faktura">Faktura</option>
                    <option value="Predračun">Predračun</option>
                    <option value="Avansni predračun">Avansni predračun</option>
                </select>
                <span class="registration-form__error"></span>
            </div>
            <div class="w-100">
                <label for="broj">Broj fakture</label>
                <input type="text" name="broj" id="broj"  class="form__input required">
                <span class="registration-form__error"></span>
            </div>
        </div>
        <div class="d-flex gap-s mb-s m-flex-column">
            <div class="w-100">
                <label for="datum">Datum izdavanja</label>
                <input type="date" name="datum" id="datum" class="form__input required">
                <span class="registration-form__error"></span>
            </div>
            <div class="w-100">
                <label for="mjesto">Mjesto izdvananja</label>
                <input type="text" name="mjesto" id="mjesto" class="form__input required">
                <span class="registration-form__error"></span>
            </div>
            <div class="w-100">
                <label for="nacin">Način plaćanja</label>
                <select name="nacin" id="nacin" class="form__input">
                    <option value="Virman" selected>Virman</option>
                    <option value="Gotovina">Gotovina</option>
                    <option value="Ček">Ček</option>
                    <option value="Kartica">Kartica</option>
                    <option value="">Drugo</option>
                </select>
                <span class="registration-form__error"></span>
            </div>
            
        </div>
        <div class="d-flex gap-s mb-s m-flex-column">
            <div class="w-100 relative">
                <label for="kupac">Ime klijenta</label>
                <select name="kupac" id="kupac" class="form__input required">
                    <option value="">Izaberite klijenta</option>
                    <option value="dodajKlijenta">Dodaj novog klijenta</option>
                    <?php if(count($clients) > 0) {
                        foreach($clients as $client) {?>
                            <option value="<?php echo $client['id']; ?>"><?php echo $client['ime']; ?></option>
                    <?php  }
                    } ?>
                </select>
                <span class="registration-form__error"></span>
            </div>
            <div class="w-100">
                <label for="fakturista">Fakturisao</label>
                <input type="text" name="fakturista" id="fakturista" class="form__input required" value="<?php echo $user['name'] ?>">
                <span class="registration-form__error"></span>
            </div>
            <div class="w-100">
                <label for="rok">Rok za plaćanje</label>
                <input type="date" name="rok" id="rok" class="form__input required">
                <span class="registration-form__error"></span>
            </div>
            <div class="w-100"></div>
        </div>
        <div id="articles">
            <div class="d-flex btn-primary" id="articlesHeader">
                <span class="w-30 p-x border weight-500 hidden mm-d-none">Artikal</span>
                <span class="w-10 p-x border weight-500 hidden mm-d-none">Jed. cijena</span>
                <span class="w-10 p-x border weight-500 hidden mm-d-none">Količina</span>
                <span class="w-10 p-x border weight-500 hidden mm-d-none">Popust</span>
                <span class="w-15 p-x border weight-500 hidden mm-d-none">Cijena bez PDV</span>
                <span class="w-10 p-x border weight-500 hidden mm-d-none">PDV</span>
                <span class="w-15 p-x border weight-500 hidden mm-d-none">Ukupno</span>
            </div>
            <div class="d-flex articlesNumber m-w-100 m-flex-column m-card m-mb-m" id="articlesBody">
                <div class="w-30 border relative m-d-flex m-w-100">
                    <span class="w-100 p-x btn-primary weight-600 d-none m-w-45 m-d-block">Artikal</span>
                    <div class="d-flex">
                        <div>
                            <input type="text" name="1-idArtikla" disabled="true" class="w-100 p-xs border-none border-right form__input h-100 imeArtikla d-none" placeholder="Šifra">
                            <span class="registration-form__error"></span>
                        </div>
                        <div>
                            <input type="text" name="1-imeArtikla" disabled="true" class="w-100 p-xs border-none form__input h-100 imeArtikla d-none" placeholder="Naziv">
                            <span class="registration-form__error"></span>
                        </div>
                    </div>
                    <div>
                    <select id="1-artikli" name="1-imeArtikla" class="w-100 p-xs border-none form__input h-100 imeArtikla dropdown">
                    
                    </select>
                    <span class="registration-form__error"></span>
                    </div>
                </div>
                <div class="w-10 border m-d-flex m-w-100">
                    <span class="w-100 d-none p-x btn-primary weight-600 d-none m-w-45 m-d-block">Cijena</span>
                    <div>
                        <input type="number" step="0.01" name="1-cijena" class="w-100 p-xs form__input border-none h-100 cijena required">
                        <span class="registration-form__error"></span>
                    </div>
                </div>
                <div class="w-10 border m-d-flex m-w-100">
                    <span class="w-100 p-x btn-primary weight-600 d-none m-w-45 m-d-block">Količina</span>
                    <div>
                        <input type="number" step="0.01" name="1-kolicina" class="w-100 p-xs form__input border-none h-100 kolicina required" >
                        <span class="registration-form__error"></span>
                    </div>
                </div>
                <div class="w-10 border m-d-flex m-w-100">
                    <span class="w-100 p-x btn-primary weight-600 d-none m-w-45 m-d-block">Popust</span>
                    <div>
                        <select name="1-rabat" class="w-100 p-xs border-none h-100 rabat form__input required">
                            <option value="0" selected>0%</option>
                            <?php for($i = 1; $i <= 100; $i++ ) { ?>
                                <option value="<?php echo $i ?>"><?php echo $i . '%'; ?></option>
                            <?php } ?>
                        </select>
                        <span class="registration-form__error"></span>
                    </div>
                </div>
                <div class="w-15 border m-d-flex m-w-100">
                    <span class="w-100 p-x btn-primary weight-600 d-none m-w-45 m-d-block">Cijena bez PDV</span>
                    <div>
                        <input type="text" name="1-bezPdv" class="w-100 p-xs form__input border-none h-100 bezPDV" >
                        <span class="registration-form__error"></span>
                    </div>
                </div>
                <div class="w-10 border m-d-flex m-w-100">
                    <span class="w-100 p-x btn-primary weight-600 d-none m-w-45 m-d-block">PDV</span>
                    <div>

                        <input type="text" name="1-pdv" class="w-100 p-xs form__input border-none h-100 PDV" >
                        <span class="registration-form__error"></span>
                    </div>
                </div>
                <div class="w-10 border m-d-flex m-w-100">
                    <span class="w-100 p-x btn-primary weight-600 d-none m-w-45 m-d-block">Ukupno</span>
                    <div>
                        <input type="text" name="1-ukupno" class="w-100 p-xs form__input border-none h-100 ukupno" >
                        <span class="registration-form__error"></span>
                    </div>
                </div>
                <div class="w-5 border m-d-flex m-w-100">
                </div>
            </div>
        </div>
        <div class="w-40 m-w-100 mt-s card ml-auto">
            <div class="card-body">    
                <h4 class="d-flex jc-sb ai-c"><span>Ukupno bez PDV:</span> <span class="ukupnoBezPdv"></span> </h4>
                <h4 class="d-flex jc-sb ai-c"><span>PDV:</span> <span class="ukupnoPDV"></span> </h4>
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
<div class="modal-overlay-firm">
    <div class="modal relative">
    <div >
        <form action="include/add_firm.inc.php" enctype="multipart/form-data" method="POST" id="addFirm">
            <h2 class="card__header text-center card__header-border weight-500 mb-xs">Dodaj firmu</h2>
            <div class="remove__modal">
                <i class="fas fa-times cancel p-s"></i>
            </div>
            <div class="card-body">
                <div class="mb-xs">
                    <label for="ime">Naziv firme</label>
                    <input type="text" name="ime"  id="ime"  class="form__input">
                    <span class="registration-form__error"></span>
                </div>
                <div class="mb-xs">
                    <label for="logo" class="file-label mb-xs">Dodaj logotip</label>
                    <input type="file"  name="logo" id="logo" class="form__input-file">
                    <img src="" alt="" class="firm-logo d-block m-auto">
                    <span class="registration-form__error"></span>
                </div>
                <div class="mb-xs">
                    <label for="jib">Jedinstveni identifikacioni broj</label>
                    <input type="text"  name="jib" id="jib"  class="form__input">
                    <span class="registration-form__error"></span>
                </div>
                <div class="mb-xs">
                    <p class="mb-xs">Da li je firma u sistemu PDV-a?</p>
                    <div class="d-flex">
                        <div class="mb-xs w-100">
                            <input type="radio"  name="pdv" id="pdv-da" value="1">
                            <label for="pdv-da">Da</label>
                        </div>
                        <div class="w-100">
                            <input type="radio"  checked name="pdv" id="pdv-ne" value="0">
                            <label for="pdv-ne">Ne</label>
                        </div>
                    </div>
                    <span class="registration-form__error"></span>
                </div>
                <div class="mb-xs d-none">
                    <label for="pib">Poreski identifikacioni broj</label>
                    <input type="text" name="pib"  id="pib"  class="form__input pib">
                    <span class="registration-form__error"></span>
                </div>
                <div class="mb-xs">
                    <label for="vlasnik">Ime vlasnika</label>
                    <input type="text" name="vlasnik"  id="vlasnik" class="form__input">
                    <span class="registration-form__error"></span>
                </div>
                <div class="mb-xs">
                    <label for="adresa">Adresa firme</label>
                    <input type="text" name="adresa"  id="adresa" class="form__input">
                    <span class="registration-form__error"></span>
                </div>
                <div class="mb-xs">
                    <label for="broj">Broj telefona</label>
                    <input type="text" name="telefon"  id="broj" class="form__input">
                    <span class="registration-form__error"></span>
                </div>
                <div class="mb-xs">
                    <label for="email">Email adresa</label>
                    <input type="text" name="email"  id="email" class="form__input">
                    <span class="registration-form__error"></span>
                </div>
                <div class="mb-xs">
                    <label for="mjesto">Mjesto</label>
                    <input type="text" name="mjesto"  id="mjesto" class="form__input">
                    <span class="registration-form__error"></span>
                </div>
                <div class="mb-s">
                    <label for="racun">Žiro račun firme</label>
                    <input type="text" name="racun"  id="racun" class="form__input">
                    <span class="registration-form__error"></span>
                </div>
                <div class="mb-s">
                    <label for="banka">Ime banke</label>
                    <input type="text" name="banka"  id="banka" class="form__input">
                    <span class="registration-form__error"></span>
                </div>
                <p class="success-message mb-xs text-center"></p>
                <button class="btn btn-primary" name="submit">Potvrdi</button>
                <button class="btn btn-secondary cancel" name="submit">Odustani</button>
            </form>
        </div>
    </div>
</div>
</div>
<div class="modal-overlay-client">
    <div class="modal"> 
        <div> 
        <form action="include/add_client.inc.php" enctype="multipart/form-data" method="POST" id="addClient">
            <h2 class="card__header text-center card__header-border weight-500 mb-xs">Dodaj klijenta</h2>
            <div class="remove__modal">
                <i class="fas fa-times p-s cancel"></i>
            </div>
            <div class="card-body">
                <div class="mb-xs">
                    <label for="ime-client">Ime klijenta(firme ili fizičkog lica)</label>
                    <input type="text" name="ime"  id="ime-client"  class="form__input">
                    <span class="registration-form__error"></span>
                </div>
                <div class="mb-xs">
                    <label for="logo-client" class="file-label mb-xs">Dodaj logotip</label>
                    <input type="file" name="logo"   id="logo-client" class="form__input-file">
                    <img src="" alt="" class="firm-logo d-block m-auto">
                    <span class="registration-form__error"></span>
                </div>
                <div class="mb-xs">
                    <label for="jib-client">Jedinstveni identifikacioni broj/JMBG</label>
                    <input type="text" name="jib"  id="jib-client"  class="form__input">
                    <span class="registration-form__error"></span>
                </div>
                <div class="mb-xs">
                    <p class="mb-xs">Da li je firma u sistemu PDV-a?</p>
                    <div class="d-flex">
                        <div class="mb-xs w-100">
                            <input type="radio" name="pdv"  id="pdv-da-client" value="1">
                            <label for="pdv-da-client">Da</label>
                        </div>
                        <div class="w-100">
                            <input type="radio" checked name="pdv"  id="pdv-ne-client" value="0">
                            <label for="pdv-ne-client">Ne</label>
                        </div>
                    </div>
                    <span class="registration-form__error"></span>
                </div>
                <div class="mb-xs d-none">
                    <label for="pib-client">Poreski identifikacioni broj</label>
                    <input type="text" name="pib" id="pib-client"  class="form__input pib">
                    <span class="registration-form__error"></span>
                </div>
                <div class="mb-xs">
                    <label for="vlasnik-client">Ime vlasnika</label>
                    <input type="text" name="vlasnik"  id="vlasnik-client" class="form__input">
                    <span class="registration-form__error"></span>
                </div>
                <div class="mb-xs">
                    <label for="adresa-client">Adresa firme</label>
                    <input type="text" name="adresa"  id="adresa-client" class="form__input">
                    <span class="registration-form__error"></span>
                </div>
                <div class="mb-xs">
                    <label for="mjesto-client">Mjesto</label>
                    <input type="text" name="mjesto"  id="mjesto-client" class="form__input">
                    <span class="registration-form__error"></span>
                </div>
                <div class="mb-xs">
                    <label for="broj-client">Broj telefona</label>
                    <input type="text" name="telefon"  id="broj-client" class="form__input">
                    <span class="registration-form__error"></span>
                </div>
                <div class="mb-xs">
                    <label for="email-client">Email adresa</label>
                    <input type="text" name="email"  id="email-client" class="form__input">
                    <span class="registration-form__error"></span>
                </div>
                <div class="mb-s">
                    <label for="racun-client">Žiro račun</label>
                    <input type="text" name="racun"  id="racun-client" class="form__input">
                    <span class="registration-form__error"></span>
                </div>
                <div class="mb-s">
                    <label for="banka-client">Ime banke</label>
                    <input type="text" name="banka"  id="banka-client" class="form__input">
                    <span class="registration-form__error"></span>
                </div>
                <p class="success-message mb-xs text-center"></p>
                <button class="btn btn-primary" name="submit">Potvrdi</button>
                <button class="btn btn-secondary cancel" name="submit">Odustani</button>
            </form>
        </div>
    </div>
</div>

</main>
<script src="<?php base(); ?>javascript/add_invoice.js"></script>
