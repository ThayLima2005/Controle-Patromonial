<?php
session_start();
if (!isset($_SESSION['usuario_logado'])) {
    header('Location: login.php');
    exit();
}

// Conexão com o banco de dados
$conn = new mysqli('localhost', 'usuario', 'senha', 'controle_patrimonio');

// Processar formulário de adição
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['descricao'])) {
    $stmt = $conn->prepare("INSERT INTO patrimonios (descricao, marca, num_patrimonio, data_aquisicao, valor_aquisicao, garantia, nota_fiscal, status, fornecedor_id, departamento_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssdisssi", 
        $_POST['descricao'],
        $_POST['marca'],
        $_POST['num_patrimonio'],
        $_POST['data_aquisicao'],
        $_POST['valor_aquisicao'],
        $_POST['garantia'],
        $_POST['nota_fiscal'],
        $_POST['status'],
        $_POST['fornecedor_id'],
        $_POST['departamento_id']
    );
    $stmt->execute();
}

// Buscar patrimônios
$patrimonios = $conn->query("SELECT * FROM patrimonios");

// Buscar fornecedores e departamentos para os selects
$fornecedores = $conn->query("SELECT id_fornecedor, razao_social FROM fornecedores");
$departamentos = $conn->query("SELECT departamento_id, nome_departamento FROM departamentos");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciamento de Patrimônios</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            padding: 20px;
        }
        .btn-toolbar {
            justify-content: space-between;
        }
    </style>
</head>
<body>

<div class="container">
    <h1 class="text-center">Gerenciamento de Patrimônios</h1>

    <!-- Formulário para adicionar um patrimônio -->
    <form id="patrimonioForm" method="POST">
        <div class="form-group">
            <label for="descricao">Descrição</label>
            <input type="text" class="form-control" id="descricao" name="descricao" placeholder="Descrição do patrimônio" required>
        </div>
        <!-- Outros campos do formulário... -->
        
        <div class="form-group">
            <label for="fornecedor_id">Fornecedor</label>
            <select class="form-control" id="fornecedor_id" name="fornecedor_id" required>
                <option value="">Selecione um fornecedor</option>
                <?php while($fornecedor = $fornecedores->fetch_assoc()): ?>
                    <option value="<?php echo $fornecedor['id_fornecedor']; ?>">
                        <?php echo $fornecedor['razao_social']; ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label for="departamento_id">Departamento</label>
            <select class="form-control" id="departamento_id" name="departamento_id" required>
                <option value="">Selecione um departamento</option>
                <?php while($departamento = $departamentos->fetch_assoc()): ?>
                    <option value="<?php echo $departamento['departamento_id']; ?>">
                        <?php echo $departamento['nome_departamento']; ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        
        <button type="submit" class="btn btn-primary">Adicionar Patrimônio</button>
    </form>

    <hr>

    <!-- Tabela para listar os patrimônios -->
    <h2 class="text-center">Lista de Patrimônios</h2>
    <table class="table table-bordered table-hover">
        <thead>
        <tr>
            <th>ID</th>
            <th>Descrição</th>
            <th>Marca</th>
            <th>Número Patrimônio</th>
            <th>Data de Aquisição</th>
            <th>Valor de Aquisição</th>
            <th>Garantia</th>
            <th>Nota Fiscal</th>
            <th>Status</th>
            <th>Ações</th>
        </tr>
        </thead>
        <tbody>
        <?php while($patrimonio = $patrimonios->fetch_assoc()): ?>
            <tr>
                <td><?php echo $patrimonio['id_patrimonio']; ?></td>
                <td><?php echo $patrimonio['descricao']; ?></td>
                <td><?php echo $patrimonio['marca']; ?></td>
                <td><?php echo $patrimonio['num_patrimonio']; ?></td>
                <td><?php echo $patrimonio['data_aquisicao']; ?></td>
                <td>R$ <?php echo number_format($patrimonio['valor_aquisicao'], 2, ',', '.'); ?></td>
                <td><?php echo $patrimonio['garantia']; ?> meses</td>
                <td><?php echo $patrimonio['nota_fiscal']; ?></td>
                <td><?php echo $patrimonio['status']; ?></td>
                <td>
                    <a href="editar_patrimonio.php?id=<?php echo $patrimonio['id_patrimonio']; ?>" class="btn btn-warning btn-sm">Editar</a>
                    <a href="excluir_patrimonio.php?id=<?php echo $patrimonio['id_patrimonio']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza que deseja excluir?')">Excluir</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php $conn->close(); ?>