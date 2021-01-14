const newArticle = document.getElementById('newArticle');

newArticle.addEventListener('submit', e => {
    e.preventDefault();
    let formData = new FormData(newArticle);
    formData.append('submit', '');
    const url = 'include/articles.inc.php';
    let inputs = newArticle.querySelectorAll('input');
    let errorArray = [];
    isEmpty(inputs, errorArray);
    if(errorArray.length < 1) {
        postData(url, formData)
        .then(result => {
            if(!result) {
                document.querySelector('.success-message').innerHTML = 'Uspješno dodat artikal';
                document.querySelector('.success-message').style.padding = '0.5rem 1rem';
                setTimeout(function() {
                    document.querySelector('.success-message').innerHTML = '';
                    document.querySelector('.success-message').style.padding = '0';
                    removeInputValues();
                }, 1000);
            } else {
                let errorMessages = document.querySelectorAll('.registration-form__error');
                for(let errorMessage of errorMessages) {
                    errorMessage.innerHTML = '';
                }
                let inputs = document.querySelectorAll('input textarea');
                for(let input of inputs) {
                    input.style.borderColor = '#ced4da';
                }
                for(const [key, value] of Object.entries(result)) {
                    field =    document.querySelector(`input[name="${key}"]`) ?
                               document.querySelector(`input[name="${key}"]`) 
                             : document.querySelector(`textarea[name="${key}"]`);
                    field.style.borderColor = '#a94442';
                    field.parentElement.querySelector('.registration-form__error').innerHTML = value;
                }
            }
        });
    }
});


function isEmpty(inputs, errorArray) {
    for(let input of inputs) {
        if(input.value == '') {
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

function removeInputValues() {
    let inputs = document.querySelectorAll('input, textarea');
    for(let input of inputs) {
        input.value = '';
    }
}