import os 
import datetime

class Paths():
    agora = datetime.datetime.now().strftime("%d-%m-%Y_%H-%M-%S")

print(Paths.agora)