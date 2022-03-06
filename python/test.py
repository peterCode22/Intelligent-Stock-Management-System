from time import time
import mysql.connector as dbCon
import pandas as pd
import matplotlib.pyplot as plt
import numpy as np
import os
import glob
import pandas as pd
import datetime
import math
import json
from sklearn.metrics import mean_squared_error

conn = dbCon.connect(host="localhost", user="root", password="cqX*5gk6^hvNFPvE", database="dsp")

with open('python/trainConfig.json', 'r') as file:
    config = json.load(file)
if config['delivery'] == 'weekly':
    timePeriod = 7
else:
    timePeriod = 30

tempTodayDate = datetime.datetime.strptime('2022-03-13', '%Y-%m-%d')

today = datetime.datetime.today().date()

startDate = tempTodayDate - datetime.timedelta(days = timePeriod)
endDate = tempTodayDate

smeDict = {}

salesSQL = "SELECT Quantity, Predicted FROM sales WHERE (Predicted IS NOT NULL) AND (DayT BETWEEN %s AND %s)"
param = (startDate, endDate)
salesCursor = conn.cursor()
salesCursor.execute(salesSQL, param)
salesResult = salesCursor.fetchall()
salesCursor.close()

salesDB = pd.DataFrame(salesResult, columns=['Quantity', 'Predicted'])

err = mean_squared_error(salesDB['Quantity'], salesDB['Predicted'])

if err > config['SME']:
    print (0)
else:
    print (1)

conn.close()