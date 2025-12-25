# User & Task Management API

A RESTful API for managing users and tasks, built with Laravel. This API demonstrates core backend development principles including API design, data modeling, database interaction, validation, and error handling.

## Features

- ✅ Complete CRUD operations for Users and Tasks
- ✅ Data validation with proper error messages
- ✅ Pagination support for list endpoints
- ✅ Task filtering by user
- ✅ Comprehensive error handling with appropriate HTTP status codes
- ✅ Database relationships (User has many Tasks)
- ✅ Logging mechanism
- ✅ Full test coverage with PHPUnit

## Tech Stack

- **Framework**: Laravel 11
- **Database**: MySQL/PostgreSQL (configurable)
- **Testing**: PHPUnit
- **PHP Version**: 8.2+

## Prerequisites

- PHP 8.2 or higher
- Composer
- MySQL or PostgreSQL
- Laravel Herd (optional, for local development)

## Installation & Setup

### 1. Clone the Repository

```bash
git clone <repository-url>
cd Benefits-Hero
```

### 2. Install Dependencies

```bash
composer install
```

### 3. Environment Configuration

Copy the example environment file and configure your database:

```bash
cp .env.example .env
```

Update the following variables in `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=user_task_db
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 4. Generate Application Key

```bash
php artisan key:generate
```

### 5. Run Migrations

```bash
php artisan migrate
```

### 6. Start the Development Server

```bash
php artisan serve
```

The API will be available at `http://localhost:8000`

## API Documentation

### Base URL

```
http://localhost:8000/api
```

### User Endpoints

#### 1. Create a New User

**POST** `/api/users`

**Request Body:**
```json
{
  "username": "johndoe",
  "email": "john@example.com"
}
```

**Response (201 Created):**
```json
{
  "id": 1,
  "username": "johndoe",
  "email": "john@example.com",
  "created_at": "2025-12-25T10:00:00.000000Z"
}
```

**Validation Rules:**
- `username`: required, string, max 255 characters, unique
- `email`: required, valid email format, max 255 characters, unique

---

#### 2. Get All Users

**GET** `/api/users`

**Query Parameters:**
- `page` (optional): Page number (default: 1)
- `limit` (optional): Items per page (default: 10)

**Response (200 OK):**
```json
{
  "data": [
    {
      "id": 1,
      "username": "johndoe",
      "email": "john@example.com",
      "created_at": "2025-12-25T10:00:00.000000Z"
    }
  ],
  "pagination": {
    "page": 1,
    "limit": 10,
    "total": 1,
    "totalPages": 1
  }
}
```

---

#### 3. Get User by ID

**GET** `/api/users/{id}`

**Response (200 OK):**
```json
{
  "id": 1,
  "username": "johndoe",
  "email": "john@example.com",
  "created_at": "2025-12-25T10:00:00.000000Z"
}
```

**Error Response (404 Not Found):**
```json
{
  "error": "User not found"
}
```

---

### Task Endpoints

#### 1. Create a New Task

**POST** `/api/tasks`

**Request Body:**
```json
{
  "title": "Complete project documentation",
  "description": "Write comprehensive API documentation",
  "status": "pending",
  "userId": 1
}
```

**Response (201 Created):**
```json
{
  "id": 1,
  "title": "Complete project documentation",
  "description": "Write comprehensive API documentation",
  "status": "pending",
  "user_id": 1,
  "created_at": "2025-12-25T10:00:00.000000Z",
  "updated_at": "2025-12-25T10:00:00.000000Z",
  "user": {
    "id": 1,
    "username": "johndoe",
    "email": "john@example.com",
    "created_at": "2025-12-25T10:00:00.000000Z"
  }
}
```

**Validation Rules:**
- `title`: required, string, max 255 characters
- `description`: optional, string
- `status`: optional, must be one of: `pending`, `in-progress`, `completed` (default: `pending`)
- `userId`: required, integer, must exist in users table

---

#### 2. Get All Tasks

**GET** `/api/tasks`

**Query Parameters:**
- `page` (optional): Page number (default: 1)
- `limit` (optional): Items per page (default: 10)
- `userId` (optional): Filter tasks by user ID

**Response (200 OK):**
```json
{
  "data": [
    {
      "id": 1,
      "title": "Complete project documentation",
      "description": "Write comprehensive API documentation",
      "status": "pending",
      "user_id": 1,
      "created_at": "2025-12-25T10:00:00.000000Z",
      "updated_at": "2025-12-25T10:00:00.000000Z",
      "user": {
        "id": 1,
        "username": "johndoe",
        "email": "john@example.com",
        "created_at": "2025-12-25T10:00:00.000000Z"
      }
    }
  ],
  "pagination": {
    "page": 1,
    "limit": 10,
    "total": 1,
    "totalPages": 1
  }
}
```

**Example with User Filter:**
```
GET /api/tasks?userId=1
```

---

#### 3. Get Task by ID

**GET** `/api/tasks/{id}`

**Response (200 OK):**
```json
{
  "id": 1,
  "title": "Complete project documentation",
  "description": "Write comprehensive API documentation",
  "status": "pending",
  "user_id": 1,
  "created_at": "2025-12-25T10:00:00.000000Z",
  "updated_at": "2025-12-25T10:00:00.000000Z",
  "user": {
    "id": 1,
    "username": "johndoe",
    "email": "john@example.com",
    "created_at": "2025-12-25T10:00:00.000000Z"
  }
}
```

---

#### 4. Update a Task

**PUT** `/api/tasks/{id}`

**Request Body (all fields optional):**
```json
{
  "title": "Updated title",
  "description": "Updated description",
  "status": "in-progress"
}
```

**Response (200 OK):**
```json
{
  "id": 1,
  "title": "Updated title",
  "description": "Updated description",
  "status": "in-progress",
  "user_id": 1,
  "created_at": "2025-12-25T10:00:00.000000Z",
  "updated_at": "2025-12-25T10:30:00.000000Z",
  "user": {
    "id": 1,
    "username": "johndoe",
    "email": "john@example.com",
    "created_at": "2025-12-25T10:00:00.000000Z"
  }
}
```

**Validation Rules:**
- `title`: optional, string, max 255 characters
- `description`: optional, string
- `status`: optional, must be one of: `pending`, `in-progress`, `completed`

---

#### 5. Delete a Task

**DELETE** `/api/tasks/{id}`

**Response (200 OK):**
```json
{
  "message": "Task deleted successfully"
}
```

**Error Response (404 Not Found):**
```json
{
  "error": "Task not found"
}
```

---

## HTTP Status Codes

The API uses the following HTTP status codes:

- `200 OK`: Request succeeded
- `201 Created`: Resource created successfully
- `400 Bad Request`: Invalid request data
- `404 Not Found`: Resource not found
- `422 Unprocessable Entity`: Validation failed
- `500 Internal Server Error`: Server error

## Error Response Format

All error responses follow this format:

```json
{
  "error": "Error message description"
}
```

For validation errors (422):

```json
{
  "status": "error",
  "message": "Validation failed",
  "errors": [
    {
      "field": "email",
      "message": "The email has already been taken."
    }
  ]
}
```

## Database Schema

### Users Table

| Column      | Type      | Constraints           |
|-------------|-----------|-----------------------|
| id          | bigint    | Primary Key, Auto Inc |
| username    | string    | Unique, Not Null      |
| email       | string    | Unique, Not Null      |
| created_at  | timestamp | Not Null              |

### Tasks Table

| Column      | Type      | Constraints                          |
|-------------|-----------|--------------------------------------|
| id          | bigint    | Primary Key, Auto Inc                |
| title       | string    | Not Null                             |
| description | text      | Nullable                             |
| status      | enum      | 'pending', 'in-progress', 'completed'|
| user_id     | bigint    | Foreign Key (users.id), Cascade      |
| created_at  | timestamp | Not Null                             |
| updated_at  | timestamp | Not Null                             |

## Running Tests

The project includes comprehensive test coverage for all endpoints.

### Run All Tests

```bash
php artisan test
```

### Run Specific Test File

```bash
php artisan test --filter=UserApiTest
php artisan test --filter=TaskApiTest
```

### Run Tests with Coverage

```bash
php artisan test --coverage
```

### Test Coverage

The test suite includes:

- ✅ User creation with validation
- ✅ Duplicate username/email prevention
- ✅ Invalid email format validation
- ✅ User listing with pagination
- ✅ User retrieval by ID
- ✅ Task creation with validation
- ✅ Task listing with pagination
- ✅ Task filtering by user ID
- ✅ Task retrieval by ID
- ✅ Task updates (full and partial)
- ✅ Task deletion
- ✅ 404 error handling for non-existent resources
- ✅ Foreign key validation

## Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   └── Api/
│   │       ├── UserController.php
│   │       └── TaskController.php
│   └── Requests/
│       ├── StoreUserRequest.php
│       ├── StoreTaskRequest.php
│       └── UpdateTaskRequest.php
├── Models/
│   ├── User.php
│   └── Task.php
database/
├── migrations/
│   ├── 0001_01_01_000000_create_users_table.php
│   └── 2025_12_25_105948_create_tasks_table.php
routes/
└── api.php
tests/
└── Feature/
    ├── UserApiTest.php
    └── TaskApiTest.php
```

## Code Quality Features

### Separation of Concerns

- **Controllers**: Handle HTTP requests and responses
- **Form Requests**: Validate incoming data
- **Models**: Define database relationships and data structure
- **Routes**: Define API endpoints

### Data Validation

All POST and PUT requests are validated using Laravel Form Requests with:
- Required field validation
- Type validation
- Format validation (email)
- Uniqueness validation
- Enum validation for status field
- Foreign key existence validation

### Error Handling

- Graceful error handling with try-catch blocks
- Appropriate HTTP status codes (404, 400, 422, 500)
- Consistent error response format
- Logging of errors for debugging

### Database Design

- Efficient schema with proper indexes
- Foreign key constraints with cascade delete
- Proper use of relationships (User hasMany Tasks, Task belongsTo User)
- Timestamps for audit trail

## Logging

The application logs errors to:
- `storage/logs/laravel.log`

Log levels can be configured in the `.env` file:
```env
LOG_CHANNEL=stack
LOG_LEVEL=debug
```

## Example Usage with cURL

### Create a User

```bash
curl -X POST http://localhost:8000/api/users \
  -H "Content-Type: application/json" \
  -d '{
    "username": "johndoe",
    "email": "john@example.com"
  }'
```

### Get All Users

```bash
curl http://localhost:8000/api/users
```

### Create a Task

```bash
curl -X POST http://localhost:8000/api/tasks \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Complete documentation",
    "description": "Write API docs",
    "status": "pending",
    "userId": 1
  }'
```

### Get Tasks for a Specific User

```bash
curl http://localhost:8000/api/tasks?userId=1
```

### Update a Task

```bash
curl -X PUT http://localhost:8000/api/tasks/1 \
  -H "Content-Type: application/json" \
  -d '{
    "status": "completed"
  }'
```

### Delete a Task

```bash
curl -X DELETE http://localhost:8000/api/tasks/1
```

## Development Best Practices

This project demonstrates:

1. **Clean Code**: Readable, well-structured code with proper naming conventions
2. **SOLID Principles**: Single responsibility, dependency injection
3. **RESTful Design**: Proper use of HTTP methods and status codes
4. **Data Validation**: Input validation on all write operations
5. **Error Handling**: Comprehensive error handling with meaningful messages
6. **Testing**: Full test coverage for all endpoints
7. **Documentation**: Clear API documentation with examples
8. **Git Practices**: Clear commit history showing development progression

## Future Enhancements

Potential improvements for production use:

- Authentication & Authorization (Laravel Sanctum/Passport)
- Rate limiting
- API versioning
- Request/Response transformers
- Soft deletes for tasks
- Task priority and due dates
- User roles and permissions
- Advanced filtering and sorting
- API documentation with Swagger/OpenAPI

## License

This project is open-source and available under the MIT License.

## Support

For issues or questions, please create an issue in the repository.
