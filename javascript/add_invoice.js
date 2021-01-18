let articles = '';
let pdv = '1';
let ukupnoBezPdv = document.querySelector('.ukupnoBezPdv');
let ukupnoPDV = document.querySelector('.ukupnoPDV');
let ukupnoSve = document.querySelector('.ukupnoSve');

window.addEventListener('DOMContentLoaded', () => {
    formDataTwo = new FormData();
    formDataTwo.append('submit', '');
    urlTwo = 'include/get_articles.inc.php';
    postData(urlTwo, formDataTwo)
    .then(result => {
        articles = result;
        document.getElementById('1-artikli').innerHTML = displayArticleOptions(result);
    });
});

// ################
// GETTING FIRM
// ################


let firma = document.getElementById('firma');

firma.addEventListener('change', e => {
    if(e.currentTarget.value !== 'dodajFirmu' && e.currentTarget.value !== '') {
        let url = 'include/get_firm.inc.php?id=' + e.currentTarget.value;
        getData(url)
        .then(result => {
            pdv = result['pdv'];
            if(result['pdv'] === '0') {
                disablePdvAndRemoveValue();
                disableBezPdvAndRemoveValue()
                hiddingTotalsNotUsed();
            } else {
                showTotals();
                setTaxesAndPriceWithoutTaxes();
                let taxInputsAndWithoutTaxInput = document.querySelectorAll('.bezPDV, .PDV');
                removeAttributeDisabled(taxInputsAndWithoutTaxInput);
            }
        });
        let url2 = 'include/get_articles.inc.php';
        let formData = new FormData();
        formData.append('submit', '');
        formData.append('id', e.currentTarget.value);
        postData(url2, formData)
        .then(result => {
            articles = result;
            if(document.getElementById('1-artikli').value.length == 0) {
                document.getElementById('1-artikli').innerHTML = displayArticleOptions(result);
            }
        });
    }
    if(e.currentTarget.value == 'dodajFirmu') {
        let modal = document.querySelector('.modal-overlay-firm');
        modal.classList.add('active');

    }
});

// ####################
// ADD NEW FIRM TO DATABASE
// ####################

let addFirm = document.getElementById('addFirm');

addFirm.addEventListener('submit', e => {
    e.preventDefault();
    let formData = new FormData(addFirm);
    formData.append('submit', '');
    const url = 'include/add_firm.inc.php';
    let inputs = document.querySelectorAll('#ime');
    let errorArray = [];
    isEmpty(inputs, errorArray);
    //###########################################################################

    if(errorArray.length < 1) {
        postData(url, formData)
        .then(result => {
            if(result['success']) {
                showSuccessMessage(addFirm);
                let option = document.createElement('option');
                option.value = result['success'];
                option.innerHTML = result['ime'];
                firma.add(option, firma[0]);
                setTimeout(function() {
                    hideSuccessMessage(addFirm);
                    removeClassActive();
                    firma.selectedIndex = 0;
                }, 1000)
            } else {
                showErrorsFromServerOnSubmit();
            }
        });
    }
}); 


let cancels = document.querySelectorAll('.cancel');
for(let cancel of cancels) {
    cancel.addEventListener('click', e => {
        e.preventDefault();
        // disableModalInputFieldsEnableOthers();
        document.querySelector('.modal-overlay-firm').classList.remove('active');
        document.querySelector('.modal-overlay-client').classList.remove('active');
    });
}
    

// ##################
// END OF ADD FIRM
// ##################

// #################
// LISTEN FOR CLICK EVENT INSIDE ARTICLES
// IF CLICK EVENT TARGET CONTAINS CLASS imeArtikla
// LISTEN FOR CHANGE EVENT TO SHOW INPUT FIELD IF USER WANTS TO ADD NEW ARTICLE
// ELSE GETTING ARTICLE PRICE AND SETTING THAT VALUE TO INPUT FIELD CIJENA
// #################

let articleContainer = document.getElementById('articles');

articleContainer.addEventListener('click', e => {
    // change value for price when name of article is selected
    if(e.target.classList.contains('imeArtikla')) {
        let container = e.target.parentElement.parentElement.parentElement;
        let inputs = container.querySelectorAll('input');
        let input = container.querySelector('select.imeArtikla');
        // let input = e.target;
        input.addEventListener("change", function(ev){
            ev.stopImmediatePropagation();
            if(ev.target.value == 'noviArtikal') {
                // if user selects noviArtikal disable select and enable input field
                let parent = ev.currentTarget.parentElement.parentElement;
                let inputFields = parent.querySelectorAll('input');
                let selectField = parent.querySelector('select');
                for(let inputField of inputFields) {
                    inputField.classList.remove('d-none');
                    inputField.removeAttribute('disabled');
                }
                inputFields[0].focus();
                inputFields[0].parentElement.classList.add('m-w-200');
                selectField.classList.add('d-none');
                selectField.setAttribute('disabled', 'true');
                inputs[2].value = '';
                inputs[3].value = '';
            } else {
                for(let item of articles) {
                    if(item['ime'] == ev.target.value) {
                        inputs[2].value = item['cijena'];
                        inputs[3].focus();
                        break;
                    }
                }
                setValueForRowTotal(container);
            }

        });
    }
    // hide input article row
    if(e.target.classList.contains('remove')) {
        let inputFieldsContainer =  e.target.parentElement;
        inputFieldsContainer.style.display = 'none';
        let imeArtiklaInput = inputFieldsContainer.querySelectorAll('.imeArtikla');
        for(let imeArtikla of imeArtiklaInput) {
            imeArtikla.name += '-none';
        }
        settingTotalValues();
    }
    if(e.target.classList.contains('remove-icon')) {
        let inputFieldsContainer = e.target.parentElement.parentElement;
        inputFieldsContainer.style.display = 'none';
        let imeArtiklaInput = inputFieldsContainer.querySelectorAll('.imeArtikla');
        for(let imeArtikla of imeArtiklaInput) {
            imeArtikla.name += '-none';
        }
        settingTotalValues()
    }
});

// ##################
// SETTING VALUE TO UKUPNO BEZ PDV, PDV, PDVUKUPNO, UKUPNOSVE
// LISTENING FOR CHANGE EVENT IN FIELDS RABAT, KOLICINA, CIJENA
// ##################


articleContainer.addEventListener('input', e => {
    if(e.target.classList.contains('cijena') || e.target.classList.contains('kolicina') || e.target.classList.contains('rabat')) {
        e.stopPropagation();
        let container = e.target.parentElement.parentElement.parentElement; 
        setValueForRowTotal(container);
    }
});

// ############################################
// ADDING NEW ARTICLE
// ############################################

let addArticlesField = document.querySelector('.add');

addArticlesField.addEventListener('click', e => {
    e.preventDefault();
    let div = document.createElement('div');
    div.classList.add('d-flex');
    div.classList.add('articlesNumber');
    div.classList.add('m-w-100');
    div.classList.add('m-flex-column');
    div.classList.add('m-card');
    div.classList.add('m-mb-m');
    let container = document.getElementById('articles');
    let numberOfArticles = container.querySelectorAll('.articlesNumber').length + 1;
    
    div.innerHTML = `
        <div class="w-30 border relative m-d-flex m-w-100">
            <span class="w-100 p-x btn-primary weight-600 d-none m-w-45 m-d-block">Naziv</span>
            <div class="d-flex"> 
                <div> 
                    <input type="text" name="${numberOfArticles}-idArtikla" disabled class="w-100 p-xs border-none border-right form__input d-none h-100 imeArtikla" placeholder="Šifra">
                    <span class="registration-form__error"></span>
                </div>
                <div> 
                    <input type="text" name="${numberOfArticles}-imeArtikla" disabled class="w-100 p-xs border-none form__input d-none h-100 imeArtikla " placeholder="Naziv">
                    <span class="registration-form__error"></span>
                </div>
            </div>
            <div>
                <select id="${numberOfArticles}-artikli" name="${numberOfArticles}-imeArtikla" class="dropdown w-100 p-xs form__input border-none h-100 imeArtikla">
                ${displayArticleOptions(articles)}
                </select>
                <span class="registration-form__error"></span>
            </div>
        </div>
        <div class="w-10 border m-d-flex m-w-100">
            <span class="w-100 d-none p-x btn-primary weight-600 d-none m-w-45 m-d-block">Cijena</span>
            <div> 
                <input type="number" name="${numberOfArticles}-cijena" step="0.01" class="w-100 p-xs form__input border-none h-100 required cijena">
                <span class="registration-form__error"></span>
            </div>
        </div>
        <div class="w-10 border m-d-flex m-w-100">
            <span class="w-100 p-x btn-primary weight-600 d-none m-w-45 m-d-block">Količina</span>
            <div> 
                <input type="number" name="${numberOfArticles}-kolicina" step="0.01" class="w-100 p-xs form__input border-none h-100 required kolicina" >
                <span class="registration-form__error"></span>
            </div>
        </div>
        <div class="w-10 border m-d-flex m-w-100">
            <span class="w-100 p-x btn-primary weight-600 d-none m-w-45 m-d-block">Rabat</span>
            <div class="m-w-55"> 
                <select name="${numberOfArticles}-rabat" class="w-100 p-xs form__input border-none h-100 rabat">
                <option value="0" selected>0%</option>
                ${displayOptionsBetweenOneAndHundred()}
                </select>
                <span class="registration-form__error"></span>
            </div>
        </div>
        <div class="w-15 border m-d-flex m-w-100">
            <span class="w-100 p-x btn-primary weight-600 d-none m-w-45 m-d-block">Cijena bez PDV</span>
            <div>
                <input type="text" name="${numberOfArticles}-bezPdv" ${pdv == '1' ? '' : "disabled='true'"} class="w-100 p-xs form__input border-none h-100 bezPDV" >
                <span class="registration-form__error"></span>
            </div>
        </div>
        <div class="w-10 border m-d-flex m-w-100">
            <span class="w-100 p-x btn-primary weight-600 d-none m-w-45 m-d-block">PDV</span>
            <div> 
                <input type="text" name="${numberOfArticles}-pdv" ${pdv == '1' ? '' : "disabled='true'"} class="w-100 p-xs border-none form__input h-100 PDV" >
                <span class="registration-form__error"></span>
            </div>
        </div>
        <div class="w-10 border m-d-flex m-w-100">
            <span class="w-100 p-x btn-primary weight-600 d-none m-w-45 m-d-block">Ukupno</span>
            <div>
            <input type="text" name="${numberOfArticles}-ukupno" class="w-100 p-xs border-none form__input h-100 ukupno" >
            <span class="registration-form__error"></span>
            </div>
        </div>
        <div class="w-5 border remove d-flex jc-c ai-c m-d-flex pointer m-w-100">
            <i class="fas fa-times remove-icon"></i>
        </div>
    `;
    container.append(div);
});

// #####################
// END OF ADDING ARTICLE
// #####################



// ###########################################
// HANDLING FORM SUBMIT FOR ADDING INVOICE
// ###########################################

const invoiceForm = document.querySelector('form');

invoiceForm.addEventListener('submit', e => {
    e.preventDefault();
    let formData = new FormData(invoiceForm);
    formData.append('submit', '');
    const url = 'include/add_invoice.inc.php';
    let requiredInputs = document.querySelectorAll('.required');
    let errorArray = [];
    isEmpty(requiredInputs, errorArray);
    if(errorArray.length < 1) {
        postData(url, formData)
        .then(result => {
            if(result['success']) {
                window.location.href = "pdf_preview/" + result['success'];
            } else {
                addClassHeight100ToArticleFields();
                showErrorsFromServerOnSubmit();
            }
        });
    }
}); 


// #####################
// END OF FORM SUBMIT 
// #####################

// #####################################
// MODAL FOR ADD CLIENT
// ####################################

let client = document.getElementById('kupac');

client.addEventListener('change', e => {
    if(e.currentTarget.value == 'dodajKlijenta') {
        let modal = document.querySelector('.modal-overlay-client');
        modal.classList.add('active');
    }
})

// #####################################
// FORM ADD CLIENT
// ####################################

let addClient = document.getElementById('addClient');

addClient.addEventListener('submit', e => {
    e.preventDefault();
    let formData = new FormData(addClient);
    formData.append('submit', '');
    const url = 'include/add_client.inc.php';
    let inputs = document.querySelectorAll('#ime-client');
    let errorArray = [];
    isEmpty(inputs, errorArray);
    if(errorArray.length < 1) {
        postData(url, formData)
        .then(result => {
            if(result['success']) {
               showSuccessMessage(addClient);
                let option = document.createElement('option');
                option.value = result['success'];
                option.innerHTML = result['ime'];
                client.add(option, client[0]);
                setTimeout(function() {
                    client.selectedIndex = 0;
                    hideSuccessMessage(addClient);
                    removeClassActive();
                }, 1000)
            } else {
                showErrorsFromServerOnSubmit();
            }
        });
    }
});


// ### IMAGE PREVIEW ###

const fileUploads = document.querySelectorAll('.form__input-file');
showImagePreviewOnChange(fileUploads);

// ### DISPLAY AND HIDE INPUT FIELD

let radioFirm = document.querySelectorAll('.modal-overlay-firm input[type="radio"]');

for(let input of radioFirm) {
    input.addEventListener('click', () => {
        let pib = document.querySelector('.modal-overlay-firm .pib');
        if(input.checked && input.value == '1') {
            pib.parentElement.style.display = 'block';
            pib.removeAttribute('disabled');
            disablePdvAndRemoveValue();
            disableBezPdvAndRemoveValue()
            hiddingTotalsNotUsed();
        } else {
            pib.parentElement.style.display = 'none';
            pib.setAttribute('disabled', 'true');
            showTotals();
            setTaxesAndPriceWithoutTaxes();
            let taxInputsAndWithoutTaxInput = document.querySelectorAll('.bezPDV, .PDV');
            removeAttributeDisabled(taxInputsAndWithoutTaxInput);
        }
    });
}

let radioClient = document.querySelectorAll('.modal-overlay-client input[type="radio"]');

for(let input of radioClient) {
    input.addEventListener('click', () => {
        let pib = document.querySelector('.modal-overlay-client .pib');
        if(input.checked && input.value == '1') {
            pib.parentElement.style.display = 'block';
            pib.removeAttribute('disabled');
        } else {
            pib.parentElement.style.display = 'none';
            pib.setAttribute('disabled', 'true');
        }
    });
}


// ####################
// FUNCTIONS
// ####################

function isEmpty(inputs, errorArray) {
    let container = document.getElementById('articles');
    let articleInputs = container.querySelectorAll('input, select');
    for(let articleInput of articleInputs) {
        articleInput.classList.add('h-100');
    }
    for(let input of inputs) {
        if(input.value.length == 0 && input.disabled !== 'true') {
            input.style.borderColor = '#a94442';
            input.classList.remove('h-100');
            input.parentElement.querySelector('.registration-form__error').innerHTML = 'Polje ne smije biti prazno';
            errorArray.push('error');
        } else {
            if(!input.disabled) {
                input.style.borderColor = '#ced4da';
                input.parentElement.querySelector('.registration-form__error').innerHTML = '';
            }
        }
    }
}


async function postData(url, data) {
    const response = await fetch(url, {
        method: 'POST',
        body: data
    });
    return response.json(); // parses JSON response into native JavaScript object
}

async function getData(url) {
    const response = await fetch(url, {
        method: 'GET'
    });
    return response.json(); // parses JSON response into native JavaScript object
}


function displayArticleOptions (articles) {
    list = '<option value="">Izaberite artikal</option>';
    list += '<option value="noviArtikal"> Dodajte novi artikal </option>'
    for(let item of articles) {
        list += `<option value="${item['ime']}"> ${item['ime']} </option>`;
    }
    return list;
}

function displayOptionsBetweenOneAndHundred(){
    list = '';
    for(let i = 1; i <= 100; i++) {
        list += `<option value="${i}"> ${i}% </option>
        `;
    }
    return list;
}

function getTotalValue() {
    let totalInputs = document.querySelectorAll('.ukupno');
    let total = 0;
    for(let totalInput of totalInputs) {
        let value = totalInput.value;
        value = value.substring(0, value.length - 2);
        const container = totalInput.parentElement.parentElement.parentElement;
        if(container.style.display !== 'none') {
            total += parseFloat(value);
        }
    }
    total = total.toFixed(2);
    total += ' KM';
    return total;
}

function getTotalTax() {
    let totalInputs = document.querySelectorAll('.PDV');
    let total = 0;
    for(let totalInput of totalInputs) {
        let value = totalInput.value;
        value = value.substring(0, value.length - 2);
        const container = totalInput.parentElement.parentElement.parentElement;
        if(container.style.display !== 'none') {
            total += parseFloat(value);
        }
    }
    total = total.toFixed(2);
    total += ' KM';
    return total;
}

function getTotalWithoutTaxes() {
    let totalInputs = document.querySelectorAll('.bezPDV');
    let total = 0;
    for(let totalInput of totalInputs) {
        let value = totalInput.value;
        value = value.substring(0, value.length - 2);
        const container = totalInput.parentElement.parentElement.parentElement;
        if(container.style.display !== 'none') {
            total += parseFloat(value);
        }
    }
    total = total.toFixed(2);
    total += ' KM';
    return total;
}

function settingTotalValues() {
    ukupnoSve.innerHTML = getTotalValue();
    if(pdv != '0') {
        ukupnoPDV.parentElement.style.display = 'flex';
        ukupnoPDV.innerHTML = getTotalTax();
        ukupnoBezPdv.parentElement.style.display = 'flex';
        ukupnoBezPdv.innerHTML = getTotalWithoutTaxes();
    }
}

function hiddingTotalsNotUsed() {
    ukupnoBezPdv.parentElement.style.display = 'none';
    ukupnoPDV.parentElement.style.display = 'none';
}

function showTotals() {
    ukupnoBezPdv.parentElement.style.display = 'flex';
    ukupnoPDV.parentElement.style.display = 'flex';
}

function setTaxesAndPriceWithoutTaxes() {
    let containers = document.querySelectorAll('.articlesNumber');
    for(let container of containers) {
        let cijena = container.querySelector('.cijena');
        let kolicina = container.querySelector('.kolicina');
        let rabat = container.querySelector('.rabat');

        let ukupno = container.querySelector('.ukupno');
        let ukupnoValue = (cijena.value * kolicina.value - (cijena.value * kolicina.value * rabat.value / 100)).toFixed(2);
        ukupno.value = ukupnoValue != 0 ? ukupnoValue + 'KM' : '';

        let pdv = container.querySelector('.PDV');
        let pdvValue = (ukupnoValue * 14.53 / 100).toFixed(2); 
        pdv.value = pdvValue != 0 ? pdvValue + 'KM' : '';

        let bezPDV = container.querySelector('.bezPDV');
        let bezPDVValue = (ukupnoValue - pdvValue).toFixed(2);
        bezPDV.value = bezPDVValue != 0 ? bezPDVValue + 'KM' : '';

    }
}

function setAttributeDisabled(inputs) {
    for(let input of inputs) {
        input.setAttribute('disabled', 'true');
    }
}

function removeAttributeDisabled(inputs) {
    for(let input of inputs) {
        input.removeAttribute('disabled');
    }
}


function setImage(inputField, img) {
    img.src = URL.createObjectURL(inputField.files[0]);
    img.onload = function() {
        URL.revokeObjectURL(img.src);
    }
}


function showImagePreviewOnChange(fileUploads) {
    for(let fileUpload of fileUploads) {
        fileUpload.addEventListener('change', e => {
            const container = e.target.parentElement;
            let img = container.querySelector('img');
            setImage(fileUpload, img);
        });
    }
}


function setValueForRowTotal(container) {
    let cijena = container.querySelector('.cijena');
    let kolicina = container.querySelector('.kolicina');
    let rabat = container.querySelector('.rabat');
    if(cijena.value !== '' && kolicina.value !== '') {
        let ukupno = container.querySelector('.ukupno');
        let ukupnoValue = (cijena.value * kolicina.value - (cijena.value * kolicina.value * rabat.value / 100)).toFixed(2);
        ukupno.value = ukupnoValue + 'KM';
        if(pdv !== '0') {
            let pdv = container.querySelector('.PDV');
            let pdvValue = (ukupnoValue * 14.53 / 100).toFixed(2); 
            pdv.value = pdvValue + 'KM';
            let bezPDV = container.querySelector('.bezPDV');
            let bezPDVValue = (ukupnoValue - pdvValue).toFixed(2);
            bezPDV.value = bezPDVValue + 'KM';
            settingTotalValues();
        } else {
            let bezPDV = container.querySelector('.bezPDV');
            bezPDV.value = '';
            let pdv = container.querySelector('.PDV');
            pdv.value = '';
            ukupnoSve.innerHTML = getTotalValue();
            hiddingTotalsNotUsed();
        }
    }
}

function removeClassActive() {
    document.querySelector('.modal-overlay-firm').classList.remove('active');
    document.querySelector('.modal-overlay-client').classList.remove('active');
}

function disablePdvAndRemoveValue() {
    let pdvs = document.querySelectorAll('.PDV');
    for(let pdv of pdvs) {
        pdv.value = '';
    }
    setAttributeDisabled(pdvs);
}

function disableBezPdvAndRemoveValue() {
    let bezPDV = document.querySelectorAll('.bezPDV');
    for(let bez of bezPDV) {
        bez.value = '';
    }
    setAttributeDisabled(bezPDV);
}

function showErrorsFromServerOnSubmit() {
    let errorMessages = document.querySelectorAll('.registration-form__error');
    for(let errorMessage of errorMessages) {
        errorMessage.innerHTML = '';
    }
    let inputs = document.querySelectorAll('input');
    for(let input of inputs) {
        input.style.borderColor = '#ced4da';
    }
    for(const [key, value] of Object.entries(result)) {
        field =    document.querySelector(`input[name="${key}"]`);
        field.style.borderColor = '#a94442';
        field.classList.remove('h-100');
        field.parentElement.querySelector('.registration-form__error').innerHTML = value;
    }
}

function addClassHeight100ToArticleFields() {
    let container = document.getElementById('articles');
    let articleInputs = container.querySelectorAll('input');
    for(let articleInput of articleInputs) {
        articleInput.classList.add('h-100');
    }
}

function showSuccessMessage(container) {
    container.querySelector('.success-message').innerHTML = 'Uspješno dodat klijent';
    container.querySelector('.success-message').style.padding = '0.5rem 1rem';
}

function hideSuccessMessage(container) {
    container.querySelector('.success-message').innerHTML = '';
    container.querySelector('.success-message').style.padding = '0';
}