
    function alternarSenha(){

        const campo =
        document.getElementById("senha");

        const botao =
        document.getElementById("btnSenha");

        if(campo.type === "password"){

            campo.type = "text";
            botao.innerHTML = "🙈";

        }else{

            campo.type = "password";
            botao.innerHTML = "🐵";

        }
    }
