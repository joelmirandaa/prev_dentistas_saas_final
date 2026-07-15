<?php
// app/Views/atendimentos/cadastrar.php

// As variáveis $dentistas e $procedimentos já estão disponíveis, injetadas pelo Controller.
?>

<div id="toast-notification" class="toast"></div>

<div class="card">
    <h2>Novo Lançamento de Atendimento</h2>
    <form id="form-atendimento" action="<?= BASE_URL ?>atendimentos/salvar" method="POST" enctype="multipart/form-data">
        <?= \App\Helpers\CsrfHelper::input() ?>
        
        <fieldset>
            <legend>Dados do Paciente</legend>
            <input type="hidden" name="paciente_id" id="paciente_id">
            
            <div class="form-group">
                <label for="paciente_busca">Buscar Paciente</label>
                <div style="display: flex;">
                    <input type="text" id="paciente_busca" name="paciente_nome" placeholder="Digite o nome para buscar ou cadastrar..." autocomplete="off" style="flex-grow: 1;">
                    <button type="button" class = "btn btn-danger" id="btn_limpar_paciente">Limpar Seleção</button>
                </div>
            </div>
        </fieldset>

        <fieldset>
            <legend>Dados do Atendimento</legend>
            <div class="form-group">
                <label for="dentista">Dentista Responsável</label>
                <select name="id_dentista" id="dentista" required>
                    <option value="">Selecione...</option>
                    <?php foreach($dentistas as $d): ?>
                        <option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['nome']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="card">
                <h2>Odontograma do Paciente</h2>
                <p>Selecione um dente para registrar o tratamento:</p>

                <div class="canvas-container">
                    <img src="../assets/img/odontograma.png" usemap="#image-map" class="img-odontograma" id="odontograma-img">
                    
                    <svg class="odontograma-overlay" id="odontograma-svg"></svg>
                    
                    <map name="image-map" id="image-map">
                        <area target="" onclick="marcarDente(this, 18, 'Arcada Superior')" alt="Molar 18" id="d18" title="3º Molar" coords="53,251,99,157" shape="rect">
                        <area target="" onclick="marcarDente(this, 17, 'Arcada Superior')" alt="Molar 17" id="d17" title="2º Molar" coords="147,156,103,249" shape="rect">
                        <area target="" onclick="marcarDente(this, 16, 'Arcada Superior')" alt="Molar 16" title="Molar 16" coords="207,155,151,246" shape="rect">
                        <area target="" onclick="marcarDente(this, 15, 'Arcada Superior')" alt="Premolar 15" title="Premolar 15" coords="241,152,209,242" shape="rect">
                        <area target="" onclick="marcarDente(this, 14, 'Arcada Superior')" alt="Premolar 14" title="Premolar 14" coords="274,149,246,241" shape="rect">
                        <area target="" onclick="marcarDente(this, 13, 'Arcada Superior')" alt="Canino 13" title="Canino 13" coords="314,148,277,238" shape="rect">
                        <area target="" onclick="marcarDente(this, 12, 'Arcada Superior')" alt="Inciso 12" title="Inciso 12" coords="352,152,317,243" shape="rect">
                        <area target="" onclick="marcarDente(this, 11, 'Arcada Superior')" alt="Inciso 11" title="Inciso 11" coords="397,153,355,246" shape="rect">
                        <area target="" onclick="marcarDente(this, 21, 'Arcada Superior')" alt="Inciso 21" title="Inciso 21" coords="442,154,403,244" shape="rect">
                        <area target="" onclick="marcarDente(this, 22, 'Arcada Superior')" alt="Inciso 22" title="Inciso 22" coords="479,153,446,243" shape="rect">
                        <area target="" onclick="marcarDente(this, 23, 'Arcada Superior')" alt="Canino 23" title="Canino 23" coords="521,142,481,243" shape="rect">
                        <area target="" onclick="marcarDente(this, 24, 'Arcada Superior')" alt="Premolar 24" title="Premolar 24" coords="561,146,525,239" shape="rect">
                        <area target="" onclick="marcarDente(this, 25, 'Arcada Superior')" alt="Premolar 25" title="Premolar 25" coords="590,146,564,237" shape="rect">
                        <area target="" onclick="marcarDente(this, 26, 'Arcada Superior')" alt="Molar 26" title="Molar 26" coords="648,148,593,238" shape="rect">
                        <area target="" onclick="marcarDente(this, 27, 'Arcada Superior')" alt="Molar 27" title="Molar 27" coords="703,151,653,239" shape="rect">
                        <area target="" onclick="marcarDente(this, 28, 'Arcada Superior')" alt="Molar 28" id="d28" title="3º Molar" coords="741,149,705,241" shape="rect">

                        <area target="" onclick="marcarDente(this, 48, 'Arcada Inferior')" alt="Molar 48" id="d48" title="Molar 48" coords="51,285,103,360" shape="rect">
                        <area target="" onclick="marcarDente(this, 47, 'Arcada Inferior')" alt="Molar 47" title="Molar 47" coords="109,284,160,363" shape="rect">
                        <area target="" onclick="marcarDente(this, 46, 'Arcada Inferior')" alt="Molar 46" title="Molar 46" coords="167,281,219,363" shape="rect">
                        <area target="" onclick="marcarDente(this, 45, 'Arcada Inferior')" alt="Premolar 45" title="Premolar 45" coords="221,278,258,378" shape="rect">
                        <area target="" onclick="marcarDente(this, 44, 'Arcada Inferior')" alt="Premolar 44" title="Premolar 44" coords="260,275,296,390" shape="rect">
                        <area target="" onclick="marcarDente(this, 43, 'Arcada Inferior')" alt="Canino 43" title="Canino 43" coords="298,276,336,384" shape="rect">
                        <area target="" onclick="marcarDente(this, 42, 'Arcada Inferior')" alt="Inciso 42" title="Inciso 42" coords="338,275,368,384" shape="rect">
                        <area target="" onclick="marcarDente(this, 41, 'Arcada Inferior')" alt="Inciso 41" title="Inciso 41" coords="370,276,395,383" shape="rect">
                        <area target="" onclick="marcarDente(this, 31, 'Arcada Inferior')" alt="Inciso 31" title="Inciso 31" coords="398,275,426,380" shape="rect">
                        <area target="" onclick="marcarDente(this, 32, 'Arcada Inferior')" alt="Inciso 32" title="Inciso 32" coords="428,275,454,382" shape="rect">
                        <area target="" onclick="marcarDente(this, 33, 'Arcada Inferior')" alt="Canino 33" title="Canino 33" coords="456,274,493,391" shape="rect">
                        <area target="" onclick="marcarDente(this, 34, 'Arcada Inferior')" alt="Premolar 34" title="Premolar 34" coords="496,274,531,383" shape="rect">
                        <area target="" onclick="marcarDente(this, 35, 'Arcada Inferior')" alt="Premolar 35" title="Premolar 35" coords="534,274,571,379" shape="rect">
                        <area target="" onclick="marcarDente(this, 36, 'Arcada Inferior')" alt="Molar 36" title="Molar 36" coords="575,274,636,384" shape="rect">
                        <area target="" onclick="marcarDente(this, 37, 'Arcada Inferior')" alt="Molar 37" title="Molar 37" coords="640,274,688,384" shape="rect">
                        <area target="" onclick="marcarDente(this, 38, 'Arcada Inferior')" alt="Molar 38" title="Molar 38" coords="694,272,742,375" shape="rect">

                        <area target="" onclick="marcarDente(this, 'Todos', 'Geral')" alt="Todos" title="Todos" coords="85,31,727,83" shape="rect">
                        <area target="" onclick="marcarDente(this, 'Todos', 'Geral')" alt="Todos" title="Todos" coords="72,449,727,498" shape="rect">
                    </map>
                </div>
            </div>

            <div id="procedimentos_pendentes_container" style="margin-top: 1rem;">
                </div>

            <div id="procedimentos_adicionados_container">
                <h3>Procedimentos Adicionados</h3>
                </div>

            <div id="total-procedimentos-container" style="text-align: right; margin-top: 10px; font-size: 1.5em;">
                <strong>Total a Pagar:</strong> <span id="total-procedimentos-valor">R$ 0,00</span>
            </div>

        <div id="procedimentos_a_deletar_container"></div>

        <button type="submit" class="btn btn-success" style="width: 100%;">Lançar Atendimento</button>
    </form>

<div id="modalTratamento" class="modal">
    <div class="modal-content">
        <h3><span id="modal-title"></span></h3>
        <form id="form-tratamento-modal">
            <input type="hidden" name="dente_id" id="inputDente">
            <input type="hidden" name="arcada" id="inputArcada">

            <div id="procedimentos-modal-container">
                </div>

            <button type="button" id="add-procedimento-modal" class="btn btn-info">Adicionar Procedimento</button>

            <div style="text-align: right; margin-top: 10px; font-size: 1.2em;">
                <strong>Total:</strong> <span id="modal-total-valor">R$ 0,00</span>
            </div>

            <div class="btn-group">
                <button type="button" id="salvar-tratamento-modal" class="btn-save">Salvar</button>
                <button type="button" onclick="fecharModal()" class="btn-cancel">Cancelar</button>
            </div>
        </form>
    </div>
</div>
</div>

<link rel="stylesheet" href="<?= BASE_URL ?>assets/css/atendimentos_cadastro.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/image-map-resizer/1.0.10/js/imageMapResizer.min.js"></script>
<script>
window.atendimentoConfig = {
    baseUrl: <?= json_encode(BASE_URL) ?>,
    procedimentos: <?= json_encode($procedimentos, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>,
    canAlterarValor: <?= (is_admin() || is_dentista() || is_recepcionista()) ? "true" : "false" ?>
};
</script>
<script src="<?= BASE_URL ?>assets/js/modules/atendimentos_cadastro.js"></script>
