import requests


url = "http://localhost:11434/api/generate"


dados = {
    "model": "llama3.2",
    "prompt": "Explique em poucas palavras o que é diversificação de investimentos.",
    "stream": False
}


try:

    resposta = requests.post(
        url,
        json=dados,
        timeout=120
    )

    print("Status:", resposta.status_code)

    if resposta.status_code == 200:

        resultado = resposta.json()

        print("\nResposta da IA:")
        print(resultado["response"])

    else:

        print("\nO Ollama retornou um erro:")
        print(resposta.text)


except requests.exceptions.RequestException as erro:

    print("\nNão foi possível conectar ao Ollama.")
    print(erro)