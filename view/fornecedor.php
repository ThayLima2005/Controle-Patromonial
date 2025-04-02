<?php
session_start();
if (!isset($_SESSION['usuario_logado'])) {
    header('Location: login.php');
    exit();
}

// Conexão com o banco de dados
$conn = new mysqli('localhost', 'usuario', 'senha', 'controle_patrimonio');

// Processar formulário de adição
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['razao_social'])) {
    $stmt = $conn->prepare("INSERT INTO fornecedores (razao_social, cnpj, cidade, cep, uf, bairro, endereco, numero, telefone, email, inscricao_municipal, inscricao_estadual) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssssssss", 
        $_POST['razao_social'],
        $_POST['cnpj'],
        $_POST['cidade'],
        $_POST['cep'],
        $_POST['uf'],
        $_POST['bairro'],
        $_POST['endereco'],
        $_POST['numero'],
        $_POST['telefone'],
        $_POST['email'],
        $_POST['inscricao_municipal'],
        $_POST['inscricao_estadual']
    );
    $stmt->execute();
}

// Buscar fornecedores
$fornecedores = $conn->query("SELECT * FROM fornecedores");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Fornecedores</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            padding-top: 56px;
        }
        .navbar {
            margin-bottom: 20px;
        }
        .card {
            background-color: #f8f9fa;
        }
        .table thead th {
            background-color: #007bff;
            color: #fff;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
    <a class="navbar-brand" href="index.php">Controle de Patrimônio</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="index.php">Página Inicial</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="fornecedor.php">Gerenciar Fornecedores</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="departamento.php">Gerenciar Departamentos</a>
            </li>
        </ul>
    </div>
</nav>

<div class="container mt-5">
    <h2 class="mb-4">Adicionar Fornecedor</h2>
    <div class="card border-light shadow-lg rounded-3 mb-4">
        <div class="card-body">
            <form id="add-fornecedor-form" method="POST">
                <div class="mb-3">
                    <label for="razao_social" class="form-label">Razão Social</label>
                    <input type="text" class="form-control" id="razao_social" name="razao_social" required>
                </div>
                <!-- Outros campos do formulário com name="" -->
                <button type="submit" class="btn btn-primary">Adicionar Fornecedor</button>
            </form>
        </div>
    </div>

    <h2 class="mb-4">Lista de Fornecedores</h2>
    <div class="table-responsive">
        <table class="table table-hover table-bordered">
            <thead class="table-primary">
            <tr>
                <th>ID</th>
                <th>Razão Social</th>
                <th>CNPJ</th>
                <th>Cidade</th>
                <th>CEP</th>
                <th>UF</th>
                <th>Bairro</th>
                <th>Endereço</th>
                <th>Número</th>
                <th>Telefone</th>
                <th>Email</th>
                <th>Inscrição Municipal</th>
                <th>Inscrição Estadual</th>
                <th>Ações</th>
            </tr>
            </thead>
            <tbody>
            <?php while($fornecedor = $fornecedores->fetch_assoc()): ?>
                <tr>
                    <td><?= $fornecedor['id_fornecedor'] ?></td>
                    <td><?= $fornecedor['razao_social'] ?></td>
                    <td><?= $fornecedor['cnpj'] ?></td>
                    <td><?= $fornecedor['cidade'] ?></td>
                    <td><?= $fornecedor['cep'] ?></td>
                    <td><?= $fornecedor['uf'] ?></td>
                    <td><?= $fornecedor['bairro'] ?></td>
                    <td><?= $fornecedor['endereco'] ?></td>
                    <td><?= $fornecedor['numero'] ?></td>
                    <td><?= $fornecedor['telefone'] ?></td>
                    <td><?= $fornecedor['email'] ?></td>
                    <td><?= $fornecedor['inscricao_municipal'] ?></td>
                    <td><?= $fornecedor['inscricao_estadual'] ?></td>
                    <td>
                        <a href="editar_fornecedor.php?id=<?= $fornecedor['id_fornecedor'] ?>" class="btn btn-warning btn-sm">Editar</a>
                        <a href="excluir_fornecedor.php?id=<?= $fornecedor['id_fornecedor'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza?')">Excluir</a>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<footer class="bg-dark text-white text-center py-4 mt-5">
    <p>&copy; <?= date('Y') ?> Controle de Patrimônio. Todos os direitos reservados.</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php $conn->close(); ?>