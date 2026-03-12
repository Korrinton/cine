document.addEventListener('DOMContentLoaded', function () {
    const togglePassword = document.querySelector('#togglePassword');
    const passwordInput = document.querySelector('#floatPassword');
    const eyeIcon = document.querySelector('#eyeIcon');

    togglePassword.addEventListener('click', function () {
        // Cambiar el tipo de atributo
        const isPassword = passwordInput.type === 'password';
        console.log (isPassword);
        passwordInput.type = isPassword ? 'text' : 'password';


        
        if (isPassword) {
            eyeIcon.classList.replace('bi-eye', 'bi-eye-slash');
        } else {
            eyeIcon.classList.replace('bi-eye-slash', 'bi-eye');
        }
    });


    const forms = document.querySelectorAll('.slow-submit'); // Usaremos esta clase para identificar qué formularios queremos bloquear

    forms.forEach(form => {
        form.addEventListener('submit', function () {
            const btn = form.querySelector('button[type="submit"]');
            if (btn) {
                // Bloqueamos el botón
                btn.disabled = true;
                
                // Añadimos el spinner y cambiamos el texto
                btn.innerHTML = `
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    Procesando...
                `;
            }
        });
    });


    document.querySelectorAll('.form-control').forEach(input => {
    input.addEventListener('input', () => {
        if (input.checkValidity()) {
            input.classList.remove('is-invalid');
            //input.classList.add('is-valid');
        }
        });
    });

});

