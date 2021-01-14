const fileUploads = document.querySelectorAll('.form__input-file');

showImagePreviewOnChange(fileUploads);

const editButtons = document.querySelectorAll('.edit');

for(let editButton of editButtons) {
    editButton.addEventListener('click', e => {
        let container = e.currentTarget.parentElement.parentElement;
        let inputs = container.querySelectorAll('input');
        enableInputFields(inputs);
        let images = container.querySelectorAll('img');
        let imgArray = [];
        getImageValues(images, imgArray);
        let inputText = container.querySelectorAll('input[type="text"]');
        let inputValueArray = getInputValues(inputText);
        let editingBtns = document.querySelectorAll('.editing');
        hideFields(editingBtns);
        let saveBtn= container.querySelectorAll('.btn');
        showFields(saveBtn);
        let cancel = container.querySelector('.cancel');
        cancel.addEventListener('click', () => {
            hideFields(saveBtn);
            showFields(editingBtns);
            returnInitialValueToInputFields(inputText, inputValueArray);
            removeErrorTextAndBorderColor(container);
            disableInputFields(inputs);
            returnImageValues(images, imgArray);
        });
    });
}


const updateBills = document.querySelectorAll('form');

for(let updateBill of updateBills) {
    updateBill.addEventListener('submit', e => {
        e.preventDefault();
        let formData = new FormData(updateBill);
        formData.append('submit', '');
        const url = 'include/update_bill.inc.php';
        let inputs = updateBill.querySelectorAll('input[type="text"]');
        let errorArray = [];
        isEmpty(inputs, errorArray);
        if(errorArray.length < 1) {
            postData(url, formData)
            .then(result => {
                if(!result) {
                    location.reload();
                } else {
                    let errorMessages = updateBill.querySelectorAll('.registration-form__error');
                    for(let errorMessage of errorMessages) {
                        errorMessage.innerHTML = '';
                    }
                    let inputs = updateBill.querySelectorAll('input');
                    for(let input of inputs) {
                        input.style.borderColor = '#ced4da';
                    }
                    for(const [key, value] of Object.entries(result)) {
                        field =    updateBill.querySelector(`input[name="${key}"]`);
                        field.parentElement.querySelector('.registration-form__error').innerHTML = value;
                    }
                }
            });
        }
    })
}

// ## FUNCTIONS ###

function isEmpty(inputs, errorArray) {
    for(let input of inputs) {
        if(input.value == '') {
            input.style.borderColor = '#a94442';
            input.parentElement.querySelector('.registration-form__error').innerHTML = 'Polje ne smije biti prazno';
            errorArray.push('error');
        } else {
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


function enableInputFields(inputFieldsArray) {
    for(let input of inputFieldsArray) {
        input.removeAttribute('disabled');
    }
}

function hideFields(fields) {
    for(let field of fields) {
        field.style.display = 'none';
    }
}
function showFields(fields) {
    for(let field of fields) {
        field.style.display = 'block';
    }
}

function getInputValues(inputFieldsArray) {
    let inputValuesArray = [];
    for(let input of inputFieldsArray) {
        const value = input.getAttribute('value');
        inputValuesArray.push(value);
    }
    return inputValuesArray;
}


function returnInitialValueToInputFields(inputFieldsArray, inputValueArray) {
    for(let i=0; i < inputFieldsArray.length; i++) {
        inputFieldsArray[i].value = inputValueArray[i];
    }
}


function removeErrorTextAndBorderColor(container) {
    let errorMessages = container.querySelectorAll('.registration-form__error');
    for(let errorMessage of errorMessages) {
        errorMessage.innerHTML = '';
    }
    let inputs = container.querySelectorAll('input, textarea');
    for (let input of inputs) {
        input.style.borderColor = '#ced4da';
    }
}

function disableInputFields(inputFieldsArray) {
    for(let input of inputFieldsArray) {
        input.setAttribute('disabled', 'true');
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


function getImageValues(images, array) {
    for(let image of images) {
        array.push(image.src);
    }
    return array;
}

function returnImageValues(images, array) {
    for(let i = 0; i < images.length; i++) {
        if(array[i] === window.location.href) {
            images[i].src = '';
        } else {
            images[i].src = array[i];
        }
    }
}