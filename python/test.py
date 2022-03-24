#This script assesses the models accuracy in last date period
#which is predefined in trainConfig.json

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

today = datetime.datetime.today().date()

startDate = today - datetime.timedelta(days = timePeriod)
endDate = today

#Fetch database data of sold and predicted quantity of products
salesSQL = "SELECT Quantity, Predicted FROM sales WHERE (Predicted IS NOT NULL) AND (DayT BETWEEN %s AND %s)"
param = (startDate, endDate)
salesCursor = conn.cursor()
salesCursor.execute(salesSQL, param)
salesResult = salesCursor.fetchall()
salesCursor.close()

salesDB = pd.DataFrame(salesResult, columns=['Quantity', 'Predicted'])

err = mean_squared_error(salesDB['Quantity'], salesDB['Predicted'])

#Compare MSE to the specified maximum MSE level
if err > config['MSE']:
    print (0)
else:
    print (1)

conn.close()