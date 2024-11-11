<?php
session_start();
require './config/conn.php'; // Inclua o arquivo de conexão com o banco de dados

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = trim($_POST['nome']);
    $senha = trim($_POST['senha']);

    if (!empty($nome) && !empty($senha)) {
        // Verifica o usuário no banco de dados
        $stmt = $conn->prepare("SELECT senha FROM usuarios WHERE nome = ?");
        $stmt->bind_param("s", $nome);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($hashSenha);
            $stmt->fetch();

            if (password_verify($senha, $hashSenha)) {
                $_SESSION['usuario'] = $nome;
                header("Location: dashboard/index.php");
                exit();
            } else {
                echo "Senha incorreta.";
            }
        } else {
            echo "Usuário não encontrado.";
        }
        $stmt->close();
    } else {
        echo "Por favor, preencha todos os campos.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
</head>
<body>
    <h2>Login</h2>
    <form method="POST" action="">
        <input type="text" name="nome" placeholder="Nome de usuário" required>
        <input type="password" name="senha" placeholder="Senha" required>
        <button type="submit">Entrar</button>
    </form>
</body>
</html>
