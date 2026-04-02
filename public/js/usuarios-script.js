document.addEventListener('DOMContentLoaded', function () {
    activarOjo('#togglePassword', '#floatPassword', '#eyeIcon');
    activarOjo('#togglePasswordConfirm', '#floatPasswordConfirm', '#eyeIconConfirm');


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


    const password = document.getElementById('floatPassword');
    const confirm = document.getElementById('floatPasswordConfirm');
    
    if (password && confirm) {
        password.addEventListener('input', validarCoincidencia);
        confirm.addEventListener('input', validarCoincidencia);
    }




    document.querySelectorAll('.form-control').forEach(input => {
    input.addEventListener('input', () => {
        if (input.checkValidity()) {
            input.classList.remove('is-invalid');
        }
        });
    });

/*
    // Auto-abrir modal si hay errores de validación
    const modalElement = document.getElementById('loginModal');
    if (modalElement) {
        const hasErrors = modalElement.querySelector('.is-invalid, .alert-danger');
        
        if (hasErrors) {
            const loginModal = new bootstrap.Modal(modalElement);
            loginModal.show();
        }
    }
*/
});



function activarOjo(botonId, inputId, iconoId) {
    const btn = document.querySelector(botonId);
    const input = document.querySelector(inputId);
    const icono = document.querySelector(iconoId);

    if (btn && input && icono) {
            //alert("¡Botón encontrado!"); // <--- AÑADE ESTO
            btn.addEventListener('click', function () {
            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';

            if (isPassword) {
                icono.classList.replace('bi-eye', 'bi-eye-slash');
            } else {
                icono.classList.replace('bi-eye-slash', 'bi-eye');
            }
        });
    }
}

function validarCoincidencia() {
    if (confirm.value === "") {
        confirm.classList.remove('is-invalid', 'is-valid');
    } else if (password.value === confirm.value) {
        confirm.classList.remove('is-invalid');
        confirm.classList.add('is-valid');
    } else {
        confirm.classList.remove('is-valid');
        confirm.classList.add('is-invalid');
    }
}
