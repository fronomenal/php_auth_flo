# PHP Request Server

A php server to demonstrate a feature full auth flow.

Implements **login | logout**, **register | account deletion**, and **password change** all with mock email confirmation using sessions.


## Technologies

### Stack
Project is created with: 
* PHP 8
* CSS
* Javascript
* SQL(sqlite)

### Packages
Project makes use of no external packages


## Run Locally

### Setup
- Have php be installed and shell executable
- Make sure the sqlite and pdo_sqlite extensions are enabled in your php.ini files.

### Launch
In the root directory perform the following:

- Navigate to the src directory.
- In the src dir, use this command to launch a local php server. 
    php -S localhost:8080


## Misc
**Please Note**
    Even though this runs locally, the dns verification for email address requires internet access to register.
