
function alternarSenha(idCampo, idBotao){

    const campo =
    document.getElementById(idCampo);

    const botao =
    document.getElementById(idBotao);

    if(campo.type === "password"){

        campo.type = "text";
        botao.innerHTML = "🙈";

    }else{

        campo.type = "password";
        botao.innerHTML = "🐵";

    }
}
