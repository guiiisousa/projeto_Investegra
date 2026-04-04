import sys
import os

sys.path.append(
    os.path.abspath(os.path.join(os.path.dirname(__file__), "..", "..", ".."))
)

from Investegra.env.classes import *

#Recarregar as Ações do fundamentus#
def RecarregarPagFundamentus_Acoes():
    acesso = Acesso()
    if acesso.response_ações.status_code == 200:
        print("Página carregada com sucesso!")
    else:
        print(f"Falha ao acessar. Código: {acesso.response_ações.status_code}")

#Recarregar os FIIs do fundamentus#
def RecarregarPagFundamentus_Fiis():
    acesso = Acesso()
    if acesso.response_fiis.status_code == 200:
        print("Página carregada com sucesso!")
    else:
        print(f"Falha ao acessar. Código: {acesso.response_fiis.status_code}")