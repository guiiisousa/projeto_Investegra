<?php

$carteira_id =
$_GET["carteira"];

?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Novo Ativo</title>
</head>

<body>

<h2>Cadastrar Ativo</h2>

<form
action="salvar_ativo.php"
method="POST">

<input
type="hidden"
name="carteira_id"
value="<?= $carteira_id ?>">

<input
type="text"
name="ticker"
placeholder="Ticker (VALE3)"
required>

<br><br>

<input
type="number"
name="quantidade"
placeholder="Quantidade"
required>

<br><br>

<input
type="number"
step="0.01"
name="preco_medio"
placeholder="Preço Médio"
required>

<br><br>

<select
name="categoria"
required>

    <option value="">
        Selecione a categoria
    </option>

    <option value="Ações">
        Ações
    </option>

    <option value="FIIs">
        FIIs
    </option>

    <option value="ETFs">
        ETFs
    </option>

    <option value="Renda Fixa">
        Renda Fixa
    </option>

    <option value="Criptomoedas">
        Criptomoedas
    </option>

</select>

<br><br>

<button type="submit">

Salvar

</button>

</form>

</body>
</html>