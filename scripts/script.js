const form = document.getElementById('thanks_form');
const name = form.querySelector('#thanks_name');
const nameRegExp = /(^[\p{L}]{1,30})+$/iu;
const phone = form.querySelector('#thanks_phone');
const email = form.querySelector('#thanks_email');
const emailRegExpFirst = /.+@.+\..+/i;
const emailRegExp = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/;

name.addEventListener('input', function (event) {
    const test = name.value.length !== 0 || nameRegExp.test(name.value);
    if (name.classList.contains('invalid')) {
        name.classList.remove('invalid')
    }
    if (!test) {
        name.classList.add("invalid");
    }
})

phone.addEventListener('input', function (event) {
    const test = phone.value.length === 0 || phone.value.length < 11;
    if (phone.classList.contains('invalid')) {
        phone.classList.remove('invalid')
    }
    if (test) {
        phone.classList.add("invalid");
    }
})

email.addEventListener('input', function (event) {
    const test = email.value.length !== 0 && emailRegExpFirst.test(email.value) && emailRegExp.test(email.value);
    if (email.classList.contains('invalid')) {
        email.classList.remove('invalid')
    }

    if (!test) {
        email.classList.add("invalid");
    }
})

form.addEventListener('submit', function (event) {

    let test = name.value.length !== 0 || nameRegExp.test(name.value);

    if (!test) {
        if (!name.classList.contains('invalid')) {
            name.classList.add("invalid");
        }
        event.preventDefault();
        return;
    }
    test = phone.value.length === 0 || phone.value.length < 11;

    if (test) {
        if (!phone.classList.contains('invalid')) {
            phone.classList.add("invalid");
        }
        event.preventDefault();
    }

    test = email.value.length !== 0 && emailRegExpFirst.test(email.value) && emailRegExp.test(email.value);

    if (!test) {
        if (!email.classList.contains('invalid')) {
            email.classList.add("invalid");
        }
        event.preventDefault();
        return;
    }
})

const imaskInputs = document.querySelectorAll('input.imask')
if( imaskInputs ) {
    imaskInputs.forEach(input => {
        IMask(
            input, {
                mask: '{7}#000000000',
                definitions: {
                    '#': /[9]/
                }
            });
    })
}