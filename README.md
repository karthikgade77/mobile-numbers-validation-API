SA mobile numbers validation

PHP JSON API that can validate phone numbers

### Running The Project

You need [Docker](https://docker.com) installed first.

The local project is meant to be run through docker-compose to provide a seamless development environment between every developer machine.

`make composer-install`

To install all the Composer dependencies.

`make start`

to initiate the following:
* NGINX server (localhost:8080)
* PHP7 
* Database (localhost:3306)
* Testing Database (localhost:3307)
* PhpMyAdmin (localhost:8000)

It may be needed to wait a few seconds (only for the first time) for the SQL scripts to complete so that is possible to run tests. 

`make test`

To run the Unit and API tests.

To call the endpoints, it's needed to use the /v1/auth endpoint to authenticate, using the following JSON body:


`{
  "username": "username",
  "password": "password"
}`

### File Structure

The main project files are in /modules/v1.

Unit tests are in /tests/unit/models.

API tests are in /tests/api.

Configs are in /config.

### Features

I decided to implement the authentication using a Bearer Token. The client uses the Auth endpoint to send a username and password that will match in the database and return the Bearer JWT. After that, the client will need to use the token in the Authorization Header until it expires and the client will need to request a new one.

I decided to implement API version control. The version 1 of the API is available. If needed, it's possible to implement a new version and to keep the first one available until it's safe to disable it.

I implemented Unit tests that test specific features (model functions) of the API, and API tests, that test endpoint calling with correct params.

I implemented the "validate file" endpoint as a binary POST endpoint. This is what makes sense for a JSON API. The other way of implementing it would be a multipart/form-data and the API doesn't have form-data requests.

PDO MySQL PHP extension helps preventing SQL Injection.

### Notes

The environment variables are public in the repository. This may be fine for a local project with local databases, but it would not be optimal in a public web service. For that I would need to store the variables using CI/CD and pull them to the project. In this challenge there was no need to do that. 

I only implemented two ways of fixing phone numbers. Removing non digits and adding the full country indicative. For me, those seem to be the ideal. It doesn't make sense to add a single country indicative number or reverse the number. It would only produce invalid numbers.

It's not possible to validate the same number or the same identifier. It doesn't make sense to save duplicate numbers and ids in a database.

I focused on South African numbers and didn't implement other types of numbers.

### Endpoints

For the full documentation of each endpoint, click here:
https://documenter.getpostman.com/view/1391010/S17qTVNu

| Endpoint                           | Method    | Description                                                                                                                                   |
| :---                               | :---    | :---                                                                                                                                           |
| http://localhost:8080/v1/auth      |  POST   | Endpoint to authenticate the client with an username and password. The user will receive a JWT and the expiration date. |
| http://localhost:8080/v1/phone     |  POST  | Endpoint that receives a single phone number and returns if it's valid, invalid or if it was fixed and how so. |
| http://localhost:8080/v1/file      |  POST | Endpoint that receives a binary file (CSV) and reads it to validate phone numbers. It will return the file id, file stats and a downloadable link of a JSON with all the info from the phone numbers, if they were valid, invalid or if they were fixed. |
| http://localhost:8080/v1/file/{id} |  GET  | Endpoint to get details about a specific file. It will return the file id, file stats and a downloadable link of a JSON with all the info from the phone numbers, if they were valid, invalid or if they were fixed. |


### Shortcuts

| Shortcut                       | Description                                                                                                                                      |
| :---                           | :---                                                                                                                                              |
| make composer-install          |  Install composer dependencies                                                                                                               |
| make start                     |  Start the development environment.                                                                                                               |
| make stop                      |  Stop the development environment.                                                                                                                |
| make restart                   |  Restart the development environment.                                                                                                             |
| make build                     |  Run if you changed any of the services initialization scripts. Will rebuild the docker-composer services.                              |
| make build-clean               |  Run if you changed any of the services initialization scripts. Will ignore cached images and rebuild the docker-composer services.              |
| make test                      |  Run automated tests.                                                                  |
| make space                     |  Remove all docker cached images.                                                                |
