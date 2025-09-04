<?php declare(strict_types=1);
session_start();
require_once("../utils/mensagem.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualizar Pokémons</title>
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
            <table>
                <?php
                    exibir_mensagem();
                ?>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Altura</th>
                    <th>Peso</th>
                    <th>Descrição</th>
                    <th>Tipos</th>
                    <th>Habilidades</th>
                    <th>Ações</th>
                </tr>
                <tr>
                    <?php
                    require_once("../conf/con_bd.php");

                    if (isset($con_bd)) {
                        $sql = "SELECT * FROM pokemon ORDER BY id";
                        $result = mysqli_query($con_bd, $sql);
                        
                        if ($result) 
                        {
                            if (mysqli_num_rows($result) > 0) 
                            {
                                while ($pokemon = mysqli_fetch_assoc($result)) 
                                {
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($pokemon['id']) . "</td>";
                                    echo "<td>" . htmlspecialchars($pokemon['nome']) . "</td>";
                                    echo "<td>" . htmlspecialchars($pokemon['altura']) . "</td>";
                                    echo "<td>" . htmlspecialchars($pokemon['peso']) . "</td>";
                                    echo "<td>" . htmlspecialchars($pokemon['descricao']) . "</td>";
                                    echo "<td>" . htmlspecialchars($pokemon['tipos']) . "</td>";
                                    echo "<td>" . htmlspecialchars($pokemon['habilidades']) . "</td>";
                                    echo "<td>";
                                    echo "<a href='visualizar.php?id=" . $pokemon['id'] . "'>Visualizar</a>";
                                    echo "</td>";
                                    echo "</tr>";
                                }
                            } 
                            else echo "<tr><td colspan='8' class='msg erro'>Nenhum Pokémon encontrado.</td></tr>";
                        } 
                        else echo "<tr><td colspan='8' class='msg erro'>Erro ao buscar Pokémons: " . mysqli_error($con_bd) . "</td></tr>";
                    } 
                    else echo "<tr><td colspan='8' class='msg erro'>Erro de conexão com o banco de dados.</td></tr>";
                    ?>
                </tr>
            </table>
        </div>
        <footer class="footer">
            <h3>Trabalho desenvolvido para a disciplina de Tópicos Especiais em Desenvolvimento de Sistemas I</h3>
            <strong>Grupo Miku Warriors</strong>
            <span>Juliana Schorro Bach, Lucas Tozo Monção, Livia Elias Cardoso Verhalen</span>
        </footer>
    </div>
</body>
</html>