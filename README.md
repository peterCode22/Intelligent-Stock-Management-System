# Description
This project provides a web interface with two views: one for a customer to buy pre-defined goods, second for a manager to manage stock. The main merit of this project is the use of a Machine Learning algorithm to predict demand, which in turns allows the system to suggest an order to restock the supplies. The manager can also view various data statistics in either text or graphical form including sales. The Machine Learning algorithm automatically assesses itself by checking whether its accuracy has exceeded a desired threshold over a specified time period. The model used in this project was Multi-Layered Perceptron from sci-kit learn module in Python.
Tests and Miscellaneous folder contains:
- data used to train and test the ML model,
- some of the scripts used to test correct functioning of the system
- data preprocessing to fit to this project's database.

# Setup
1. Set up local MySQL Server
2. Open MySQL Workbench and connect to localhost
3. Go to Server/Data Import
4. Import from Self-Contained File
5. Choose Dump.sql from DataPersistance folder.
6. Ensure Dump Structure and Data is selected
7. Start import
8. Go to projects Config/config.php
9. Change the connection string to match your MySQL Server setup in this file as well as python files in MachineLearning.
10. Set up wampserver on your machine.
11. Move all project files to *INSTALLATION DRIVE*/wamp64/www/*project name*
12. Start wampserver
13. You can now open the website at localhost/*project name*/index.php
