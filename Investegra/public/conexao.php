<?php

$conn = new mysqli(
    "mysql",
    "root",
    "123456",
    "Investegra"
);

if ($conn->connect_error) {die("Erro: " . $conn->connect_error);}

?>