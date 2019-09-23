# PostgreSQLSessionHandler
A PHP Sesionhandler which uses its own database preferable on its own server for scaling purposes.
The reason behind the writing of this session handler is for the development of YottaDots, scaling and the collaboration with websocket. The websocket itself didn't handle sessions in the way it was wanted.

This session handler writes the sessiondata in the database so independent of the webserver used it will alwys collect the right session data belonging to the set cookie. 
