import bs4 as BeautifulSoup
import csv
from utils.acesso import Acesso
from config import config_pags, config_all
from python.utils import acesso

dataAgora = config_all.Apps.dataAgora

def pegarAcoes():

    if config_pags.carregarPáginasAcoes() == "Página carregada com sucesso!":
        
        soup = BeautifulSoup.BeautifulSoup(acesso.response_ações.text, 'html.parser')
        table = soup.find('table', {'id': 'resultado'})
        print(table)
        if table:
            rows = table.find_all('tr')[1:] 
            data = []
            
            for row in rows:
                cols = row.find_all('td')
                cols = [col.text.strip() for col in cols]
                data.append(cols)
    
            with open(f'Investegra/env/ações/{dataAgora}.csv', 'w', newline='', encoding='utf-8') as f:
                writer = csv.writer(f)
                writer.writerow(['Código', 'Cotação', 'P/L', 'P/VP', 'RSR', 'DY', 'P/Ativo', 'P/Cap.Giro', 'P/EBIT', 'P/ACL', 'EV/EBITDA', 'EV/EBIT', 'Mrg.Liq', 'Lig.Corr', 'ROIC', 'ROE', 'Liquidez 2 meses', 'Patrimonio Líquido', 'Dív. Bruta/Patrimonio', 'Cresc. Rec. 5a'])
                writer.writerows(data)
                
            print("Dados salvos com sucesso!")
            
        else:
            print("Tabela de resultados não encontrada.")
    else:
        print(f"Erro: {acesso.response_ações.status_code}")
        