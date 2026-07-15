// --- FUNÇÕES DE INTERAÇÃO DO ODONTOGRAMA (RESPONSIVIDADE) ---
function initOdontograma() {
    const img = document.getElementById('odontograma-img');
    const svg = document.getElementById('odontograma-svg');
    const map = document.getElementById('image-map');
    const areas = map.getElementsByTagName('area');

    const setup = () => {
        // Configura o SVG para usar o sistema de coordenadas da imagem ORIGINAL
        svg.setAttribute('viewBox', `0 0 ${img.naturalWidth} ${img.naturalHeight}`);
        
        // Salva as coordenadas originais
        for (let area of areas) {
            area.dataset.originalCoords = area.coords;
        }
        
        // Inicia a biblioteca que ajusta os cliques responsivamente
        imageMapResize();
    };

    if (img.complete) { setup(); } else { img.onload = setup; }
}

function marcarDente(areaElement, dente, arcada) {
    const svg = document.getElementById('odontograma-svg');
    const rawCoords = areaElement.dataset.originalCoords;
    
    if (!rawCoords) {
        // Fallback caso não tenha carregado o dataset (erro raro)
        window.abrirModal(dente, arcada);
        return;
    }

    const coords = rawCoords.split(',').map(Number);
    const idMarca = `marca-${dente}`;
    const marcaExistente = document.getElementById(idMarca);
    
    // Remove marca anterior se existir (limpa antes de abrir modal ou alterna)
    if (marcaExistente) { 
        marcaExistente.remove(); 
    } else {
        const x = Math.min(coords[0], coords[2]);
        const y = Math.min(coords[1], coords[3]);
        const width = Math.abs(coords[2] - coords[0]);
        const height = Math.abs(coords[3] - coords[1]);

        const rect = document.createElementNS("http://www.w3.org/2000/svg", "rect");
        rect.setAttribute("x", x);
        rect.setAttribute("y", y);
        rect.setAttribute("width", width);
        rect.setAttribute("height", height);
        rect.setAttribute("fill", "rgba(255, 0, 0, 0.3)"); 
        rect.setAttribute("stroke", "red");                
        rect.setAttribute("stroke-width", "2");
        rect.id = idMarca;
        svg.appendChild(rect);
    }

    // Chama a função original de abrir o modal
    window.abrirModal(dente, arcada);
}
// -----------------------------------------------------------

function showToast(message, type = 'success') {
    const toast = document.getElementById('toast-notification');
    if (toast) {
        toast.textContent = message;
        toast.className = 'toast show ' + type; // Add type for styling
        setTimeout(() => {
            toast.className = toast.className.replace(' show', '');
        }, 5000); // Hide after 5 seconds
    }
}

$(document).ready(function() {
    // Inicializa o Odontograma Responsivo
    initOdontograma();

    const pacienteIdInput = $('#paciente_id');
    const pacienteBuscaInput = $('#paciente_busca');
    const btnLimparPaciente = $('#btn_limpar_paciente');

    // Inicializa o autocomplete no campo de busca
    pacienteBuscaInput.autocomplete({
        source: function(request, response) {
            $.ajax({
                url: "window.atendimentoConfig.baseUrlpacientes/buscar",
                dataType: "json",
                data: { term: request.term },
                success: function(data) {
                    if (!data.length) {
                        response([{ label: 'Nenhum paciente encontrado.', value: 'new' }]);
                    } else {
                        response($.map(data, function(item) {
                            return { label: `${item.nome} (${item.cpf || 'sem CPF'})`, value: item.id, patient: item };
                        }));
                    }
                }
            });
        },
        minLength: 2,
        select: function(event, ui) {
            if (ui.item.value === 'new') {
                // Novo paciente: mantém o nome digitado e trava o campo
                pacienteIdInput.val('');
                pacienteBuscaInput.prop('readonly', true);
                btnLimparPaciente.show();
                return false;
            } else {
                // Paciente existente: preenche os dados e carrega pendentes
                const patient = ui.item.patient;
                pacienteIdInput.val(patient.id);
                pacienteBuscaInput.val(ui.item.patient.nome).prop('readonly', true);
                btnLimparPaciente.show();
                carregarProcedimentosPendentes(patient.id);
            }
            return false;
        }
    });

    // Ação do botão de limpar
    btnLimparPaciente.on('click', function() {
        pacienteIdInput.val('');
        pacienteBuscaInput.val('').prop('readonly', false).show().focus();
        btnLimparPaciente.hide();
        $('#procedimentos_pendentes_container').empty();
        $('#procedimentos_adicionados_container').html('<h3>Procedimentos Adicionados</h3>');
        $('#odontograma-svg').empty(); // Limpa marcações visuais
    });
    
    const procedimentos = window.atendimentoConfig.procedimentos;

    function carregarProcedimentosPendentes(pacienteId) {
        const container = $('#procedimentos_pendentes_container');
        container.html('<h4>Carregando procedimentos pendentes...</h4>');

        $.ajax({
            url: "window.atendimentoConfig.baseUrlpacientes/pendentes",
            dataType: "json",
            data: { paciente_id: pacienteId },
            success: function(data) {
                container.empty();
                if (data && data.length > 0) {
                    container.append('<h3>Procedimentos Pendentes Anteriores</h3>');
                    data.forEach(proc => {
                        const procHtml = `
                            <div class="procedimento-pendente-item" id="pendente-${proc.atendimento_procedimento_id}" style="display: flex; justify-content: space-between; align-items: center; padding: 10px; border: 1px solid #ddd; margin-bottom: 5px; border-radius: 5px;">
                                <div>
                                    <strong>Procedimento:</strong> ${proc.procedimento_nome} (${proc.categoria})<br>
                                    <strong>Local:</strong> ${proc.local} | <strong>Descrição:</strong> ${proc.descricao || 'N/A'}
                                </div>
                                <button type="button" class="btn btn-success btn-sm finalizar-pendente-btn" 
                                        data-proc-id="${proc.id_procedimento}"
                                        data-proc-nome="${proc.procedimento_nome} (${proc.categoria})"
                                        data-proc-valor="${proc.valor_procedimento / proc.quantidade}"
                                        data-proc-local="${proc.local}"
                                        data-proc-custo-auxiliar="${proc.custo_auxiliar || 0}"
                                        data-proc-descricao="${proc.descricao}"
                                        data-original-id="${proc.atendimento_procedimento_id}"
                                        data-proc-categoria="${proc.categoria}"
                                        data-proc-natureza="${proc.natureza || ''}">Finalizar Agora</button>
                            </div>
                        `;
                        container.append(procHtml);
                    });
                }
            },
            error: () => container.html('<p class="error">Erro ao carregar pendências.</p>')
        });
    }

    $(document).on('click', '.finalizar-pendente-btn', function() {
        const btn = $(this);
        const originalId = btn.data('original-id');

        // Adiciona o procedimento à lista principal como 'finalizado'
        criarLinhaProcedimentoPrincipal(
            btn.data('proc-id'), 
            btn.data('proc-nome'), 
            1, 
            btn.data('proc-valor'),
            btn.data('proc-local'), 
            btn.data('proc-descricao'), 
            'finalizado',
            btn.data('proc-custo-auxiliar'),
            originalId, // Passa o originalId
            btn.data('proc-natureza'),
            btn.data('proc-categoria')
        );
        
        // Adiciona o ID original a um campo hidden para ser deletado no backend
        $('#procedimentos_a_deletar_container').append(
            `<input type="hidden" name="procedimentos_a_deletar[]" value="${originalId}" id="delete-${originalId}">`
        );

        // Remove o item da lista de pendentes na UI
        $(`#pendente-${originalId}`).remove();
    });

    // Odontogram Modal
    const modal = document.getElementById('modalTratamento');
    const modalTitle = document.getElementById('modal-title');
    const inputDente = document.getElementById('inputDente');
    const inputArcada = document.getElementById('inputArcada');
    const procedimentosModalContainer = document.getElementById('procedimentos-modal-container');
    const salvarTratamentoModalButton = document.getElementById('salvar-tratamento-modal');

    // Abre o modal
    window.abrirModal = function(numero, arcada) {
        modal.classList.add('show');
        if (arcada === 'Geral') {
            modalTitle.innerText = 'Tratamento geral';
        } else {
            modalTitle.innerText = arcada + ' - Dente ' + numero;
        }
        inputDente.value = numero;
        inputArcada.value = arcada;
        procedimentosModalContainer.innerHTML = '';
        adicionarLinhaProcedimentoModal();
        updateModalTotal(); 
    }

    // Fecha o modal
    window.fecharModal = function() {
        modal.classList.remove('show');
    }

    // Fecha o modal ao clicar fora
    window.onclick = function(event) {
        if (event.target == modal) {
            fecharModal();
        }
    }

    function adicionarLinhaProcedimentoModal() {
        const row = document.createElement('div');
        row.classList.add('procedimento-row');
        row.style.marginBottom = '15px';

        const select = document.createElement('select');
        select.name = 'procedimentos_modal[id][]';
        select.required = true;
        let option = document.createElement('option');
        option.value = '';
        option.textContent = 'Selecione...';
        select.appendChild(option);
        procedimentos.forEach(p => {
            let opt = document.createElement('option');
            opt.value = p.id;
            opt.textContent = `${p.nome} (${p.categoria})`;
            opt.dataset.valor = p.valor_base;
            opt.dataset.categoria = p.categoria;
            select.appendChild(opt);
        });

        const valorSpan = document.createElement('span');
        valorSpan.classList.add('procedimento-valor-span');
        valorSpan.style.marginLeft = '10px';
        valorSpan.style.fontWeight = 'bold';

        const naturezaContainer = document.createElement('div');
        naturezaContainer.style.display = 'none';
        naturezaContainer.style.marginTop = '5px';
        const naturezaLabel = document.createElement('label');
        naturezaLabel.textContent = 'Tipo de Procedimento Especializado';
        naturezaLabel.style.display = 'block';
        naturezaLabel.style.fontSize = '0.9em';
        const naturezaSelect = document.createElement('select');
        naturezaSelect.name = 'procedimentos_modal[natureza][]';
        const naturezas = {'': 'Selecione...', 'orto': 'Orto', 'canal': 'Canal', 'protese': 'Prótese', 'cirurgia_especializada': 'Cirurgia Especializada'};
        for (const key in naturezas) {
            let opt = document.createElement('option');
            opt.value = key;
            opt.textContent = naturezas[key];
            naturezaSelect.appendChild(opt);
        }
        naturezaContainer.appendChild(naturezaLabel);
        naturezaContainer.appendChild(naturezaSelect);

        const custoAuxiliarInput = document.createElement('input');
        custoAuxiliarInput.type = 'number';
        custoAuxiliarInput.name = 'procedimentos_modal[custo_auxiliar][]';
        custoAuxiliarInput.step = '0.01';
        custoAuxiliarInput.value = '250.00'; // Inicializa com 0
        custoAuxiliarInput.placeholder = 'Custo Auxiliar (R$)';
        custoAuxiliarInput.style.width = '100%';
        custoAuxiliarInput.style.marginTop = '5px';
        custoAuxiliarInput.style.display = 'none';

        select.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const valor = selectedOption.dataset.valor || 0;
            valorSpan.textContent = `R$ ${parseFloat(valor).toFixed(2)}`;

            // Reset all conditional fields first
            naturezaContainer.style.display = 'none';
            naturezaSelect.required = false;
            naturezaSelect.value = '';
            custoAuxiliarInput.style.display = 'none';
            custoAuxiliarInput.required = false;
            custoAuxiliarInput.value = '';

            if (selectedOption && selectedOption.dataset.categoria === 'especializado') {
                naturezaContainer.style.display = 'block';
                naturezaSelect.required = true;
            } else if (selectedOption && selectedOption.dataset.categoria === 'protese') {
                custoAuxiliarInput.style.display = 'block';
                custoAuxiliarInput.required = true;
            }
            updateModalTotal();
        });

        naturezaSelect.addEventListener('change', function() {
            if (this.value === 'protese') {
                custoAuxiliarInput.style.display = 'block';
                custoAuxiliarInput.required = true;
            } else {
                custoAuxiliarInput.style.display = 'none';
                custoAuxiliarInput.required = false;
                custoAuxiliarInput.value = '';
            }
        });

        const descricao = document.createElement('textarea');
        descricao.name = 'procedimentos_modal[descricao][]';
        descricao.placeholder = 'Descrição';
        descricao.rows = 2;
        descricao.style.width = '100%';
        descricao.style.marginTop = '5px';

        const finalizadoLabel = document.createElement('label');
        finalizadoLabel.style.display = 'flex';
        finalizadoLabel.style.alignItems = 'center';
        finalizadoLabel.style.marginTop = '5px';
        const finalizadoCheckbox = document.createElement('input');
        finalizadoCheckbox.type = 'checkbox';
        finalizadoCheckbox.name = 'procedimentos_modal[finalizado][]';
        finalizadoCheckbox.value = '1';
        finalizadoCheckbox.checked = true;
        finalizadoCheckbox.addEventListener('change', updateModalTotal);
        finalizadoLabel.appendChild(finalizadoCheckbox);
        finalizadoLabel.append(' Finalizado');

        const removeButton = document.createElement('button');
        removeButton.type = 'button';
        removeButton.textContent = 'Remover';
        removeButton.classList.add('btn', 'btn-danger', 'btn-sm');
        removeButton.style.marginLeft = '10px';
        removeButton.onclick = () => {
            row.remove();
            updateModalTotal();
        };
        
        const controls = document.createElement('div');
        controls.style.display = 'flex';
        controls.style.alignItems = 'center';
        
        const mainContent = document.createElement('div');
        mainContent.style.flexGrow = '1';
        mainContent.appendChild(select);
        mainContent.appendChild(valorSpan);
        mainContent.appendChild(naturezaContainer);
        mainContent.appendChild(custoAuxiliarInput);
        mainContent.appendChild(descricao);
        mainContent.appendChild(finalizadoLabel);
        
        controls.appendChild(mainContent);
        controls.appendChild(removeButton);
        row.appendChild(controls);

        procedimentosModalContainer.appendChild(row);
        updateModalTotal();
    }

    function updateModalTotal() {
        let total = 0;
        const rows = procedimentosModalContainer.querySelectorAll('.procedimento-row');
        rows.forEach(row => {
            const finalizadoCheckbox = row.querySelector('input[type="checkbox"]');
            if (finalizadoCheckbox && finalizadoCheckbox.checked) {
                const select = row.querySelector('select');
                const selectedOption = select.options[select.selectedIndex];
                if (selectedOption && selectedOption.dataset.valor) {
                    total += parseFloat(selectedOption.dataset.valor);
                }
            }
        });
        document.getElementById('modal-total-valor').textContent = `R$ ${total.toFixed(2)}`;
    }

    $('#add-procedimento-modal').on('click', adicionarLinhaProcedimentoModal);

    function criarLinhaProcedimentoPrincipal(procedimentoId, procedimentoNome, quantidade, valor, local, descricao, status_execucao, custoAuxiliar = 0, originalId = null, natureza = '', categoria = '') {
        const container = $('#procedimentos_adicionados_container');
        const uniqueId = 'proc-' + Date.now() + Math.random().toString(36).substr(2, 9);
        const canAlterarValor = window.atendimentoConfig.canAlterarValor;

        const isChecked = status_execucao === 'finalizado';
        const originalIdAttr = originalId ? `data-original-id="${originalId}"` : '';

        let alterarValorHtml = '';
        if (canAlterarValor) {
            alterarValorHtml = `
                <div class="valor-container" style="margin-top: 5px;">
                    <strong>Valor:</strong> 
                    <span class="valor-display">R$ ${parseFloat(valor).toFixed(2)}</span>
                    <button type="button" class="btn btn-info btn-sm alterar-valor-btn" style="margin-left: 10px; padding: 2px 10px; font-size: 12px;">Alterar Valor</button>
                    <div class="alterar-valor-form" style="display: none; margin-top: 5px;">
                        <input type="number" step="0.01" class="novo-valor-input" style="width: 100px;" value="${parseFloat(valor).toFixed(2)}">
                        <button type="button" class="btn btn-success btn-sm salvar-valor-btn" style="margin-left: 5px; padding: 2px 10px; font-size: 12px;">Salvar</button>
                    </div>
                </div>
            `;
        } else {
            alterarValorHtml = `<div class="valor-container" style="margin-top: 5px;"><strong>Valor:</strong> <span class="valor-display">R$ ${parseFloat(valor).toFixed(2)}</span></div>`;
        }

        const naturezaHtml = (categoria !== 'geral' && natureza) ? ` | <strong>Natureza:</strong> ${natureza.charAt(0).toUpperCase() + natureza.slice(1)}` : '';

        let alterarCustoHtml = '';
        if (canAlterarValor && custoAuxiliar > 0) {
            alterarCustoHtml = `
                <div class="custo-auxiliar-container" style="margin-top: 5px;">
                    <strong>Custo Auxiliar:</strong> 
                    <span class="custo-display">R$ ${parseFloat(custoAuxiliar).toFixed(2)}</span>
                    <button type="button" class="btn btn-warning btn-sm alterar-custo-btn" style="margin-left: 10px; padding: 2px 10px; font-size: 12px;">Alterar Custo</button>
                    <div class="alterar-custo-form" style="display: none; margin-top: 5px;">
                        <input type="number" step="0.01" class="novo-custo-input" style="width: 100px;" value="${parseFloat(custoAuxiliar).toFixed(2)}">
                        <button type="button" class="btn btn-success btn-sm salvar-custo-btn" style="margin-left: 5px; padding: 2px 10px; font-size: 12px;">Confirmar</button>
                    </div>
                </div>
            `;
        }

        const procHtml = `
            <div class="procedimento-row-principal" id="${uniqueId}" 
                 data-valor="${valor}" data-status_execucao="${status_execucao}"
                 data-proc-id="${procedimentoId}" data-proc-nome="${procedimentoNome}"
                 data-proc-local="${local}" data-proc-descricao="${descricao}"
                 data-custo-auxiliar="${custoAuxiliar}"
                 ${originalIdAttr}
                 data-natureza="${natureza}"
                 data-categoria="${categoria}"
                 style="display: flex; justify-content: space-between; align-items: center; padding: 10px; border: 1px solid #ddd; margin-bottom: 5px; border-radius: 5px;">
                
                <input type="hidden" name="procedimentos[id][]" value="${procedimentoId}">
                <input type="hidden" name="procedimentos[quantidade][]" value="${quantidade}">
                <input type="hidden" class="valor-input" name="procedimentos[valor][]" value="${valor}">
                <input type="hidden" name="procedimentos[local][]" value="${local}">
                <input type="hidden" name="procedimentos[descricao][]" value="${descricao}">
                <input type="hidden" name="procedimentos[custo_auxiliar][]" value="${custoAuxiliar}">
                <input type="hidden" class="status-execucao-input" name="procedimentos[status_execucao][]" value="${status_execucao}">
                <input type="hidden" name="procedimentos[natureza][]" value="${natureza}">

                <div>
                    <strong>Procedimento:</strong> ${procedimentoNome}<br>
                    <strong>Local:</strong> ${local} | <strong>Status:</strong> <span class="status-text">${status_execucao}</span>${naturezaHtml} <br>
                    <strong>Descrição:</strong> ${descricao || 'N/A'}
                    ${alterarCustoHtml || (custoAuxiliar > 0 ? `<br><strong>Custo Auxiliar:</strong> R$ ${parseFloat(custoAuxiliar).toFixed(2)}` : '')}
                    ${alterarValorHtml}
                </div>
                <div style="text-align: center;">
                    <label style="display: block; margin-bottom: 5px;">
                        <input type="checkbox" class="finalizado-checkbox" ${isChecked ? 'checked' : ''}>
                        Finalizado
                    </label>
                    <button type="button" class="btn btn-danger btn-sm remover-proc-principal">Remover</button>
                </div>
            </div>
        `;
        const procElement = $(procHtml);

        procElement.find('.remover-proc-principal').on('click', function() {
            removerProcedimento(uniqueId);
        });

        procElement.find('.finalizado-checkbox').on('change', function() {
            const isChecked = $(this).is(':checked');
            const newStatus = isChecked ? 'finalizado' : 'pendente';
            const parentRow = $(this).closest('.procedimento-row-principal');
            
            parentRow.attr('data-status_execucao', newStatus);
            parentRow.find('.status-execucao-input').val(newStatus);
            parentRow.find('.status-text').text(newStatus);

            updateTotalAPagar();
        });

        // Lógica para alterar valor
        procElement.find('.alterar-valor-btn').on('click', function() {
            $(this).hide();
            procElement.find('.valor-display').hide();
            procElement.find('.alterar-valor-form').show();
            procElement.find('.novo-valor-input').val(procElement.attr('data-valor')).focus();
        });

        procElement.find('.salvar-valor-btn').on('click', function() {
            const novoValor = procElement.find('.novo-valor-input').val();
            if (novoValor && !isNaN(novoValor) && parseFloat(novoValor) >= 0) {
                const novoValorFloat = parseFloat(novoValor);
                procElement.attr('data-valor', novoValorFloat);
                procElement.find('.valor-input').val(novoValorFloat);
                procElement.find('.valor-display').text(`R$ ${novoValorFloat.toFixed(2)}`);
                updateTotalAPagar();
            }
            procElement.find('.alterar-valor-form').hide();
            procElement.find('.alterar-valor-btn').show();
            procElement.find('.valor-display').show();
        });

        // Lógica para alterar custo auxiliar
        procElement.find('.alterar-custo-btn').on('click', function() {
            $(this).hide();
            procElement.find('.custo-display').hide();
            procElement.find('.alterar-custo-form').show();
            procElement.find('.novo-custo-input').focus();
        });

        procElement.find('.salvar-custo-btn').on('click', function() {
            const novoCusto = procElement.find('.novo-custo-input').val();
            if (novoCusto && !isNaN(novoCusto) && parseFloat(novoCusto) >= 0) {
                const novoCustoFloat = parseFloat(novoCusto);
                procElement.attr('data-custo-auxiliar', novoCustoFloat);
                procElement.find('input[name="procedimentos[custo_auxiliar][]"]').val(novoCustoFloat);
                procElement.find('.custo-display').text(`R$ ${novoCustoFloat.toFixed(2)}`);
            }
            procElement.find('.alterar-custo-form').hide();
            procElement.find('.alterar-custo-btn').show();
            procElement.find('.custo-display').show();
        });

        container.append(procElement);
        updateTotalAPagar();
    }

    function updateTotalAPagar() {
        let total = 0;
        $('.procedimento-row-principal').each(function() {
            const row = $(this);
            if (row.attr('data-status_execucao') === 'finalizado') {
                total += parseFloat(row.attr('data-valor'));
            }
        });
        $('#total-procedimentos-valor').text(`R$ ${total.toFixed(2)}`);
    }

    function removerProcedimento(elementId) {
        const procElement = $('#' + elementId);
        if (!procElement.length) return;

        const originalId = procElement.data('original-id');

        // Se tem ID original, significa que veio da lista de pendentes
        if (originalId) {
            const container = $('#procedimentos_pendentes_container');
            if (container.find(`#pendente-${originalId}`).length === 0) {
                // Remonta o HTML do item pendente
                 const procHtml = `
                    <div class="procedimento-pendente-item" id="pendente-${originalId}" style="display: flex; justify-content: space-between; align-items: center; padding: 10px; border: 1px solid #ddd; margin-bottom: 5px; border-radius: 5px;">
                        <div>
                            <strong>Procedimento:</strong> ${procElement.data('proc-nome')}<br>
                            <strong>Local:</strong> ${procElement.data('proc-local')} | <strong>Descrição:</strong> ${procElement.data('proc-descricao') || 'N/A'}
                        </div>
                        <button type="button" class="btn btn-success btn-sm finalizar-pendente-btn" 
                                data-proc-id="${procElement.data('proc-id')}"
                                data-proc-nome="${procElement.data('proc-nome')}"
                                data-proc-valor="${procElement.data('valor')}"
                                data-proc-local="${procElement.data('proc-local')}"
                                data-proc-descricao="${procElement.data('proc-descricao')}"
                                data-proc-custo-auxiliar="${procElement.data('custo-auxiliar') || 0}"
                                data-original-id="${originalId}"
                                data-proc-categoria="${procElement.data('categoria')}"
                                data-proc-natureza="${procElement.data('natureza')}">Finalizar Agora</button>
                    </div>
                `;
                container.append(procHtml);
            }
            // Remove the input that marked for backend deletion
            $(`#delete-${originalId}`).remove();
        }

        procElement.remove();
        updateTotalAPagar();
    }

    salvarTratamentoModalButton.addEventListener('click', function() {
        const dente = inputDente.value;
        const arcada = inputArcada.value;
        const local = (arcada === 'Geral') ? 'Todos' : dente;

        let formValido = true;
        const rowsParaValidar = procedimentosModalContainer.querySelectorAll('.procedimento-row');
        rowsParaValidar.forEach(row => {
            const select = row.querySelector('select[name="procedimentos_modal[id][]"]');
            const selectedOption = select.options[select.selectedIndex];
            if (!selectedOption || !selectedOption.value) return;

            const categoria = selectedOption.dataset.categoria;
            const naturezaSelect = row.querySelector('select[name="procedimentos_modal[natureza][]"]');
            const custoAuxiliarInput = row.querySelector('input[name="procedimentos_modal[custo_auxiliar][]"]');
            
            // Reseta a borda para o padrão
            if (naturezaSelect) naturezaSelect.style.border = '1px solid #ccc';
            if (custoAuxiliarInput) custoAuxiliarInput.style.border = '1px solid #ccc';

            if (categoria === 'especializado' && (!naturezaSelect || !naturezaSelect.value)) {
                formValido = false;
                naturezaSelect.style.border = '2px solid red';
            }

            // Validação para custo auxiliar de prótese
            const isProtese = (categoria === 'protese') || (categoria === 'especializado' && naturezaSelect && naturezaSelect.value === 'protese');
            if (isProtese && (!custoAuxiliarInput || !custoAuxiliarInput.value || parseFloat(custoAuxiliarInput.value) <= 0)) {
                formValido = false;
                if (custoAuxiliarInput) {
                    custoAuxiliarInput.style.border = '2px solid red';
                }
            }
        });

        if (!formValido) {
            showToast('Verifique os campos obrigatórios (Natureza para especializados, Custo para próteses).', 'error');
            return;
        }

        const rows = procedimentosModalContainer.querySelectorAll('.procedimento-row');
        rows.forEach(row => {
            const select = row.querySelector('select');
            const selectedOption = select.options[select.selectedIndex];
            if (!selectedOption.value) return;

            const descricao = row.querySelector('textarea').value;
            const naturezaSelect = row.querySelector('select[name="procedimentos_modal[natureza][]"]');
            const natureza = naturezaSelect ? naturezaSelect.value : '';
            let custoAuxiliar = row.querySelector('input[name="procedimentos_modal[custo_auxiliar][]"]').value || 0;
            const finalizado = row.querySelector('input[type="checkbox"]').checked;
            const status_execucao = finalizado ? 'finalizado' : 'pendente';
            const procedimentoId = selectedOption.value;
            const procedimentoNome = selectedOption.textContent;
            const valor = selectedOption.dataset.valor;
            const categoria = selectedOption.dataset.categoria;
            if (categoria === 'especializado' && (natureza === 'canal' || natureza === 'cirurgia_especializada')) {
                custoAuxiliar = parseFloat(valor) * 0.50;
            }

            criarLinhaProcedimentoPrincipal(procedimentoId, procedimentoNome, 1, valor, local, descricao, status_execucao, custoAuxiliar, null, natureza, categoria);
        });

        fecharModal();
        updateTotalAPagar();
    });

    const form = document.getElementById('form-atendimento');
    form.addEventListener('submit', async function(event) {
        event.preventDefault(); 
        
        const submitButton = form.querySelector('button[type="submit"]');
        const pacienteId = pacienteIdInput.val();
        const pacienteNome = pacienteBuscaInput.val();

        if (!pacienteId && !pacienteNome) {
            showToast('É necessário selecionar ou cadastrar um paciente.', 'error');
            return;
        }

        // 1. Verificar pagamento pendente
        if (pacienteId) {
            try {
                const response = await fetch(`window.atendimentoConfig.baseUrlatendimentos/verificar-pagamento?paciente_id=${pacienteId}`);
                const data = await response.json();

                if (data.pendente) {
                    alert(`O(A) paciente ${pacienteNome} tem um pagamento pendente de outro procedimento. Necessário finalizar o anterior para Lançar Novos.`);
                    return; // Bloqueia o envio
                }
            } catch (error) {
                console.error('Erro ao verificar pagamento pendente:', error);
                showToast('Não foi possível verificar pagamentos pendentes. Verifique o console.', 'error');
                return; // Bloqueia por segurança
            }
        }
       
        // 2. Prosseguir com o envio do formulário
        submitButton.disabled = true;
        submitButton.textContent = 'Salvando...';

        const formData = new FormData(form);

        try {
            const response = await fetch('window.atendimentoConfig.baseUrlatendimentos/salvar', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.sucesso) {
                showToast(result.mensagem, 'success');
                setTimeout(() => {
                    window.location.href = result.redirectUrl || 'window.atendimentoConfig.baseUrlindex.php';
                }, 1500);
            } else {
                showToast(result.erro || 'Ocorreu um erro desconhecido.', 'error');
                submitButton.disabled = false;
                submitButton.textContent = 'Lançar Atendimento';
            }
        } catch (error) {
            console.error('Fetch Error:', error);
            showToast('Ocorreu um erro de comunicação. Verifique o console para detalhes.', 'error');
            submitButton.disabled = false;
            submitButton.textContent = 'Lançar Atendimento';
        }
    });
});
