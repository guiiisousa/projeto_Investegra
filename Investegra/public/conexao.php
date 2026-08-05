<?php

$conn = new mysqli(
    "investegra",
    "root",
    "123456",
    "investegra"
);

if ($conn->connect_error) {die("Erro: " . $conn->connect_error);}

?>