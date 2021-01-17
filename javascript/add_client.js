// ### IMAGE PREVIEW ###

const fileUploads = document.querySelectorAll('.form__input-file');

showImagePreviewOnChange(fileUploads);

// ### DISPLAY AND HIDE INPUT FIELD

let radioInputs = document.querySelectorAll('input[type="radio"]');

for(let input of radioInputs) {
    input.addEventListener('click', () => {
        let pib = document.querySelector('.pib');
        if(input.checked && input.value == '1') {
            pib.parentElement.style.display = 'block';
            pib.removeAttribute('disabled');
        } else {
            pib.parentElement.style.display = 'none';
            pib.setAttribute('disabled', 'true');
        }
    });
}

// ### HANDLE ADD FIRM SUBMIT ###

const firmForm = document.querySelector('form');

firmForm.addEventListener('submit', e => {
    e.preventDefault();
    let formData = new FormData(firmForm);
    formData.append('submit', '');
    const url = 'include/add_client.inc.php';
    let inputs = document.querySelectorAll('#ime');
    let errorArray = [];
    isEmpty(inputs, errorArray);
    if(errorArray.length < 1) {
        postData(url, formData)
        .then(result => {
            if(result['success']) {
                document.querySelector('.success-message').innerHTML = 'Uspješno dodat klijent';
                document.querySelector('.success-message').style.padding = '0.5rem 1rem';
                removeErrorTextAndBorderColor(firmForm);
                removeInputValues();
                removeImages(firmForm);
                setTimeout(function() {
                    document.querySelector('.success-message').innerHTML = '';
                    document.querySelector('.success-message').style.padding = '0';
                }, 1000)
            } else {
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
                    field.parentElement.querySelector('.registration-form__error').innerHTML = value;
                }
            }
        });
    }
}); 



// ### FUNCTIONS ### 

async function postData(url, data) {
    const response = await fetch(url, {
        method: 'POST',
        body: data
    });
    return response.json(); // parses JSON response into native JavaScript object
}

function isEmpty(inputs, errorArray) {
    for(let input of inputs) {
        if(input.value == '' && !input.disabled) {
            input.style.borderColor = '#a94442';
            input.parentElement.querySelector('.registration-form__error').innerHTML = 'Polje ne smije biti prazno';
            errorArray.push('error');
        } else {
            input.style.borderColor = '#ced4da';
            input.parentElement.querySelector('.registration-form__error').innerHTML = '';
        }
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



function removeInputValues() {
    let inputs = document.querySelectorAll('input, textarea');
    for(let input of inputs) {
        input.value = '';
    }
}

function removeImages(container) {
    let images = container.querySelectorAll('img');
    for(let img of images) {
        img.src = '';
    }
}

function removeErrorTextAndBorderColor(container) {
    let errorMessages = container.querySelectorAll('.registration-form__error');
    for(let errorMessage of errorMessages) {
        errorMessage.innerHTML = '';
    }
    let inputs = container.querySelectorAll('input, textarea, select');
    for (let input of inputs) {
        input.style.borderColor = '#ced4da'
    }
}