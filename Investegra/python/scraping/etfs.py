import bs4 as BeautifulSoup
import csv
import config_pags
from acesso import Acesso
from config import config_pags, config_all
from python.utils import acesso

dataAgora = config_all.Apps.dataAgora

def GetEtfs():

    if config_pags.carregarPáginasEtfs() == "Página carregada com sucesso!":
        
        soup = BeautifulSoup.BeautifulSoup(acesso.response_etfs.text, 'html.parser')
        table = soup.find('table', {'id': 'BODY_TABLE_ID_ASSETS_TABLE'})
        
        if table:
            rows = table.find_all('tr')[1:]  
            data = []
            
            for row in rows:
                cols = row.find_all('td')
                cols = [col.text.strip() for col in cols][1:]
                data.append(cols)
                
            with open(f'Investegra/env/etfs/{dataAgora}.csv', 'w', newline='', encoding='utf-8') as f:
                writer = csv.writer(f)
                writer.writerow(['Ticker','Categoria','Provedor do indice','Retorno 30 dias','Retorno no ano','Retorno 12 meses','Cotação','Patrimonio líquido','Número de Cotistas','Negociação diária média','Taxa de administração primaria','Taxa de admisnistração total'])
                writer.writerows(data)              

            print("Dados salvos com sucesso!")
        else:
            print("Tabela de resultados não encontrada.")
    else:
        print(f"Erro: {acesso.response_etfs.status_code}")