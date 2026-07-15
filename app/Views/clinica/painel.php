<?php
use App\Helpers\FormatHelper;
?>
<div class="card">
    <h2>Configurações da Clínica</h2>

    <?php require_once __DIR__ . '/../partials/alert.php'; ?>

    <div class="tabs">
        <button class="tab-link active" onclick="openTab(event, 'dados')">Dados Institucionais</button>
        <button class="tab-link" onclick="openTab(event, 'regras')">Regras de Comissão</button>
        <button class="tab-link" onclick="openTab(event, 'taxas')">Taxas de Cartão</button>
    </div>

    <!-- Dados Institucionais -->
    <div id="dados" class="tab-content" style="display: block;">
        <h3>Dados Institucionais</h3>
        <form action="<?= BASE_URL ?>clinica/salvar-dados" method="POST">
            <?= \App\Helpers\CsrfHelper::input() ?>
            <div class="form-group">
                <label>Nome Fantasia</label>
                <input type="text" name="nome_fantasia" value="<?= htmlspecialchars($clinica['nome_fantasia'] ?? '') ?>" required readonly class="readonly-field">
            </div>
            <div class="form-group">
                <label>Razão Social</label>
                <input type="text" name="razao_social" value="<?= htmlspecialchars($clinica['razao_social'] ?? '') ?>" readonly class="readonly-field">
            </div>
            <div class="form-group">
                <label>CNPJ</label>
                <input type="text" name="cnpj" value="<?= htmlspecialchars(FormatHelper::cnpj($clinica['cnpj'] ?? '')) ?>" oninput="mascaraCNPJ(this)" readonly class="readonly-field">
            </div>
            <div style="margin-top: 1rem; display: flex; gap: 10px;">
                <button type="button" class="btn btn-secondary" onclick="toggleEdit('dados')">Editar</button>
                <button type="submit" class="btn btn-primary" style="display: none;">Salvar Dados</button>
                <button type="button" class="btn btn-secondary" onclick="location.reload()" style="display: none;">Cancelar</button>
            </div>
        </form>
    </div>

    <!-- Regras de Comissão -->
    <div id="regras" class="tab-content">
        <h3>Regras de Comissão e Rateio</h3>
        <form action="<?= BASE_URL ?>clinica/salvar-configuracoes" method="POST">
            <?= \App\Helpers\CsrfHelper::input() ?>
            
            <div class="card" style="background: #f9f9f9; border: 1px solid #ddd;">
                <h4>Repasse Geral (Clínico)</h4>
                <div class="form-group">
                    <label>Tipo de Repasse</label>
                    <select name="tipo_comissao" disabled class="readonly-field">
                        <option value="percentual" <?= ($comissao['tipo'] ?? '') === 'percentual' ? 'selected' : '' ?>>Percentual (%)</option>
                        <option value="fixo" <?= ($comissao['tipo'] ?? '') === 'fixo' ? 'selected' : '' ?>>Valor Fixo (R$)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Valor/Percentual Base</label>
                    <input type="number" step="0.01" name="valor_regra" value="<?= $comissao['valor_regra'] ?? 0 ?>" readonly class="readonly-field">
                </div>
                <div class="form-group">
                    <label>Meta de Faturamento Mensal (R$)</label>
                    <input type="number" step="0.01" name="valor_meta" value="<?= $comissao['valor_meta'] ?? 0 ?>" readonly class="readonly-field">
                </div>
                <div class="form-group">
                    <label>Bônus após bater meta (%)</label>
                    <input type="number" step="0.01" name="percentual_bonus" value="<?= $comissao['percentual_bonus'] ?? 0 ?>" readonly class="readonly-field">
                </div>
            </div>

            <div class="card" style="background: #f9f9f9; border: 1px solid #ddd; margin-top: 1rem;">
                <h4>Procedimentos Especializados (%)</h4>
                <div class="form-group">
                    <label>Percentual Base Especializado (%)</label>
                    <input type="number" step="0.01" name="comissao_especializado" value="<?= $configs['comissao_especializado'] ?? 0 ?>" readonly class="readonly-field">
                </div>
                <div class="form-group">
                    <label>Percentual Canal (%)</label>
                    <input type="number" step="0.01" name="comissao_canal" value="<?= $configs['comissao_canal'] ?? 0 ?>" readonly class="readonly-field">
                </div>
                <div class="form-group">
                    <label>Percentual Prótese (%)</label>
                    <input type="number" step="0.01" name="comissao_protese" value="<?= $configs['comissao_protese'] ?? 0 ?>" readonly class="readonly-field">
                </div>
            </div>

            <div class="card" style="background: #f9f9f9; border: 1px solid #ddd; margin-top: 1rem;">
                <h4>Contatos e Endereço (Recibo)</h4>
                <div class="form-group">
                    <label>Endereço Completo</label>
                    <input type="text" name="clinica_endereco" value="<?= htmlspecialchars($configs['clinica_endereco'] ?? '') ?>" readonly class="readonly-field">
                </div>
                <div class="form-group">
                    <label>Telefone de Contato</label>
                    <input type="text" name="clinica_telefone" value="<?= htmlspecialchars($configs['clinica_telefone'] ?? '') ?>" oninput="mascaraTelefone(this)" readonly class="readonly-field">
                </div>
            </div>

            <div style="margin-top: 1rem; display: flex; gap: 10px;">
                <button type="button" class="btn btn-secondary" onclick="toggleEdit('regras')">Editar</button>
                <button type="submit" class="btn btn-primary" style="display: none;">Salvar Regras</button>
                <button type="button" class="btn btn-secondary" onclick="location.reload()" style="display: none;">Cancelar</button>
            </div>
        </form>
    </div>

    <!-- Taxas de Cartão -->
    <div id="taxas" class="tab-content">
        <h3>Gestão de Taxas de Cartão</h3>
        
        <div class="card" style="background: #f9f9f9; border: 1px solid #ddd; margin-bottom: 1rem;">
            <h4>Adicionar/Editar Taxa</h4>
            <form action="<?= BASE_URL ?>clinica/salvar-taxa" method="POST" id="form-taxa">
                <?= \App\Helpers\CsrfHelper::input() ?>
                <input type="hidden" name="taxa_id" id="taxa_id">
                
                <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                    <div class="form-group" style="flex: 1; min-width: 150px;">
                        <label>Bandeira</label>
                        <input type="text" name="bandeira" id="taxa_bandeira" placeholder="ex: Visa, Master ou default" required>
                    </div>
                    <div class="form-group" style="flex: 1; min-width: 150px;">
                        <label>Modalidade</label>
                        <select name="modalidade" id="taxa_modalidade" required>
                            <option value="credito">Crédito</option>
                            <option value="debito">Débito</option>
                        </select>
                    </div>
                    <div class="form-group" style="flex: 1; min-width: 80px;">
                        <label>Parcelas</label>
                        <input type="number" name="parcelas" id="taxa_parcelas" value="1" min="1" max="24" required>
                    </div>
                    <div class="form-group" style="flex: 1; min-width: 100px;">
                        <label>Taxa (%)</label>
                        <input type="number" step="0.01" name="taxa_percentual" id="taxa_percentual" required>
                    </div>
                    <div style="display: flex; align-items: flex-end; padding-bottom: 15px;">
                        <button type="submit" class="btn btn-success">Salvar Taxa</button>
                        <button type="button" class="btn btn-secondary" onclick="resetTaxaForm()" style="margin-left: 5px;">Limpar</button>
                    </div>
                </div>
            </form>
        </div>

        <table class="mobile-card-table">
            <thead>
                <tr>
                    <th>Bandeira</th>
                    <th>Modalidade</th>
                    <th>Parcelas</th>
                    <th>Taxa (%)</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($taxas as $t): ?>
                <tr>
                    <td data-label="Bandeira"><?= htmlspecialchars($t['bandeira']) ?></td>
                    <td data-label="Modalidade"><?= ucfirst($t['modalidade']) ?></td>
                    <td data-label="Parcelas"><?= $t['parcelas'] ?>x</td>
                    <td data-label="Taxa"><?= number_format($t['taxa_percentual'], 2) ?>%</td>
                    <td data-label="Ações" style="display: flex; gap: 5px;">
                        <button class="btn btn-primary btn-sm" onclick="editTaxa(<?= htmlspecialchars(json_encode($t)) ?>)">Editar</button>
                        <form action="<?= BASE_URL ?>clinica/excluir-taxa" method="POST" style="display: inline;" onsubmit="return confirm('Excluir esta taxa?');">
                            <?= \App\Helpers\CsrfHelper::input() ?>
                            <input type="hidden" name="id" value="<?= $t['id'] ?>">
                            <button type="submit" class="btn btn-danger btn-sm">Remover</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="<?= BASE_URL ?>assets/js/modules/clinica_painel.js"></script>

<link rel="stylesheet" href="<?= BASE_URL ?>assets/css/clinica_painel.css">
