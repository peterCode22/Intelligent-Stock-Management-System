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

conn = dbCon.connect(host="localhost", user="root", password="cqX*5gk6^hvNFPvE", database="dsp")

with open('python/trainConfig.json', 'r') as file:
    config = json.load(file)
if config['delivery'] == 'weekly':
    timePeriod = 6
else:
    timePeriod = 29
#Date
tomorrowDate = datetime.datetime.today().date() + datetime.timedelta(days=1)
endDate = tomorrowDate + datetime.timedelta(days = timePeriod)
startDate = tomorrowDate.strftime('%Y-%m-%d') 
endDate = endDate.strftime('%Y-%m-%d')

#Current products
prodCursor = conn.cursor()
prodSQL = 'SELECT ProdID, RetailPrice FROM products'
prodCursor.execute(prodSQL)
prodResult = prodCursor.fetchall()
prodCursor.close()

#Current batches
batchCursor = conn.cursor()
batchSQL = 'SELECT * FROM batch_prod'
batchCursor.execute(batchSQL)
batchResult = batchCursor.fetchall()
batchCursor.close()

prodDF = pd.DataFrame(prodResult, columns=['Product', 'RPrice'])
batchDF = pd.DataFrame(batchResult, columns=['BatchID', 'ProdID', 'Quantity', 'Name'])

currData = batchDF.groupby('ProdID')['Quantity'].sum().reset_index()

#Go through all products
allProducts = prodDF['Product']
diffProd = {} #Dictionary of products and difference in their predicted quantity vs actual

salesSQL = "SELECT DayT, ProductID, Predicted FROM sales WHERE (Predicted IS NOT NULL) AND (DayT BETWEEN %s AND %s)"
param = (startDate, endDate)
salesCursor = conn.cursor()
salesCursor.execute(salesSQL, param)
salesResult = salesCursor.fetchall()
salesCursor.close()

conn.close()

salesDB = pd.DataFrame(salesResult, columns=['Date', 'Product', 'Predicted'])

predictionDB = salesDB.groupby('Product')['Predicted'].sum().reset_index()

for prod in allProducts:
    desiredQuantity = predictionDB[predictionDB['Product'] == prod]['Predicted'].values[0]
    if any(currData.ProdID == prod):
        currQuantity = currData[currData['ProdID'] == prod]['Quantity'].values[0]
        if currQuantity < desiredQuantity:
            diffProd[prod] = int(desiredQuantity - currQuantity)
        else:
            diffProd[prod] = 0
    else:
        diffProd[prod] = int(desiredQuantity)

print(json.dumps(diffProd))
