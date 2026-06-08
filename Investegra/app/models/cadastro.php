<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Cadastro - INVESTEGRA</title>

    <style>

    *{
        margin:0;
        padding:0;
        box-sizing:border-box;
        font-family:'Segoe UI',sans-serif;
    }

    body{

        background:#f6f8fa;

        display:flex;
        justify-content:center;
        align-items:center;

        min-height:100vh;

        padding:20px;
    }

    .container{

        width:480px;

        background:#ffffff;

        padding:40px;

        border-radius:24px;

        box-shadow:
        0 15px 35px rgba(0,0,0,.08);

        animation:fadeIn .5s ease;
    }

    .logo{

        text-align:center;

        margin-bottom:30px;
    }

    .logo h1{

        color:#00a86b;

        font-size:2rem;

        margin-bottom:8px;
    }

    .logo p{

        color:#6b7280;

        font-size:14px;
    }

    h2{

        text-align:center;

        margin-bottom:25px;

        color:#1f2937;

        font-size:1.5rem;
    }

    input,
    select{

        width:100%;

        padding:14px;

        border:1px solid #d1d5db;

        border-radius:12px;

        font-size:15px;

        background:#fff;

        transition:.3s;
    }

    input:focus,
    select:focus{

        outline:none;

        border-color:#00a86b;

        box-shadow:
        0 0 0 4px rgba(0,168,107,.12);
    }

    .campo{

        margin-bottom:15px;
    }

    .senha-container{

        position:relative;
    }

    .senha-container input{

        padding-right:55px;
    }

    .mostrar-senha{

        position:absolute;

        right:15px;

        top:50%;

        transform:translateY(-50%);

        background:none;

        border:none;

        cursor:pointer;

        font-size:18px;

        color:#6b7280;

        width:auto;

        padding:0;
    }

    .mostrar-senha:hover{

        background:none;
    }

    button[type="submit"]{

        width:100%;

        padding:15px;

        border:none;

        border-radius:12px;

        background:#00a86b;

        color:white;

        font-size:16px;

        font-weight:600;

        cursor:pointer;

        transition:.3s;
    }

    button[type="submit"]:hover{

        background:#00915d;

        transform:translateY(-2px);
    }

    .footer-link{

        margin-top:25px;

        text-align:center;

        color:#6b7280;
    }

    .footer-link a{

        color:#00a86b;

        text-decoration:none;

        font-weight:600;
    }

    .footer-link a:hover{

        text-decoration:underline;
    }

    @keyframes fadeIn{

        from{

            opacity:0;
            transform:translateY(20px);
        }

        to{

            opacity:1;
            transform:translateY(0);
        }
    }

    </style>

</head>

<body>

<div class="container">

    <div class="logo">
        <h1>INVESTEGRA</h1>
        <p>Controle e acompanhe seus investimentos</p>
    </div>

    <h2>Criar Conta</h2>

    <form action="processando_cadastro.php" method="POST">

        <div class="campo">
            <input
                type="text"
                name="nome"
                placeholder="Nome completo"
                required>
        </div>

        <div class="campo">
            <input
                type="email"
                name="email"
                placeholder="E-mail"
                required>
        </div>

        <div class="campo">
            <input
                type="text"
                name="documento"
                placeholder="CPF ou CNPJ"
                required>
        </div>

        <div class="campo">
            <select name="tipo_usuario" required>

                <option value="">
                    Tipo de usuário
                </option>

                <option value="PF">
                    Pessoa Física
                </option>

                <option value="PJ">
                    Pessoa Jurídica
                </option>

            </select>
        </div>

        <div class="campo senha-container">

            <input
                type="password"
                id="senha"
                name="senha"
                placeholder="Senha"
                required>

            <button
                type="button"
                id="btnSenha"
                class="mostrar-senha"
                onclick="alternarSenha('senha','btnSenha')">

                👁
            </button>

        </div>

        <div class="campo senha-container">

            <input
                type="password"
                id="confirmarSenha"
                name="confirmar_senha"
                placeholder="Confirmar senha"
                required>

            <button
                type="button"
                id="btnConfirmar"
                class="mostrar-senha"
                onclick="alternarSenha('confirmarSenha','btnConfirmar')">

                👁
            </button>

        </div>

        <button type="submit">
            Criar Conta
        </button>

    </form>

    <div class="footer-link">
        Já possui conta?
        <a href="login.php">Entrar</a>
    </div>

</div>

<script>

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
        botao.innerHTML = "👁";

    }
}

</script>

</body>
</html>