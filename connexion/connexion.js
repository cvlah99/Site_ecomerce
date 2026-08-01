const form = document.getElementById('connexionForm');

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

    document.getElementById('email').addEventListener('input', function() {
        validerCondition('email-format', this.value.length > 0);
    });

    document.getElementById('mot_de_passe').addEventListener('input', function() {
        validerCondition('mdp-longueur', this.value.length >= 1);
    });

    document.getElementById('toggleMdp').addEventListener('click', function() {
        const input = document.getElementById('mot_de_passe');
        const icon = document.getElementById('iconMdp');
        if (input.type === 'password') {
            input.type = 'text';
            icon.className = 'bi bi-eye-slash';
        } else {
            input.type = 'password';
            icon.className = 'bi bi-eye';
        }
    });

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const email = document.getElementById('email').value;
        const mdp = document.getElementById('mot_de_passe').value;

        const emailValide = email.length > 0; // Allows any text (like "admin")
        const mdpValide = mdp.length >= 1;

        if(emailValide && mdpValide) {
            this.submit();
        }
    });
}