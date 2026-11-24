document.addEventListener("DOMContentLoaded", function () {
    const temaSalvo = localStorage.getItem("investegra_tema");
    const temaInicial = temaSalvo === "dark" ? "dark" : "light";

    document.documentElement.dataset.theme = temaInicial;
    document.documentElement.style.colorScheme = temaInicial;

    document.querySelectorAll("[data-theme-option]").forEach(function (botao) {
        const ativo = botao.dataset.themeOption === temaInicial;
        botao.classList.toggle("ativo", ativo);
        botao.setAttribute("aria-pressed", ativo ? "true" : "false");

        botao.addEventListener("click", function () {
            const tema = botao.dataset.themeOption === "dark" ? "dark" : "light";

            localStorage.setItem("investegra_tema", tema);
            document.documentElement.dataset.theme = tema;
            document.documentElement.style.colorScheme = tema;

            document.querySelectorAll("[data-theme-option]").forEach(function (opcao) {
                const selecionado = opcao.dataset.themeOption === tema;
                opcao.classList.toggle("ativo", selecionado);
                opcao.setAttribute("aria-pressed", selecionado ? "true" : "false");
            });
        });
    });
});
