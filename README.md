# Weather Forecast Application

A Symfony-based weather forecast application that allows users to check weather conditions for any city and save their favorite cities. The application uses the OpenWeatherMap API to fetch real-time weather data.

## Features

- Real-time weather data for any city
- 5-day weather forecast with temperature trends
- User authentication and registration
- Favorite cities management
- Automatic weather data updates for favorite cities
- Rate limiting to prevent API abuse
- Caching to optimize API calls

## Requirements

- PHP 8.0 or higher
- Composer
- Symfony CLI
- MySQL/PostgreSQL database
- OpenWeatherMap API key

## Installation

1. Clone the repository:
```bash
git clone https://github.com/emreires/weather-api-symfony.git
cd weather-api-symfony
```

2. Install dependencies:
```bash
composer install
```

3. Create a `.env.local` file and configure your environment variables:
```env
APP_ENV=dev
APP_SECRET=your_secret_here
DATABASE_URL="mysql://user:password@127.0.0.1:3306/weather_db"
OPENWEATHERMAP_API_KEY=your_api_key_here
JWT_SECRET_KEY=your_jwt_secret_key
JWT_PUBLIC_KEY=your_jwt_public_key
JWT_PASSPHRASE=your_jwt_passphrase
CORS_ALLOW_ORIGIN='^https?://(localhost|127\.0\.0\.1)(:[0-9]+)?$'
```

4. Create the database:
```bash
php bin/console doctrine:database:create
```

5. Run migrations:
```bash
php bin/console doctrine:migrations:migrate
```

6. Generate JWT keys:
```bash
php bin/console lexik:jwt:generate-keypair
```

7. Start the Symfony development server:
```bash
symfony server:start
```

8. In a separate terminal, start the message consumer for background jobs:
```bash
php bin/console messenger:consume async
```

## Usage

1. Register a new account or log in to an existing one
2. Search for a city to view its current weather and forecast
3. Add cities to your favorites for quick access
4. View weather data for all your favorite cities in one place

## API Endpoints

- `POST /api/register` - Register a new user
- `POST /api/login` - Authenticate user and get JWT token
- `GET /api/weather/current/{city}/{countryCode}` - Get current weather for a city
- `GET /api/weather/forecast/{city}/{countryCode}` - Get 5-day forecast for a city
- `GET /api/weather/favorites` - Get weather data for all favorite cities
- `GET /api/favorite-cities` - List user's favorite cities
- `POST /api/favorite-cities` - Add a new favorite city
- `DELETE /api/favorite-cities/{id}` - Remove a favorite city

## Scheduled Updates

To schedule weather data updates for favorite cities, run:
```bash
php bin/console app:schedule-weather-updates
```

## Development

### Running Tests

```bash
php bin/phpunit
```

### Code Style

Check code style:
```bash
vendor/bin/php-cs-fixer fix --dry-run --diff
```

Fix code style:
```bash
vendor/bin/php-cs-fixer fix
```

### Static Analysis

Run PHPStan:
```bash
vendor/bin/phpstan analyse src tests --level=5
```

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## License

This project is licensed under the MIT License - see the LICENSE file for details. 