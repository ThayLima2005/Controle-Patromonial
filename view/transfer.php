<?php
session_start();
if (!isset($_SESSION['usuario_logado'])) {
    header('Location: login.php');
    exit();
}

// Conexão com o banco de dados
$conn = new mysqli('localhost', 'usuario', 'senha', 'controle_patrimonio');

// Processar formulário de transferência
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['departamento_id'])) {
    $stmt = $conn->prepare("INSERT INTO transferencias (departamento_id, departamento_destino, patrimonio_id, responsavel, data_transferencia, observacao) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iiisss",
        $_POST['departamento_id'],
        $_POST['departamento_destino'],
        $_POST['patrimonio_id'],
        $_POST['responsavel'],
        $_POST['data_transferencia'],
        $_POST['observacao']
    );
    $stmt->execute();
}

// Buscar dados para os selects
$departamentos = $conn->query("SELECT departamento_id, nome_departamento FROM departamentos");
$patrimonios = $conn->query("SELECT id_patrimonio FROM patrimonios");
$usuarios = $conn->query("SELECT id, nome FROM usuarios");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Transferências</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        html, body {
            height: 100%;
            margin: 0;
        }
        .main-content {
            min-height: 100%;
            padding-bottom: 100px;
        }
        .card {
            margin-top: 20px;
        }
        footer {
            background-color: #343a40;
            color: white;
            text-align: center;
            padding: 10px 0;
            position: relative;
            bottom: 0;
            width: 100%;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <a class="navbar-brand" href="index.php">Controle de Patrimônio</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
            <li class="nav-item"><a class="nav-link" href="index.php">Página Inicial</a></li>
            <li class="nav-item"><a class="nav-link" href="transfer.php">Gerenciar Transferências</a></li>
            <li class="nav-item"><a class="nav-link" href="movimento.php">Movimento de Transferências</a></li>
        </ul>
        <a class="btn btn-outline-light ms-auto" href="logout.php">Logoff</a>
    </div>
</nav>

<div class="container mt-4 main-content">
    <h2 class="mb-4">Adicionar Transferência</h2>
    <div class="card border-light shadow-lg rounded-3 mb-4">
        <div class="card-body">
            <form id="add-transfer-form" method="POST">
                <div class="mb-3">
                    <label for="departamento_id" class="form-label">Departamento Atual</label>
                    <select class="form-select" id="departamento_id" name="departamento_id" required>
                        <option value="">Selecione o Departamento Atual</option>
                        <?php while($depto = $departamentos->fetch_assoc()): ?>
                            <option value="<?= $depto['departamento_id'] ?>"><?= $depto['nome_departamento'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="departamento_destino" class="form-label">Departamento Destino</label>
                    <select class="form-select" id="departamento_destino" name="departamento_destino" required>
                        <option value="">Selecione o Departamento Destino</option>
                        <?php 
                        $departamentos->data_seek(0); // Reinicia o ponteiro do resultado
                        while($depto = $departamentos->fetch_assoc()): ?>
                            <option value="<?= $depto['departamento_id'] ?>"><?= $depto['nome_departamento'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="patrimonio_id" class="form-label">ID do Patrimônio</label>
                    <select class="form-select" id="patrimonio_id" name="patrimonio_id" required>
                        <option value="">Selecione o Patrimônio</option>
                        <?php while($patrimonio = $patrimonios->fetch_assoc()): ?>
                            <option value="<?= $patrimonio['id_patrimonio'] ?>"><?= $patrimonio['id_patrimonio'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="responsavel" class="form-label">Responsável</label>
                    <select class="form-select" id="responsavel" name="responsavel" required>
                        <option value="">Selecione o Responsável</option>
                        <?php while($usuario = $usuarios->fetch_assoc()): ?>
                            <option value="<?= $usuario['id'] ?>"><?= $usuario['nome'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="data_transferencia" class="form-label">Data da Transferência</label>
                    <input type="date" class="form-control" id="data_transferencia" name="data_transferencia" required>
                </div>
                <div class="mb-3">
                    <label for="observacao" class="form-label">Observação</label>
                    <textarea class="form-control" id="observacao" name="observacao"></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Adicionar Transferência</button>
            </form>
        </div>
    </div>
</div>

<footer>
    <p>&copy; <?= date('Y') ?> Controle de Patrimônio. Todos os direitos reservados.</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php $conn->close(); ?>