<?php
session_start();
if (!isset($_SESSION['usuario_logado'])) {
    header('Location: login.php');
    exit();
}

// Conexão com o banco de dados
$conn = new mysqli('localhost', 'usuario', 'senha', 'controle_patrimonio');

// Processar filtro de datas
$filtro = "";
if ($_SERVER['REQUEST_METHOD'] === 'GET' && (isset($_GET['startDate']) || isset($_GET['endDate']))) {
    $where = [];
    
    if (!empty($_GET['startDate'])) {
        $startDate = $conn->real_escape_string($_GET['startDate']);
        $where[] = "data_transferencia >= '$startDate'";
    }
    
    if (!empty($_GET['endDate'])) {
        $endDate = $conn->real_escape_string($_GET['endDate']);
        $where[] = "data_transferencia <= '$endDate'";
    }
    
    if (!empty($where)) {
        $filtro = "WHERE " . implode(" AND ", $where);
    }
}

// Buscar transferências
$query = "SELECT t.*, d1.nome_departamento as depto_atual, d2.nome_departamento as depto_destino, u.nome as responsavel_nome 
          FROM transferencias t
          JOIN departamentos d1 ON t.departamento_id = d1.departamento_id
          JOIN departamentos d2 ON t.departamento_destino = d2.departamento_id
          JOIN usuarios u ON t.responsavel = u.id
          $filtro
          ORDER BY t.data_transferencia DESC";
          
$transferencias = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Movimento de Transferências</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        html, body {
            height: 100%;
            margin: 0;
        }
        .main-content {
            min-height: 100%;
            padding-bottom: 50px;
        }
        .table { 
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
    </div>
</nav>

<div class="container mt-4 main-content">
    <h2 class="mb-4">Movimento de Transferências</h2>

    <!-- Filtro de data -->
    <form id="filter-form" class="row g-3" method="GET">
        <div class="col-md-4">
            <label for="startDate" class="form-label">Data Início</label>
            <input type="date" class="form-control" id="startDate" name="startDate" value="<?= $_GET['startDate'] ?? '' ?>">
        </div>
        <div class="col-md-4">
            <label for="endDate" class="form-label">Data Fim</label>
            <input type="date" class="form-control" id="endDate" name="endDate" value="<?= $_GET['endDate'] ?? '' ?>">
        </div>
        <div class="col-md-4 d-flex align-items-end">
            <button type="submit" class="btn btn-primary">Filtrar</button>
            <a href="movimento.php" class="btn btn-secondary ms-2">Limpar</a>
        </div>
    </form>

    <!-- Tabela de transferências -->
    <div class="table-responsive mt-4">
        <table class="table table-hover table-bordered">
            <thead class="table-primary">
            <tr>
                <th>ID</th>
                <th>Departamento Atual</th>
                <th>Departamento Destino</th>
                <th>ID do Patrimônio</th>
                <th>Responsável</th>
                <th>Data da Transferência</th>
                <th>Observação</th>
            </tr>
            </thead>
            <tbody>
            <?php while($transfer = $transferencias->fetch_assoc()): ?>
                <tr>
                    <td><?= $transfer['id_transferencia'] ?></td>
                    <td><?= $transfer['depto_atual'] ?></td>
                    <td><?= $transfer['depto_destino'] ?></td>
                    <td><?= $transfer['patrimonio_id'] ?></td>
                    <td><?= $transfer['responsavel_nome'] ?></td>
                    <td><?= date('d/m/Y', strtotime($transfer['data_transferencia'])) ?></td>
                    <td><?= $transfer['observacao'] ?></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<footer>
    <p>&copy; <?= date('Y') ?> Controle de Patrimônio. Todos os direitos reservados.</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php $conn->close(); ?>