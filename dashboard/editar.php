<?php
session_start();
require '../config/conn.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'salvar') {
    $codigo = (int)$_POST['codigo'];
    $nome = trim($_POST['nome']);
    $autor = trim($_POST['autor']);
    $editora = trim($_POST['editora']);
    $ano = (int)$_POST['ano'];

    $stmt = $conn->prepare("UPDATE livros SET nome = ?, autor = ?, editora = ?, ano = ? WHERE codigo = ?");
    $stmt->bind_param("sssii", $nome, $autor, $editora, $ano, $codigo);

    if ($stmt->execute()) {
        echo "<p>Livro atualizado com sucesso!</p>";
        header("Location: ./index.php");
        exit();
    } else {
        echo "<p>Erro ao atualizar o livro.</p>";
    }
    $stmt->close();
} else {
    $codigo = $_GET['codigo'];
    $nome = $_GET['nome'];
    $autor = $_GET['autor'];
    $editora = $_GET['editora'];
    $ano = $_GET['ano'];
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Livro</title>
</head>
<body>
    <h1>Editar Livro</h1>
    <form method="POST" action="">
        <input type="hidden" name="codigo" value="<?php echo $codigo; ?>">
        <input type="text" name="nome" value="<?php echo htmlspecialchars($nome); ?>" required>
        <input type="text" name="autor" value="<?php echo htmlspecialchars($autor); ?>" required>
        <input type="text" name="editora" value="<?php echo htmlspecialchars($editora); ?>" required>
        <input type="number" name="ano" value="<?php echo $ano; ?>" required>
        <input type="hidden" name="acao" value="salvar">
        <button type="submit">Salvar</button>
    </form>
    <a href="../dashboard/index.php">Cancelar</a>
</body>
</html>
