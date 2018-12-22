const authorizeBtn = document.querySelector('#authorizeBtn');
const authorizeRedirect = document.querySelector('#authorizeRedirect');

const loaderContainer = document.querySelector('#loaderContainer');
const recaptchaResponse = document.querySelector('#recaptchaResponse');

document.addEventListener('DOMContentLoaded', function() {
    authorizeBtn.classList.add("hide");
    authorizeRedirect.classList.add("hide");

    grecaptcha.ready(function() {
        grecaptcha.execute('6LeuWIEUAAAAAF6aZy05cC5uNot2veX4IbsBxjza', {action: 'auhtorize_oauth'}).then(function(token) {
            loaderContainer.parentNode.removeChild(loaderContainer);

            recaptchaResponse.value = token;

            authorizeBtn.classList.remove("hide");
            authorizeRedirect.classList.remove("hide");
        });
    });
});
