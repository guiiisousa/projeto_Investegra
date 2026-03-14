from brapi import Brapi
from flask import Flask, render_template

client = Brapi(api_key='YOUR_TOKEN')
quotes = client.quote.retrieve(
    tickers='PETR4,VALE3',
    range='5d',
    interval='1d',
    fundamental=True
)

print(quotes.results[0].regular_market_price)