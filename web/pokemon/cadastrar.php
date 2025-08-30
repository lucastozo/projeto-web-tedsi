<?php declare(strict_types=1);
$mensagem = "";
$tipoMensagem = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $imagem = $_POST['imagem'];
    $nome = $_POST['nome'];
    $altura = $_POST['altura'];
    $peso = $_POST['peso'];
    $descricao = $_POST['descricao'];
    $tipos = $_POST['tipos'];
    $habilidades = $_POST['habilidades'];
    
    // Logica para validar dados, restriçoes
    // Nome: texto, maxlength 50
    // Altura: numero, 0 <= altura <= 10
    // Peso: numero, 0 <= peso
    // Descricao: texto, maxlength 255

    function validar($imagem, $nome, $altura, $peso, $descricao, $habilidades) : bool {
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

        return true;
    }
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
    form {
    padding: 20px;
    max-width: 400px;
    }

    form, form div input, form div textarea {
        border: 1px solid #aaa;
    }

    form div.campo {
        margin-bottom: 12px;
    }

    form div input, form div textarea, form div select {
        width: 100%;
        padding: 6px;
        box-sizing: border-box;
        resize: none;
    }

    form div button {
        padding: 15px;
        cursor: pointer;
    }

    .msg-sucesso {
        background: #d4edda;
        color: #155724;
        padding: 10px;
        margin-bottom: 15px;
        border: 1px solid #c3e6cb;
        border-radius: 5px;
    }

    .msg-erro {
        background: #f8d7da;
        color: #721c24;
        padding: 10px;
        margin-bottom: 15px;
        border: 1px solid #f5c6cb;
        border-radius: 5px;
    }
</style>

<body>
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
                    $tipos = mysqli_fetch_assoc($result);
                    do {
                        echo "<option value='{$tipos['id']}'>{$tipos['tipo']}</option>";
                    } while ($tipos !== null);
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
                    $habilidades = mysqli_fetch_assoc($result);
                    do {
                        echo "<option value='{$habilidades['id']}'>{$habilidades['nome']}</option>";
                    } while ($habilidades !== null);
                }
                ?>
            </select>
        </div>
        <div id="div_enviar">
            <button type="submit">Cadastrar</button>
        </div>
    </form>

</body>
</html>