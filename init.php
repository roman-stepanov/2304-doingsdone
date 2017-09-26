<?php
$connect = mysqli_connect('localhost', 'root', '', 'doingsdone');

if ($connect === false) {
    $error = render_template('templates/error.php', ['error' => mysqli_connect_error()]);
    print($error);
    exit();
}
