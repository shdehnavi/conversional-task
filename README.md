# Invoicing API task

## Table of Contents

- [Tech Stack](#tech-stack)
- [How to Run the Project](#how-to-run-the-project)
- [Live Application](#live-application)
- [Tools and Features](#tools-and-features)
- [Additional Notes](#additional-notes)

---

## Tech Stack

- **PHP**: `v8.3`
- **Laravel Framework**: `v11.36`
- **PostgreSQL**: `v16.3`

---

## How to Run the Project

This project includes a Dockerized setup for simplified development and deployment. Two Docker compose configurations are provided:

- **Local Setup**:
    - **File:** `./docker-compose-local.yml`.
    - **Use Case:** Developers who prefer running the project locally with development-specific settings on their host machine.

- **Dev Setup**:
    - **File:** `./docker-compose-dev.yml`.
    - **Use Case:** Running the project with all dependencies installed inside the container, requiring minimal setup on the host machine. This setup avoids using any volume for the PostgreSQL database to prevent potential permission issues for the test purpose.

### Steps to Run the Project
For a seamless experience without any permission issues, it is recommended to use the `docker-compose-dev.yml` file. Run the following command to build and start the containers:
   ```bash
   docker compose -f docker-compose-dev.yml up -d --build
   ```
Once the containers are up, you don't need to do anything more, everything is set up, and you can access the application through the following url:
   ```
   http://localhost:2025
   ```
Also, you can see the routes and all API specifications in the API docs page:
   ```
   http://localhost:2025/docs/api
   ```

---

## Live Application

A live version of this application is available at:
- **URL:** [conv-task.kingcode.app](https://conv-task.kingcode.app)
- **API Documentation:** [conv-task.kingcode.app/docs/api](https://conv-task.kingcode.app/docs/api)

---

## Tools and Features

- **Larastan (PHPStan)**
- **Pest (Unit and feature tests)**
- **Pest Type Coverage**
- **Pint (PHP CS Fixer)**
- **GitHub Actions**:
    - Automated CI/CD workflows for running tests, code style check and static analysis.
    - These workflows can be viewed in the **Actions** page of the repository.

---

## Additional Notes
- **CORS** and **Trust Proxies** configurations in Laravel were not set up, as this is a test project.
- **Rate limiting** is configured through Laravel to control and limit API request traffic.
- **Factories** are created for all models to facilitate testing and seeding.
- **Mock data** is stored in the following file:
`./database/seeders/data/mock.json`, The database is populated with this mock data using the `MockDataSeeder` class, which is automatically executed in the Docker setup. I used the sample data excel file that was attached in the email.
---- 
- When creating an **invoice** for a specified period (e.g., `2021-01-01` to `2021-02-01`), any sessions or registrations from earlier periods **that have not been invoiced** are **not included** in the current invoice calculations. **Uninvoiced sessions and registrations** from **earlier periods** are not considered and do not affect the calculations for the current invoicing period. To include earlier sessions or registrations in the current period's invoice calculations, they must first be invoiced before creating the new invoice.
- The frequency of events occurring within a period is calculated based on session activations, appointments, and user creation fields. Duplicate and non-invoiced events (eg: cheaper events) are included in these counts, which can be viewed through the `GET /invoice` API.
