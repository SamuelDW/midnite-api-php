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
I will create 3 tables, Users, TransactionTypes, AlertCodes