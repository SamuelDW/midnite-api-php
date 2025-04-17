# Midnite API technical Test.

This technical test is to build an API that takes in a user id, action and amount alongside a timestamp and return a series of codes, alarms and the users id

## Assumptions 

1. That the user_id may not exist, in which case, this needs to be handled
2. Users accounts can't dip below 0, it must be a positive amount.
3. Time is in seconds

## Setting the project up

### Requirements
1. Apache
2. PHP 8.2
3. MariaDB/MySQL.


## Routes

1. `/event`
Expected Payload:
```json
{
    "type": "deposit",
    "amount": "42.00",
    "user_id": 1,
    "time": 10
}
```

## Database Models
I will create 4 tables, Users, TransactionTypes, AlertCodes and Transactions

Users should be self explanatory

TransactionTypes is for future transaction types, perhaps there may be more than just deposit and withdrawal

Alert Codes for a list of the type of records to match against

Transactions for recording user transactions


### How I approached this

1. Define what information needed recording to be able to complete checks.
2. Setup the CakePHP project.


### Issues and challenges. 
1. No idea why, a new project wasn't allowing the type hinting to function properly.


### Possible Improvements

1. Could potentially do the transaction and in the background check the account, utilising a 202 response