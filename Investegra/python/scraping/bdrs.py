import bs4 as BeautifulSoup
import csv
from utils.acesso import Acesso
from config import config_pags, config_all
from python.utils import acesso

dataAgora = config_all.Apps.dataAgora

def pegarBdrs():
    
    if config_pags.carregarPáginasBdrs() == "Página carregada com sucesso!":
        
        soup = BeautifulSoup.BeautifulSoup(acesso.response_etfs.text, 'html.parser')
        button = soup.find('button', {'class': 'toggle-buttons-module-scss-module__Kt5eJq__activeButton'})
        table = soup.find('table', {'id': 'BODY_TABLE_ID_ASSETS_TABLE'})
        
        if table:
            rows = table.find_all('tr')[1:]  
            data = []
            
            for row in rows:
                cols = row.find_all('td')
                cols = [col.text.strip() for col in cols][1:]
                data.append(cols)
                
            with open(f'Investegra/env/bdrs/{dataAgora}.csv', 'w', newline='', encoding='utf-8') as f:
                writer = csv.writer(f)
                writer.writerow(['Ticker','Nome do Fundo','Categoria','Provedor do indice','Retorno 30 dias','Retorno no ano','Retorno 12 meses','Cotação(R$)','Negociação diária média'])
                writer.writerows(data)              

            print("Dados salvos com sucesso!")
        else:
            print("Tabela de resultados não encontrada.")
    else:
        print(f"Erro: {acesso.response_etfs.status_code}")