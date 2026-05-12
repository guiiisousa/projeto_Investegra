import bs4 as BeautifulSoup
import csv
import sys
import os
import backend.config.config_pags as config_pags
import backend.config.config_all as config_all
from Investegra.env import classes
from datetime import datetime
import shutil


sys.path.append(os.path.abspath(os.path.join(os.path.dirname(__file__), "..", "..", "..")))

acesso = classes.Acesso()
agora = config_all.Apps.agora


def GetAcoes():

    if config_pags.CarregarPagAcoes() == "Página carregada com sucesso!":
        
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
    
            with open(f'Investegra/env/ações/{agora}.csv', 'w', newline='', encoding='utf-8') as f:
                writer = csv.writer(f)
                writer.writerow(['Código', 'Cotação', 'P/L', 'P/VP', 'RSR', 'DY', 'P/Ativo', 'P/Cap.Giro', 'P/EBIT', 'P/ACL', 'EV/EBITDA', 'EV/EBIT', 'Mrg.Liq', 'Lig.Corr', 'ROIC', 'ROE', 'Liquidez 2 meses', 'Patrimonio Líquido', 'Dív. Bruta/Patrimonio', 'Cresc. Rec. 5a'])
                writer.writerows(data)
                
            print("Dados salvos com sucesso!")
            
        else:
            print("Tabela de resultados não encontrada.")
    else:
        print(f"Erro: {acesso.response_ações.status_code}")
        
def GetFiis():
    
    if config_pags.CarregarPagFiis() == "Página carregada com sucesso!":
        
        
        soup = BeautifulSoup.BeautifulSoup(acesso.response_fiis.text, 'html.parser')
        table = soup.find('table', {'id': 'tabelaResultado'})
        
        if table:
            rows = table.find_all('tr')[1:]  
            data = []
            
            for row in rows:
                cols = row.find_all('td')
                cols = [col.text.strip() for col in cols]
                data.append(cols)
           
            
            with open(f'Investegra/env/fiis/{agora}.csv', 'w', newline='', encoding='utf-8') as f:
                writer = csv.writer(f)
                writer.writerow(['Código', 'Empresa', 'Setor', 'Preço', 'P/VP', 'DY'])
                writer.writerows(data)
            
            print("Dados salvos com sucesso!")
        else:
            print("Tabela de resultados não encontrada.")
    else:
        print(f"Erro: {acesso.response_fiis.status_code}")

def GetCripto():
    
    if config_pags.CarregarPagCripto(acesso.response_cripto_part_1.status_code) == "Página carregada com sucesso!" and config_pags.CarregarPagCripto(acesso.response_cripto_part_2.status_code) == "Página carregada com sucesso!" and config_pags.CarregarPagCripto(acesso.response_cripto_part_3.status_code) == "Página carregada com sucesso!":
        
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
            
            with open(f'Investegra/env/criptos/{agora}.csv', 'w', newline='', encoding='utf-8') as f:
                writer = csv.writer(f)
                writer.writerow(['Capitalização','Cotação','Cotação (R$)','Volume 24h','Volume 30d','Volume 3m','Variação 24h','Variação 30d','Variação 3m','Variação 6m','Variação 12m','Variação 5 Anos'])
                writer.writerows(data)
            
            print("Dados do Fundamentus salvos com sucesso!")
        else:
            print("Tabela de resultados não encontrada.")
    else:
        print(f"Erro: {acesso.response_cripto_part_1.status_code}, {acesso.response_cripto_part_2.status_code}, {acesso.response_cripto_part_3.status_code}")

def GetEtfs():

    if config_pags.CarregarPagEtfs() == "Página carregada com sucesso!":
        
        soup = BeautifulSoup.BeautifulSoup(acesso.response_etfs.text, 'html.parser')
        table = soup.find('table', {'id': 'BODY_TABLE_ID_ASSETS_TABLE'})
        
        if table:
            rows = table.find_all('tr')[1:]  
            data = []
            
            for row in rows:
                cols = row.find_all('td')
                cols = [col.text.strip() for col in cols][1:]
                data.append(cols)
                
            with open(f'Investegra/env/etfs/{agora}.csv', 'w', newline='', encoding='utf-8') as f:
                writer = csv.writer(f)
                writer.writerow(['Ticker','Categoria','Provedor do indice','Retorno 30 dias','Retorno no ano','Retorno 12 meses','Cotação','Patrimonio líquido','Número de Cotistas','Negociação diária média','Taxa de administração primaria','Taxa de admisnistração total'])
                writer.writerows(data)              

            print("Dados salvos com sucesso!")
        else:
            print("Tabela de resultados não encontrada.")
    else:
        print(f"Erro: {acesso.response_etfs.status_code}")

def GetBdrs():
    
    if config_pags.CarregarPagBdrs() == "Página carregada com sucesso!":
        
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
                
            with open(f'Investegra/env/bdrs/{agora}.csv', 'w', newline='', encoding='utf-8') as f:
                writer = csv.writer(f)
                writer.writerow(['Ticker','Nome do Fundo','Categoria','Provedor do indice','Retorno 30 dias','Retorno no ano','Retorno 12 meses','Cotação(R$)','Negociação diária média'])
                writer.writerows(data)              

            print("Dados salvos com sucesso!")
        else:
            print("Tabela de resultados não encontrada.")
    else:
        print(f"Erro: {acesso.response_etfs.status_code}")
        
def ArmazenarAcoes_Mensal():
    pasta = "Investegra/env/ações"
    pasta_destino = "Investegra/env/ações/mes"
    
    for arquivo in os.listdir(pasta):
        if arquivo.endswith(".csv"):
        
            data_str = arquivo.split("-")[1]
            mes = int(data_str)
            
            match mes:
                case 5 : 
                    caminho_final = shutil.move(f'{pasta}/{arquivo}', f'{pasta_destino}/{arquivo}')
                    print(f"Arquivo {arquivo} movido para {caminho_final}")
                case _ :
                    print(f"Arquivo {arquivo} não corresponde ao critério de movimentação.")
                    
def ArmazenarAcoes_Diario():
    pasta = "Investegra/env/ações"
    pasta_destino = "Investegra/env/ações/dia"
    
    for arquivo in os.listdir(pasta):
        if arquivo.endswith(".csv"):
        
            data_str = arquivo.split("-")[1]
            dia = int(data_str)
            
            match dia:
                case 5 : 
                    caminho_final = shutil.move(f'{pasta}/{arquivo}', f'{pasta_destino}/{arquivo}')
                    print(f"Arquivo {arquivo} movido para {caminho_final}")
                case _ :
                    print(f"Arquivo {arquivo} não corresponde ao critério de movimentação.")
                    
