<?php
function exibir_mensagem() {
    if (isset($_SESSION['flash_msg'])) {
        $class = isset($_SESSION['flash_status']) && $_SESSION['flash_status'] === 0 ? 'sucesso' : 'erro';
        echo '<div class="msg ' . $class . '">' . htmlspecialchars($_SESSION['flash_msg']) . '</div>';

        unset($_SESSION['flash_msg'], $_SESSION['flash_status']);
    }
}

function definir_mensagem($message, $status = 0) {
    $_SESSION['flash_msg'] = $message;
    $_SESSION['flash_status'] = $status;
}

?>