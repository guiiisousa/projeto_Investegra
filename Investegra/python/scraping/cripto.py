import bs4 as BeautifulSoup
import csv
from utils.acesso import Acesso
from config import config_pags, config_all
from python.utils import acesso

dataAgora = config_all.Apps.dataAgora

def GetCripto():
    
    if config_pags.carregarPáginasCripto(acesso.response_cripto_part_1.status_code) == "Página carregada com sucesso!" and config_pags.carregarPáginasCripto(acesso.response_cripto_part_2.status_code) == "Página carregada com sucesso!" and config_pags.carregarPáginasCripto(acesso.response_cripto_part_3.status_code) == "Página carregada com sucesso!":
        
        soup1 = BeautifulSoup.BeautifulSoup(acesso.response_cripto_part_1.text, 'html.parser')
        table1 = soup1.find('table', {'id': 'rankigns'})
        
        soup2 = BeautifulSoup.BeautifulSoup(acesso.response_cripto_part_2.text, 'html.parser')
        table2 = soup2.find('table', {'id': 'rankigns'})

        soup3 = BeautifulSoup.BeautifulSoup(acesso.response_cripto_part_3.text, 'html.parser')
        table3 = soup3.find('table', {'id': 'rankigns'})

        if table1 and table2 and table3:
            rows1 = table1.find_all('tr')[1:]
            rows2 = table2.find_all('tr')[1:]
            rows3 = table3.find_all('tr')[1:]
            data = []

            for row in rows1:
                cols = row.find_all('td')
                cols = [col.text.strip().replace('\n', '') for col in cols]
                data.append(cols)

            for row in rows2:
                cols = row.find_all('td')
                cols = [col.text.strip().replace('\n', '') for col in cols]
                data.append(cols)

            for row in rows3:
                cols = row.find_all('td')
                cols = [col.text.strip().replace('\n', '') for col in cols]
                data.append(cols)
            
            with open(f'Investegra/env/criptos/{dataAgora}.csv', 'w', newline='', encoding='utf-8') as f:
                writer = csv.writer(f)
                writer.writerow(['Capitalização','Cotação','Cotação (R$)','Volume 24h','Volume 30d','Volume 3m','Variação 24h','Variação 30d','Variação 3m','Variação 6m','Variação 12m','Variação 5 Anos'])
                writer.writerows(data)
            
            print("Dados do Fundamentus salvos com sucesso!")
        else:
            print("Tabela de resultados não encontrada.")
    else:
        print(f"Erro: {acesso.response_cripto_part_1.status_code}, {acesso.response_cripto_part_2.status_code}, {acesso.response_cripto_part_3.status_code}")
