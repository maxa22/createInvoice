const deleteInvoices = document.querySelectorAll('.delete__invoice');
const cancelButtons = document.querySelectorAll('.cancel');

for(let deleteInvoice of deleteInvoices) {
    deleteInvoice.addEventListener('click', e => {
        let container = e.currentTarget.parentElement;
        let modal = container.querySelector('.modal-overlay');
        modal.classList.add('active');
    });
}

for(let cancel of cancelButtons) {
    cancel.addEventListener('click', e => {
        let modal = e.currentTarget.parentElement.parentElement.parentElement;
        modal.classList.remove('active');
    });
}