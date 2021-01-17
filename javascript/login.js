const registrationForm = document.querySelector('form');

registrationForm.addEventListener('submit', e => {
    e.preventDefault();
    let formData = new FormData(registrationForm);
    formData.append('submit', '');
    const url = 'include/login.inc.php';
    let inputs = document.querySelectorAll('input');
    let errorArray = [];
    isEmpty(inputs, errorArray);
    if(errorArray.length < 1) {
        postData(url, formData)
        .then(data => {
            if(!data){
                window.location.href = 'firms';
            } else {
                let errorMessage = document.querySelectorAll('.registration-form__error')[1];
                errorMessage.innerHTML = data['error'];
                let inputs = document.querySelectorAll('input');
                for(let input of inputs) {
                    input.style.borderColor = '#a94442'
                }
            }
        });
    }
}); 


async function postData(url, data) {
    const response = await fetch(url, {
        method: 'POST',
        body: data 
    });
    return response.json(); // parses JSON response into native JavaScript object
}

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
