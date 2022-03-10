from ast import arg
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
argDict = json.loads(sys.argv[1])
if bool(argDict['month']): #month
    date = datetime.datetime.strptime(argDict['month'], '%Y-%m')
    month = date.month
    year = date.year
    monthNames = ["January",
                "Febuary",
                "March",
                "April",
                "May",
                "June",
                "July",
                "August",
                "September",
                "October",
                "November",
                "December"]

    if argDict['type'] == 'moneySales':
        sql = "SELECT DayT, Value FROM money_sales WHERE MONTH(DayT) = %s AND YEAR(DayT) = %s"
        salesCursor = conn.cursor()
        param = (month, year)
        salesCursor.execute(sql, param)
        salesResult = salesCursor.fetchall()
        salesCursor.close()
        moneyDF = pd.DataFrame(salesResult, columns=['Date', 'Value'])
        moneyDF['Day'] = pd.to_datetime(moneyDF['Date']).dt.day
        dailyDF = moneyDF.groupby('Day')['Value'].sum().reset_index()
        dailyPreviousMoneyDF = None
        dailyPredictionMoneyDF = None

        if 'previous' in argDict:
            previousCursor = conn.cursor()
            paramPrevious = (month - 1, year)
            previousCursor.execute(sql, paramPrevious)
            previousResult = previousCursor.fetchall()
            previousCursor.close()
            previousMoneyDF = pd.DataFrame(data=previousResult, columns=['Date', 'PrevValue'])
            previousMoneyDF['Day'] = pd.to_datetime(previousMoneyDF['Date']).dt.day
            dailyPreviousMoneyDF = previousMoneyDF.groupby('Day')['PrevValue'].sum().reset_index()

        if 'prediction' in argDict:
            predictionCursor = conn.cursor()
            predictionSql = 'SELECT DayT, Predicted_Value from money_pred WHERE MONTH(DayT) = %s AND YEAR(DayT) = %s'
            predictionCursor.execute(predictionSql, param)
            predictionResult = predictionCursor.fetchall()
            predictionCursor.close()
            predictionMoneyDF = pd.DataFrame(data=predictionResult, columns=['Date', 'PredValue'])
            predictionMoneyDF['Day'] = pd.to_datetime(predictionMoneyDF['Date']).dt.day
            dailyPredictionMoneyDF = predictionMoneyDF.groupby('Day')['PredValue'].sum().reset_index()

        if argDict['format'] == 'graph':
            plt.figure(dpi=200)
            plt.bar(x = dailyDF['Day'], height = dailyDF['Value'], color = 'green', label='Actual sales')
            plt.title('Daily sales summary in ' + monthNames[month - 1] + ' ' + str(year))
            plt.xlabel('Day')
            plt.ylabel('Value(£)')
            
            if dailyPredictionMoneyDF is not None:
                plt.plot(dailyPredictionMoneyDF['Day'], dailyPredictionMoneyDF['PredValue'], c = 'purple', label='Predicted sales')

            if dailyPreviousMoneyDF is not None:
                plt.plot(dailyPreviousMoneyDF['Day'], dailyPreviousMoneyDF['PrevValue'], c = 'orange', label="Last month's sales")
            
            
            plt.legend()
            plt.savefig('python/graph.jpg', bbox_inches = 'tight')

        else: #table
            
            table = dailyDF
            if dailyPredictionMoneyDF is not None:
                table = pd.merge(table, dailyPredictionMoneyDF, how='outer')
            
            if dailyPreviousMoneyDF is not None:
                table = pd.merge(table, dailyPreviousMoneyDF, how='outer')

            htmlResult = table.to_html(index=False)
            print(htmlResult)

    if argDict['type'] == 'prodSales':
        sql = "SELECT DayT, Quantity FROM sales WHERE MONTH(DayT) = %s AND YEAR(DayT) = %s AND ProductID = %s"
        productCursor = conn.cursor()
        product = int(argDict['prodID'])
        param = (month, year, product)
        productCursor.execute(sql, param)
        productResult = productCursor.fetchall()
        productCursor.close()
        productDF = pd.DataFrame(productResult, columns=['Date', 'Quantity'])
        productDF['Day'] = pd.to_datetime(productDF['Date']).dt.day
        dailyProdDF = productDF.groupby('Day')['Quantity'].sum().reset_index()
        dailyPreviousProductDF = None
        dailyPredictionProductDF = None

        if 'previous' in argDict:
            previousCursor = conn.cursor()
            paramPrevious = (month - 1, year, product)
            previousCursor.execute(sql, paramPrevious)
            previousResult = previousCursor.fetchall()
            previousCursor.close()
            previousProductDF = pd.DataFrame(data=previousResult, columns=['Date', 'PreviousQuantity'])
            previousProductDF['Day'] = pd.to_datetime(previousProductDF['Date']).dt.day
            dailyPreviousProductDF = previousProductDF.groupby('Day')['PreviousQuantity'].sum().reset_index()

        if 'prediction' in argDict:
            predictionCursor = conn.cursor()
            predictionSql = 'SELECT DayT, Predicted from sales WHERE MONTH(DayT) = %s AND YEAR(DayT) = %s AND ProductID = %s'
            predictionCursor.execute(predictionSql, param)
            predictionResult = predictionCursor.fetchall()
            predictionCursor.close()
            predictionProductDF = pd.DataFrame(data=predictionResult, columns=['Date', 'PredictedQuantity'])
            predictionProductDF['Day'] = pd.to_datetime(predictionProductDF['Date']).dt.day
            dailyPredictionProductDF = predictionProductDF.groupby('Day')['PredictedQuantity'].sum().reset_index()

        if argDict['format'] == 'graph':
            plt.figure(dpi=200)
            plt.bar(x = dailyProdDF['Day'], height = dailyProdDF['Quantity'], color = 'green', label='Actual quantity sold')
            plt.title('Daily sales of product ID ' + str(product) + ' in ' + monthNames[month - 1] + ' ' + str(year))
            plt.xlabel('Day')
            plt.ylabel('Quantity')
            
            if dailyPredictionProductDF is not None:
                plt.plot(dailyPredictionProductDF['Day'], dailyPredictionProductDF['PredictedQuantity'], c = 'purple', label='Predicted quantity')

            if dailyPreviousProductDF is not None:
                plt.plot(dailyPreviousProductDF['Day'], dailyPreviousProductDF['PreviousQuantity'], c = 'orange', label="Last month")
            
            
            plt.legend()
            plt.savefig('python/graph.jpg', bbox_inches = 'tight')

        else: #table
            
            table = dailyProdDF
            if dailyPredictionProductDF is not None:
                table = pd.merge(table, dailyPredictionProductDF, how='outer')
            
            if dailyPreviousProductDF is not None:
                table = pd.merge(table, dailyPreviousProductDF, how='outer')

            htmlResult = table.to_html(index=False)
            print(htmlResult)

 
conn.close()