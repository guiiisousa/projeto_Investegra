import sys
import os
from wsgiref import headers
from acesso import Acesso
import requests
sys.path.append(os.path.abspath(os.path.join(os.path.dirname(__file__), "..", "..", "..")))

acesso = Acesso()

def carregarPáginasAcoes():

    if acesso.response_ações.status_code == 200:
        return "Página carregada com sucesso!"
    else:
        print(f"Falha ao acessar. Código: {acesso.response_ações.status_code}")

def carregarPáginasFiis():

    if acesso.response_fiis.status_code == 200:
        return "Página carregada com sucesso!"
    else: 
        print(f"Falha ao acessar. Código: {acesso.response_fiis.status_code}")

# Passando url como parâmetro já que há 3 urls para criptomoedas, e o processo de pegar os dados é o mesmo para as 3 páginas. Assim, evita-se a repetição de código.
def carregarPáginasCripto(url):

    if acesso.response_cripto.status_code == 200:
        return "Página carregada com sucesso!"
    else: 
        print(f"Falha ao acessar. Código: {acesso.response_cripto.status_code}")

def carregarPáginasEtfs():

    if acesso.response_etfs.status_code == 200:
        return "Página carregada com sucesso!"
    else: 
        print(f"Falha ao acessar. Código: {acesso.response_etfs.status_code}")

def carregarPáginasBdrs():

    if acesso.response_bdrs.status_code == 200:
        return "Página carregada com sucesso!"
    else: 
        print(f"Falha ao acessar. Código: {acesso.response_bdrs.status_code}")
    