const botaoVisibilidadeValores = document.querySelector("[data-values-toggle]");

if (botaoVisibilidadeValores) {
    const raiz = document.documentElement;
    const iconeOlhoAberto = botaoVisibilidadeValores.querySelector("[data-eye-open]");
    const iconeOlhoFechado = botaoVisibilidadeValores.querySelector("[data-eye-closed]");
    const textoAcessivel = botaoVisibilidadeValores.querySelector(".texto-acessivel");
    const chaveArmazenamento = "investegra_valores_ocultos_v3";

    function atualizarVisibilidadeValores(ocultos) {
        raiz.dataset.valuesHidden = ocultos ? "true" : "false";
        botaoVisibilidadeValores.setAttribute("aria-pressed", String(ocultos));
        botaoVisibilidadeValores.setAttribute(
            "aria-label",
            ocultos ? "Mostrar valores financeiros" : "Ocultar valores financeiros"
        );
        botaoVisibilidadeValores.title = ocultos
            ? "Mostrar valores financeiros"
            : "Ocultar valores financeiros";
        textoAcessivel.textContent = botaoVisibilidadeValores.title;
        iconeOlhoAberto.hidden = !ocultos;
        iconeOlhoFechado.hidden = ocultos;
        localStorage.setItem(chaveArmazenamento, String(ocultos));
    }

    atualizarVisibilidadeValores(
        localStorage.getItem(chaveArmazenamento) !== "false"
    );

    botaoVisibilidadeValores.addEventListener("click", function () {
        const ocultos = raiz.dataset.valuesHidden !== "true";
        atualizarVisibilidadeValores(ocultos);
    });
}
