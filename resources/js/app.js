import Toastify from 'toastify-js';
import 'toastify-js/src/toastify.css';
import '@fortawesome/fontawesome-free/css/all.min.css';
import Alpine from 'alpinejs';

window.Toastify = Toastify;
window.Alpine = Alpine;
Alpine.start();

window.formatCurrency = function(val) {
    if (!val) return '';
    let clean = val.replace(/[^0-9.]/g, '');
    let parts = clean.split('.');
    if (parts.length > 2) {
        clean = parts[0] + '.' + parts.slice(1).join('');
        parts = clean.split('.');
    }
    if (parts[1] !== undefined) {
        parts[1] = parts[1].substring(0, 2);
    }
    parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    return parts.join('.');
};

window.formatOnBlur = function(val) {
    if (!val) return '';
    let clean = val.replace(/[^0-9.]/g, '');
    let num = parseFloat(clean);
    if (isNaN(num)) return '';
    return num.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
};

window.initCurrencyInput = function(displaySelector, rawSelector) {
    const displayInput = document.querySelector(displaySelector);
    const rawInput = document.querySelector(rawSelector);
    if (!displayInput || !rawInput) return;

    if (rawInput.value) {
        displayInput.value = window.formatOnBlur(rawInput.value);
    }

    displayInput.addEventListener('input', function() {
        let val = displayInput.value;
        let formatted = window.formatCurrency(val);
        displayInput.value = formatted;
        
        let rawVal = formatted.replace(/,/g, '');
        rawInput.value = rawVal;
    });

    displayInput.addEventListener('blur', function() {
        let val = displayInput.value;
        let formatted = window.formatOnBlur(val);
        displayInput.value = formatted;
        
        let rawVal = formatted.replace(/,/g, '');
        rawInput.value = rawVal;
    });
};

window.showImagePopup = function(src) {
    if (!src) return;
    
    let modal = document.getElementById('global-image-modal');
    if (!modal) {
        modal = document.createElement('div');
        modal.id = 'global-image-modal';
        modal.className = 'fixed inset-0 z-50 flex items-center justify-center bg-black/75 transition-opacity duration-300 opacity-0 pointer-events-none';
        modal.innerHTML = `
            <div class="relative max-w-4xl max-h-[90vh] mx-4 overflow-hidden rounded-xl bg-white shadow-2xl transition-transform duration-300 scale-95">
                <button type="button" class="absolute top-3 right-3 text-gray-500 hover:text-gray-800 bg-white/80 hover:bg-white rounded-full p-1.5 shadow-sm transition-colors duration-150 focus:outline-none flex items-center justify-center w-8 h-8" onclick="window.closeImagePopup()">
                    <i class="fa-solid fa-xmark text-base"></i>
                </button>
                <div class="p-2">
                    <img id="global-image-modal-img" src="" class="max-w-full max-h-[80vh] object-contain rounded-lg">
                </div>
            </div>
        `;
        document.body.appendChild(modal);
        
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                window.closeImagePopup();
            }
        });
    }
    
    const img = document.getElementById('global-image-modal-img');
    img.src = src;
    
    // Show modal
    modal.classList.remove('pointer-events-none', 'opacity-0');
    modal.classList.add('opacity-100');
    setTimeout(() => {
        modal.querySelector('.relative').classList.remove('scale-95');
        modal.querySelector('.relative').classList.add('scale-100');
    }, 10);
};

window.closeImagePopup = function() {
    const modal = document.getElementById('global-image-modal');
    if (modal) {
        modal.querySelector('.relative').classList.remove('scale-100');
        modal.querySelector('.relative').classList.add('scale-95');
        setTimeout(() => {
            modal.classList.remove('opacity-100');
            modal.classList.add('opacity-0', 'pointer-events-none');
        }, 150);
    }
};