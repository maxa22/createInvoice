const editButtons = document.querySelectorAll('.edit');

for(let editButton of editButtons) {
    editButton.addEventListener('click', e => {
        let container = e.currentTarget.parentElement.parentElement;
        let inputs = container.querySelectorAll('input, textarea');
        enableInputFields(inputs);
        let inputValueArray = getInputValues(inputs);
        let editingBtns = document.querySelectorAll('.editing');
        hideFields(editingBtns);
        let saveBtn= container.querySelectorAll('.saving');
        showFields(saveBtn);
        inputs[0].focus();
        let cancel = container.querySelector('.cancel');
        cancel.addEventListener('click', () => {
            hideFields(saveBtn);
            showFields(editingBtns);
            returnInitialValueToInputFields(inputs, inputValueArray);
            removeErrorTextAndBorderColor(container);
            disableInputFields(inputs);
        });
    });
}


const updateArticles = document.querySelectorAll('.update-article');
for(let updateArticle of updateArticles) {
    updateArticle.addEventListener('submit', e => {
        e.preventDefault();
        let formData = new FormData(updateArticle);
        formData.append('submit', '');
        const url = 'include/update_article.inc.php';
        let inputs = updateArticle.querySelectorAll('input');
        let errorArray = [];
        isEmptyArticle(inputs, errorArray);
        if(errorArray.length < 1) {
            postData(url, formData)
            .then(result => {
                if(!result) {
                    location.reload();
                } else {
                    let errorMessages = updateArticle.querySelectorAll('.registration-form__error');
                    for(let errorMessage of errorMessages) {
                        errorMessage.innerHTML = '';
                    }
                    let inputs = updateArticle.querySelectorAll('input textarea');
                    for(let input of inputs) {
                        input.style.borderColor = '#ced4da';
                    }
                    for(const [key, value] of Object.entries(result)) {
                        field =    updateArticle.querySelector(`input[name="${key}"]`) ?
                                   updateArticle.querySelector(`input[name="${key}"]`) 
                                 : updateArticle.querySelector(`textarea[name="${key}"]`);
                        field.classList.remove('h-100');
                        field.parentElement.querySelector('.registration-form__error').innerHTML = value;
                    }
                }
            });
        }
    })
}

// ## FUNCTIONS ###

function isEmptyArticle(inputs, errorArray) {
    for(let input of inputs) {
        if(input.value == '') {
            input.style.borderBottom = '1px solid #a94442';
            input.style.borderColor = '#a94442';
            input.classList.remove('h-100');
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
        input.style.border = 'none';
        input.classList.add('h-100');
    }
}

function disableInputFields(inputFieldsArray) {
    for(let input of inputFieldsArray) {
        input.setAttribute('disabled', 'true');
    }
}

