<?php declare(strict_types=1);
session_start();
require_once("../utils/mensagem.php");

$eh_edicao = isset($_GET['id']) && !empty($_GET['id']);
$id_habilidade = $eh_edicao ? (int)$_GET['id'] : null;
$habilidade_atual = null;

if ($eh_edicao) {
    require_once("../conf/con_bd.php");
    
    if (isset($con_bd)) {
        $sql = "SELECT * FROM habilidade WHERE id = $id_habilidade";
        $result = mysqli_query($con_bd, $sql);
        
        if ($result && mysqli_num_rows($result) > 0) {
            $habilidade_atual = mysqli_fetch_assoc($result);
        } else {
            definir_mensagem("Habilidade não encontrada.", -1);
            header("Location: listar.php");
            exit;
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $descricao = $_POST['descricao'];
    $id_para_atualizar = isset($_POST['id']) ? (int)$_POST['id'] : null;

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

        if ($id_para_atualizar) {
            $sql = "CALL atualizar_habilidade($id_para_atualizar, '$nome', '$descricao');";
            $success_msg = "Habilidade atualizada com sucesso.";
        } else {
            $sql = "CALL nova_habilidade('$nome', '$descricao');";
            $success_msg = "Habilidade cadastrada com sucesso.";
        }
        
        $result = mysqli_query($con_bd, $sql);
        
        if (!$result) {
            throw new Exception("Falha ao " . ($id_para_atualizar ? "atualizar" : "cadastrar") . " dados: " . mysqli_error($con_bd));
        }
        
        definir_mensagem($success_msg);
        
    } catch (Exception $e) {
        definir_mensagem($e->getMessage(), -1);
    }
    
    if (isset($con_bd_err_code)) {
        definir_mensagem("Erro com o banco de dados. Código: " . $con_bd_err_code, -1);
    }

    if ($id_para_atualizar) {
        header("Location: listar.php");
    } else {
        header("Location: " . $_SERVER['PHP_SELF']);
    }
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
                <h1><?php echo $eh_edicao ? 'Atualizar' : 'Cadastrar'; ?> Habilidade</h1>

                <form action="" method="POST">
                    <?php if ($eh_edicao): ?>
                        <input type="hidden" name="id" value="<?php echo $id_habilidade; ?>">
                    <?php endif; ?>

                    <div id="div_nome" class="campo">
                        <label for="nome">Nome:</label>
                        <input 
                            type="text" 
                            id="nome" 
                            name="nome" 
                            maxlength="30" 
                            required 
                            pattern="[a-zA-Z]*" 
                            placeholder="Overgrow"
                            value="<?php echo $habilidade_atual ? htmlspecialchars($habilidade_atual['nome']) : ''; ?>"
                        />
                    </div>
                    <div id="div_descricao" class="campo">
                        <label for="descricao">Descrição:</label> <br> <!-- quebra linha -->
                        <textarea 
                            id="descricao" 
                            name="descricao" 
                            required 
                            maxlength="255" 
                            rows="5" 
                            cols="30" 
                            placeholder="Powers up Grass-type moves when the Pokémon’s HP is low."
                            ><?php echo $habilidade_atual ? htmlspecialchars($habilidade_atual['descricao']) : ''; ?></textarea>
                    </div>
                    <div id="div_enviar">
                        <button class="btn cadastrar" type="submit">
                            <?php echo $eh_edicao ? 'Atualizar' : 'Cadastrar'; ?>
                        </button>
                        <?php if ($eh_edicao): ?>
                            <a class="btn" href="listar.php">Cancelar</a>
                        <?php endif; ?>
                    </div>
                    <?php
                        exibir_mensagem();
                    ?>
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