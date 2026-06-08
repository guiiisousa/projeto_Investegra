<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <title>Nova Carteira - INVESTEGRA</title>

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

        width:500px;

        background:white;

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
    textarea{

        width:100%;

        padding:14px;

        margin-bottom:15px;

        border:1px solid #d1d5db;

        border-radius:12px;

        font-size:15px;

        background:#fff;

        transition:.3s;
    }

    textarea{

        min-height:120px;

        resize:none;
    }

    input:focus,
    textarea:focus{

        outline:none;

        border-color:#00a86b;

        box-shadow:
        0 0 0 4px rgba(0,168,107,.12);
    }

    button{

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

    button:hover{

        background:#00915d;

        transform:translateY(-2px);
    }

    .voltar{

        margin-top:20px;

        text-align:center;
    }

    .voltar a{

        color:#00a86b;

        text-decoration:none;

        font-weight:600;
    }

    .voltar a:hover{

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

            <p>
                Organize e acompanhe seus investimentos
            </p>

        </div>

        <h2>Criar Carteira</h2>

        <form
        action="salvar_carteira.php"
        method="POST">

            <input
                type="text"
                name="nome"
                placeholder="Nome da carteira"
                required>

            <textarea
                name="descricao"
                placeholder="Descreva o objetivo desta carteira (opcional)">
            </textarea>

            <button type="submit">
                Criar Carteira
            </button>

        </form>

        <div class="voltar">

            <a href="carteiras.php">
                ← Voltar para Minhas Carteiras
            </a>

        </div>

    </div>

</body>

</html>