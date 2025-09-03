<?php declare(strict_types=1);
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $imagem = $_POST['imagem'];
    $nome = $_POST['nome'];
    $altura = $_POST['altura'];
    $peso = $_POST['peso'];
    $descricao = $_POST['descricao'];
    $tipos = $_POST['tipos'];
    $habilidades = $_POST['habilidades'];

    function validar($imagem, $nome, $altura, $peso, $descricao, $tipos, $habilidades) : bool {
        if (
            !isset($imagem) || !isset($nome) || !isset($altura) || !isset($peso) || 
            !isset($descricao) || !isset($tipos) || !isset($habilidades)
            ) 
        {
            return false;
        }

        if (is_numeric($nome)) return false;
        if (mb_strlen($nome) > 50) return false;

        if (!is_numeric($altura)) return false;
        $_altura_cast = (float)$altura;
        if ($_altura_cast < 0 || $_altura_cast > 10) return false;

        if (!is_numeric($peso)) return false;
        $_peso_cast = (float)$peso;
        if ($_peso_cast < 0) return false;

        if (is_numeric($descricao)) return false;
        if (mb_strlen($descricao) > 255) return false;

        if (count($tipos) > 2) return false;
        if (count($habilidades) > 2) return false;

        return true;
    }

    require_once("../conf/con_bd.php");
    $dados_validos = validar($imagem, $nome, $altura, $peso, $descricao, $tipos, $habilidades);

    try {
        if (!$dados_validos) {
            throw new Exception("Dados preenchidos inválidos.");
        }

        if (!isset($con_bd)) {
            throw new Exception("Conexão com o banco de dados falhou.");
        }

        $imagem = mysqli_real_escape_string($con_bd, $imagem);
        $nome = mysqli_real_escape_string($con_bd, $nome);
        $altura = mysqli_real_escape_string($con_bd, $altura);
        $peso = mysqli_real_escape_string($con_bd, $peso);
        $descricao = mysqli_real_escape_string($con_bd, $descricao);

        $sql = ""; // todo: ajustar depois
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
    <title>Cadastrar Pokémon</title>
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
                <h1>Cadastrar Pokémon</h1>
                <form action="" method="POST">
                    <div id="div_imagem" class="campo">
                        <label for="imagem">Imagem:</label>
                        <input type="file" id="imagem" name="imagem" required accept=".png,.jpg,.jpeg"/>
                    </div>
                    <div id="div_nome" class="campo">
                        <label for="nome">Nome:</label>
                        <input type="text" id="nome" name="nome" maxlength="50" required pattern="[a-zA-Z]*" placeholder="Charmander"/>
                    </div>
                    <div id="div_altura" class="campo">
                        <label for="altura">Altura (m):</label>
                        <input type="number" id="altura" name="altura" required min="0" max="10" step="0.01" placeholder="0.6"/>
                    </div>
                    <div id="div_peso" class="campo">
                        <label for="peso">Peso (kg):</label>
                        <input type="number" id="peso" name="peso" required step="0.01" min="0" placeholder="8.5"/>
                    </div>
                    <div id="div_descricao" class="campo">
                        <label for="descricao">Descrição:</label> <br> <!-- quebra linha -->
                        <textarea id="descricao" name="descricao" required maxlength="255" rows="5" cols="30" placeholder="The flame on its tail shows the strength of its life-force. If Charmander is weak, the flame also burns weakly."></textarea>
                    </div>
                    <div id="div_tipos" class="campo">
                        <label for="tipos">Tipos:</label>
                        <select id="tipos" name="tipos[]" required multiple>
                            <?php
                            require_once("../conf/con_bd.php");
                            if (isset($con_bd)) {
                                $sql = "SELECT id, nome FROM tipo";
                                $result = mysqli_query($con_bd, $sql);
                                while ($tipos = mysqli_fetch_assoc($result)) {
                                    echo "<option value='{$tipos['id']}'>{$tipos['nome']}</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div id="div_habilidades" class="campo">
                        <label for="habilidades">Habilidades:</label>
                        <select id="habilidades" name="habilidades[]" required multiple>
                            <?php
                            require_once("../conf/con_bd.php");
                            if (isset($con_bd)) {
                                $sql = "SELECT id, nome FROM habilidade";
                                $result = mysqli_query($con_bd, $sql);
                                while ($habilidades = mysqli_fetch_assoc($result)) {
                                    echo "<option value='{$habilidades['id']}'>{$habilidades['nome']}</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div id="div_enviar">
                        <button class="btn cadastrar" type="submit">Cadastrar</button>
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