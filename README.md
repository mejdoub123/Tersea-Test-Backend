# Tersea-Test-Backend - Laravel REST API

This is a backend application built with Laravel that serves as a mini CRM (Customer Relationship Management) system. The purpose of this technical test is to evaluate your problem-solving skills and code structuring abilities.

The application connects a company with its employees, allowing the company's administrator to create a company and invite employees to join via email. Once an employee is invited and registered, they can access and modify their own data, as well as view the data of their company and colleagues.

The application consists of two protected areas, each requiring authentication:

1. Administrator Area:
   - This section is accessible only to administrators.
   - Administrators have the privilege to create new companies.
   - Administrators can invite employees to join a company by sending email invitations.
   - Administrators can manage and update company information.

2. Employee Area:
   - This section is accessible to registered employees.
   - Employees can view and edit their personal information.
   - Employees can access and view data related to their company and colleagues.

## Installation

1. Clone the repository:

git clone https://github.com/your-username/Tearsea-Test-Backend.git


2. Install the dependencies by navigating into the project directory and running the following command:

composer install


3. Create a copy of the `.env.example` file and rename it to `.env`. Configure the database connection and email settings in this file.

4. Run the database migrations to set up the required tables:

php artisan migrate

5. Start the development server:

php artisan serve

## API Endpoints

The application provides the following REST API endpoints:

- `POST /api/register`: Register a new admin account (Administrator only).
- `POST /api/login`: Authenticate and generate an access token.
- `POST /api/logout`: Invalidate the access token and log out the user.
- `POST /api/companies`: Create a new company (Administrator only).
- `POST /api/invite`: Invite an employee to join a company by email and name (Administrator only).
- `GET /api/companies`: Retrieve a list of all companies (Administrator only).
- `GET /api/companies/{company_id}`: Retrieve details of a specific company (Administrator only).
- `PUT /api/companies/{company_id}`: Update the details of a specific company (Administrator only).
- `DELETE /api/companies/{company_id}`: Delete a specific company (Administrator only).
- `GET /api/employees`: Retrieve a list of all employees (Administrator only).
- `GET /api/employees/{employee_id}`: Retrieve details of a specific employee (Administrator only).
- `GET /api/invitations/{admin_id}`: Retrieve a list of all invitations and their status of a specific admin (Administrator only).
- `GET /api/histories`: Retrieve a list of all admins histories (Administrator only).
- `GET /api/histories/{admin_id}`: Retrieve a list of histories of a specific admin (Administrator only).
- `PUT /api/users/{user_id}`: Update the details of a spicefic user (Administrator or Employee).

## Conclusion

This mini CRM backend provides a foundation for managing companies and employees, allowing administrators to create companies, invite employees, and manage company and employee data. Employees can access and modify their own information, as well as view company and colleague data.

Feel free to explore the provided API endpoints and customize the application.
