# Midnite API technical Test.

This technical test is to build an API that takes in a user id, action and amount alongside a timestamp and return a series of codes, alarms and the users id

## Assumptions 

1. That the user_id may not exist, in which case, this needs to be handled
2. Time is in seconds
3. That for code 123, it does not matter if the deposit amount is not consecutive, if it fits into a 30 second window, it should error. Withdrawals still count towards the time limit
4. Users should not be able to withdraw into a negative balance.
5. Time must be greater than 0, 0 is usually treated as a null value in most languages

## Setting the project up

### Requirements
1. Apache
2. PHP 8.2
3. MariaDB/MySQL.
4. Either Postman or other such application

### Setup
1. Download the folder, extract the files, or clone from GitHub  and place them into an empty folder.
2. Create a table in your database called `midnite_api_php`.
2. Run the command `composer install`.
3. Run the command `composer initialis` this will set up the database and seed some data into the relevant tables.
4. The project should be started using `bin/cake server` after navigating to the project location in the command line.

5. Tests can be run with composer test


## Routes

1. POST `/event`
Expected Payload:
```json
{
    "type": "deposit",
    "amount": "42.00",
    "user_id": 1,
    "time": 10
}
```

Expected Response
```json
{
    "user_id": 1,
    "alerts": true,
    "alert_codes": [1100, 123]
}

## Database Models
I will create 4 tables, Users, TransactionTypes, Transactions and AlertCodes

Users should be self explanatory

TransactionTypes is for future transaction types, perhaps there may be more than just deposit and withdrawal

Alert Codes for a list of the type of records to match against
Addendum, as it turns out, couldn't quite figure why this was needed, potentially could just be a table for info or later it could be improved with conditions attached to each. 

Transactions for recording user transactions


### How I approached this

1. Define what information needed recording to be able to complete checks. Found this would a users table, a transaction types table, and a transactions table at minimum
2. Setup the CakePHP project.
3. Get the initial request working, confirming that the event is being hit.
4. Start writing the tests for expected responses and expected failures
5. Started writing defensively, making sure that values are as expected, and returning an error response if something didn't match. 
6. Started on the checks, I started with the easiest, code 1100, as this only depended on the current input. Then I followed with 30 and 300, as these both depended on deposits and withdrawals consecutively, which was easier to implement with a limit on transactions pulled from the database.
7. Finished off all the tests for the tables


### Issues and challenges. 
1. No idea why, a new project wasn't allowing the type hinting to function properly. Not like usual, though I have recently switched linting tools so this may be the issue. Doesn't affect functionality
2. The deposit in a certain amount of time, I've assumed this was in seconds, rather than anything else, if it was in milliseconds, of course the function that deals with this could be handled to deal with milliseconds, or a boolean flag to determine time frames
3. Alert Codes table, whilst I thought it might be important, I actually discovered that I couldn't find a use for it that would be effective more so than what was added
4. Code 123 was a bit of a puzzle. On the one hand, part of it was simple, if the transaction most recent is a deposit and is above 200, its an alert, if it isn't, how does one calculate it. I
dealt with this by adding the time from either transaction types to a counter, and only adding the amount to the limit if the transaction is a deposit. 


### Possible Improvements

1. Could potentially do the transaction and in the background check the account, utilising a 202 response and return the full response after it has done processing, however that might require additional resources that could be better spent elsewhere. If the async parts of JS or Python are enough, should be able to return the full response
2. In regards to point one, each check could be its own process, and in languages such as Python and JavaScript, make use of async abilities to improve the response speed
3. Add tests for the Utility functions written.
4. Make use of the alert codes table is one possibility, however, given the conditions need to be checked manually, it may be easier to remove this table.