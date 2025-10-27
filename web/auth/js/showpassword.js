document.addEventListener('DOMContentLoaded', function () {
    var pwd = document.getElementById('password');
    var btn = document.querySelector('.showpass');
    var toggleImg = btn ? btn.querySelector('img.toggle-icon') : null;
    var eyeSrc = 'css/img/eye.svg';
    var eyeOffSrc = 'css/img/eye-off.svg';
    if (!pwd || !btn || !toggleImg) return;
    btn.addEventListener('click', function () {
        var isPwd = pwd.getAttribute('type') === 'password';
        pwd.setAttribute('type', isPwd ? 'text' : 'password');
        btn.setAttribute('aria-pressed', isPwd ? 'true' : 'false');
        btn.setAttribute('title', isPwd ? 'Hide password' : 'Show password');
        toggleImg.setAttribute('src', isPwd ? eyeOffSrc : eyeSrc);
        toggleImg.setAttribute('alt', isPwd ? 'Hide password icon' : 'Show password icon');
    });
});