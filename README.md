# ATLBackEnd

### Prerequisites

- [PHP](https://www.php.net/downloads.php)
- [MySQL](https://www.mysql.com/downloads/)
- [Composer](http://getcomposer.org/)

## Getting Started

### Configure the application

#### Setting up the database

```sql
CREATE DATABASE IF NOT EXISTS ATLBackend;
USE ATLBackend;

CREATE TABLE IF NOT EXISTS Contacts(
id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
firstname VARCHAR(30) NOT NULL,
lastname VARCHAR(30) NOT NULL,
email VARCHAR(50)
);

CREATE TABLE Contacts_Phones(
id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
contactId INT UNSIGNED,
phone VARCHAR(15),
FOREIGN KEY (contactId) REFERENCES Contacts(id)
);
```

Copy `.env.example` and rename to `.env` file and enter your database details.

## Development

Install the project dependencies:

```sh
composer install
```

Start the application on the desired port:

```sh
php -S localhost:port -t api
```

## Test with API Client

Use an API client to test the app. Ex: [Postman](https://www.postman.com/).

The available endpoints are:

```sh
GET:
localhost:port/contacts - All database contacts
localhost:port/contacts/:id - Single database contact

POST:
localhost:port/contacts - Properties ->
                              firstName (string, required),
                              lastName (string, required),
                              email (string, optional),
                              phones(array, required)

PUT / DELETE:
localhost:port/contacts/:id

```
