import sys
import json
import requests


# Garante que o terminal trabalhe corretamente com acentos
if hasattr(sys.stdout, "reconfigure"):
    sys.stdout.reconfigure(encoding="utf-8")

if hasattr(sys.stdin, "reconfigure"):
    sys.stdin.reconfigure(encoding="utf-8")


def analisar_carteira(dados):

    perfil = dados.get("perfil", "Não informado")
    score = dados.get("score", "Não informado")
    experiencia = dados.get("experiencia", "Não informado")
    horizonte = dados.get("horizonte", "Não informado")
    objetivo = dados.get("objetivo", "Não informado")

    patrimonio = dados.get("patrimonio", 0)
    quantidade_ativos = dados.get("quantidade_ativos", 0)

    distribuicao = dados.get("distribuicao", {})
    alertas = dados.get("alertas", [])


    texto_distribuicao = ""

    if distribuicao:
        for categoria, percentual in distribuicao.items():
            texto_distribuicao += f"- {categoria}: {percentual:.2f}%\n"
    else:
        texto_distribuicao = "Nenhuma distribuição informada."


    texto_alertas = ""

    if alertas:
        for alerta in alertas:
            texto_alertas += f"- {alerta}\n"
    else:
        texto_alertas = "Nenhum alerta registrado."


    prompt = f"""
Você é o assistente de análise do sistema INVESTEGRA.

Sua função é analisar, de maneira educacional e objetiva, a carteira de investimentos de um usuário.

IMPORTANTE:
- Utilize somente os dados fornecidos.
- Não invente informações.
- Não altere ou crie valores que não foram fornecidos.
- Não prometa rentabilidade.
- Não garanta resultados financeiros.
- Não diga que determinado ativo deve ser comprado ou vendido.
- Não substitua uma orientação profissional.
- A análise deve ser informativa.

DADOS DO INVESTIDOR

Perfil de risco: {perfil}
Score de risco: {score}
Experiência: {experiencia}
Horizonte de investimento: {horizonte}
Objetivo: {objetivo}

DADOS DA CARTEIRA

Patrimônio total: R$ {patrimonio:.2f}
Quantidade de ativos: {quantidade_ativos}

DISTRIBUIÇÃO POR CATEGORIA

{texto_distribuicao}

ALERTAS IDENTIFICADOS PELO SISTEMA

{texto_alertas}

Com base nesses dados, produza uma análise contendo:

1. Visão geral da carteira
2. Análise da diversificação
3. Relação entre a carteira e o perfil de risco
4. Pontos positivos
5. Pontos de atenção
6. Conclusão

Escreva em português do Brasil, de maneira clara e compreensível para um investidor iniciante.

Não faça recomendações específicas de compra ou venda de ativos.
"""


    url = "http://localhost:11434/api/generate"

    dados_ollama = {
        "model": "llama3.2",
        "prompt": prompt,
        "stream": False
    }


    try:

        resposta = requests.post(
            url,
            json=dados_ollama,
            timeout=120
        )


        if resposta.status_code != 200:

            return {
                "sucesso": False,
                "erro": "O Ollama retornou um erro.",
                "detalhes": resposta.text
            }


        resultado = resposta.json()

        analise = resultado.get("response", "").strip()


        if not analise:

            return {
                "sucesso": False,
                "erro": "O Ollama não retornou uma análise."
            }


        return {
            "sucesso": True,
            "analise": analise
        }


    except requests.exceptions.RequestException as erro:

        return {
            "sucesso": False,
            "erro": "Não foi possível conectar ao Ollama.",
            "detalhes": str(erro)
        }


    except Exception as erro:

        return {
            "sucesso": False,
            "erro": "Ocorreu um erro ao processar a análise.",
            "detalhes": str(erro)
        }


if __name__ == "__main__":

    try:

        entrada = sys.stdin.read()

        dados = json.loads(entrada)

        resultado = analisar_carteira(dados)

        print(json.dumps(
            resultado,
            ensure_ascii=False
        ))


    except json.JSONDecodeError:

        print(json.dumps({
            "sucesso": False,
            "erro": "Os dados recebidos não estão em formato JSON válido."
        }, ensure_ascii=False))


    except Exception as erro:

        print(json.dumps({
            "sucesso": False,
            "erro": "Erro inesperado.",
            "detalhes": str(erro)
        }, ensure_ascii=False))