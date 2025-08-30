<?php declare(strict_types=1);
$mensagem = "";
$tipoMensagem = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $imagem = $_POST['imagem'];
    $nome = $_POST['nome'];
    $altura = $_POST['altura'];
    $peso = $_POST['peso'];
    $descricao = $_POST['descricao'];
    $tipo1 = $_POST['tipo1'];
    $tipo2 = $_POST['tipo2'];
    $habilidades = $_POST['habilidades'];
    
    // Logica para validar dados, restriçoes
    // Nome: texto, maxlength 50
    // Altura: numero, 0 <= altura <= 10
    // Peso: numero, 0 <= peso
    // Descricao: texto, maxlength 255

    function validar($imagem, $nome, $altura, $peso, $descricao, $tipo1, $tipo2, $habilidades) : bool {
        if (
            !isset($imagem) || !isset($nome) || !isset($altura) || !isset($peso) || 
            !isset($descricao) || !isset($tipo1) || !isset($tipo2) || !isset($habilidades)
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

        if ($tipo1 === $tipo2) return false;

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
        <div id="div_tipo1" class="campo">
            <label for="tipo1">Tipo 1:</label>
            <select id="tipo1" name="tipo1" required>
                <option value="bug">Bug</option>
                <option value="dragao">Dragão</option>
                <option value="fada">Fada</option>
                <option value="fogo">Fogo</option>
                <option value="ghost">Ghost</option>
                <option value="ground">Ground</option>
                <option value="normal">Normal</option>
                <option value="psiquico">Psíquico</option>
                <option value="steel">Steel</option>
                <option value="dark">Dark</option>
                <option value="electric">Electric</option>
                <option value="luta">Luta</option>
                <option value="flying">Flying</option>
                <option value="planta">Planta</option>
                <option value="ice">Ice</option>
                <option value="poison">Poison</option>
                <option value="rock">Rock</option>
                <option value="agua">Água</option>
            </select>
        </div>
        <div id="div_tipo2" class="campo">
            <label for="tipo2">Tipo 2 (Se aplicável):</label>
            <select id="tipo2" name="tipo2" required>
                <optgroup label="Não aplicável">
                    <option value="na">N/A</option>
                </optgroup>
                    <optgroup label="Aplicável">
                    <option value="bug">Bug</option>
                    <option value="dragao">Dragão</option>
                    <option value="fada">Fada</option>
                    <option value="fogo">Fogo</option>
                    <option value="ghost">Ghost</option>
                    <option value="ground">Ground</option>
                    <option value="normal">Normal</option>
                    <option value="psiquico">Psíquico</option>
                    <option value="steel">Steel</option>
                    <option value="dark">Dark</option>
                    <option value="electric">Electric</option>
                    <option value="luta">Luta</option>
                    <option value="flying">Flying</option>
                    <option value="planta">Planta</option>
                    <option value="ice">Ice</option>
                    <option value="poison">Poison</option>
                    <option value="rock">Rock</option>
                    <option value="agua">Água</option>
                </optgroup>
            </select>
        </div>
        <div id="div_enviar">
            <button type="submit">Cadastrar</button>
        </div>
    </form>

</body>
</html>