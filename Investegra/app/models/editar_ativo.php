<?php

include("conexao.php");

$id =
$_GET["id"];

$sql =
"SELECT * FROM ativos WHERE id = ?";

$stmt =
$conn->prepare($sql);

$stmt->bind_param(
    "i",
    $id
);

$stmt->execute();

$ativo =
$stmt->get_result()->fetch_assoc();

?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Editar Ativo</title>
</head>

<body>

<h2>Editar Ativo</h2>

<form
action="atualizar_ativo.php"
method="POST">

<input
type="hidden"
name="id"
value="<?= $ativo["id"] ?>">

<input
type="hidden"
name="carteira_id"
value="<?= $ativo["carteira_id"] ?>">

<input
type="text"
name="ticker"
value="<?= htmlspecialchars($ativo["ticker"]) ?>"
required>

<br><br>

<input
type="number"
name="quantidade"
value="<?= $ativo["quantidade"] ?>"
required>

<br><br>

<input
type="number"
step="0.01"
name="preco_medio"
value="<?= $ativo["preco_medio"] ?>"
required>

<br><br>

<select
name="categoria"
required>

<option value="Ações"
<?= $ativo["categoria"] == "Ações" ? "selected" : "" ?>>
Ações
</option>

<option value="FIIs"
<?= $ativo["categoria"] == "FIIs" ? "selected" : "" ?>>
FIIs
</option>

<option value="ETFs"
<?= $ativo["categoria"] == "ETFs" ? "selected" : "" ?>>
ETFs
</option>

<option value="Renda Fixa"
<?= $ativo["categoria"] == "Renda Fixa" ? "selected" : "" ?>>
Renda Fixa
</option>

<option value="Criptomoedas"
<?= $ativo["categoria"] == "Criptomoedas" ? "selected" : "" ?>>
Criptomoedas
</option>

</select>

<br><br>

<button type="submit">

Salvar Alterações

</button>

</form>

</body>
</html>