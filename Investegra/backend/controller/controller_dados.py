import bs4 as BeautifulSoup
import csv
import sys
import os
# sys.path.append(os.path.abspath(".."))
sys.path.append(
    os.path.abspath(os.path.join(os.path.dirname(__file__), "..", "..", ".."))
)
import Investegra.backend.config.config_pags as config_pags

acesso = config_pags.Acesso()

def GetAcoes():
    
    if acesso.response_ações.status_code == 200:
        
        soup = BeautifulSoup.BeautifulSoup(acesso.response_ações.text, 'html.parser')
        table = soup.find('table', {'id': 'resultado'})
        
        print(soup)
        
        if table:
            rows = table.find_all('tr')[1:]  # Ignorar o cabeçalho
            data = []
            
            for row in rows:
                cols = row.find_all('td')
                cols = [col.text.strip() for col in cols]
                data.append(cols)
           
            # Salvar os dados em um arquivo CSV
            with open(f'Investegra/env/ações/Dados_Acoes.csv', 'w', newline='', encoding='utf-8') as f:
                writer = csv.writer(f)
                writer.writerow(['Código', 'Empresa', 'Setor', 'Preço', 'P/L', 'P/VP', 'ROE', 'DY'])
                writer.writerows(data)
            
            print("Dados do Fundamentus salvos com sucesso!")
        else:
            print("Tabela de resultados não encontrada.")
    else:
        print(f"Erro: {acesso.response_ações.status_code}")
        
def GetFiis():
    
    if acesso.response_fiis.status_code == 200:
        
        
        soup = BeautifulSoup.BeautifulSoup(acesso.response_fiis.text, 'html.parser')
        table = soup.find('table', {'id': 'tabelaResultado'})
        
        if table:
            rows = table.find_all('tr')[1:]  # Ignorar o cabeçalho
            data = []
            
            for row in rows:
                cols = row.find_all('td')
                cols = [col.text.strip() for col in cols]
                data.append(cols)
           
            # Salvar os dados em um arquivo CSV
            with open(f'Investegra/env/fiis/Dados_Fiis.csv', 'w', newline='', encoding='utf-8') as f:
                writer = csv.writer(f)
                writer.writerow(['Código', 'Empresa', 'Setor', 'Preço', 'P/VP', 'DY'])
                writer.writerows(data)
            
            print("Dados do Fundamentus salvos com sucesso!")
        else:
            print("Tabela de resultados não encontrada.")
    else:
        print(f"Erro: {acesso.response_fiis.status_code}")