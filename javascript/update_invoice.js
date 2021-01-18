let articles = '';
let pdv = '';
window.addEventListener('DOMContentLoaded', () => {
    formDataTwo = new FormData();
    formDataTwo.append('submit', '');
    let firmId = document.getElementById('firma').value;
    formDataTwo.append('id', firmId);
    urlTwo = '../include/get_articles.inc.php';
    postData(urlTwo, formDataTwo)
    .then(result => {
        articles = result;
        let articleOptions = document.querySelectorAll('.imeArtikla');
        for(let articleOption of articleOptions) {
            articleOption.innerHTML += displayArticleOptions(result);
        }
    });

    let url = '../include/get_firm.inc.php?id=' + document.getElementById('firma').value;
    getData(url)
    .then(result => {
        pdv = result['pdv'];
    });
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
                let parent = ev.currentTarget.parentElement;
                let inputFields = parent.querySelectorAll('input');
                let selectField = parent.querySelector('select');
                for(let inputField of inputFields) {
                    inputField.style.display = 'unset';
                    inputField.removeAttribute('disabled');
                }
                inputFields[0].focus();
                inputFields[0].parentElement.parentElement.classList.add('m-w-200');
                selectField.style.display = 'none';
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

let ukupnoBezPdv = document.querySelector('.ukupnoBezPdv');
let ukupnoPDV = document.querySelector('.ukupnoPDV');
let ukupnoSve = document.querySelector('.ukupnoSve');
ukupnoSve.innerHTML = getTotalValue();
if(ukupnoBezPdv) {
    ukupnoBezPdv.innerHTML = getTotalWithoutTaxes();
    ukupnoPDV.innerHTML = getTotalTax();
}

articleContainer.addEventListener('input', e => {
    // change event on price, quantity
    if(e.target.classList.contains('cijena') || e.target.classList.contains('kolicina') || e.target.classList.contains('rabat')) {
        e.stopPropagation();
        let container = e.target.parentElement.parentElement; 
        setValueForRowTotal(container);
    }
});


let addArticle = document.querySelector('.add');

addArticle.addEventListener('click', e => {
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
            <span class="w-100 p-x btn-primary weight-600 d-none m-d-block">Naziv</span>
            <div class="d-flex"> 
                <div> 
                    <input type="text" name="${numberOfArticles}-idArtikla" disabled class="w-100 form__input p-xs border-none border-right d-none h-100 imeArtikla" placeholder="Šifra">
                    <span class="registration-form__error"></span>
                </div>
                <div> 
                    <input type="text" name="${numberOfArticles}-imeArtikla-new" disabled class="w-100 form__input p-xs border-none d-none h-100 imeArtikla" placeholder="Naziv">
                    <span class="registration-form__error"></span>
                </div>
            </div>
            <select id="${numberOfArticles}-artikli" name="${numberOfArticles}-imeArtikla-new" class="dropdown w-100 p-xs form__input border-none h-100 imeArtikla">
                ${displayNewArticleOptions(articles)}
            </select>
            <span class="registration-form__error"></span>
        </div>
        <div class="w-10 border relative m-d-flex m-w-100">
            <span class="w-100 p-x btn-primary weight-600 d-none m-d-block">Cijena</span>
            <input type="number" name="${numberOfArticles}-cijena" step="0.01" class="w-100 p-xs form__input cijena border-none h-100">
            <span class="registration-form__error"></span>
        </div>
        <div class="w-10 border relative m-d-flex m-w-100">
            <span class="w-100 p-x btn-primary weight-600 d-none m-d-block">Količina</span>
            <input type="number" name="${numberOfArticles}-kolicina" step="0.01" class="w-100 p-xs form__input kolicina border-none h-100" >
            <span class="registration-form__error"></span>
        </div>
        <div class="w-10 border m-d-flex m-w-100">
            <span class="w-100 p-x btn-primary weight-600 d-none m-d-block">Rabat</span>
            <select name="${numberOfArticles}-rabat" class="w-100 p-xs form__input border-none rabat h-100 rabat">
                <option value="0" selected>0%</option>
                ${displayOptionsBetweenOneAndHundred()}
            </select>
            <span class="registration-form__error"></span>
        </div>
        <div class="w-15 border m-d-flex m-w-100">
            <span class="w-100 p-x btn-primary weight-600 d-none m-d-block">Cijena bez PDV</span>
            <input type="text" name="${numberOfArticles}-bezPdv" ${pdv == '1' ? '' : 'disabled'} class="w-100 p-xs form__input border-none h-100 bezPDV" >
            <span class="registration-form__error"></span>
        </div>
        <div class="w-10 border m-d-flex m-w-100">
            <span class="w-100 p-x btn-primary weight-600 d-none m-d-block">PDV</span>
            <input type="text" name="${numberOfArticles}-pdv" ${pdv == '1' ? '' : 'disabled'} class="w-100 p-xs border-none form__input h-100 PDV" >
            <span class="registration-form__error"></span>
        </div>
        <div class="w-10 border m-d-flex m-w-100">
            <span class="w-100 p-x btn-primary weight-600 d-none m-d-block">Ukupno</span>
            <input type="text" name="${numberOfArticles}-ukupno" class="w-100 p-xs border-none form__input h-100 ukupno" >
            <span class="registration-form__error"></span>
        </div>
        <div class="w-5 border remove d-flex jc-c ai-c m-d-flex pointer m-p-xs m-w-100">
            <i class="fas fa-times remove-icon"></i>
        </div>
    `;
    container.append(div);
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
                document.querySelector('.success-message').innerHTML = 'Uspješno izmijenjeno';
                document.querySelector('.success-message').style.padding = '0.5rem 1rem';
                setTimeout(function() {
                    document.querySelector('.success-message').innerHTML = '';
                    document.querySelector('.success-message').style.padding = '0';
                    window.location.href = '../invoices';
                }, 1000);
            } else {
                let errorMessages = document.querySelectorAll('.registration-form__error');
                for(let errorMessage of errorMessages) {
                    errorMessage.innerHTML = '';
                }
                let container = document.getElementById('articles');
                let articleInputs = container.querySelectorAll('input');
                for(let articleInput of articleInputs) {
                    articleInput.classList.add('h-100');
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
        });
    }
}); 



let firma = document.getElementById('firma');

firma.addEventListener('change', e => {
    if(e.currentTarget.value !== '') {
        let url = '../include/get_firm.inc.php?id=' + e.currentTarget.value;
        getData(url)
        .then(result => {
            pdv = result['pdv'];
            if(result['pdv'] === '0') {
                let bezPDV = document.querySelectorAll('.bezPDV');
                for(let bez of bezPDV) {
                    bez.value = '';
                }
                setAttributeDisabled(bezPDV);
                let pdvs = document.querySelectorAll('.PDV');
                for(let pdv of pdvs) {
                    pdv.value = '';
                }
                setAttributeDisabled(pdvs);
                hiddingTotalsNotUsed();
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
    let url2 = '../include/get_articles.inc.php';
    let formData = new FormData();
    formData.append('submit', '');
    formData.append('id', e.currentTarget.value);
    postData(url2, formData)
    .then(result => {
        articles = result;
    });
});

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

function displayArticleOptions(articles) {
    list = '<option value="noviArtikal"> Dodajte novi artikal </option>';
    for(let item of articles) {
        list += `<option value="${item['ime']}"> ${item['ime']} </option>`;
    }
    return list;
}

function displayNewArticleOptions(articles) {
    list = '<option value="">Izaberite artikal</option>';
    list += '<option value="noviArtikal"> Dodajte novi artikal </option>';
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

function settingTotalValues() {
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