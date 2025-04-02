<?php
session_start();
if (!isset($_SESSION['usuario_logado'])) {
    header('Location: login.php');
    exit();
}

$conn = new mysqli('localhost', 'usuario', 'senha', 'controle_patrimonio');

// Adicionar departamento
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nome_departamento'])) {
    $stmt = $conn->prepare("INSERT INTO departamentos (nome_departamento, responsavel, telefone, email) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", 
        $_POST['nome_departamento'],
        $_POST['responsavel'],
        $_POST['telefone'],
        $_POST['email']
    );
    $stmt->execute();
}

// Buscar departamentos
$departamentos = $conn->query("SELECT * FROM departamentos");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Departamentos</title>
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
<!-- Navbar similar ao index.php -->

<div class="container mt-5">
    <h2 class="mb-4">Adicionar Departamento</h2>
    <div class="card border-light shadow-lg rounded-3 mb-4">
        <div class="card-body">
            <form id="add-departamento-form" method="POST">
                <div class="mb-3">
                    <label for="nome_departamento" class="form-label">Nome do Departamento</label>
                    <input type="text" class="form-control" id="nome_departamento" name="nome_departamento" required>
                </div>
                <!-- Outros campos do formulário... -->
                <button type="submit" class="btn btn-primary">Adicionar Departamento</button>
            </form>
        </div>
    </div>

    <h2 class="mb-4">Lista de Departamentos</h2>
    <div class="table-responsive">
        <table class="table table-hover table-bordered">
            <thead class="table-primary">
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Responsável</th>
                <th>Telefone</th>
                <th>Email</th>
                <th>Ações</th>
            </tr>
            </thead>
            <tbody>
            <?php while($departamento = $departamentos->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $departamento['departamento_id']; ?></td>
                    <td><?php echo $departamento['nome_departamento']; ?></td>
                    <td><?php echo $departamento['responsavel']; ?></td>
                    <td><?php echo $departamento['telefone']; ?></td>
                    <td><?php echo $departamento['email']; ?></td>
                    <td>
                        <a href="editar_departamento.php?id=<?php echo $departamento['departamento_id']; ?>" class="btn btn-warning btn-sm">Editar</a>
                        <a href="excluir_departamento.php?id=<?php echo $departamento['departamento_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza?')">Excluir</a>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Footer e scripts... -->
<?php $conn->close(); ?>