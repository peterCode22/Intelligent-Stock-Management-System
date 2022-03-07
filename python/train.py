import sys
import mysql.connector as dbCon
import pandas as pd
import numpy as np
import os
import glob
import pandas as pd
import datetime
from pickle import dump

#Extracting
conn = dbCon.connect(host="localhost", user="root", password="cqX*5gk6^hvNFPvE", database="dsp")

#Sales from specified period
saleCursor = conn.cursor()
dateFrom = sys.argv[1]
dateTo = sys.argv[2]
saleSQL = 'SELECT DayT, ProductID, Quantity, RetailPrice FROM sales INNER JOIN products ON sales.ProductID = products.ProdID WHERE DayT BETWEEN (%s) AND (%s)'
params = (dateFrom, dateTo)
saleCursor.execute(saleSQL, params)
saleResult = saleCursor.fetchall()
saleCursor.close()
conn.close()

salesDF = pd.DataFrame(saleResult, columns=['Date', 'Product', 'Quantity', 'Price'])

#Creating input and output dataframes
xSalesDF = salesDF[['Product']]
xSalesDF['Price'] = salesDF['Price']
xSalesDF['WeekDay'] = pd.to_datetime(salesDF['Date']).dt.dayofweek
xSalesDF['MonthDay'] = pd.to_datetime(salesDF['Date']).dt.day

from sklearn.preprocessing import StandardScaler
scalerX = StandardScaler()
scalerY = StandardScaler()
X = scalerX.fit_transform(xSalesDF)

ySalesDF = salesDF[['Quantity']]
Y = scalerY.fit_transform(ySalesDF) 

#Export scalers
dump(scalerX, open('scalerX.pkl', 'wb'))
dump(scalerY, open('scalerY.pkl', 'wb'))

#Training
from sklearn.neural_network import MLPRegressor
bpnn = MLPRegressor(hidden_layer_sizes=(10,10), solver='sgd', learning_rate='constant', learning_rate_init=0.0005, activation='tanh', max_iter = 1000)
bpnn.fit(X, Y)

#Export model
dump(bpnn, open('model.pkl','wb'))
