# Car Rental Management System

## Overview
The **Car Rental Management System** is a web application designed to streamline the operations of a car rental business. It includes tools for managing cars, clients, reservations, and vehicle maintenance, providing an intuitive and efficient user experience.

## Features
- **Car Management**: Add, update, and manage the fleet of cars available for rent.
- **Client Management**: Store and manage detailed information about clients.
- **Reservations**: Schedule and manage car bookings with real-time availability tracking.
- **Maintenance Management**: Record and monitor maintenance activities for all vehicles.

## Technologies Used
- **Backend**: Symfony 7
- **Frontend**: Twig, Bootstrap, CSS, JavaScript

## Installation
1. Clone the repository:
   ```bash
   git clone https://github.com/yourusername/car-rental-management.git
   ```
2. Navigate to the project directory:
   ```bash
   cd car-rental-management
   ```
3. Install dependencies:
   ```bash
   composer install
   npm install
   ```
4. Set up the database and update the `.env` file with your credentials.
5. Run migrations:
   ```bash
   php bin/console doctrine:migrations:migrate
   ```
6. Compile assets:
   ```bash
   npm run dev
   ```
7. Start the development server:
   ```bash
   symfony server:start
   ```

## Usage
1. Access the application in your browser at `http://localhost:8000`.
2. Log in to the admin panel to manage cars, clients, reservations, and maintenance.
