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

conn.close()

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
        tempTup = elem + (date.weekday(), date.day)
        inputData.append(tempTup)

inputDF = pd.DataFrame(inputData, columns=['Product', 'Price', 'WeekDay', 'MonthDay'])

X = scalerX.transform(inputDF)

prediction = NN.predict(X)
prediction = prediction.reshape(-1,1)
prediction = scalerY.inverse_transform(prediction)

currData = batchDF.groupby('ProdID')['Quantity'].sum().reset_index()

inputDF['Prediction'] = np.ceil(prediction)

prodPredDF = inputDF.groupby('Product')['Prediction'].sum().reset_index()
print(prodPredDF)

#Go through all products
allProducts = prodDF['Product']
diffProd = {} #Dictionary of products and difference in their predicted quantity vs actual

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