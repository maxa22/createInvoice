let articles = '';
let pdv = '';
let firma = document.getElementById('firma');
let ukupnoBezPdv = document.querySelector('.ukupnoBezPdv');
let ukupnoPDV = document.querySelector('.ukupnoPDV');
let ukupnoSve = document.querySelector('.ukupnoSve');

url = '../include/get_articles.inc.php';
fetchArticlesForCurrentUser(url);
let firmId = firma.value;
let urlTwo = '../include/get_firm.inc.php?id=' + firmId;
getData(urlTwo)
.then(result => {
    pdv = result['pdv'];
    setTotalValues();
});

let articleContainer = document.getElementById('articles');

articleContainer.addEventListener('click', e => {
    e.stopPropagation();
    // on input show dropdown
    if(e.target.classList.contains('imeArtikla')) {
        
        let container = e.target.parentElement.parentElement;
        let inputs = container.querySelectorAll('input');
        let input = container.querySelector('select.imeArtikla');
        input.addEventListener("change", function(ev){
            ev.stopImmediatePropagation();
            if(ev.target.value == 'noviArtikal') {
                // if user selects noviArtikal disable select and enable input fields
                let articleModal = document.querySelector('.modal-overlay-article');
                articleModal.classList.add('active');
                let articleFirm = document.getElementById('firma-artikla');
                if(firma.value !== '' && firma.value !== 'dodajFirmu') {
                    createAndSelectNewOption(firma.value, firma.options[firma.selectedIndex].text, articleFirm);
                }
            } else {
                for(let item of articles) {
                    if(item['ime'] == ev.target.value) {
                        inputs[0].value = item['cijena'];
                        inputs[1].focus();
                        break;
                    }
                }
                setValueForRowTotal(container);
            }

        });
    }

    if(e.target.classList.contains('remove-icon')) {
        let inputFieldsContainer = e.target.parentElement;
        const deleteArticles = inputFieldsContainer.querySelectorAll('.deleteArticle');
        removeArticlesModal(inputFieldsContainer);
        for(let deleteArticle of deleteArticles) {
            deleteArticle.addEventListener('click', event => {
                event.preventDefault();
                let url = event.currentTarget.getAttribute('href');
                let modal = event.currentTarget.parentElement.parentElement.parentElement;
                getData(url)
                .then(result => {
                    if(!result) {
                        inputFieldsContainer.remove();
                        modal.classList.remove('active');
                        setTotalValues();
                    } else {
                        modal.classList.remove('active');
                        setTotalValues();
                    }
                });
            });
        }
        setTotalValues();
    }
});



// #########################
// ADD ARTICLE FORM
// #########################

const newArticleForm = document.getElementById('newArticle');

newArticleForm.addEventListener('submit', e =>{
    e.preventDefault();
    let formData = new FormData(newArticleForm);
    formData.append('submit', '');
    const url = '../include/articles.inc.php';
    let inputs = newArticle.querySelectorAll('input, select');
    let errorArray = [];
    isEmpty(inputs, errorArray);
    if(errorArray.length < 1) {
        postData(url, formData)
        .then(result => {
            if(!result) {
                showSuccessMessage(newArticleForm);
                const articles = document.querySelectorAll('.imeArtikla');
                let option = document.createElement('option');
                let optionValue = newArticleForm.querySelector('#ime-artikla').value;
                let optionPriceValue = newArticleForm.querySelector('#cijena-artikla').value;
                option.value = optionValue;
                option.innerHTML = optionValue;
                for(let article of articles) {
                    if(article.value == 'noviArtikal') {
                        article.add(option, article[0]);
                        let price = article.parentElement.parentElement.querySelector('.cijena');
                        price.value = optionPriceValue;
                        article.selectedIndex = 0;
                        break;
                    }
                }
                setTimeout(function() {
                    hideSuccessMessage(newArticleForm);
                    document.querySelector('.modal-overlay-article').classList.remove('active');
                }, 1000);
            } else {
                showErrorsFromServerOnSubmit(result, newArticleForm);
            }
        });
    }
});




// ##################
// SETTING VALUE TO UKUPNO BEZ PDV, PDV, PDVUKUPNO, UKUPNOSVE
// LISTENING FOR CHANGE EVENT IN FIELDS RABAT, KOLICINA, CIJENA
// ##################


articleContainer.addEventListener('input', e => {
    // change event on price, quantity
    if(e.target.classList.contains('cijena') || e.target.classList.contains('kolicina') || e.target.classList.contains('rabat')) {
        e.stopPropagation();
        let container = e.target.parentElement.parentElement; 
        setValueForRowTotal(container);
    }
});


let numberOfArticles = document.querySelectorAll('.articlesNumber').length;
let addArticle = document.querySelector('.add');

addArticle.addEventListener('click', e => {
    e.preventDefault();
    ++numberOfArticles;
    let div = createArticleDiv();
    articleContainer.append(div);
});


const invoiceForm = document.querySelector('form');

invoiceForm.addEventListener('submit', e => {
    e.preventDefault();
    let formData = new FormData(invoiceForm);
    formData.append('submit', '');
    const url = '../include/update_invoice.inc.php';
    let requiredInputs = document.querySelectorAll('.required');
    let errorArray = [];
    isEmpty(requiredInputs, errorArray);
    if(errorArray.length < 1) {
        postData(url, formData)
        .then(result => {
            if(!result) {
               showSuccessMessage(invoiceForm);
                setTimeout(function() {
                    hideSuccessMessage(invoiceForm);
                    window.location.href = '../invoices';
                }, 1000);
            } else {
                showErrorsFromServerOnSubmit(result, invoiceForm);
            }
        });
    }
}); 




firma.addEventListener('change', e => {
    if(e.currentTarget.value !== '') {
        let url = '../include/get_firm.inc.php?id=' + e.currentTarget.value;
        getData(url)
        .then(result => {
            pdv = result['pdv'];
            if(result['pdv'] === '0') {
                disablePdvAndRemoveValue();
                disableBezPdvAndRemoveValue()
                hideTotalsNotUsed();
            } else {
                showTotals();
                setTaxesAndPriceWithoutTaxes();
                let taxInputsAndWithoutTaxInput = document.querySelectorAll('.bezPDV, .PDV');
                removeAttributeDisabled(taxInputsAndWithoutTaxInput);
                ukupnoBezPdv.innerHTML = getTotalWithoutTaxes();
                ukupnoPDV.innerHTML = getTotalTax();
            }
        });
    }
    fetchArticlesForCurrentUser('../include/get_articles.inc.php');
});



// ##############################
// ADD BILL
// ##############################

let addNewBill = document.getElementById('fiskalni');

addNewBill.addEventListener('change', e => {
    if(addNewBill.value == 'dodajFiskalni') {
        let billModal = document.querySelector('.modal-overlay-bill');
        billModal.classList.add('active');
    }
});

let addBillForm = document.getElementById('add-bill-form');

addBillForm.addEventListener('submit', e => {
    e.preventDefault();
    const url = '../include/add_bill.inc.php';
    insertDataToDatabase(url, addBillForm, addNewBill);
}); 

// ################################
// INVOICE TYPE
// #################################
let selectInvoiceType = document.getElementById('tip');

selectInvoiceType.addEventListener('change', e => {
    checkType(selectInvoiceType);
});

checkType(selectInvoiceType);

let cancelButtons = document.querySelectorAll('.cancel');
const modalArticle = document.querySelector('.modal-overlay-article');
const modalFirm = document.querySelector('.modal-overlay-firm');
const modalClient = document.querySelector('.modal-overlay-client');
const modalBill = document.querySelector('.modal-overlay-bill');

for(let cancel of cancelButtons) {
    cancel.addEventListener('click', e => {
        e.preventDefault();
         if(modalBill.classList.contains('active')) {
            modalBill.classList.remove('active');
            addNewBill.selectedIndex = 0;
        } else {
            modalArticle.classList.remove('active');
            let articles = document.querySelectorAll('.imeArtikla');
            for(let article of articles) {
                if(article.value == 'noviArtikal') {
                    article.selectedIndex = 0;
                }
            }
        }
    });
}

// ### IMAGE PREVIEW ###

const fileUploads = document.querySelectorAll('.form__input-file');
showImagePreviewOnChange(fileUploads);



function isEmpty(inputs, errorArray) {
    let container = document.getElementById('articles');
    let articleInputs = container.querySelectorAll('input');
    for(let articleInput of articleInputs) {
        articleInput.classList.add('h-100');
    }
    for(let input of inputs) {
        if(input.value.length == 0 && !input.disabled) {
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
        method: 'GET',
    });
    return response.json(); // parses JSON response into native JavaScript object
}

function displayOptionsBetweenOneAndHundred(){
    list = '';
    for(let i = 1; i <= 100; i++) {
        list += `<option value="${i}"> ${i}% </option>
        `;
    }
    return list;
}

function displayArticleOptionsOnFirmChange(articles) {
    list = '<option value="noviArtikal"> Dodajte novi artikal </option>';
    for(let item of articles) {
        list += `<option value="${item['ime']}"> ${item['ime']} </option>`;
    }
    return list;
}

function displayArticleOptions(articles) {
    list = '<option value="">Izaberite artikal</option>';
    list += '<option value="noviArtikal"> Dodajte novi artikal </option>';
    for(let item of articles) {
        list += `<option value="${item['ime']}"> ${item['ime']} </option>`;
    }
    return list;
}

function displayArticleOptionsOnFirmChange() {
    list = '<option value="noviArtikal"> Dodajte novi artikal </option>'
    for(let item of articles) {
        list += `<option value="${item['ime']}"> ${item['ime']} </option>`;
    }
    return list;
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
        ukupno.value = ukupnoValue != 0 ? ukupnoValue : '';
        ukupno.innerHTML = ukupnoValue != 0 ? ukupnoValue + ' KM' : '';

        let pdv = container.querySelector('.PDV');
        let pdvValue = (ukupnoValue * 14.53 / 100).toFixed(2); 
        pdv.value = pdvValue != 0 ? pdvValue : '';
        pdv.innerHTML = pdvValue != 0 ? pdvValue + ' KM' : '';

        let bezPDV = container.querySelector('.bezPDV');
        let bezPDVValue = (ukupnoValue - pdvValue).toFixed(2);
        bezPDV.value = bezPDVValue != 0 ? bezPDVValue : '';
        bezPDV.innerHTML = bezPDVValue != 0 ? bezPDVValue + ' KM' : '';

    }
}
function getTotalValue() {
    let totalInputs = document.querySelectorAll('.ukupno');
    let total = 0;
    for(let totalInput of totalInputs) {
        let value = totalInput.value ? totalInput.value : '0KM';
        value = value.substring(0, value.length - 2);
        const container = totalInput.parentElement.parentElement;
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
        let value = totalInput.value ? totalInput.value : '0KM';
        value = value.substring(0, value.length - 2);
        const container = totalInput.parentElement.parentElement;
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
        let value = totalInput.value ? totalInput.value : '0KM';
        value = value.substring(0, value.length - 2);
        const container = totalInput.parentElement.parentElement;
        if(container.style.display !== 'none') {
            total += parseFloat(value);
        }
    }
    total = total.toFixed(2);
    total += ' KM';
    return total;
}

function setTotalValues() {
    ukupnoSve.innerHTML = getTotalValue();
    if(pdv != '0') {
        ukupnoPDV.parentElement.style.display = 'flex';
        ukupnoPDV.innerHTML = getTotalTax();
        ukupnoBezPdv.parentElement.style.display = 'flex';
        ukupnoBezPdv.innerHTML = getTotalWithoutTaxes();
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
                setTotalValues();
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

function showErrorsFromServerOnSubmit(result, form) {
    let errorMessages = document.querySelectorAll('.registration-form__error');
    for(let errorMessage of errorMessages) {
        errorMessage.innerHTML = '';
    }
    let inputs = document.querySelectorAll('input, textarea');
    for(let input of inputs) {
        input.style.borderColor = '#ced4da';
    }
    for(const [key, value] of Object.entries(result)) {
        field =     form.querySelector(`input[name="${key}"]`) ?
                    form.querySelector(`input[name="${key}"]`) 
                  : form.querySelector(`select[name="${key}"]`) ?
                    form.querySelector(`select[name="${key}"]`)
                  : form.querySelector(`textarea[name="${key}"]`) ;
        field.style.borderColor = '#a94442';
        field.classList.remove('h-100');
        field.parentElement.querySelector('.registration-form__error').innerHTML = value;
    }
}
function showSuccessMessage(container) {
    container.querySelector('.success-message').innerHTML = 'Uspješno sačuvano';
    container.querySelector('.success-message').style.padding = '0.5rem 1rem';
}

function hideSuccessMessage(container) {
    container.querySelector('.success-message').innerHTML = '';
    container.querySelector('.success-message').style.padding = '0';
}

function checkType(selectInvoiceType) {
    if(selectInvoiceType.value !== 'Faktura') {
        addNewBill.setAttribute('disabled', 'true');
        addNewBill.parentElement.style.display = 'none';
    } else {
        addNewBill.removeAttribute('disabled');
        addNewBill.parentElement.style.display = 'block';
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

function fetchArticlesForCurrentUser(url) {
    formData = new FormData();
    formData.append('submit', '');
    let firmId = document.getElementById('firma').value;
    if(firmId != '') {
        formData.append('id', firmId);
    }
    postData(url, formData)
    .then(result => {
        articles = result;
        addArticleOptionsToArticleFields(result);
    });
}

function addArticleOptionsToArticleFields(result) {
    let articleOptions = document.querySelectorAll('.imeArtikla');
    for(let articleOption of articleOptions) {
        if(articleOption.value == '') {
            articleOption.innerHTML = displayArticleOptions(result);
        } else {
            let currentArticleOption = `<option value="${articleOption.value}" selected>${articleOption.value}</option>`;
            articleOption.innerHTML = currentArticleOption + displayArticleOptionsOnFirmChange(result) ;
        }
    }
}


function insertDataToDatabase(url, data, selectField) {
    let formData = new FormData(data);
    formData.append('submit', '');
      postData(url, formData)
      .then(result => {
          if(result['success']) {
              showSuccessMessage(data);
              createAndSelectNewOption(result['success'], result['ime'], selectField);
              setTimeout(function() {
                  hideSuccessMessage(data);
                  removeClassActive();
              }, 1000)
          } else {
              showErrorsFromServerOnSubmit(result, data);
          }
      });
  }
  
  
  function createAndSelectNewOption (value,innerText, select) {
    let option = document.createElement('option');
    option.value = value;
    option.innerHTML = innerText;
    select.add(option, select[0]);
    select.selectedIndex = 0;
  }

function createArticleDiv(numberOfArticles) {
    let div = document.createElement('div');
    div.classList.add('d-flex');
    div.classList.add('articlesNumber');
    div.classList.add('m-w-100');
    div.classList.add('m-flex-column');
    div.classList.add('m-card');
    div.classList.add('m-mb-m');
     
    div.innerHTML = `
        <div class="w-30 border relative m-d-flex m-w-100">
            <span class="w-100 p-x btn-primary weight-600 d-none m-w-45 m-d-block">Naziv</span>
            <select id="${numberOfArticles}-artikli" name="${numberOfArticles}-imeArtikla" class="dropdown w-100 p-xs form__input border-none h-100 imeArtikla">
            ${displayArticleOptions(articles)}
            </select>
            <span class="registration-form__error"></span>
        </div>
        <div class="w-10 border m-d-flex m-w-100">
            <span class="w-100 d-none p-x btn-primary weight-600 d-none m-w-100 m-d-block">Cijena</span>
            <input type="number" name="${numberOfArticles}-cijena" step="0.01" class="w-100 p-xs form__input border-none h-100 cijena">
            <span class="registration-form__error"></span>
        </div>
        <div class="w-10 border m-d-flex m-w-100">
            <span class="w-100 p-x btn-primary weight-600 d-none m-w-100 m-d-block">Količina</span>
            <input type="number" name="${numberOfArticles}-kolicina" step="0.01" class="w-100 p-xs form__input border-none h-100 kolicina" >
            <span class="registration-form__error"></span>
        </div>
        <div class="w-10 border m-d-flex m-w-100">
            <span class="w-100 p-x btn-primary weight-600 d-none m-w-100 m-d-block">Rabat</span>
            <select name="${numberOfArticles}-rabat" class="w-100 p-xs form__input border-none h-100 rabat">
            <option value="0" selected>0%</option>
            ${displayOptionsBetweenOneAndHundred()}
            </select>
            <span class="registration-form__error"></span>
        </div>
        <div class="w-15 border m-d-flex m-w-100">
            <span class="w-100 p-x btn-primary weight-600 d-none m-w-100 m-d-block">Cijena bez PDV</span>
            <input type="text" name="${numberOfArticles}-bezPdv" ${pdv == '1' ? '' : "disabled='true'"} class="w-100 p-xs form__input border-none h-100 bezPDV" >
            <span class="registration-form__error"></span>
        </div>
        <div class="w-10 border m-d-flex m-w-100">
            <span class="w-100 p-x btn-primary weight-600 d-none m-w-45 m-d-block">PDV</span>
            <input type="text" name="${numberOfArticles}-pdv" ${pdv == '1' ? '' : "disabled='true'"} class="w-100 p-xs border-none form__input h-100 PDV" >
            <span class="registration-form__error"></span>
        </div>
        <div class="w-10 border m-d-flex m-w-100">
            <span class="w-100 p-x btn-primary weight-600 d-none m-w-45 m-d-block">Ukupno</span>
            <input type="text" name="${numberOfArticles}-ukupno" class="w-100 p-xs border-none form__input h-100 ukupno" >
            <span class="registration-form__error"></span>
        </div>
        <i class="fas fa-times remove-icon w-5 border d-flex jc-c ai-c m-d-flex pointer m-p-xs m-w-100"></i>
        <div class="modal-overlay">
            <div class="modal">
                <div class="modal__heading">
                    <h3>POTVRDA O BRISANJU</h3>
                </div>
                <div class="modal__warning">
                    <p>Da li ste sigurni da želite da izbrišete artiakl?</p>
                </div>
                <div class="modal__button mt-s text-right p-xs">
                    <span class="btn btn-danger removeArticle">Izbriši</span>
                    <span class="btn btn-secondary cancelRemoveArticle">Odustani</span>
                </div>
            </div>
        </div>
    `;
    return div;
}


function removeArticlesModal(inputFieldsContainer) {
    const deleteArticles = inputFieldsContainer.querySelectorAll('.removeArticle');
    const cancelButtons = inputFieldsContainer.querySelectorAll('.cancelRemoveArticle');

    let modal = inputFieldsContainer.querySelector('.modal-overlay');
    modal.classList.add('active');

    for(let cancel of cancelButtons) {
        cancel.addEventListener('click', e => {
            let modal = e.currentTarget.parentElement.parentElement.parentElement;
            modal.classList.remove('active');
        });
    }
    for(let deleteArticle of deleteArticles) {
        deleteArticle.addEventListener('click', () => {
            inputFieldsContainer.remove();
        });
    }
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

function removeClassActive() {
    document.querySelector('.modal-overlay-bill').classList.remove('active');
    document.querySelector('.modal-overlay-article').classList.remove('active');
}