# INVESTEGRA 📊

O **INVESTEGRA** é uma plataforma desenvolvida com o objetivo de facilitar a organização e o acompanhamento de investimentos em um único ambiente.

A proposta do projeto surgiu da necessidade de evitar a utilização de diferentes plataformas para consultar investimentos, permitindo que o usuário tenha suas informações reunidas e organizadas de forma simples e prática.

## 🎯 Objetivo

O principal objetivo do INVESTEGRA é auxiliar o usuário no **gerenciamento e acompanhamento de seus investimentos**, proporcionando uma visão mais organizada de sua carteira e das opções disponíveis no mercado.

A plataforma busca reunir diferentes tipos de investimentos em um único sistema, facilitando a consulta, organização e análise das informações.

## 💡 Funcionalidades

Entre as principais funcionalidades do INVESTEGRA estão:

* Cadastro e login de usuários;
* Criação e gerenciamento de carteiras;
* Adição e organização de investimentos;
* Visualização dos ativos presentes em cada carteira;
* Acompanhamento dos investimentos;
* Análise de diferentes tipos de ativos;
* Organização dos investimentos por categorias;
* Criação de carteiras personalizadas;
* Calculadoras de juros simples e compostos.

## 📈 Tipos de investimentos

O INVESTEGRA contempla diferentes modalidades de investimentos, incluindo:

**Renda Fixa**

* CDB;
* LCI;
* Tesouro Direto.

**Renda Variável**

* Ações;
* Fundos Imobiliários (FIIs);
* ETFs;
* Criptomoedas.

## 🖥️ Tecnologias utilizadas

O projeto foi desenvolvido utilizando:

* **PHP**
* **MySQL**
* **HTML**
* **CSS**
* **JavaScript**
* **Chart.js**
* **Docker**

## Dados de mercado e renda fixa

Ativos de mercado exibem a cotação e a variação diária consultadas na Brapi. ETFs são localizados
e cotados pelo endpoint de tickers da API. A cotação de criptomoedas exige `BRAPI_API_KEY` no
ambiente do servidor. Os FIIs usam a cotação pública da Brapi; para obter indicadores de qualquer
FII na simulação de bola de neve, é necessário acesso ao plano Pro. Sem a chave, a Brapi limita
esses indicadores a MXRF11 e HGLG11.

O cadastro de renda fixa é manual e registra produto/emissor, valor aplicado, indexador, taxa e
vencimento. Aportes e resgates atualizam o valor aplicado e são registrados no histórico. A Brapi
não fornece cotação de mercado para esses títulos através do fluxo de ações.

Informe manualmente a quantidade de cotas dos ativos de mercado.
Em bancos já existentes, inclusive volumes Docker já criados, execute
`Investegra/database/migrations/20261126_adicionar_dados_renda_fixa.sql` no banco atual pelo
phpMyAdmin ou por um cliente MySQL. O arquivo `Investegra/database/Investegra.sql` é executado
automaticamente apenas quando o volume do MySQL é criado pela primeira vez.

## 📊 Modelagem do sistema

Para auxiliar no planejamento e desenvolvimento do projeto, foram elaborados diagramas que representam diferentes aspectos do funcionamento do INVESTEGRA, como o **Diagrama de Classes**, **Diagrama de Sequência** e o **Diagrama de casos de uso**.

Essas representações ajudaram a compreender melhor as funcionalidades do sistema e a relação entre seus diferentes elementos.

## 🚧 Status do projeto

**Em desenvolvimento.**

O INVESTEGRA é um projeto acadêmico que continua sendo aprimorado, podendo receber novas funcionalidades e melhorias ao longo de seu desenvolvimento.

## 👨‍💻 Sobre o projeto

O INVESTEGRA foi desenvolvido como um projeto acadêmico com o propósito de aplicar, na prática, conhecimentos relacionados ao desenvolvimento de sistemas, banco de dados, programação e análise de software.

> **INVESTEGRA — Seus investimentos organizados em um só lugar.** 📈