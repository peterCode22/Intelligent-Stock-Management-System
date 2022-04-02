#This script checks current products in the database, and shapes the
#fetched data along with date information and feeds it to the trained
#prediction model and stores the prediction in the database.

import mysql.connector as dbCon
import pandas as pd
import numpy as np
import os
import glob
import pandas as pd
import datetime
from pickle import dump, load
from sklearn.preprocessing import StandardScaler
import math
import json

conn = dbCon.connect(host="localhost", user="root", password="cqX*5gk6^hvNFPvE", database="dsp")
#Date
tomorrowDate = datetime.datetime.today() + datetime.timedelta(days=1) 

#Current products
prodCursor = conn.cursor()
prodSQL = 'SELECT ProdID, RetailPrice FROM products'
prodCursor.execute(prodSQL)
prodResult = prodCursor.fetchall()
prodCursor.close()

prodDF = pd.DataFrame(prodResult, columns=['Product', 'RPrice'])

with open(os.path.dirname(__file__) + '/trainConfig.json', 'r') as file:
    config = json.load(file)

if config['delivery'] == 'weekly':
    timePeriod = 6
else:
    timePeriod = 29

endDate = tomorrowDate + datetime.timedelta(days = timePeriod)

dateRange = pd.date_range(tomorrowDate, endDate)

#Load neural network model and scalers
NN = load(open(os.path.dirname(__file__) + "/Models/model.pkl",'rb'))
scalerX = load(open(os.path.dirname(__file__) + "Models/scalerX.pkl", 'rb'))
scalerY = load(open(os.path.dirname(__file__) + "Models/scalerY.pkl", 'rb'))

#Store input data for the ML model in an array of tuples
inputData = []
for date in dateRange:
    for elem in prodResult:
        tempTup = elem + (date.weekday(), date.day, date.date())
        inputData.append(tempTup)

#Convert the input array to a dataframe
DF = pd.DataFrame(inputData, columns=['Product', 'Price', 'WeekDay', 'MonthDay', 'Date'])
inputDF = DF[['Product', 'Price', 'WeekDay', 'MonthDay']]

X = scalerX.transform(inputDF)

prediction = NN.predict(X)

#The prediction is scaled using Standard Scaler
#so it needs to be "unscaled" back to original form
prediction = prediction.reshape(-1,1)
prediction = scalerY.inverse_transform(prediction)

fullDF = DF
fullDF['Prediction'] = np.ceil(prediction)

#Go through all products
allProducts = prodDF['Product']

#Function that stores predictions in the database
def predictInDB():
    dbCursor = conn.cursor()
    salesSQL = 'INSERT INTO sales (DayT, ProductID, Predicted) VALUES (%s, %s, %s) ON DUPLICATE KEY UPDATE Predicted = %s'
    for currDay in dateRange:
        for product in allProducts:
            tempPred = fullDF[(fullDF['Date'] == currDay.date()) & (fullDF['Product'] == product)]['Prediction'].values[0]
            dayVar = currDay.strftime('%Y-%m-%d')
            prodVar = int(product)
            insVar = (dayVar, prodVar, tempPred, tempPred)
            dbCursor.execute(salesSQL, insVar)
    conn.commit()
    dbCursor.close()

predictInDB()
conn.close()