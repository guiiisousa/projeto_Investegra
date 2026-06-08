import bs4 as BeautifulSoup
import csv
from acesso import Acesso
from config import config_pags, config_all
from python.utils import acesso

dataAgora = config_all.Apps.dataAgora

def pegarFiis():

    if config_pags.carregarPáginasFiis() == "Página carregada com sucesso!":
    
        soup = BeautifulSoup.BeautifulSoup(acesso.response_fiis.text, 'html.parser')
        table = soup.find('table', {'id': 'tabelaResultado'})
        
        if table:
            rows = table.find_all('tr')[1:]  
            data = []
            
            for row in rows:
                cols = row.find_all('td')
                cols = [col.text.strip() for col in cols]
                data.append(cols)
           
            
            with open(f'Investegra/env/fiis/{dataAgora}.csv', 'w', newline='', encoding='utf-8') as f:
                writer = csv.writer(f)
                writer.writerow(['Código', 'Empresa', 'Setor', 'Preço', 'P/VP', 'DY'])
                writer.writerows(data)
            
            print("Dados salvos com sucesso!")
        else:
            print("Tabela de resultados não encontrada.")
    else:
        print(f"Erro: {acesso.response_fiis.status_code}")