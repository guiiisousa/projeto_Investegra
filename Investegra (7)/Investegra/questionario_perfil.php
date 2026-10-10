<?php

session_start();

if (!isset($_SESSION["id"])) {
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
        content="width=device-width, initial-scale=1.0"
    >

    <title>Perfil de Risco - INVESTEGRA</title>

    <link
        rel="stylesheet"
        href="css/questionario.css"
    >

</head>

<body>

<div class="quiz-container">

    <div class="quiz-header">

        <h1>
            Descubra seu perfil de investidor
        </h1>

        <p>
            Responda às perguntas para que o INVESTEGRA
            possa analisar seu perfil de risco.
        </p>

    </div>


    <div class="progresso">

        <div
            class="progresso-barra"
            id="progressoBarra"
        ></div>

    </div>


    <div class="contador">

        Pergunta
        <span id="numeroPergunta">1</span>
        de 8

    </div>


    <form
        action="processar_questionario.php"
        method="POST"
        id="quizForm"
    >


        <!-- =========================
             PERGUNTA 1
        ========================== -->

        <div class="pergunta ativa">

            <h2>
                1. Qual é a sua idade?
            </h2>

            <input
                type="number"
                name="idade"
                min="1"
                max="120"
                placeholder="Digite sua idade"
                required
            >

        </div>


        <!-- =========================
             PERGUNTA 2
        ========================== -->

        <div class="pergunta">

            <h2>
                2. Qual é sua experiência com investimentos?
            </h2>

            <label class="opcao">

                <input
                    type="radio"
                    name="experiencia"
                    value="Nenhuma"
                    data-pontos="0"
                    required
                >

                Nunca investi

            </label>


            <label class="opcao">

                <input
                    type="radio"
                    name="experiencia"
                    value="Baixa"
                    data-pontos="1"
                >

                Tenho pouca experiência

            </label>


            <label class="opcao">

                <input
                    type="radio"
                    name="experiencia"
                    value="Media"
                    data-pontos="2"
                >

                Tenho experiência moderada

            </label>


            <label class="opcao">

                <input
                    type="radio"
                    name="experiencia"
                    value="Alta"
                    data-pontos="3"
                >

                Tenho bastante experiência

            </label>

        </div>


        <!-- =========================
             PERGUNTA 3
        ========================== -->

        <div class="pergunta">

            <h2>
                3. Qual é aproximadamente sua renda mensal?
            </h2>

            <select
                name="renda_mensal"
                required
            >

                <option value="">
                    Selecione uma opção
                </option>

                <option value="Ate_2000">
                    Até R$ 2.000
                </option>

                <option value="2000_5000">
                    R$ 2.000 a R$ 5.000
                </option>

                <option value="5000_10000">
                    R$ 5.000 a R$ 10.000
                </option>

                <option value="Acima_10000">
                    Acima de R$ 10.000
                </option>

            </select>

        </div>


        <!-- =========================
             PERGUNTA 4
        ========================== -->

        <div class="pergunta">

            <h2>
                4. Por quanto tempo pretende manter seus investimentos?
            </h2>

            <label class="opcao">

                <input
                    type="radio"
                    name="horizonte_investimento"
                    value="Curto"
                    data-pontos="0"
                    required
                >

                Menos de 2 anos

            </label>


            <label class="opcao">

                <input
                    type="radio"
                    name="horizonte_investimento"
                    value="Medio"
                    data-pontos="1"
                >

                De 2 a 5 anos

            </label>


            <label class="opcao">

                <input
                    type="radio"
                    name="horizonte_investimento"
                    value="Longo"
                    data-pontos="3"
                >

                Mais de 5 anos

            </label>

        </div>


        <!-- =========================
             PERGUNTA 5
        ========================== -->

        <div class="pergunta">

            <h2>
                5. Como você se sente em relação ao risco?
            </h2>

            <label class="opcao">

                <input
                    type="radio"
                    name="tolerancia_risco"
                    value="Baixa"
                    data-pontos="0"
                    required
                >

                Prefiro evitar riscos

            </label>


            <label class="opcao">

                <input
                    type="radio"
                    name="tolerancia_risco"
                    value="Media"
                    data-pontos="2"
                >

                Aceito algum risco em busca de retorno

            </label>


            <label class="opcao">

                <input
                    type="radio"
                    name="tolerancia_risco"
                    value="Alta"
                    data-pontos="4"
                >

                Aceito riscos maiores em busca de maiores retornos

            </label>

        </div>


        <!-- =========================
             PERGUNTA 6
        ========================== -->

        <div class="pergunta">

            <h2>
                6. Imagine que seus investimentos caiam 20%.
                O que você faria?
            </h2>

            <label class="opcao">

                <input
                    type="radio"
                    name="queda"
                    value="Vender"
                    data-pontos="0"
                    required
                >

                Venderia para evitar mais perdas

            </label>


            <label class="opcao">

                <input
                    type="radio"
                    name="queda"
                    value="Esperar"
                    data-pontos="2"
                >

                Manteria os investimentos e esperaria

            </label>


            <label class="opcao">

                <input
                    type="radio"
                    name="queda"
                    value="Aumentar"
                    data-pontos="4"
                >

                Consideraria investir mais

            </label>

        </div>


        <!-- =========================
             PERGUNTA 7
        ========================== -->

        <div class="pergunta">

            <h2>
                7. Qual é seu principal objetivo?
            </h2>

            <select
                name="objetivo"
                required
            >

                <option value="">
                    Selecione seu objetivo
                </option>

                <option value="Preservacao">
                    Preservar patrimônio
                </option>

                <option value="Reserva">
                    Construir uma reserva
                </option>

                <option value="Crescimento">
                    Aumentar patrimônio
                </option>

                <option value="Longo_Prazo">
                    Construir patrimônio no longo prazo
                </option>

            </select>

        </div>


        <!-- =========================
             PERGUNTA 8
        ========================== -->

        <div class="pergunta">

            <h2>
                8. Conte um pouco sobre seus objetivos financeiros
                ou sobre sua relação com investimentos.
            </h2>

            <textarea
                name="descricao_pessoal"
                rows="6"
                maxlength="1000"
                placeholder="Escreva aqui..."
                required
            ></textarea>

        </div>


        <div class="botoes">

            <button
                type="button"
                id="voltar"
                class="botao secundario"
            >
                Voltar
            </button>

            <button
                type="button"
                id="proximo"
                class="botao"
            >
                Próxima
            </button>

            <button
                type="submit"
                id="finalizar"
                class="botao"
            >
                Finalizar
            </button>

        </div>

    </form>

</div>


<script>

const perguntas =
    document.querySelectorAll(".pergunta");

const proximo =
    document.getElementById("proximo");

const voltar =
    document.getElementById("voltar");

const finalizar =
    document.getElementById("finalizar");

const numeroPergunta =
    document.getElementById("numeroPergunta");

const progressoBarra =
    document.getElementById("progressoBarra");


let atual = 0;


/* =========================
   ATUALIZAR INTERFACE
========================= */

function atualizarQuiz() {

    perguntas.forEach(
        pergunta => pergunta.classList.remove("ativa")
    );

    perguntas[atual].classList.add("ativa");


    numeroPergunta.textContent =
        atual + 1;


    progressoBarra.style.width =
        ((atual + 1) / perguntas.length * 100) + "%";


    if (atual === 0) {

        voltar.style.display = "none";

    } else {

        voltar.style.display = "block";

    }


    if (atual === perguntas.length - 1) {

        proximo.style.display = "none";

        finalizar.style.display = "block";

    } else {

        proximo.style.display = "block";

        finalizar.style.display = "none";

    }

}


/* =========================
   VALIDAR PERGUNTA
========================= */

function validarPergunta() {

    const campos =
        perguntas[atual].querySelectorAll(
            "input, select, textarea"
        );


    for (const campo of campos) {

        if (!campo.checkValidity()) {

            campo.reportValidity();

            return false;

        }

    }

    return true;

}


/* =========================
   PRÓXIMA
========================= */

proximo.addEventListener(
    "click",
    function() {

        if (!validarPergunta()) {
            return;
        }

        if (atual < perguntas.length - 1) {

            atual++;

            atualizarQuiz();

        }

    }
);


/* =========================
   VOLTAR
========================= */

voltar.addEventListener(
    "click",
    function() {

        if (atual > 0) {

            atual--;

            atualizarQuiz();

        }

    }
);


atualizarQuiz();

</script>

</body>

</html>