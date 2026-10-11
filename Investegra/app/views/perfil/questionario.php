<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz de investidor | Investegra</title>
    <link rel="stylesheet" href="css/questionario.css">
</head>
<body>
    <main class="quiz-container">
        <header class="quiz-header">
            <h1>Descubra seu perfil de investidor</h1>
            <p>Responda às perguntas para conhecer seu perfil de risco.</p>
        </header>

        <div class="progresso" aria-hidden="true">
            <div class="progresso-barra" id="progressoBarra"></div>
        </div>
        <p class="contador" aria-live="polite">
            Pergunta <span id="numeroPergunta">1</span> de 8
        </p>

        <form action="processar_questionario.php" method="POST" id="quizForm">
            <section class="pergunta ativa">
                <h2>1. Qual é a sua idade?</h2>
                <input type="number" name="idade" min="1" max="120" placeholder="Digite sua idade" required>
            </section>

            <section class="pergunta">
                <h2>2. Qual é sua experiência com investimentos?</h2>
                <label class="opcao"><input type="radio" name="experiencia" value="Nenhuma" required> Nunca investi</label>
                <label class="opcao"><input type="radio" name="experiencia" value="Baixa"> Tenho pouca experiência</label>
                <label class="opcao"><input type="radio" name="experiencia" value="Media"> Tenho experiência moderada</label>
                <label class="opcao"><input type="radio" name="experiencia" value="Alta"> Tenho bastante experiência</label>
            </section>

            <section class="pergunta">
                <h2>3. Qual é aproximadamente sua renda mensal?</h2>
                <select name="renda_mensal" required>
                    <option value="">Selecione uma opção</option>
                    <option value="Ate_2000">Até R$ 2.000</option>
                    <option value="2000_5000">R$ 2.000 a R$ 5.000</option>
                    <option value="5000_10000">R$ 5.000 a R$ 10.000</option>
                    <option value="Acima_10000">Acima de R$ 10.000</option>
                </select>
            </section>

            <section class="pergunta">
                <h2>4. Por quanto tempo pretende manter seus investimentos?</h2>
                <label class="opcao"><input type="radio" name="horizonte_investimento" value="Curto" required> Menos de 2 anos</label>
                <label class="opcao"><input type="radio" name="horizonte_investimento" value="Medio"> De 2 a 5 anos</label>
                <label class="opcao"><input type="radio" name="horizonte_investimento" value="Longo"> Mais de 5 anos</label>
            </section>

            <section class="pergunta">
                <h2>5. Como você se sente em relação ao risco?</h2>
                <label class="opcao"><input type="radio" name="tolerancia_risco" value="Baixa" required> Prefiro evitar riscos</label>
                <label class="opcao"><input type="radio" name="tolerancia_risco" value="Media"> Aceito algum risco em busca de retorno</label>
                <label class="opcao"><input type="radio" name="tolerancia_risco" value="Alta"> Aceito riscos maiores em busca de maiores retornos</label>
            </section>

            <section class="pergunta">
                <h2>6. Imagine que seus investimentos caiam 20%. O que você faria?</h2>
                <label class="opcao"><input type="radio" name="queda" value="Vender" required> Venderia para evitar mais perdas</label>
                <label class="opcao"><input type="radio" name="queda" value="Esperar"> Manteria os investimentos e esperaria</label>
                <label class="opcao"><input type="radio" name="queda" value="Aumentar"> Consideraria investir mais</label>
            </section>

            <section class="pergunta">
                <h2>7. Qual é seu principal objetivo?</h2>
                <select name="objetivo" required>
                    <option value="">Selecione seu objetivo</option>
                    <option value="Preservacao">Preservar patrimônio</option>
                    <option value="Reserva">Construir uma reserva</option>
                    <option value="Crescimento">Aumentar patrimônio</option>
                    <option value="Longo_Prazo">Construir patrimônio no longo prazo</option>
                </select>
            </section>

            <section class="pergunta">
                <h2>8. Conte um pouco sobre seus objetivos financeiros ou sua relação com investimentos.</h2>
                <textarea name="descricao_pessoal" rows="6" maxlength="1000" placeholder="Escreva aqui..." required></textarea>
            </section>

            <div class="botoes">
                <a href="meu_perfil.php" class="botao secundario cancelar-quiz">Cancelar quiz</a>
                <button type="button" id="voltar" class="botao secundario">Voltar</button>
                <button type="button" id="proximo" class="botao">Próxima</button>
                <button type="submit" id="finalizar" class="botao">Finalizar</button>
            </div>
        </form>
    </main>

    <script>
        const perguntas = document.querySelectorAll(".pergunta");
        const proximo = document.getElementById("proximo");
        const voltar = document.getElementById("voltar");
        const finalizar = document.getElementById("finalizar");
        const numeroPergunta = document.getElementById("numeroPergunta");
        const progressoBarra = document.getElementById("progressoBarra");
        let atual = 0;

        function atualizarQuiz() {
            perguntas.forEach((pergunta, indice) => {
                pergunta.classList.toggle("ativa", indice === atual);
            });
            numeroPergunta.textContent = String(atual + 1);
            progressoBarra.style.width = `${((atual + 1) / perguntas.length) * 100}%`;
            voltar.style.display = atual === 0 ? "none" : "block";
            proximo.style.display = atual === perguntas.length - 1 ? "none" : "block";
            finalizar.style.display = atual === perguntas.length - 1 ? "block" : "none";
        }

        proximo.addEventListener("click", () => {
            const campos = perguntas[atual].querySelectorAll("input, select, textarea");
            for (const campo of campos) {
                if (!campo.checkValidity()) {
                    campo.reportValidity();
                    return;
                }
            }
            atual += 1;
            atualizarQuiz();
        });

        voltar.addEventListener("click", () => {
            atual -= 1;
            atualizarQuiz();
        });

        atualizarQuiz();
    </script>
</body>
</html>
