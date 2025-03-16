# Weather Forecast API & Web App

A modern weather forecast application built with Symfony that provides weather information through a REST API and a beautiful web interface.

## Features

- User Authentication with JWT
- Favorite Cities Management
- Real-time Weather Data from OpenWeatherMap API
- Rate Limiting & Caching
- Weather Data Visualization with Chart.js
- Automated Weather Data Updates via Symfony Messenger & RabbitMQ

## Requirements

- PHP 8.2 or higher
- Composer
- Symfony CLI
- MySQL/PostgreSQL
- RabbitMQ (for background jobs)

## Installation

1. Clone the repository:
```bash
git clone [repository-url]
cd weather-api
```

2. Install dependencies:
```bash
composer install
```

3. Configure your environment:
```bash
cp .env.example .env
```

4. Update the `.env` file with your database credentials and OpenWeatherMap API key:
```env
DATABASE_URL="mysql://user:password@127.0.0.1:3306/weather_db"
OPENWEATHERMAP_API_KEY="your-api-key"
```

5. Create the database:
```bash
php bin/console doctrine:database:create
```

6. Run migrations:
```bash
php bin/console doctrine:migrations:migrate
```

7. Generate JWT keys:
```bash
php bin/console lexik:jwt:generate-keypair
```

8. Start the development server:
```bash
symfony server:start
```

## API Documentation

Once the application is running, you can access the API documentation at:
```
http://localhost:8000/api/doc
```

## Development

- Run tests:
```bash
php bin/phpunit
```

- Start RabbitMQ consumer:
```bash
php bin/console messenger:consume async
```

## License

This project is licensed under the MIT License. 