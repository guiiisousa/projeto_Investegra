<?php

if (!isset($contextoIA)) {
    $contextoIA = "geral";
}

if (!isset($dadosIA)) {
    $dadosIA = null;
}

$dadosIAJson = json_encode(
    $dadosIA,
    JSON_UNESCAPED_UNICODE
);

if ($dadosIAJson === false) {
    $dadosIAJson = "null";
}

?>

<div
    class="assistente-ia"
    data-contexto="<?php echo htmlspecialchars(
        $contextoIA,
        ENT_QUOTES,
        'UTF-8'
    ); ?>"
    data-dados="<?php echo htmlspecialchars(
        $dadosIAJson,
        ENT_QUOTES,
        'UTF-8'
    ); ?>"
>

    <!-- CABEÇALHO -->

    <div class="assistente-header">

        <div class="assistente-icon">
            🤖
        </div>

        <div>

            <h3>
                Assistente INVESTEGRA
            </h3>

            <p>
                Tire suas dúvidas sobre esta página
            </p>

        </div>

    </div>


    <!-- MENSAGENS -->

    <div
        class="assistente-mensagens"
        id="assistenteMensagens"
    >

        <div class="mensagem mensagem-assistente">

            <div class="mensagem-avatar">
                🤖
            </div>

            <div class="mensagem-conteudo">

                Olá! 👋

                <br><br>

                Sou o assistente do INVESTEGRA.

                Posso ajudar você a entender os conceitos,
                funcionalidades e informações desta página.

                <br><br>

                <strong>
                    Qual é a sua dúvida?
                </strong>

            </div>

        </div>

    </div>


    <!-- INPUT -->

    <div class="assistente-input-area">

        <textarea
            id="assistenteInput"
            placeholder="Digite sua dúvida..."
            rows="1"
            maxlength="1000"
        ></textarea>

        <button
            type="button"
            id="assistenteEnviar"
            title="Enviar pergunta"
        >
            ➤
        </button>

    </div>


    <!-- AVISO -->

    <div class="assistente-aviso">

        💡 O assistente possui finalidade
        educacional e informativa.

    </div>

</div>


<script src="js/assistente.js?v=20261003"></script>