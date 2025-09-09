<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Início</title>
    <link rel="stylesheet" href="global.css"/>
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
            <?php
                session_start();
                require_once("conf/con_bd.php");
                if (isset($con_bd)) {
                    $sql = "SELECT id, imagem, nome FROM pokemon ORDER BY rand()";
                    $result = mysqli_query($con_bd, $sql);
                    if ($result && mysqli_num_rows($result) > 0) {
                        ?>
                        <div class="cards">
                        <?php
                        define("LIMITE_CARDS_QTD", 4);
                        $i = 0;
                        while (($pokemon = mysqli_fetch_assoc($result)) && $i < LIMITE_CARDS_QTD) {
                            // Tirar o "../" do path
                            $path_img = substr($pokemon['imagem'], 3);
                            ?>
                            <div class="pokemon-card">
                                <img src="<?=$path_img?>" alt="<?=$pokemon['nome']?>">
                                <div class="info">
                                    <a href="pokemon/visualizar.php?id=<?=$pokemon['id']?>"><?=$pokemon['nome']?></a>
                                </div>
                            </div>
                            <?php
                            $i++;
                        }
                        ?>
                        </div>
                        <?php
                    }
                }
            ?>
        </div>
        <footer class="footer">
            <h3>Trabalho desenvolvido para a disciplina de Tópicos Especiais em Desenvolvimento de Sistemas I</h3>
            <strong>Grupo Miku Warriors</strong>
            <span>Juliana Schorro Bach, Lucas Tozo Monção, Livia Elias Cardoso Verhalen</span>
        </footer>
    </div>
</body>
</html>
