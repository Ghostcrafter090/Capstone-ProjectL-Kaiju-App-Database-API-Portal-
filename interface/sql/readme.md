# Commands for updating the database will be located here. The app will need to use the api to communicate with the database. The API is fairly easy to use. As outlined bellow:

# To actually use the API, the game needs an account associated with it, to do that:

<server>/interface?comm=addauth&userId=<string>&passwd=<string>

# The above command (if succsessful) will return a "HTTP200 Ok" response.
# userId: the users username
# passwd: the users password

# Once an account has been created, the game instance will actually have to authenticate itself on the server. Here is the process for that:

<server>/interface?comm=auth&userId=<string>&passwd=<string>

# The above command will return a sessionId, in the form of a 64 character base64 string, which is used to further access the API. This sessionId will expire with 1 minute of no activity from the client. Requireing the client to rerun the above command.
# userId: the users username
# passwd: the users password

# Once the client has received there sessionId, they can than use that to access the database using the following commands:

<server>/interface/index.php?comm=get&userId=<string>&sessionId=<base64>&table=<string>
<Post-Data> data: <json>

// Data Submission Structure
// -------------------------------------------------------------------------------
// {
//     "condition": {
//         "name": <string>
//         "value": <string>
//         "valuetype": <string>
//     }
// }

# The above command allows the retreival of any key within any table.
# sessionId: the users sessionId most recently retreived from the authentication process outlined above.
# table: the name of the table that data is being accessed from. // Note: leaving blank, or setting to "all" or "*", will return data for all tables.

<server>/interface/index.php?comm=update&userId=<string>&sessionId=<base64>&table=<string>
<Post-Data> data: <json>

// Data Submission Structure
// -------------------------------------------------------------------------------
// {
//     "condition": {
//         "name": <string>,
//         "value": <string>,
//         "valuetype": <string> <-- used as stmt type ('s' for string for example)
//     },
//     "set": {
//         "key0": <string>,
//         "key1": <string>,
//         "key<...>": <string>,
//         "key<n>": <string>
//     },
//     "valuetypes": "<key0 type><key1 type>...<ken<n> type>"
// }

# The above command allows the updating of any key within any table.
# sessionId: the users sessionId most recently retreived from the authentication process outlined above.
# table: the name of the table that data is being accessed from. (required)

<server>/interface/index.php?comm=insert&userId=<string>&sessionId=<base64>&table=<string>
<Post-Data> data: <json>

// Data Submission Structure
// -------------------------------------------------------------------------------
// {
//     "set": {
//         "key0": <string>,
//         "key1": <string>,
//         "key<...>": <string>,
//         "key<n>": <string>
//     },
//     "valuetypes": "<key0 type><key1 type>...<ken<n> type>"
// }

# The above command allows the inserting of data within any table.
# sessionId: the users sessionId most recently retreived from the authentication process outlined above.
# table: the name of the table that data is being accessed from. (required)