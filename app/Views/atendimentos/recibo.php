<?php
use App\Helpers\FormatHelper;

// Dados da clínica injetados pelo Controller
$clinica_nome = $clinica_nome ?? '';
$clinica_endereco = $clinica_endereco ?? '';
$clinica_cnpj = $clinica_cnpj ?? '';
$clinica_telefone = $clinica_telefone ?? '';

// Formata a data por extenso
$formatter = new IntlDateFormatter(
    'pt_BR',
    IntlDateFormatter::FULL,
    IntlDateFormatter::NONE,
    'America/Sao_Paulo',
    IntlDateFormatter::GREGORIAN,
    'd \'de\' MMMM \'de\' yyyy'
);
$data_atendimento_formatada = $formatter->format(strtotime($atendimento['data_atendimento']));
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Recibo de Pagamento</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.3/html2pdf.bundle.min.js"></script>
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/recibo.css">
</head>
<body>
    <div class="container">
        <div id="recibo">
            <div class="header-recibo">
                <h1><?= htmlspecialchars($clinica_nome) ?></h1>
                <p><?= htmlspecialchars($clinica_endereco ?? '') ?></p>
                <p>CNPJ: <?= htmlspecialchars($clinica_cnpj) ?> | Telefone: <?= htmlspecialchars($clinica_telefone) ?></p>
            </div>

            <h2 style="text-align:center; margin-bottom: 2rem; font-weight: 500;">RECIBO DE PAGAMENTO</h2>

            <div class="section">
                <p class="declaracao">
                    Recebemos de <strong><?= htmlspecialchars($atendimento['paciente_nome']) ?></strong>,
                    CPF/CNPJ nº <strong><?= htmlspecialchars($atendimento['paciente_cpf'] ?? '') ?></strong>,
                    a importância de <strong>R$ <?= number_format($atendimento['valor_total'], 2, ',', '.') ?>
                    (<?= FormatHelper::valorPorExtenso($atendimento['valor_total']) ?>)</strong>,
                    referente aos serviços odontológicos abaixo descritos.
                </p>
            </div>
            
            <div class="section">
                <h2>Serviços Prestados</h2>
                <table class="procedimentos-table">
                    <thead>
                        <tr>
                            <th>Procedimento</th>
                            <th>Valor</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($atendimento['procedimentos'] as $proc): ?>
                        <tr>
                            <td><?= htmlspecialchars($proc['nome']) ?></td>
                            <td>R$ <?= number_format($proc['valor_procedimento'], 2, ',', '.') ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="total">
                    Total: R$ <?= number_format($atendimento['valor_total'], 2, ',', '.') ?>
                </div>
            </div>

            <div class="section">
                <p>
                    Para clareza, firmamos o presente.
                </p>
                <p style="text-align: right;">
                    <?= htmlspecialchars($atendimento['cidade'] ?? 'Ananindeua') ?>, <?= $data_atendimento_formatada ?>.
                </p>
            </div>

            <div class="assinatura">
                <div class="assinatura-linha"></div>
                <p style="margin-top: 0.5rem;"><?= htmlspecialchars($clinica_nome ?? '') ?></p>
                <p style="font-size: 0.9rem; color: #666;"><?= htmlspecialchars($atendimento['dentista_nome'] ?? 'Dentista Responsável') ?></p>
            </div>
        </div>

        <div class="actions">
            <button onclick="window.print()" class="btn btn-primary">Imprimir</button>
            <button id="download-btn" class="btn btn-secondary" data-filename="recibo_<?= htmlspecialchars(str_replace(' ', '_', $atendimento['paciente_nome']), ENT_QUOTES, 'UTF-8') ?>.pdf">Baixar PDF</button>
            <button onclick="window.close()" class="btn btn-cancel">Fechar</button>
        </div>
    </div>

    <script src="<?= BASE_URL ?>assets/js/modules/recibo.js"></script>
</body>
</html>
