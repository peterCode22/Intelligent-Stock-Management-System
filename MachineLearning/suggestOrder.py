#This script returns a json file which has ProductID : Quantity Difference pairs
#The quantity difference is based on predicted quantity for preconfigured time period
#(which is specified in trainConfig.json) and current quantity of said product
#For example if predicted quantity sold for ProductID = 1 should be 100 in
#said time period and current quantity is 90, the json entry will be '1': '10'

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

with open(os.path.dirname(__file__) + '/trainConfig.json', 'r') as file:
    config = json.load(file)
if config['delivery'] == 'weekly':
    timePeriod = 6
else:
    timePeriod = 29

#Dates
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

#Store sql returns in pandas dataframe
prodDF = pd.DataFrame(prodResult, columns=['Product', 'RPrice'])
batchDF = pd.DataFrame(batchResult, columns=['BatchID', 'ProdID', 'Quantity', 'Name'])
#Sum up the quantity of each product
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
#Daily sum of each product's predicted quantity
predictionDB = salesDB.groupby('Product')['Predicted'].sum().reset_index()
#Compare daily predicted quantity with actual
#store the difference in diffProd dictionary
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
