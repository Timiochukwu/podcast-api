# Podcast Platform API (Laravel 12)

A robust and scalable RESTful API for a podcast platform built with Laravel 12. This API allows clients to browse podcasts, episodes, and categories, with authentication for content creators.

## Features

- **Category Management**: Browse, create, update and delete podcast categories
- **Podcast Management**: Browse featured podcasts, search by categories, and manage podcasts
- **Episode Management**: Browse latest episodes, search by podcasts, and manage episodes
- **Authentication**: Secure authentication using Laravel Sanctum
- **Rate Limiting**: API rate limiting to prevent abuse
- **Comprehensive Testing**: Feature tests for main endpoints


## API Documentation

The API documentation is available as a Postman collection:
To use the Postman collection:
1. Download the collection file from the `/docs` directory
2. Open Postman
3. Click "Import" and select the downloaded file
4. Set up environment variables:
   - `baseUrl`: Your API URL (default: http://localhost:8000)
   - `accessToken`: Authentication token (obtained after login)

The collection includes all endpoints with request parameters, headers, and example payloads.


## Tech Stack

- PHP 8.4
- Laravel 12
- MySQL 8.0
- Redis (for caching and rate limiting)
- Docker & Docker Compose
- Postman for documentation

## Project Structure

The project follows Laravel's standard architecture with additional patterns:

- **Repository Pattern**: Separates business logic from controllers
- **Resource Classes**: Standardized API responses
- **Form Requests**: Request validation
- **Middleware**: Authentication and rate limiting

## Prerequisites

- PHP 8.4 or higher
- Composer
- MySQL or SQLite
- Git

## Installation & Setup

### Local Setup

1. Clone the repository:
   ```bash
   git clone https://github.com/your-username/podcast-api.git
   cd podcast-api
   ```

2. Install dependencies:
   ```bash
   composer install
   ```

3. Copy the environment file and update with your database credentials:
   ```bash
   cp .env.example .env
   ```

4. Generate application key:
   ```bash
   php artisan key:generate
   ```

5. Create the database:
   ```sql
   CREATE DATABASE podcast_api;
   ```

6. Update your `.env` file with database credentials:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=podcast_api
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

7. Run migrations to create database tables:
   ```bash
   php artisan migrate
   ```

8. Seed the database with sample data:
   ```bash
   php artisan db:seed
   ```

9. Start the development server:
   ```bash
   php artisan serve
   ```

10. Your API should now be available at: http://127.0.0.1:8000/api/v1

### Docker Setup (Optional)

1. Make sure Docker and Docker Compose are installed on your system.

2. Update the `docker-compose.yml` file if needed.

3. Build and start the containers:
   ```bash
   docker-compose up -d
   ```

4. Run commands inside the container:
   ```bash
   docker-compose exec app php artisan migrate
   docker-compose exec app php artisan db:seed
   ```

## API Documentation

The API documentation is available at:

```
http://localhost:8000/api/v1/documentation
```

## API Endpoints

### Authentication

- `POST /api/v1/auth/register` - Register a new user
- `POST /api/v1/auth/login` - Login and get access token
- `POST /api/v1/auth/logout` - Logout and invalidate token (requires authentication)
- `GET /api/v1/auth/user` - Get authenticated user information (requires authentication)

### Categories

- `GET /api/v1/categories` - Get all categories
- `GET /api/v1/categories/featured` - Get featured categories
- `GET /api/v1/categories/{slug}` - Get category by slug
- `GET /api/v1/categories/{slug}/podcasts` - Get podcasts by category
- `POST /api/v1/categories` - Create a new category (requires authentication)
- `PUT /api/v1/categories/{id}` - Update a category (requires authentication)
- `DELETE /api/v1/categories/{id}` - Delete a category (requires authentication)

### Podcasts

- `GET /api/v1/podcasts` - Get all podcasts
- `GET /api/v1/podcasts/featured` - Get featured podcasts
- `GET /api/v1/podcasts/{slug}` - Get podcast by slug
- `GET /api/v1/podcasts/{slug}/episodes` - Get episodes by podcast
- `POST /api/v1/podcasts` - Create a new podcast (requires authentication)
- `PUT /api/v1/podcasts/{id}` - Update a podcast (requires authentication)
- `DELETE /api/v1/podcasts/{id}` - Delete a podcast (requires authentication)

### Episodes

- `GET /api/v1/episodes` - Get all episodes
- `GET /api/v1/episodes/featured` - Get featured episodes
- `GET /api/v1/episodes/recent` - Get recent episodes
- `GET /api/v1/episodes/{slug}` - Get episode by slug
- `POST /api/v1/episodes` - Create a new episode (requires authentication)
- `PUT /api/v1/episodes/{id}` - Update an episode (requires authentication)
- `DELETE /api/v1/episodes/{id}` - Delete an episode (requires authentication)

## Testing

Run the tests using PHPUnit:

```bash
php artisan test
```

## Rate Limiting

The API implements rate limiting to prevent abuse:

- By default, the API allows 60 requests per minute per user or IP address
- Rate limit information is included in the response headers:
  - `X-RateLimit-Limit`: Maximum number of requests per minute
  - `X-RateLimit-Remaining`: Number of requests remaining in the current window
  - `X-RateLimit-Reset`: Time until the rate limit window resets in seconds

## Authentication

The API uses Laravel Sanctum for token-based authentication:

1. Register a new user or login to get an access token
2. Include the token in your requests as a Bearer token in the Authorization header:
   ```
   Authorization: Bearer {your_token}
   ```

## Troubleshooting

If you encounter any issues during setup:

1. **Missing Application Key**: Run `php artisan key:generate`
2. **Database Connection Issues**: Check your `.env` file and database credentials
3. **Migration Issues**: Make sure all migration files are properly created and formatted
4. **Seeder Problems**: Check your model and factory relationships

## Database Structure

All necessary migration files are included in this repository, so you don't need to create them manually. When you run `php artisan migrate`, Laravel will automatically create all the required tables:

- `categories` - Stores podcast categories
- `podcasts` - Stores podcast information
- `episodes` - Stores podcast episodes
- `tags` - Stores tags for categorizing podcasts
- `podcast_tag` - Pivot table for podcast-tag relationships

The database schema includes appropriate indexes and relationships to ensure optimal performance and data integrity.
