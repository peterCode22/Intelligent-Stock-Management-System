import mysql.connector as dbCon
import pandas as pd
import matplotlib.pyplot as plt
import numpy as np
import os
import glob
import pandas as pd
import datetime
from pickle import dump, load
from sklearn.preprocessing import StandardScaler
import math
import json

#Extracting current data
conn = dbCon.connect(host="localhost", user="root", password="cqX*5gk6^hvNFPvE", database="dsp")
#Date
currentDate = datetime.datetime.today()

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

with open('python/trainConfig.json', 'r') as file:
    config = json.load(file)

if config['delivery'] == 'weekly':
    timePeriod = 7
else:
    timePeriod = 30

endDate = currentDate + datetime.timedelta(days = timePeriod)

dateRange = pd.date_range(currentDate, endDate)

NN = load(open("python/model.pkl",'rb'))
scalerX = load(open("python/scalerX.pkl", 'rb'))
scalerY = load(open("python/scalerY.pkl", 'rb'))

inputData = []
for date in dateRange:
    for elem in prodResult:
        tempTup = elem + (date.weekday(), date.day, date.date())
        inputData.append(tempTup)

DF = pd.DataFrame(inputData, columns=['Product', 'Price', 'WeekDay', 'MonthDay', 'Date'])
inputDF = DF[['Product', 'Price', 'WeekDay', 'MonthDay']]

X = scalerX.transform(inputDF)

prediction = NN.predict(X)
prediction = prediction.reshape(-1,1)
prediction = scalerY.inverse_transform(prediction)

currData = batchDF.groupby('ProdID')['Quantity'].sum().reset_index()

fullDF = DF
fullDF['Prediction'] = np.ceil(prediction)

prodPredDF = fullDF.groupby('Product')['Prediction'].sum().reset_index()

#Go through all products
allProducts = prodDF['Product']
diffProd = {} #Dictionary of products and difference in their predicted quantity vs actual

dbCursor = conn.cursor()
salesSQL = 'INSERT INTO sales (DayT, ProductID, Predicted) VALUES (%s, %s, %s) ON DUPLICATE KEY UPDATE Predicted = %s'
for currDay in dateRange:
    for product in allProducts:
        tempPred = fullDF[(fullDF['Date'] == currDay.date()) & (fullDF['Product'] == product)]['Prediction'].values[0]
        dayVar = currDay.strftime('%Y-%m-%d')
        prodVar = int(product)
        insVar = (dayVar, prodVar, tempPred, tempPred)

conn.commit()
dbCursor.close()
conn.close()

for prod in allProducts:
    desiredQuantity = prodPredDF[prodPredDF['Product'] == prod]['Prediction'].values[0]
    if any(currData.ProdID == prod):
        currQuantity = currData[currData['ProdID'] == prod]['Quantity'].values[0]
        if currQuantity < desiredQuantity:
            diffProd[prod] = int(desiredQuantity - currQuantity)
        else:
            diffProd[prod] = 0
    else:
        diffProd[prod] = int(desiredQuantity)

import json

print(json.dumps(diffProd))