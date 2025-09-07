<?php declare(strict_types=1);
session_start();
require_once("../utils/mensagem.php");

$id = null;
$nome = null;
$descricao = null;

if (isset($_GET['id']) && isset($_GET['delete'])) {
    deletar();
}
else if (isset($_GET['id'])) {
    listar($id, $nome, $descricao);
}

function listar(&$id, &$nome, &$descricao) : bool {
    $param_id = $_GET['id'];

    require_once("../conf/con_bd.php");
    
    if (!isset($con_bd)) {
        definir_mensagem("Erro de conexão com o banco de dados.", -1);
        header("Location: listar.php");
        exit;
    }
    
    $sql = "SELECT * FROM habilidade WHERE id=$param_id;";
    $result = mysqli_query($con_bd, $sql);

    if (!$result) return false;
    if (mysqli_num_rows($result) !== 1) return false;

    $habilidade = mysqli_fetch_assoc($result);
    $id = $habilidade['id'];
    $nome = $habilidade['nome'];
    $descricao = $habilidade['descricao'];
    return true;
}

function deletar() {
    $param_id = $_GET['id'];

    require_once("../conf/con_bd.php");

    if (!isset($con_bd)) {
        definir_mensagem("Erro de conexão com o banco de dados.", -1);
        header("Location: listar.php");
        exit;
    }

    try {
        $sql = "CALL delete_habilidade('$param_id');";
        $result = mysqli_query($con_bd, $sql);
        
        if (!$result) {
            throw new Exception("Erro ao excluir habilidade: " . mysqli_error($con_bd));
        }
        
        definir_mensagem("Habilidade excluída com sucesso.");

    } catch (Exception $e) {
        definir_mensagem($e->getMessage(), -1);
    }

    header("Location: listar.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualizar Habilidade</title>
    <link rel="stylesheet" href="/global.css"/>
</head>

<style>
    /* Pode escrever CSS específico para essa página aqui, ou usar um arquivo para estilo global  */
</style>

<body>
    <div class="container">
        <header class="header">
            <h1>Cadastro de Pokémon</h1>
        </header>
        <nav class="col-nav">
            <div>
                <p>Início</p>
                <a href="/">Ir ao início</a>
            </div>
            <div>
                <p>Pokémon</p>
                <a href="/pokemon/cadastrar.php">Cadastrar Pokémon</a>
                <a href="/pokemon/listar.php">Listar Pokémons</a>
            </div>
            <div>
                <p>Tipo</p>
                <a href="/tipo/cadastrar.php">Cadastrar Tipo</a>
                <a href="/tipo/listar.php">Listar Tipos</a>
            </div>
            <div>
                <p>Habilidade</p>
                <a href="/habilidade/cadastrar.php">Cadastrar Habilidade</a>
                <a href="/habilidade/listar.php">Listar Habilidades</a>
            </div>
        </nav>
        <div class="col-main">
            <?php if ($id !== null && $nome !== null && $descricao !== null): ?>
                <div class="card">
                    <h1>Detalhes da Habilidade</h1>
                    <div>
                        <p><strong>ID:</strong> <?= htmlspecialchars($id) ?></p>
                        <p><strong>Nome:</strong> <?= htmlspecialchars($nome) ?></p>
                        <p><strong>Descrição:</strong> <?= htmlspecialchars($descricao) ?></p>
                    </div>
                    <div>
                        <a class="btn" href="listar.php">Voltar</a>
                        <a class="btn deletar" href="visualizar.php?id=<?=$id?>&delete">Excluir</a>
                        <a class="btn atualizar" href="cadastrar.php?id=<?=$id?>">Atualizar</a>
                    </div>
                </div>
            <?php else: ?>
                <div>
                    <?php
                        definir_mensagem("Habilidade não encontrada ou ID inválido.", -1);
                        exibir_mensagem();
                    ?>
                    <a class="btn" href="listar.php">Voltar</a>
                </div>
            <?php endif; ?>
        </div>
        <footer class="footer">
            <h3>Trabalho desenvolvido para a disciplina de Tópicos Especiais em Desenvolvimento de Sistemas I</h3>
            <strong>Grupo Miku Warriors</strong>
            <span>Juliana Schorro Bach, Lucas Tozo Monção, Livia Elias Cardoso Verhalen</span>
        </footer>
    </div>
</body>
</html>