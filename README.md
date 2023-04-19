# README #

Jizdan README is a document for the steps necessary to get your application up and running.

### What is this repository for? ###

* Jizdan : A digital wallet provided as B2B fintech services, which allow performing many operations such as deposit, withdraw, withdraw on behalf vouchers generation and voucher redeem.
* Jizadn contains 3 type of users as shown below :
  admin: which has full access to create accounts, check all wallets, generate vouchers, logs, and reports.
  company (merchant): used to access Jizdan APIs and control all clients related to the company (like a middleware). Currently, Eduba and Miswarak is a company. Each company should be created by the admin and assigned a merchant key to this company.
  client : related to the company (like the students in Eduba )can perform many operations like deposit, withdraw, withdraw on behalf and voucher redeem. The client may be related to many companies and can be distinguished by the merchant key in the request header.

* Version 1


### How do I get set up? ###

* Jizdan is lumen application.
* Please run "Composer install" to install dependance.
* Please run the seeder (CreateAccounts seeder) when migrate the data.
* Please to generate passport client and put the details in .env file
    * PASSPORT_CLIENT_SECRET=Secret Key 
    * PASSPORT_CLIENT_ID= Client ID 
    * PASSPORT_LOGIN_ENDPOINT=/oauth/token 

* Please to generate application key.
* Response codes explain as shown below :
  * SUCCESS_CODE =1021
  * ERROR_CODE = 1022
  * NOT_FOUND_CODE = 1023
  * FORBIDDEN_CODE = 1024
  * VALIDATION_CODE = 1025
  * TOKEN_MISMATCH_CODE = 1026
  * METHOD_NOT_ALLOWED_CODE = 1027
  * AUTHORIZATION_EXCEPTION_CODE = 1028
  * THROTTLE_REQUESTS_CODE = 1029
  * UNAUTHORIZED_TOKEN_CODE = 1030
  * GENERAL_ERROR_CODE = 1031
  * MAINTENANCE_MODE_CODE = 1032
  * VOUCHER_USED_CODE = 1033

* Seeder create 3 accounts as shown below : (update 17/9/2022)
  * username: admin@test.com / password : password
  * username: company@test.co / password : password
  * username: client@test.co / password : password

* Passport does not work on  "php -S localhost:8000 -t public"
* Postman API collection have 2 folder :
  * Admin : All APIs related to admin.
  * Company(merchant): All APIs related to the company and the client of company.


