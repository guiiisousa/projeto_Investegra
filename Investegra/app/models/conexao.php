<?php

$conn = new mysqli(
    "localhost",
    "root",
    "Beto2009!",
    "Projeto_Final"
);

if ($conn->connect_error) {
    die("Erro: " . $conn->connect_error);
}

?>