AOS.init({ duration: 800, once: true, offset: 100 });

window.addEventListener('scroll', function() {
    const navbar = document.getElementById('mainNavbar');
    if (window.scrollY > 50) {
        navbar.classList.add('navbar-scrolled');
    } else {
        navbar.classList.remove('navbar-scrolled');
    }
});

const form = document.getElementById('contactForm');

if(form) {
    function validerCondition(elementId, estValide) {
        const el = document.getElementById(elementId);
        if (estValide) {
            el.classList.add('valid');
            el.querySelector('i').className = 'bi bi-check-circle-fill';
        } else {
            el.classList.remove('valid');
            el.querySelector('i').className = 'bi bi-circle-fill';
        }
    }

    document.getElementById('nom').addEventListener('input', function() {
        validerCondition('nom-valide', this.value.length >= 3 && this.value.length <= 30);
    });

    document.getElementById('email').addEventListener('input', function() {
        validerCondition('email-valide', /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.value));
    });

    document.getElementById('sujet').addEventListener('input', function() {
        validerCondition('sujet-valide', this.value.length >= 5 && this.value.length <= 200);
    });

    document.getElementById('message').addEventListener('input', function() {
        validerCondition('message-valide', this.value.length >= 20);
    });

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const nom = document.getElementById('nom').value;
        const email = document.getElementById('email').value;
        const sujet = document.getElementById('sujet').value;
        const message = document.getElementById('message').value;

        const nomValide = nom.length >= 3 && nom.length <= 30;
        const emailValide = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
        const sujetValide = sujet.length >= 5 && sujet.length <= 200;
        const messageValide = message.length >= 20;

        if(nomValide && emailValide && sujetValide && messageValide) {
            this.submit();
        }
    });
}