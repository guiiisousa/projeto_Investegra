document.addEventListener("DOMContentLoaded", function () {

    const mensagens = document.getElementById(
        "assistenteMensagens"
    );

    const input = document.getElementById(
        "assistenteInput"
    );

    const botao = document.getElementById(
        "assistenteEnviar"
    );

    const assistente = document.querySelector(
        ".assistente-ia"
    );


    if (!mensagens || !input || !botao || !assistente) {
        return;
    }


    /*
    |--------------------------------------------------------------------------
    | CONTEXTO
    |--------------------------------------------------------------------------
    */

    const contexto =
        assistente.dataset.contexto || "geral";


    /*
    |--------------------------------------------------------------------------
    | DADOS DA PÁGINA
    |--------------------------------------------------------------------------
    */

    let dadosIA = null;

    try {

        const dadosTexto =
            assistente.dataset.dados;

        if (dadosTexto) {

            dadosIA =
                JSON.parse(dadosTexto);

        }

    } catch (erro) {

        console.error(
            "Erro ao interpretar dados da IA:",
            erro
        );

    }


    console.log(
        "Contexto da IA:",
        contexto
    );

    console.log(
        "Dados enviados para a IA:",
        dadosIA
    );


    /*
    |--------------------------------------------------------------------------
    | HISTÓRICO DA CONVERSA
    |--------------------------------------------------------------------------
    */

    let historico = [];


    /*
    |--------------------------------------------------------------------------
    | ADICIONAR MENSAGEM
    |--------------------------------------------------------------------------
    */

    function adicionarMensagem(
        texto,
        tipo
    ) {

        const mensagem =
            document.createElement("div");

        mensagem.className =
            "mensagem " +
            (
                tipo === "usuario"
                    ? "mensagem-usuario"
                    : "mensagem-assistente"
            );


        /*
        | Avatar
        */

        const avatar =
            document.createElement("div");

        avatar.className =
            "mensagem-avatar";

        avatar.textContent =
            tipo === "usuario"
                ? "👤"
                : "🤖";


        /*
        | Conteúdo
        */

        const conteudo =
            document.createElement("div");

        conteudo.className =
            "mensagem-conteudo";

        conteudo.textContent =
            texto;


        mensagem.appendChild(
            avatar
        );

        mensagem.appendChild(
            conteudo
        );


        mensagens.appendChild(
            mensagem
        );


        mensagens.scrollTop =
            mensagens.scrollHeight;


        return mensagem;
    }


    /*
    |--------------------------------------------------------------------------
    | LOADING
    |--------------------------------------------------------------------------
    */

    function criarLoading() {

        const mensagem =
            document.createElement("div");

        mensagem.className =
            "mensagem mensagem-assistente";


        const avatar =
            document.createElement("div");

        avatar.className =
            "mensagem-avatar";

        avatar.textContent =
            "🤖";


        const conteudo =
            document.createElement("div");

        conteudo.className =
            "mensagem-conteudo";

        conteudo.textContent =
            "...";


        mensagem.appendChild(
            avatar
        );

        mensagem.appendChild(
            conteudo
        );


        mensagens.appendChild(
            mensagem
        );


        mensagens.scrollTop =
            mensagens.scrollHeight;


        return mensagem;
    }


    /*
    |--------------------------------------------------------------------------
    | ENVIAR PERGUNTA
    |--------------------------------------------------------------------------
    */

    async function enviarPergunta() {

        const pergunta =
            input.value.trim();


        if (!pergunta) {
            return;
        }


        /*
        | Mostra pergunta do usuário
        */

        adicionarMensagem(
            pergunta,
            "usuario"
        );


        /*
        | Limpa campo
        */

        input.value = "";


        /*
        | Desabilita temporariamente
        */

        botao.disabled = true;

        input.disabled = true;


        /*
        | Loading
        */

        const loading =
            criarLoading();


        try {

            const resposta =
                await fetch(
                    "api/chat.php",
                    {
                        method: "POST",

                        headers: {
                            "Content-Type":
                                "application/json"
                        },

                        body: JSON.stringify({

                            pergunta:
                                pergunta,

                            contexto:
                                contexto,

                            dados:
                                dadosIA,

                            historico:
                                historico

                        })

                    }
                );


            /*
            |--------------------------------------------------------------------------
            | RESPOSTA HTTP
            |--------------------------------------------------------------------------
            */

            if (!resposta.ok) {

                throw new Error(
                    "Erro HTTP " +
                    resposta.status
                );

            }


            const dados =
                await resposta.json();


            /*
            |--------------------------------------------------------------------------
            | REMOVE LOADING
            |--------------------------------------------------------------------------
            */

            loading.remove();


            /*
            |--------------------------------------------------------------------------
            | RESPOSTA DA IA
            |--------------------------------------------------------------------------
            */

            if (
                dados.sucesso &&
                dados.resposta
            ) {

                adicionarMensagem(
                    dados.resposta,
                    "assistente"
                );


                /*
                | Salva no histórico
                */

                historico.push({
                    role: "user",
                    content: pergunta
                });

                historico.push({
                    role: "assistant",
                    content: dados.resposta
                });


                /*
                | Mantém somente
                | as últimas 10 mensagens
                */

                if (
                    historico.length > 10
                ) {

                    historico =
                        historico.slice(-10);

                }

            } else {

                let mensagemErro =
                    "Não foi possível obter uma resposta do assistente.";


                if (dados.erro) {

                    mensagemErro +=
                        "\n\n" +
                        dados.erro;

                }


                adicionarMensagem(
                    mensagemErro,
                    "assistente"
                );

            }


        } catch (erro) {

            console.error(
                "Erro no assistente:",
                erro
            );


            loading.remove();


            adicionarMensagem(
                "Não foi possível conectar ao assistente de IA. Verifique se o Ollama está executando.",
                "assistente"
            );

        }


        /*
        |--------------------------------------------------------------------------
        | REATIVA CAMPOS
        |--------------------------------------------------------------------------
        */

        botao.disabled = false;

        input.disabled = false;

        input.focus();

    }


    /*
    |--------------------------------------------------------------------------
    | BOTÃO
    |--------------------------------------------------------------------------
    */

    botao.addEventListener(
        "click",
        enviarPergunta
    );


    /*
    |--------------------------------------------------------------------------
    | ENTER
    |--------------------------------------------------------------------------
    */

    input.addEventListener(
        "keydown",
        function (evento) {

            if (
                evento.key === "Enter" &&
                !evento.shiftKey
            ) {

                evento.preventDefault();

                enviarPergunta();

            }

        }
    );

});