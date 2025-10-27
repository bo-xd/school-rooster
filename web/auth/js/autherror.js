(function() {
    const params = new URLSearchParams(window.location.search);
    const error = params.get('error');
    const user = params.get('user');
    const mapping = {
        'empty': 'Please enter both username and password.',
        'invalid': 'Invalid username or password.',
        'dberr': 'Server error. Please try again later.',
        'method': 'Invalid request.'
    };

    if (user) {
        const u = document.getElementById('username');
        if (u) u.value = user;
    }

    if (error) {
        const el = document.getElementById('notification');
        if (!el) return;
        el.textContent = mapping[error] || 'An error occurred.';
        el.classList.add('show');
        el.setAttribute('aria-hidden', 'false');

        if (window.history && window.history.replaceState) {
            const cleanUrl = window.location.protocol + '//' + window.location.host + window.location.pathname;
            window.history.replaceState({}, document.title, cleanUrl);
        }

        setTimeout(() => {
            el.classList.remove('show');
            el.setAttribute('aria-hidden', 'true');
        }, 5000);
    }
})();