USE dsp;

INSERT INTO PRODUCTS (ProdID, ProdName, RetailPrice, SupplierPrice) VALUES (1, 'Screwdriver', 4.5, 3.6);
INSERT INTO PRODUCTS (ProdID, ProdName, RetailPrice, SupplierPrice) VALUES (2, '2m Power Cable', 3.4, 2.5);
INSERT INTO PRODUCTS (ProdID, ProdName, RetailPrice, SupplierPrice) VALUES (3, 'Game controller', 100, 80);
INSERT INTO PRODUCTS (ProdID, ProdName, RetailPrice, SupplierPrice) VALUES (4, 'SD card 32GB', 10.5, 8.9);
INSERT INTO PRODUCTS (ProdID, ProdName, RetailPrice, SupplierPrice) VALUES (5, 'Wrench', 5, 4.2);

INSERT INTO USERS (UserID, Username, Password, FName, Surname, PhoneNo, Address, AccountType) VALUES 
(1, 'jackeen456', 'ba7816bf8f01cfea414140de5dae2223b00361a396177a9cb410ff61f20015ad', 'Jack', 'Enteree', '07700 900262', '23 Jackus Grove', 'customer');
INSERT INTO USERS (UserID, Username, Password, FName, Surname, PhoneNo, Address, AccountType) VALUES 
(2, 'jeaHal23', '6d970874d0db767a7058798973f22cf6589601edab57996312f2ef7b56e5584d', 'Jean', 'Hallow', '0131 496 0782', '45 Halloween Street', 'customer');
INSERT INTO USERS (UserID, Username, Password, FName, Surname, PhoneNo, Address, AccountType) VALUES 
(3, 'sarpeeBra57', 'fb8e20fc2e4c3f248c60c39bd652f3c1347298bb977b8b4d5903b85055620603', 'Brian', 'Sarpeel', '0118 496 0164', '890 Jolly Avenue', 'admin');
INSERT INTO USERS (UserID, Username, Password, FName, Surname, PhoneNo, Address, AccountType) VALUES 
(4, 'gerWitch945', 'ca978112ca1bbdcafac231b39a23dc4da786eff8147c4e72b9807785afee48bb', 'Geralt', 'Trapeck', '0116 496 0814', '189 Govert Street', 'admin');

INSERT INTO SUPPLIER_ORDERS (SuppOrdID, DTime, ManagerID) VALUES (1, '2021-01-05 10:22:05', 4);
INSERT INTO SUPPLIER_ORDERS (SuppOrdID, DTime, ManagerID) VALUES (2, '2021-03-24 15:45:08', 3);
INSERT INTO SUPPLIER_ORDERS (SuppOrdID, DTime, ManagerID) VALUES (3, '2021-02-16 21:38:41', 3);
INSERT INTO SUPPLIER_ORDERS (SuppOrdID, DTime, ManagerID) VALUES (4, '2021-04-30 08:10:19', 4);

INSERT INTO BATCHES (BatchID, ProdID, SuppOrdID, Quantity) VALUES (1, 5, 1, 200);
INSERT INTO BATCHES (BatchID, ProdID, SuppOrdID, Quantity) VALUES (2, 4, 2, 100);
INSERT INTO BATCHES (BatchID, ProdID, SuppOrdID, Quantity) VALUES (3, 2, 3, 150);
INSERT INTO BATCHES (BatchID, ProdID, SuppOrdID, Quantity) VALUES (4, 3, 4, 40);
INSERT INTO BATCHES (BatchID, ProdID, SuppOrdID, Quantity) VALUES (5, 1, 1, 30);
INSERT INTO BATCHES (BatchID, ProdID, SuppOrdID, Quantity) VALUES (6, 1, 4, 50);

INSERT INTO CUSTOMER_ORDERS (OrderID, OrderDate, CustomerID) VALUES (1, '2021-04-08 23:22:05', 2);
INSERT INTO CUSTOMER_ORDERS (OrderID, OrderDate, CustomerID) VALUES (2, '2021-04-18 13:45:15', 1);
INSERT INTO CUSTOMER_ORDERS (OrderID, OrderDate, CustomerID) VALUES (3, '2021-04-11 16:06:37', 1);

INSERT INTO ORDER_PRODUCTS (orderID, productID, prodQuantity) VALUES (1, 3, 10);
INSERT INTO ORDER_PRODUCTS (orderID, productID, prodQuantity) VALUES (1, 4, 5);
INSERT INTO ORDER_PRODUCTS (orderID, productID, prodQuantity) VALUES (1, 2, 4);
INSERT INTO ORDER_PRODUCTS (orderID, productID, prodQuantity) VALUES (2, 1, 5);
INSERT INTO ORDER_PRODUCTS (orderID, productID, prodQuantity) VALUES (2, 3, 30);
INSERT INTO ORDER_PRODUCTS (orderID, productID, prodQuantity) VALUES (3, 5, 10);
