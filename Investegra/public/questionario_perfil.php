<?php

session_start();

if(!isset($_SESSION["id"])){

    header("Location: login.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>

<meta charset="UTF-8">

<meta
name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Quiz Inteligente</title>

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Segoe UI',sans-serif;
}

body{

    background:
    linear-gradient(
        135deg,
        #0f172a,
        #111827,
        #1f2937
    );

    min-height:100vh;

    display:flex;

    justify-content:center;

    align-items:center;

    padding:30px;
}

/* ==========================
   CONTAINER
========================== */

.container{

    width:100%;

    max-width:850px;

    background:white;

    border-radius:30px;

    overflow:hidden;

    box-shadow:
    0 20px 50px rgba(0,0,0,.25);
}

/* ==========================
   TOPO
========================== */

.topo{

    padding:35px;

    background:#00a86b;

    color:white;
}

.topo h1{

    margin-bottom:10px;

    font-size:34px;
}

.topo p{

    opacity:.9;

    line-height:1.7;
}

/* ==========================
   PROGRESSO
========================== */

.barra{

    width:100%;

    height:8px;

    background:#d1d5db;
}

.progresso{

    height:100%;

    width:12.5%;

    background:#111827;

    transition:.4s;
}

/* ==========================
   FORM
========================== */

form{

    padding:45px;
}

/* ==========================
   CARD
========================== */

.card{

    display:none;

    animation:fade .4s ease;
}

.card.ativo{

    display:block;
}

@keyframes fade{

    from{

        opacity:0;
        transform:translateX(20px);
    }

    to{

        opacity:1;
        transform:translateX(0);
    }
}

.card h2{

    color:#111827;

    margin-bottom:30px;

    line-height:1.5;
}

/* ==========================
   INPUTS
========================== */

input,
select,
textarea{

    width:100%;

    padding:16px;

    border:1px solid #d1d5db;

    border-radius:14px;

    font-size:16px;
}

textarea{

    resize:none;

    height:180px;
}

input:focus,
select:focus,
textarea:focus{

    outline:none;

    border-color:#00a86b;

    box-shadow:
    0 0 0 5px rgba(0,168,107,.12);
}

.opcao{

    display:block;

    background:#f9fafb;

    padding:18px;

    border-radius:14px;

    margin-bottom:15px;

    cursor:pointer;

    transition:.3s;

    border:2px solid transparent;
}

.opcao:hover{

    border-color:#00a86b;

    background:#ecfdf5;
}

.opcao input{

    width:auto;

    margin-right:10px;
}

/* ==========================
   BOTÕES
========================== */

.botoes{

    margin-top:35px;

    display:flex;

    justify-content:space-between;

    gap:15px;
}

button{

    flex:1;

    padding:16px;

    border:none;

    border-radius:14px;

    font-size:16px;

    font-weight:bold;

    cursor:pointer;

    transition:.3s;
}

.btn-voltar{

    background:#e5e7eb;

    color:#111827;
}

.btn-voltar:hover{

    background:#d1d5db;
}

.btn-proximo{

    background:#00a86b;

    color:white;
}

.btn-proximo:hover{

    background:#00915d;
}

</style>

</head>

<body>

<div class="container">

    <div class="topo">

        <h1>

            🧠 Quiz Inteligente

        </h1>

        <p>

            O INVESTEGRA irá analisar
            seu comportamento financeiro
            e identificar seu perfil de investidor.

        </p>

    </div>

    <div class="barra">

        <div
        class="progresso"
        id="progresso"></div>

    </div>

    <form
    action="processar_questionario.php"
    method="POST">

        <!-- CARD 1 -->

        <div class="card ativo">

            <h2>

                Qual sua idade?

            </h2>

            <input
            type="number"
            name="idade"
            required>

        </div>

        <!-- CARD 2 -->

        <div class="card">

            <h2>

                Qual seu nível de experiência
                com investimentos?

            </h2>

            <select
            name="experiencia"
            required>

                <option value="Nenhuma">

                    Nenhuma

                </option>

                <option value="Básica">

                    Básica

                </option>

                <option value="Intermediária">

                    Intermediária

                </option>

                <option value="Avançada">

                    Avançada

                </option>

            </select>

        </div>

        <!-- CARD 3 -->

        <div class="card">

            <h2>

                Qual sua renda mensal aproximada?

            </h2>

            <input
            type="number"
            step="0.01"
            name="renda_mensal"
            required>

        </div>

        <!-- CARD 4 -->

        <div class="card">

            <h2>

                Qual seu horizonte de investimento?

            </h2>

            <select
            name="horizonte"
            required>

                <option value="Curto Prazo">

                    Curto Prazo

                </option>

                <option value="Médio Prazo">

                    Médio Prazo

                </option>

                <option value="Longo Prazo">

                    Longo Prazo

                </option>

            </select>

        </div>

        <!-- CARD 5 -->

        <div class="card">

            <h2>

                Como você reage
                a oscilações no mercado?

            </h2>

            <label class="opcao">

                <input
                type="radio"
                name="tolerancia"
                value="Baixa"
                required>

                Fico desconfortável rapidamente

            </label>

            <label class="opcao">

                <input
                type="radio"
                name="tolerancia"
                value="Média">

                Tolero pequenas oscilações

            </label>

            <label class="opcao">

                <input
                type="radio"
                name="tolerancia"
                value="Alta">

                Aceito volatilidade
                por maior retorno

            </label>

        </div>

        <!-- CARD 6 -->

        <div class="card">

            <h2>

                O que faria se sua carteira
                caísse 30%?

            </h2>

            <label class="opcao">

                <input
                type="radio"
                name="queda"
                value="1"
                required>

                Venderia tudo

            </label>

            <label class="opcao">

                <input
                type="radio"
                name="queda"
                value="2">

                Esperaria recuperação

            </label>

            <label class="opcao">

                <input
                type="radio"
                name="queda"
                value="3">

                Compraria mais

            </label>

        </div>

        <!-- CARD 7 -->

        <div class="card">

            <h2>

                Qual seu principal
                objetivo financeiro?

            </h2>

            <textarea
            name="objetivo"
            required></textarea>

        </div>

        <!-- CARD 8 -->

        <div class="card">

            <h2>

                Descreva sua relação
                com dinheiro, riscos
                e expectativas financeiras

            </h2>

            <textarea
            name="descricao_pessoal"
            required></textarea>

        </div>

        <!-- BOTÕES -->

        <div class="botoes">

            <button
            type="button"
            class="btn-voltar"
            onclick="voltar()">

                Voltar

            </button>

            <button
            type="button"
            class="btn-proximo"
            onclick="proximo()"
            id="btnProximo">

                Próximo

            </button>

        </div>

    </form>

</div>

<script>

const cards =
document.querySelectorAll(".card");

const progresso =
document.getElementById(
"progresso"
);

const btn =
document.getElementById(
"btnProximo"
);

let atual = 0;

/* ==========================
   MOSTRAR CARD
========================== */

function mostrarCard(index){

    cards.forEach(card => {

        card.classList.remove("ativo");

    });

    cards[index]
    .classList.add("ativo");

    const porcentagem =

    ((index + 1)
    / cards.length)

    * 100;

    progresso.style.width =
    porcentagem + "%";

    if(index == cards.length - 1){

        btn.innerText =
        "Finalizar";

        btn.type =
        "submit";

    }else{

        btn.innerText =
        "Próximo";

        btn.type =
        "button";
    }
}

/* ==========================
   PRÓXIMO
========================== */

function proximo(){

    if(atual < cards.length - 1){

        atual++;

        mostrarCard(atual);
    }
}

/* ==========================
   VOLTAR
========================== */

function voltar(){

    if(atual > 0){

        atual--;

        mostrarCard(atual);
    }
}

</script>

</body>
</html>