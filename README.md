# Weather API Symfony

A Symfony-based weather forecast application that allows users to check weather conditions for any city and save their favorite cities.

## Requirements

- PHP 8.0 or higher
- Composer
- Symfony CLI (optional)

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

3. Configure your environment:
```bash
cp .env .env.local
```

4. Update the `.env.local` file with your configuration:
```env
APP_ENV=dev
APP_DEBUG=1
APP_SECRET=your_secret_here
```

5. Generate JWT keys:
```bash
php bin/console lexik:jwt:generate-keypair
```

6. Create the database and run migrations:
```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

7. Start the development server:
```bash
symfony server:start
```

## API Endpoints

### Authentication
- `POST /api/login_check` - Get JWT token

### Weather
- `GET /api/weather/{city}` - Get weather for a specific city
- `GET /api/weather/favorites` - Get weather for favorite cities
- `POST /api/weather/favorites/{city}` - Add city to favorites
- `DELETE /api/weather/favorites/{city}` - Remove city from favorites

## Development

### Running Tests
```bash
php bin/phpunit
```

### Code Quality
```bash
# Run PHPStan
vendor/bin/phpstan analyse

# Run PHP CS Fixer
vendor/bin/php-cs-fixer fix
```

## License

This project is licensed under the MIT License - see the LICENSE file for details. 