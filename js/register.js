const submitBtn = document.querySelector('#submitBtn');

const loaderContainer = document.querySelector('#loaderContainer');
const recaptchaResponse = document.querySelector('#recaptchaResponse');

document.addEventListener('DOMContentLoaded', function() {
    authorizeBtn.classList.add("hide");

    grecaptcha.ready(function() {
        grecaptcha.execute('6LeuWIEUAAAAAF6aZy05cC5uNot2veX4IbsBxjza', {action: 'register'}).then(function(token) {
            loaderContainer.parentNode.removeChild(loaderContainer);
            recaptchaResponse.value = token;
            authorizeBtn.classList.remove("hide");
        });
    });
});
