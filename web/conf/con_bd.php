<?php

define("ENDERECO", "localhost");
define("USUARIO", "root");
define("SENHA", "root");
define("BD_NOME", "pokemon-tedsi");
define("PORTA", 3306);

$con_bd;

try {
    $con_bd = mysqli_connect(
        ENDERECO,
        USUARIO,
        SENHA,
        BD_NOME,
        PORTA
    );
} catch (Exception $e) {
    echo "Erro conectando ao banco de dados!"
        . " <br>Código do erro: ".$e->getCode();
}

?>