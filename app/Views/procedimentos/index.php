<div class="card">
    <h2>Gestão de Procedimentos</h2>

    <?php require_once __DIR__ . '/../partials/alert.php'; ?>

    <table class="mobile-card-table">

        <h3>Novo Procedimento</h3>
        <form action="<?= BASE_URL ?>procedimentos/salvar" method="POST">
            <?= \App\Helpers\CsrfHelper::input() ?>
            <div class="form-group">
                <label for="nome">Nome do Procedimento</label>
                <input type="text" name="nome" id="nome" required>
            </div>
            <div class="form-group">
                <label for="categoria">Categoria</label>
                <select name="categoria" id="categoria" required>
                    <option value="geral">Geral</option>
                    <option value="especializado">Especializado</option>
                    <option value="protese">Prótese</option>
                </select>
            </div>
            <div class="form-group">
                <label for="tipo">Arquivo</label>
                <select name="tipo" id="tipo" required>
                    <option value="0">Sem Arquivo</option>
                    <option value="1">Com Arquivo</option>
                </select>
            </div>
            <div class="form-group">
                <label for="valor_base">Valor Base (R$)</label>
                <input type="number" step="0.01" name="valor_base" id="valor_base">
            </div>
            <button type="submit" class="btn btn-success">Salvar Procedimento</button>
        </form>
    </div>

    <h3 style="margin-top: 2rem;">Procedimentos Cadastrados</h3>
    <table class="mobile-card-table">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Categoria</th>
                <th>Valor Base</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($procedimentos) > 0): ?>
                <?php foreach ($procedimentos as $proc): ?>
                    <tr>
                        <td data-label="Nome"><?= htmlspecialchars($proc['nome']) ?></td>
                        <td data-label="Categoria"><?= ucfirst($proc['categoria']) ?></td>
                        <td data-label="Valor Base">
                            R$ <?= number_format((float)$proc['valor_base'], 2, ',', '.') ?>
                        </td>
                        <td data-label="Ações" style="display: flex; gap: 0.5rem; align-items: center;">
                            <a href="<?= BASE_URL ?>procedimentos/editar?id=<?= $proc['id'] ?>"
                               class="btn btn-primary">
                                Editar
                            </a>
                            <form action="<?= BASE_URL ?>procedimentos/excluir" method="POST" style="display:inline;" onsubmit="return confirm('Tem certeza que deseja remover este procedimento?');">
                                <?= \App\Helpers\CsrfHelper::input() ?>
                                <input type="hidden" name="id" value="<?= $proc['id'] ?>">
                                <button type="submit" class="btn btn-danger" style="padding: 5px 15px; font-size: 0.875rem; border-radius: 30px; cursor: pointer;">Remover</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" style="text-align: center;">Nenhum procedimento registrado.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<style>
.success { color: green; background: #e8f5e9; padding: 1rem; border-radius: 6px; }
.error   { color: red;   background: #ffebee; padding: 1rem; border-radius: 6px; }
</style>
