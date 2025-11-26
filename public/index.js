// Manejo del formulario de consulta
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('consultaForm');
    const loadingOverlay = document.getElementById('loading-overlay');
    const confirmationModal = document.getElementById('confirmation-modal');
    const privacyCheck = document.getElementById('privacy-check');
    const confirmBtn = document.querySelector('.confirm-btn');
    
    // Habilitar/deshabilitar botón de confirmación según checkbox
    if (privacyCheck) {
        privacyCheck.addEventListener('change', function() {
            if (confirmBtn) {
                confirmBtn.disabled = !this.checked;
            }
        });
    }
    
    // Manejar envío del formulario
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const documentType = document.getElementById('documentType').value;
            const documentNumber = document.getElementById('documentNumber').value;
            
            if (!documentNumber || documentNumber.trim() === '') {
                alert('Por favor, ingresa un número de documento');
                return;
            }
            
            // Mostrar modal de confirmación
            document.getElementById('modal-doc-type').textContent = getDocumentTypeName(documentType);
            document.getElementById('modal-doc-number').textContent = documentNumber;
            confirmationModal.style.display = 'flex';
        });
    }
});

// Función para obtener el nombre del tipo de documento
function getDocumentTypeName(type) {
    const types = {
        'CC': 'Cédula de Ciudadanía',
        'CE': 'Cédula de Extranjería',
        'PA': 'Pasaporte',
        'NIT': 'NIT'
    };
    return types[type] || type;
}

// Función para cerrar el modal de confirmación
function closeConfirmationModal() {
    const confirmationModal = document.getElementById('confirmation-modal');
    if (confirmationModal) {
        confirmationModal.style.display = 'none';
    }
    // Resetear checkbox
    const privacyCheck = document.getElementById('privacy-check');
    if (privacyCheck) {
        privacyCheck.checked = false;
    }
    const confirmBtn = document.querySelector('.confirm-btn');
    if (confirmBtn) {
        confirmBtn.disabled = true;
    }
}

// Función para enviar la consulta
function submitConsulta() {
    const form = document.getElementById('consultaForm');
    const loadingOverlay = document.getElementById('loading-overlay');
    const confirmationModal = document.getElementById('confirmation-modal');
    
    if (!form) return;
    
    const documentType = document.getElementById('documentType').value;
    const documentNumber = document.getElementById('documentNumber').value;
    
    // Cerrar modal de confirmación
    if (confirmationModal) {
        confirmationModal.style.display = 'none';
    }
    
    // Mostrar overlay de carga
    if (loadingOverlay) {
        loadingOverlay.style.display = 'flex';
    }
    
    // Realizar petición AJAX a la ruta de consulta
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                      document.getElementById('csrf_token')?.value || 
                      document.querySelector('input[name="_token"]')?.value;
    
    fetch('/consulta', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            tipo_documento: documentType,
            numero_documento: documentNumber
        })
    })
    .then(response => {
        if (response.ok) {
            return response.json();
        } else if (response.status === 404) {
            return response.json().then(data => {
                throw new Error(data.message || 'No se encontraron registros para el documento ingresado.');
            });
        } else {
            throw new Error('Error en la consulta');
        }
    })
    .then(data => {
        if (loadingOverlay) {
            loadingOverlay.style.display = 'none';
        }
        
        // Manejar la respuesta
        if (data.success && data.redirect) {
            window.location.href = data.redirect;
        } else if (data.redirect) {
            window.location.href = data.redirect;
        } else if (data.url) {
            window.location.href = data.url;
        } else {
            alert('No se encontraron resultados para el documento ingresado.');
        }
    })
    .catch(error => {
        if (loadingOverlay) {
            loadingOverlay.style.display = 'none';
        }
        console.error('Error:', error);
        alert(error.message || 'Ocurrió un error al realizar la consulta. Por favor, intenta nuevamente.');
    });
}
