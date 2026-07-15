function openTab(evt, tabName) {
    var i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName("tab-content");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }
    tablinks = document.getElementsByClassName("tab-link");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }
    document.getElementById(tabName).style.display = "block";
    evt.currentTarget.className += " active";
}

function editTaxa(data) {
    document.getElementById('taxa_id').value = data.id;
    document.getElementById('taxa_bandeira').value = data.bandeira;
    document.getElementById('taxa_modalidade').value = data.modalidade;
    document.getElementById('taxa_parcelas').value = data.parcelas;
    document.getElementById('taxa_percentual').value = data.taxa_percentual;
    
    window.scrollTo({ top: document.getElementById('form-taxa').offsetTop - 100, behavior: 'smooth' });
}

function resetTaxaForm() {
    document.getElementById('form-taxa').reset();
    document.getElementById('taxa_id').value = '';
}

function toggleEdit(tabId) {
    const container = document.getElementById(tabId);
    const inputs = container.querySelectorAll('input:not([type="hidden"]), select, textarea');
    const buttons = container.querySelectorAll('button');
    
    let editBtn, saveBtn, cancelBtn;
    buttons.forEach(btn => {
        if (btn.innerText === 'Editar') editBtn = btn;
        if (btn.type === 'submit') saveBtn = btn;
        if (btn.innerText === 'Cancelar') cancelBtn = btn;
    });

    const isLocked = inputs[0].hasAttribute('readonly') || inputs[0].hasAttribute('disabled');

    if (isLocked) {
        // Unlock
        inputs.forEach(input => {
            input.removeAttribute('readonly');
            input.removeAttribute('disabled');
            input.classList.remove('readonly-field');
            
            // Forçar disparar a máscara ao abrir para garantir formatação correta
            if (input.name === 'cnpj') mascaraCNPJ(input);
            if (input.name === 'clinica_telefone') mascaraTelefone(input);
        });
        editBtn.style.display = 'none';
        saveBtn.style.display = 'block';
        cancelBtn.style.display = 'block';
    } else {
        // Lock (Reload to reset changes)
        location.reload();
    }
}

function mascaraCNPJ(i) {
    let v = i.value.replace(/\D/g, ''); // Remove tudo que não é dígito
    if (v.length > 14) v = v.substring(0, 14); // Limita a 14 dígitos

    // Aplica a máscara progressivamente
    v = v.replace(/^(\d{2})(\d)/, '$1.$2');
    v = v.replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3');
    v = v.replace(/\.(\d{3})(\d)/, '.$1/$2');
    v = v.replace(/(\d{4})(\d)/, '$1-$2');

    i.value = v;
}

function mascaraTelefone(i) {
    let v = i.value.replace(/\D/g, '');
    if (v.length > 11) v = v.substring(0, 11); // Limita a 11 dígitos (celular com DDD)

    if (v.length > 10) {
        v = v.replace(/^(\d{2})(\d{5})(\d{4}).*/, '($1) $2-$3');
    } else if (v.length > 5) {
        v = v.replace(/^(\d{2})(\d{4})(\d{0,4}).*/, '($1) $2-$3');
    } else if (v.length > 2) {
        v = v.replace(/^(\d{2})(\d{0,5})/, '($1) $2');
    } else if (v.length > 0) {
        v = v.replace(/^(\d*)/, '($1');
    }

    i.value = v;
}
