<?php
define("ENDERECO", "127.0.0.1");
define("USUARIO", "root");
define("SENHA", "root");
define("BD_NOME", "pokemon_tedsi");
define("PORTA", 3306);

$con_bd = null;
$con_bd_err_code = null;

try {
    $con_bd = mysqli_connect(
        ENDERECO,
        USUARIO,
        SENHA,
        BD_NOME,
        PORTA
    );
} catch (Exception $e) {
    $con_bd_err_code = $e->getCode();
}
?>