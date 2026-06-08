import requests
class Acesso():
    url_ações = 'https://www.fundamentus.com.br/resultado.php'
    url_fiis = 'https://www.fundamentus.com.br/fii_resultado.php'
    url_cripto_part_1 = 'https://investidor10.com.br/criptomoedas/?page=1'
    url_cripto_part_2 = 'https://investidor10.com.br/criptomoedas/?page=2'
    url_cripto_part_3 = 'https://investidor10.com.br/criptomoedas/?page=3'
    url_etfs = 'https://www.etfsbrasil.com.br/#ativos-disponiveis'
    url_bdrs = 'https://www.etfsbrasil.com.br/#ativos-disponiveis'
    headers = {
        'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
    }
    response_ações = requests.get(url_ações, headers=headers)
    response_fiis = requests.get(url_fiis, headers=headers)
    response_cripto_part_1 = requests.get(url_cripto_part_1, headers=headers)
    response_cripto_part_2 = requests.get(url_cripto_part_2, headers=headers)
    response_cripto_part_3 = requests.get(url_cripto_part_3, headers=headers)
    response_etfs = requests.get(url_etfs, headers=headers)
    response_bdrs = requests.get(url_bdrs, headers=headers)
    