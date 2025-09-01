<?php declare(strict_types=1);
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $descricao = $_POST['descricao'];

    function validar($nome, $descricao) : bool {
        if (!isset($nome) || !isset($descricao)) 
        {
            return false;
        }

        if (is_numeric($nome)) return false;
        if (mb_strlen($nome) > 30) return false;

        if (is_numeric($descricao)) return false;
        if (mb_strlen($descricao) > 255) return false;

        return true;
    }

    require_once("../conf/con_bd.php");
    $dados_validos = validar($nome, $descricao);
    try {
        if (!$dados_validos) {
            throw new Exception("Dados preenchidos inválidos.");
        }

        if (!isset($con_bd)) {
            throw new Exception("Conexão com o banco de dados falhou.");
        }

        $nome = mysqli_real_escape_string($con_bd, $nome);
        $descricao = mysqli_real_escape_string($con_bd, $descricao);

        $sql = "";
        $result = mysqli_query($con_bd, $sql);
        
        if (!$result) {
            throw new Exception("Falha ao cadastrar dados: " . mysqli_error($con_bd));
        }
        
        $_SESSION['flash_msg'] = "Dados cadastrados com sucesso.";
        $_SESSION['flash_status'] = 0;
        
    } catch (Exception $e) {
        $_SESSION['flash_msg'] = $e->getMessage();
        $_SESSION['flash_status'] = -1;
    }
    
    if (isset($con_bd_err_code)) {
        $_SESSION['flash_msg'] = "Erro com o banco de dados. Código: " . $con_bd_err_code;
        $_SESSION['flash_status'] = -1;
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Habilidade</title>
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
            <div class="form">
                <h1>Cadastrar Habilidade</h1>
                <form action="" method="POST">
                    <div id="div_nome" class="campo">
                        <label for="nome">Nome:</label>
                        <input type="text" id="nome" name="nome" maxlength="30" required pattern="[a-zA-Z]*" placeholder="Overgrow"/>
                    </div>
                    <div id="div_descricao" class="campo">
                        <label for="descricao">Descrição:</label> <br> <!-- quebra linha -->
                        <textarea id="descricao" name="descricao" required maxlength="255" rows="5" cols="30" placeholder="Powers up Grass-type moves when the Pokémon’s HP is low."></textarea>
                    </div>
                    <div id="div_enviar">
                        <button type="submit">Cadastrar</button>
                    </div>
                    <?php if (isset($_SESSION['flash_msg'])): ?>
                        <div class="msg <?= $_SESSION['flash_status'] === 0 ? 'sucesso' : 'erro' ?>">
                            <?= htmlspecialchars($_SESSION['flash_msg']) ?>
                        </div>
                        <?php 
                            unset($_SESSION['flash_msg'], $_SESSION['flash_status']); 
                        ?>
                    <?php endif; ?>
                </form>
            </div>
        </div>
        <footer class="footer">
            <h3>Trabalho desenvolvido para a disciplina de Tópicos Especiais em Desenvolvimento de Sistemas I</h3>
            <strong>Grupo Miku Warriors</strong>
            <span>Juliana Schorro Bach, Lucas Tozo Monção, Livia Elias Cardoso Verhalen</span>
        </footer>
    </div>
</body>
</html>