const form = document.getElementById('inscriptionForm');

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
        const val = this.value;
        validerCondition('nom-longueur', val.length >= 3 && val.length <= 30);
        validerCondition('nom-lettres', /^[a-zA-ZÀ-ÿ\s]+$/.test(val));
    });

    document.getElementById('prenom').addEventListener('input', function() {
        const val = this.value;
        validerCondition('prenom-longueur', val.length >= 3 && val.length <= 30);
        validerCondition('prenom-lettres', /^[a-zA-ZÀ-ÿ\s]+$/.test(val));
    });

    document.getElementById('email').addEventListener('input', function() {
        const val = this.value;
        validerCondition('email-format', /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val));
    });

    document.getElementById('telephone').addEventListener('input', function() {
        const val = this.value;
        validerCondition('tel-format', /^(\+212|0)[5-7][0-9]{8}$/.test(val));
        validerCondition('tel-longueur', val.replace(/\D/g, '').length >= 10 && val.replace(/\D/g, '').length <= 15);
    });

    document.getElementById('mot_de_passe').addEventListener('input', function() {
        const val = this.value;
        validerCondition('mdp-longueur', val.length >= 8);
        validerCondition('mdp-majuscule', /[A-Z]/.test(val));
        validerCondition('mdp-chiffre', /[0-9]/.test(val));
        validerCondition('mdp-special', /[!@#$%^&*]/.test(val));
        const confirm = document.getElementById('confirmer_mdp').value;
        if (confirm) validerCondition('mdp-match', val === confirm);
    });

    document.getElementById('confirmer_mdp').addEventListener('input', function() {
        const mdp = document.getElementById('mot_de_passe').value;
        validerCondition('mdp-match', this.value === mdp && this.value !== '');
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

    document.getElementById('toggleConfirm').addEventListener('click', function() {
        const input = document.getElementById('confirmer_mdp');
        const icon = document.getElementById('iconConfirm');
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

        const nom = document.getElementById('nom').value;
        const prenom = document.getElementById('prenom').value;
        const email = document.getElementById('email').value;
        const telephone = document.getElementById('telephone').value;
        const mdp = document.getElementById('mot_de_passe').value;
        const confirmer = document.getElementById('confirmer_mdp').value;

        const nomValide = nom.length >= 3 && nom.length <= 30 && /^[a-zA-ZÀ-ÿ\s]+$/.test(nom);
        const prenomValide = prenom.length >= 3 && prenom.length <= 30 && /^[a-zA-ZÀ-ÿ\s]+$/.test(prenom);
        const emailValide = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
        const telValide = /^(\+212|0)[5-7][0-9]{8}$/.test(telephone);
        const mdpValide = mdp.length >= 8 && /[A-Z]/.test(mdp) && /[0-9]/.test(mdp) && /[!@#$%^&*]/.test(mdp);
        const matchValide = mdp === confirmer && confirmer !== '';

        if(nomValide && prenomValide && emailValide && telValide && mdpValide && matchValide) {
            this.submit();
        }
    });
}