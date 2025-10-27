(function() {
    var clicked = localStorage.getItem("darkmode") === "true";
    const toggleswitch = document.getElementById("Switch");

    function applyDarkmode(enable) {
        document.documentElement.classList.toggle("dark-invert", enable);
        if (!document.getElementById("dark-invert-style")) {
            const s = document.createElement("style");
            s.id = "dark-invert-style";
            s.textContent = `
            .dark-invert { filter: invert(0.85) hue-rotate(180deg) brightness(1.1) contrast(0.9) !important; }
            .dark-invert img, .dark-invert video, .dark-invert iframe, .dark-invert svg { filter: invert(1) hue-rotate(180deg) !important; }
        `;
            document.head.appendChild(s);
        }
    }

    applyDarkmode(clicked);

    if (toggleswitch) {
        toggleswitch.addEventListener("click", function () {
            clicked = !clicked;
            localStorage.setItem("darkmode", clicked);
            applyDarkmode(clicked);
            console.log("darkmode:", clicked);
        });
    }
})();

