const settingsUser = document.querySelector('#settings-user');
const settingsUserBtn = document.querySelector('#settings-user-btn');

const settingsPassword = document.querySelector('#settings-password');
const settingsPasswordBtn = document.querySelector('#settings-password-btn');

settingsPasswordBtn.addEventListener("click", function() {
    settingsPassword.classList.add("hide");
    settingsUser.classList.remove("hide");
});

settingsUserBtn.addEventListener("click", function() {
    settingsPassword.classList.remove("hide");
    settingsUser.classList.add("hide");
});
