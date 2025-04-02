<?php
session_start();

// Se já estiver logado, redireciona para a página principal
if (isset($_SESSION['usuario_logado'])) {
    header('Location: index.php');
    exit();
}

// Processar formulário de login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    // Aqui você deve verificar as credenciais no banco de dados
    // Exemplo simplificado:
    $email = $_POST['email'];
    $senha = $_POST['password'];
    
    // Conexão com o banco de dados (substitua com suas credenciais)
    $conn = new mysqli('localhost', 'usuario', 'senha', 'controle_patrimonio');
    
    if ($conn->connect_error) {
        die("Falha na conexão: " . $conn->connect_error);
    }
    
    $stmt = $conn->prepare("SELECT id, senha FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $usuario = $result->fetch_assoc();
        
        // Verifica a senha (use password_verify() se estiver usando hash)
        if ($senha === $usuario['senha']) { // Substitua por verificação segura
            $_SESSION['usuario_logado'] = true;
            $_SESSION['usuario_id'] = $usuario['id'];
            header('Location: index.php');
            exit();
        }
    }
    
    $erro = "Email ou senha incorretos!";
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Controle de Patrimônio</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    /* Estilos mantidos iguais ao original */
    body {
      background-color: #f8f9fa;
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }

    .container {
      flex: 1;
      display: flex;
      justify-content: center;
      align-items: center;
      flex-direction: column;
    }

    .card-wrapper {
      width: 100%;
      max-width: 500px;
      margin-top: 1rem;
    }

    .card {
      background-color: #ffffff;
      border: none;
      border-radius: 15px;
      padding: 2rem;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .card-title {
      color: #007bff;
    }

    .btn-custom {
      background-color: #007bff;
      color: white;
      border: none;
      border-radius: 25px;
      padding: 10px;
      transition: background-color 0.3s ease, transform 0.3s ease;
    }

    .btn-custom:hover {
      background-color: #0056b3;
      transform: translateY(-3px);
    }

    .btn-custom:focus {
      box-shadow: 0 0 5px rgba(0,123,255, .5);
    }

    #register-section {
      display: none;
    }
  </style>
</head>
<body>
<div class="container">
  <div class="card-wrapper" id="login-section">
    <div class="card">
      <div class="card-body">
        <h3 class="card-title text-center mb-4">Login</h3>
        <?php if (isset($erro)): ?>
          <div class="alert alert-danger"><?php echo $erro; ?></div>
        <?php endif; ?>
        <form id="login-form" method="POST" action="login.php">
          <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" placeholder="Digite seu email" required>
          </div>
          <div class="mb-3">
            <label for="password" class="form-label">Senha</label>
            <input type="password" class="form-control" id="password" name="password" placeholder="Digite sua senha" required>
          </div>
          <button type="submit" class="btn btn-custom w-100">Entrar</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>