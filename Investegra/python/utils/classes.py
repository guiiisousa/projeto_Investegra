class Usuario():
    nome = ""
    email = ""
    senha = ""
    
class Perfil(Usuario):
    nome = Usuario.nome
    email = Usuario.email
    senha = Usuario.senha

class Login():
    usuario = Usuario()
    
class Cadastro():
    usuario = Usuario()
    
class Carteira():
    fiis = []
    acoes = []

class Carteira_Logada(Carteira):
    fiis = Carteira.fiis
    acoes = Carteira.acoes