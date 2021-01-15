window.addEventListener('DOMContentLoaded', () => {
    let articles = '';
    formDataTwo = new FormData();
    formDataTwo.append('submit', '');
    urlTwo = 'include/get_articles.inc.php';
    postData(urlTwo, formDataTwo)
    .then(result => {
        articles = result;
    });

    let articleContainer = document.getElementById('articles');

    articleContainer.addEventListener('input', e => {
        e.stopPropagation();
        // on input show dropdown
        if(e.target.classList.contains('imeArtikla')) {
            let container = e.target.parentElement;
            let inputs = container.parentElement.querySelectorAll('input');
            let dropdownMenu = container.querySelector('datalist');
            dropdownMenu.innerHTML = '';
            let input = e.target;
            
            for(let item of articles) {
                let listItem = document.createElement('option');
                listItem.innerHTML = item['ime'];
                listItem.value = item['ime'];
                dropdownMenu.append(listItem);
            }
            input.addEventListener("input", function(ev){
                
                let isInputEvent = (Object.prototype.toString.call(ev).indexOf("InputEvent") > -1);
                
                if(!isInputEvent)
                    for(let item of articles) {
                        if(item['ime'] == ev.target.value) {
                            inputs[1].value = item['cijena'];
                            inputs[2].focus();
                            break;
                        }
                    }
                }, false);
        }
    });

    let add = document.querySelector('.add');
    add.addEventListener('click', e => {
        e.preventDefault();
        let div = document.createElement('div');
        div.classList.add('d-flex');
        div.classList.add('articlesNumber');
        div.classList.add('s-w-100');
        div.classList.add('s-flex-column');
        div.classList.add('s-card');
        div.classList.add('s-mb-xs');
        let container = document.getElementById('articles');
        let numberOfArticles = container.querySelectorAll('.articlesNumber').length + 1;
        
        div.innerHTML = `
            <div class="w-60 border relative s-d-flex s-w-100">
                <span class="w-40 p-x btn-primary weight-600 d-none s-d-block">Naziv</span>
                <input type="text" name="${numberOfArticles}-imeArtikla" class="w-100 p-xs border-none h-100 imeArtikla"  list="${numberOfArticles}-artikli">
                <span class="registration-form__error"></span>
                <datalist id="${numberOfArticles}-artikli" class="dropdown"></ul>
            </div>
            <div class="w-20 border s-d-flex s-w-100">
                <span class="w-40 d-none p-x btn-primary weight-600 d-none s-d-block">Cijena</span>
                <input type="number" name="${numberOfArticles}-cijena" step="0.01" class="w-100 p-xs border-none h-100">
                <span class="registration-form__error"></span>
            </div>
            <div class="w-20 border s-d-flex s-w-100">
                <span class="w-40 p-x btn-primary weight-600 d-none s-d-block">Količina</span>
                <input type="number" name="${numberOfArticles}-kolicina" step="0.01" class="w-100 p-xs border-none h-100" >
                <span class="registration-form__error"></span>
            </div>
        `;
        container.append(div);
    });


});



const invoiceForm = document.querySelector('form');

invoiceForm.addEventListener('submit', e => {
    e.preventDefault();
    let formData = new FormData(invoiceForm);
    formData.append('submit', '');
    const url = 'include/add_invoice.inc.php';
    let inputs = document.querySelectorAll('input, select');
    let errorArray = [];
    isEmpty(inputs, errorArray);
    if(errorArray.length < 1) {
        postData(url, formData)
        .then(result => {
            if(result['success']) {
                window.location.href = "render_pdf/" + result['success'];
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


function isEmpty(inputs, errorArray) {
    for(let input of inputs) {
        if(input.value == '' && !input.disabled) {
            input.style.borderColor = '#a94442';
            input.classList.remove('h-100');
            input.parentElement.querySelector('.registration-form__error').innerHTML = 'Polje ne smije biti prazno';
            errorArray.push('error');
        } else {
            let container = document.getElementById('articles');
            let articleInputs = container.querySelectorAll('input');
            for(let articleInput of articleInputs) {
                articleInput.classList.add('h-100');
            }
            input.style.borderColor = '#ced4da';
            input.parentElement.querySelector('.registration-form__error').innerHTML = '';
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

