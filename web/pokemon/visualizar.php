<?php declare(strict_types=1);
session_start();
require_once("../utils/mensagem.php");

$id = null;
$imagem = null;
$nome = null;
$altura = null;
$peso = null;
$descricao = null;
$tipos = null;
$habilidades = null;

if (isset($_GET['id']) && isset($_GET['delete'])) {
    deletar();
}
else if (isset($_GET['id'])) {
    listar($id, $imagem, $nome, $altura, $peso, $descricao, $tipos, $habilidades);
}

function listar(&$id, &$imagem, &$nome, &$altura, &$peso, &$descricao, &$tipos, &$habilidades) : bool {
    $param_id = $_GET['id'];

    require_once("../conf/con_bd.php");
    
    if (!isset($con_bd)) {
        definir_mensagem("Erro de conexão com o banco de dados.", -1);
        header("Location: listar.php");
        exit;
    }
    
    $sql = "SELECT * FROM vw_pokemon WHERE id=$param_id;";
    $result = mysqli_query($con_bd, $sql);

    if (!$result) return false;
    if (mysqli_num_rows($result) !== 1) return false;

    $pokemon = mysqli_fetch_assoc($result);
    $id = $pokemon['id'];
    $imagem = $pokemon['imagem'];
    $nome = $pokemon['nome'];
    $altura = $pokemon['altura'];
    $peso = $pokemon['peso'];
    $descricao = $pokemon['descricao'];
    $tipos = json_decode($pokemon['tipos'], true);
    $habilidades = json_decode($pokemon['habilidades'], true);
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
        $sql = "CALL delete_pokemon('$param_id');";
        $result = mysqli_query($con_bd, $sql);
        
        if (!$result) {
            throw new Exception("Erro ao excluir Pokémon: " . mysqli_error($con_bd));
        }
        
        definir_mensagem("Pokémon excluído com sucesso.");

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
    <title>Visualizar Pokémon</title>
    <link rel="stylesheet" href="/global.css"/>
</head>

<style>
    /* Pode escrever CSS específico para essa página aqui, ou usar um arquivo para estilo global  */
    #pokemon-img {
        width: 200px;
    }
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
            <?php if ($id !== null && $imagem !== null && $nome !== null && $altura !== null && $peso !== null && $descricao !== null && $tipos !== null && $habilidades !== null): ?>
                <div class="card">
                    <h1>Detalhes do Pokémon</h1>
                    <div>
                        <img class="img" id="pokemon-img" src="<?=$imagem?>" alt="<?=$nome?>">
                        <p><strong>ID:</strong> <?= htmlspecialchars($id) ?></p>
                        <p><strong>Nome:</strong> <?= htmlspecialchars($nome) ?></p>
                        <p><strong>Altura:</strong> <?= htmlspecialchars($altura) ?>m</p>
                        <p><strong>Peso:</strong> <?= htmlspecialchars($peso) ?>kg</p>
                        <p><strong>Descrição:</strong> <?= htmlspecialchars($descricao) ?></p>
                        <p><strong>Tipos:</strong>
                        <?php
                            foreach ($tipos as $tipo) 
                            {
                                ?>
                                <a class='btn info'><?=htmlspecialchars($tipo)?></a>
                                <?php
                            }
                        ?>
                        </p>
                        <p><strong>Habilidades:</strong>
                        <?php
                            foreach ($habilidades as $habilidade) 
                            {
                                ?>
                                <a class='btn info'><?=htmlspecialchars($habilidade)?></a>
                                <?php
                            }
                        ?>
                        </p>
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
                        definir_mensagem("Pokémon não encontrado ou ID inválido.", -1);
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