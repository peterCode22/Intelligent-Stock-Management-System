from time import time
import mysql.connector as dbCon
import pandas as pd
import numpy as np
import os
import glob
import pandas as pd
import datetime
import math
import json
from sklearn.metrics import mean_squared_error
import matplotlib.pyplot as plt

import sys

conn = dbCon.connect(host="localhost", user="root", password="cqX*5gk6^hvNFPvE", database="dsp")

argc = len(sys.argv)
#if argc < 6:
    #exit()

reportType = sys.argv[1]
#'moneySales'#
dInterval = sys.argv[2]
#'weekly'#
reportFormat = sys.argv[3]
#'table' 
dFrom = sys.argv[4]
 #'2019-01-01'
dTo = sys.argv[5]
#'2019-02-01'#

if reportType == 'moneySales':
    sql = "SELECT * FROM money_sales WHERE DayT BETWEEN %s AND %s"
    salesCursor = conn.cursor()
    param = (dFrom, dTo)
    salesCursor.execute(sql, param)
    salesResult = salesCursor.fetchall()
    salesCursor.close()
    moneyDF = pd.DataFrame(salesResult, columns=['Date', 'Product', 'Quantity', 'Value'])
    moneyDF['Date'] = pd.to_datetime(moneyDF['Date'])

    if dInterval == 'daily':
        dailyDF = moneyDF.groupby('Date')['Value'].sum().reset_index()
        if reportFormat == 'graph':
            plt.figure(figsize=(20,10))
            plt.bar(x = dailyDF['Date'], height = dailyDF['Value'])
            plt.title('Daily sales summary')
            plt.xlabel('Date')
            plt.ylabel('Value(£)')
            plt.savefig('python/graph.jpg', bbox_inches = 'tight')
        else:
            html = dailyDF.to_html(index=False)
            print(html)
    else:
        weeklyDF = moneyDF
        weeklyDF['Week'] = weeklyDF['Date'].dt.isocalendar().week
        weeklyDF = weeklyDF.groupby('Week')['Value'].sum().reset_index()
        if  reportFormat == 'graph':
            plt.figure(figsize=(20,10))
            plt.bar(x = weeklyDF['Week'], height = weeklyDF['Value'], color = 'green')
            plt.title('Weekly sales summary')
            plt.xlabel('Week number')
            plt.ylabel('Value(£)')
            plt.savefig('python/graph.jpg', bbox_inches = 'tight')
        else:
            html = weeklyDF.to_html(index=False)
            print(html)


conn.close()