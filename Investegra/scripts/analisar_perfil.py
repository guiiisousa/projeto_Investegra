import os
import sys
import json
import requests


# Garante UTF-8 na comunicação com o PHP
if hasattr(sys.stdout, "reconfigure"):
    sys.stdout.reconfigure(encoding="utf-8")

if hasattr(sys.stdin, "reconfigure"):
    sys.stdin.reconfigure(encoding="utf-8")


OLLAMA_URL = os.environ.get("OLLAMA_URL", "http://localhost:11434/api/generate")

MODELO = "llama3.2"


def analisar_perfil(dados):

    prompt = f"""
Você é um assistente de análise financeira educacional
do sistema INVESTEGRA.

Sua função é produzir uma análise complementar sobre
o perfil de risco calculado pelo sistema.

REGRAS IMPORTANTES:

- Não altere o perfil calculado pelo sistema.
- Não diga ao usuário para comprar ou vender ativos.
- Não prometa rentabilidade.
- Não faça previsões de mercado.
- Não apresente uma recomendação como garantia.
- Use linguagem educativa e clara.
- A análise deve ser complementar ao cálculo tradicional.

DADOS DO USUÁRIO:

Idade: {dados["idade"]}

Experiência:
{dados["experiencia"]}

Renda mensal aproximada:
{dados["renda_mensal"]}

Horizonte de investimento:
{dados["horizonte_investimento"]}

Tolerância ao risco:
{dados["tolerancia_risco"]}

Objetivo:
{dados["objetivo"]}

Perfil calculado pelo sistema:
{dados["perfil_risco"]}

Descrição pessoal:
{dados["descricao_pessoal"]}


Produza uma análise curta.

Explique:

1. O que significa esse perfil.

2. Quais características geralmente estão associadas
   a esse perfil.

3. Quais aspectos o usuário deve observar ao organizar
   sua carteira.

4. Por que diversificação e acompanhamento são importantes.

Não faça recomendações específicas de compra ou venda.
Não utilize promessas de retorno.
"""


    dados_ollama = {
        "model": MODELO,
        "prompt": prompt,
        "stream": False
    }


    try:

        resposta = requests.post(
            OLLAMA_URL,
            json=dados_ollama,
            timeout=120
        )


        if resposta.status_code != 200:

            return {
                "sucesso": False,
                "erro": resposta.text
            }


        resultado = resposta.json()


        return {
            "sucesso": True,
            "analise": resultado.get("response", "")
        }


    except requests.exceptions.RequestException as erro:

        return {
            "sucesso": False,
            "erro": str(erro)
        }


if __name__ == "__main__":

    try:

        entrada = sys.stdin.read()

        dados = json.loads(entrada)

        resultado = analisar_perfil(dados)

        print(
            json.dumps(
                resultado,
                ensure_ascii=False
            )
        )


    except Exception as erro:

        print(
            json.dumps(
                {
                    "sucesso": False,
                    "erro": str(erro)
                },
                ensure_ascii=False
            )
        )