<?php
require_once __DIR__ . '/../app/autoload.php';
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/session.php';

use App\Database\Connection;

try {
    $pdo = Connection::getInstance();
    $clinicaId = (int)($pdo->query("SELECT id FROM clinicas WHERE status = 'ativo' ORDER BY id ASC LIMIT 1")->fetchColumn() ?: 1);

    echo "Iniciando cadastro de dados padrão...<br>";

    $stmt = $pdo->query("SELECT COUNT(*) FROM usuarios WHERE clinica_id = {$clinicaId}");
    if ((int)$stmt->fetchColumn() === 0) {
        $senhaRoberto = password_hash('123', PASSWORD_BCRYPT);
        $senhaAna = password_hash('123', PASSWORD_BCRYPT);
        $senhaAdmin = password_hash('123', PASSWORD_BCRYPT);

        $sql = "INSERT INTO usuarios (nome, login, senha, perfil, clinica_id) VALUES 
                ('Administrador', 'admin', ?, 'proprietario', ?),
                ('Dr. Roberto Silva', 'roberto', ?, 'dentista', ?),
                ('Dra. Ana Costa', 'ana', ?, 'dentista', ?)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$senhaAdmin, $clinicaId, $senhaRoberto, $clinicaId, $senhaAna, $clinicaId]);
        echo "Usuários (proprietário e dentistas) cadastrados.<br>";
    }

    $stmt = $pdo->query("SELECT COUNT(*) FROM procedimentos WHERE clinica_id = {$clinicaId}");
    if ((int)$stmt->fetchColumn() === 0) {
        $sql = "INSERT INTO procedimentos (nome, categoria, valor_base, clinica_id) VALUES 
                ('Limpeza Completa', 'geral', 150.00, ?),
                ('Restauração Simples', 'geral', 200.00, ?),
                ('Canal (Endodontia)', 'especializado', 800.00, ?),
                ('Implante Unitário', 'especializado', 2500.00, ?),
                ('Prótese Total', 'protese', 1800.00, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$clinicaId, $clinicaId, $clinicaId, $clinicaId, $clinicaId]);
        echo "Procedimentos cadastrados.<br>";
    }

    echo "<strong>Configuração concluída.</strong>";
} catch (Throwable $e) {
    die("Erro: " . $e->getMessage());
}
