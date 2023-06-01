# Verification Portal

Welcome to the Verification Portal! This application provides a RESTful API for user registration, authentication, and file verification.

## Getting Started

### Prerequisites

Make sure you have the following software installed on your machine:

- PHP (version 7.4 or higher)
- Composer
- MySQL

### Installation

1. Clone the repository:
   git clone https://github.com/vandanapareek/verification-portal.git

2. Install dependencies and set up the environment:
   cd verification-portal
   composer install
   cp .env.example .env

3. Update the necessary environment variables in the .env file, such as the database connection details.

4. Generate the application key:
   php artisan key:generate

5. Run the database migrations:
   php artisan migrate

6. Start the development server:
   php artisan serve

Congratulations! The Verification Portal is now up and running on http://localhost:8000.

## API Endpoints

### Register User

Registers a new user in the system.

- Endpoint: POST /api/register
- Request Body:
  {
    "name": "Alice",
    "email": "alice@test.com",
    "password": "password123"
  }
- Response:
  {
    "message": "Registration successful"
  }

### Login

Authenticates a user and generates an authentication token.

- Endpoint: POST /api/login
- Request Body:
  {
    "email": "alice@test.com",
    "password": "password123"
  }
- Response:
  {
    "token": "xxxxxxxxxxxxxxxxxxxxxxxxxxxx"
  }

### Verify File

Verifies a JSON file using the provided verification service.

- Endpoint: POST /api/verify
- Request Headers:
  Authorization: Bearer {token}
- Request Body:
  - Content-Type: multipart/form-data
  - Key: file
  - Value: Select a JSON file to upload
- Response:
  {
    "data": {
      "issuer": "ABC Company",
      "result": "valid"
    }
  }

## Testing

To run the automated tests for the Verification Portal, use the following command:

php artisan test

