<?php

$conn = new mysqli(
    "localhost",
    "root",
    "cefet123",
    "projeto_final"
);

if ($conn->connect_error) {
    die("Erro: " . $conn->connect_error);
}

?>