let parkingsList = [];
let voituresList = [];

const confirmed = {
    parking: false,
    immatriculation: false,
};

document.addEventListener('DOMContentLoaded', function () {
    const parkingInput = document.querySelector('input[name="parking"]');
    const immatriculationInput = document.querySelector('input[name="immatriculation"]');
    const submitBtn = document.querySelector('button[type="submit"]');
    const form = document.querySelector('form');

    updateSubmitButton(submitBtn);

    if (parkingInput) {
        initAutocomplete(parkingInput, () => parkingsList, 'parking-dropdown', 'parking', submitBtn);
    }

    if (immatriculationInput) {
        initAutocomplete(immatriculationInput, () => voituresList, 'immat-dropdown', 'immatriculation', submitBtn);
    }

    if (form) {
        form.addEventListener('submit', function (e) {
            if (!confirmed.parking || !confirmed.immatriculation) {
                e.preventDefault();
            }
        });
    }

    fetchParkings();
    fetchUserVehicles();
});

function updateSubmitButton(btn) {
    if (!btn) return;
    const allConfirmed = confirmed.parking && confirmed.immatriculation;
    btn.disabled = !allConfirmed;
    btn.style.opacity = allConfirmed ? '1' : '0.4';
    btn.style.cursor = allConfirmed ? 'pointer' : 'not-allowed';
}

function initAutocomplete(input, getList, dropdownId, fieldName, submitBtn) {
    const wrapper = document.createElement('div');
    wrapper.style.cssText = 'position:relative; display:inline-block; flex:1; min-width:0;';
    input.parentNode.insertBefore(wrapper, input);
    wrapper.appendChild(input);
    input.style.width = '100%';

    // Dropdown monté dans le body pour éviter overflow:hidden des parents
    const dropdown = document.createElement('ul');
    dropdown.id = dropdownId;
    dropdown.style.cssText = `
        position: fixed;
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0,0,0,0.12);
        z-index: 9999;
        max-height: 114px;
        overflow-y: auto;
        padding: 4px 0;
        margin: 0;
        list-style: none;
        display: none;
    `;
    document.body.appendChild(dropdown);

    // Repositionner au-dessus de l'input
    function positionDropdown() {
        const rect = input.getBoundingClientRect();
        dropdown.style.left  = rect.left + 'px';
        dropdown.style.width = rect.width + 'px';
        // On place d'abord visible pour mesurer la hauteur
        dropdown.style.visibility = 'hidden';
        dropdown.style.display = 'block';
        const dropH = dropdown.offsetHeight;
        dropdown.style.top = (rect.top - dropH - 6) + 'px';
        dropdown.style.visibility = 'visible';
    }

    // Recalculer la position au scroll et au resize
    window.addEventListener('scroll', () => {
        if (dropdown.style.display !== 'none') positionDropdown();
    }, true);
    window.addEventListener('resize', () => {
        if (dropdown.style.display !== 'none') positionDropdown();
    });

    let selectedValue = '';
    let justSelected = false;
    let isConfirmed = false;

    function confirm(value) {
        isConfirmed = true;
        selectedValue = value;
        confirmed[fieldName] = true;
        updateSubmitButton(submitBtn);
    }

    function unconfirm() {
        isConfirmed = false;
        selectedValue = '';
        confirmed[fieldName] = false;
        updateSubmitButton(submitBtn);
    }

    input.addEventListener('keydown', function (e) {
        if (isConfirmed && e.key.length === 1) {
            input.value = '';
            unconfirm();
        }

        if (isConfirmed && (e.key === 'Backspace' || e.key === 'Delete')) {
            e.preventDefault();
            input.value = '';
            unconfirm();
            renderDropdown(getList());
            return;
        }

        const items = dropdown.querySelectorAll('li');
        const active = dropdown.querySelector('li.active');
        let index = Array.from(items).indexOf(active);

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            index = Math.min(index + 1, items.length - 1);
            setActive(items, index);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            index = Math.max(index - 1, 0);
            setActive(items, index);
        } else if (e.key === 'Enter' && active) {
            e.preventDefault();
            justSelected = true;
            input.value = active.textContent;
            confirm(active.textContent);
            hideDropdown();
        } else if (e.key === 'Escape') {
            input.value = '';
            unconfirm();
            hideDropdown();
        }
    });

    input.addEventListener('input', function () {
        unconfirm();
        const query = input.value.trim().toLowerCase();
        const filtered = query
            ? getList().filter(item => item.toLowerCase().includes(query))
            : getList();
        renderDropdown(filtered);
    });

    input.addEventListener('focus', function () {
        if (isConfirmed) return;
        const query = input.value.trim().toLowerCase();
        const filtered = query
            ? getList().filter(item => item.toLowerCase().includes(query))
            : getList();
        renderDropdown(filtered);
    });

    input.addEventListener('blur', function () {
        setTimeout(() => {
            if (!justSelected && input.value.trim() !== selectedValue) {
                input.value = '';
                unconfirm();
            }
            hideDropdown();
            justSelected = false;
        }, 150);
    });

    function renderDropdown(items) {
        dropdown.innerHTML = '';
        if (items.length === 0) { hideDropdown(); return; }

        items.forEach(item => {
            const li = document.createElement('li');
            li.textContent = item;
            li.style.cssText = `
                padding: 9px 16px;
                cursor: pointer;
                font-size: 14px;
                color: #374151;
                border-radius: 8px;
                margin: 2px 4px;
                transition: background 0.15s;
            `;
            li.addEventListener('mouseenter', () => li.style.background = '#f3f4f6');
            li.addEventListener('mouseleave', () => li.style.background = 'transparent');
            li.addEventListener('mousedown', function (e) {
                e.preventDefault();
                justSelected = true;
                input.value = item;
                confirm(item);
                hideDropdown();
            });
            dropdown.appendChild(li);
        });

        positionDropdown();
    }

    function hideDropdown() {
        dropdown.style.display = 'none';
    }

    document.addEventListener('click', function (e) {
        if (!wrapper.contains(e.target) && !dropdown.contains(e.target)) {
            hideDropdown();
        }
    });

    function setActive(items, index) {
        items.forEach(li => { li.classList.remove('active'); li.style.background = 'transparent'; });
        if (items[index]) {
            items[index].classList.add('active');
            items[index].style.background = '#f3f4f6';
            items[index].scrollIntoView({ block: 'nearest' });
        }
    }
}

function fetchParkings() {
    fetch('/api/parkings', {
        credentials: 'same-origin',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
            'Accept': 'application/json'
        }
    })
        .then(r => r.json())
        .then(parkings => { parkingsList = parkings.map(p => p.lib_parking); })
        .catch(error => console.error('Erreur chargement parkings:', error));
}

function fetchUserVehicles() {
    fetch('/api/user/voitures', {
        credentials: 'same-origin',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
            'Accept': 'application/json'
        }
    })
        .then(r => r.json())
        .then(voitures => { voituresList = voitures.map(v => v.immatriculation); })
        .catch(error => console.error('Erreur chargement voitures:', error));
}