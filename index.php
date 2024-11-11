<?php
session_start();
require './config/conn.php'; // Inclua o arquivo de conexão com o banco de dados

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = trim($_POST['nome']);
    $senha = trim($_POST['senha']);

    if (!empty($nome) && !empty($senha)) {
        // Verifica se o nome já existe
        $stmt = $conn->prepare("SELECT id FROM usuarios WHERE nome = ?");
        $stmt->bind_param("s", $nome);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            echo "Nome de usuário já existe!";
        } else {
            // Insere o usuário no banco de dados
            $hashSenha = password_hash($senha, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO usuarios (nome, senha) VALUES (?, ?)");
            $stmt->bind_param("ss", $nome, $hashSenha);

            if ($stmt->execute()) {
                $_SESSION['usuario'] = $nome;
                header("Location: dashboard/index.php");
                exit();
            } else {
                echo "Erro ao registrar usuário.";
            }
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
    <title>Registro</title>
</head>
<body>
    <h2>Registrar</h2>
    <form method="POST" action="">
        <input type="text" name="nome" placeholder="Nome de usuário" required>
        <input type="password" name="senha" placeholder="Senha" required>
        <button type="submit">Registrar</button>
    </form>
</body>
</html>
