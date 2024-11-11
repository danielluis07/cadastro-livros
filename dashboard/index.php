<?php
session_start();
require '../config/conn.php'; // Conexão com o banco de dados

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario'])) {
    header("Location: ../login.php");
    exit();
}

// Cadastro de um novo livro
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'cadastrar') {
    $nome = trim($_POST['nome']);
    $autor = trim($_POST['autor']);
    $editora = trim($_POST['editora']);
    $ano = (int)$_POST['ano'];

    if (!empty($nome) && !empty($autor) && !empty($editora) && !empty($ano)) {
        $stmt = $conn->prepare("INSERT INTO livros (nome, autor, editora, ano) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $nome, $autor, $editora, $ano);

        if ($stmt->execute()) {
            echo "<p>Livro cadastrado com sucesso!</p>";
        } else {
            echo "<p>Erro ao cadastrar o livro.</p>";
        }
        $stmt->close();
    } else {
        echo "<p>Por favor, preencha todos os campos.</p>";
    }
}

// Exclusão de um livro
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'excluir') {
    $codigo = (int)$_POST['codigo'];

    $stmt = $conn->prepare("DELETE FROM livros WHERE codigo = ?");
    $stmt->bind_param("i", $codigo);

    if ($stmt->execute()) {
        echo "<p>Livro excluído com sucesso!</p>";
    } else {
        echo "<p>Erro ao excluir o livro.</p>";
    }
    $stmt->close();
}

// Alteração de um livro
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'alterar') {
    $codigo = (int)$_POST['codigo'];
    $nome = trim($_POST['nome']);
    $autor = trim($_POST['autor']);
    $editora = trim($_POST['editora']);
    $ano = (int)$_POST['ano'];

    $stmt = $conn->prepare("UPDATE livros SET nome = ?, autor = ?, editora = ?, ano = ? WHERE codigo = ?");
    $stmt->bind_param("sssii", $nome, $autor, $editora, $ano, $codigo);

    if ($stmt->execute()) {
        echo "<p>Livro atualizado com sucesso!</p>";
    } else {
        echo "<p>Erro ao atualizar o livro.</p>";
    }
    $stmt->close();
}

// Consulta de livros
$filtro = isset($_GET['filtro']) ? trim($_GET['filtro']) : '';
$query = "SELECT * FROM livros WHERE nome LIKE ? ORDER BY nome";
$stmt = $conn->prepare($query);
$searchTerm = '%' . $filtro . '%';
$stmt->bind_param("s", $searchTerm);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Livros</title>
</head>
<body>
    <h1>Bem-vindo, <?php echo htmlspecialchars($_SESSION['usuario']); ?>!</h1>

    <h2>Consultar Livros</h2>
    <form method="GET" action="">
        <input type="text" name="filtro" placeholder="Pesquisar por nome" value="<?php echo htmlspecialchars($filtro); ?>">
        <button type="submit">Pesquisar</button>
    </form>

    <h2>Lista de Livros</h2>
    <table border="1" cellpadding="5">
        <thead>
            <tr>
                <th>Código</th>
                <th>Nome</th>
                <th>Autor</th>
                <th>Editora</th>
                <th>Ano</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($livro = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $livro['codigo']; ?></td>
                    <td><?php echo htmlspecialchars($livro['nome']); ?></td>
                    <td><?php echo htmlspecialchars($livro['autor']); ?></td>
                    <td><?php echo htmlspecialchars($livro['editora']); ?></td>
                    <td><?php echo $livro['ano']; ?></td>
                    <td>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="codigo" value="<?php echo $livro['codigo']; ?>">
                            <input type="hidden" name="acao" value="excluir">
                            <button type="submit">Excluir</button>
                        </form>
                        <form method="GET" action="editar.php" style="display: inline;">
                            <input type="hidden" name="codigo" value="<?php echo $livro['codigo']; ?>">
                            <input type="hidden" name="nome" value="<?php echo htmlspecialchars($livro['nome']); ?>">
                            <input type="hidden" name="autor" value="<?php echo htmlspecialchars($livro['autor']); ?>">
                            <input type="hidden" name="editora" value="<?php echo htmlspecialchars($livro['editora']); ?>">
                            <input type="hidden" name="ano" value="<?php echo $livro['ano']; ?>">
                            <button type="submit">Alterar</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <h2>Cadastrar Novo Livro</h2>
    <form method="POST" action="">
        <input type="text" name="nome" placeholder="Nome do Livro" required>
        <input type="text" name="autor" placeholder="Autor" required>
        <input type="text" name="editora" placeholder="Editora" required>
        <input type="number" name="ano" placeholder="Ano de Publicação" required>
        <input type="hidden" name="acao" value="cadastrar">
        <button type="submit">Cadastrar</button>
    </form>

    <br>
    <a href="../logout.php">Sair</a>
</body>
</html>
