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

NN = load(open("python/model.pkl",'rb'))
scalerX = load(open("python/scalerX.pkl", 'rb'))
scalerY = load(open("python/scalerY.pkl", 'rb'))

prodDF = pd.DataFrame(prodResult, columns=['Product', 'RPrice'])
batchDF = pd.DataFrame(batchResult, columns=['BatchID', 'ProdID', 'Quantity', 'Name'])

currData = batchDF.groupby('ProdID')['Quantity'].sum().reset_index()

inputData = prodDF[['Product']]
inputData['WeekDay'] = currentDate.weekday()
inputData['MonthDay'] = currentDate.day
inputData['Price'] = prodDF[prodDF['Product'] == inputData['Product']]['RPrice']

X = scalerX.transform(inputData)

prediction = NN.predict(X)
prediction = prediction.reshape(-1,1)
prediction = scalerY.inverse_transform(prediction)

#Go through all products
allProducts = prodDF['Product']
diffProd = {} #Dictionary of products and difference in their predicted quantity vs actual

index = 0
for prod in allProducts:
    desiredQuantity = math.ceil(prediction[index])
    if any(currData.ProdID == prod):
        currQuantity = currData[currData['ProdID'] == prod]['Quantity'].values[0]
        if currQuantity < desiredQuantity:
            diffProd[prod] = int(desiredQuantity - currQuantity)
        else:
            diffProd[prod] = 0
    else:
        diffProd[prod] = int(desiredQuantity)
    index = index + 1

import json

print(json.dumps(diffProd))