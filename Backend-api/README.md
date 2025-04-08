# Student Grading API (Laravel)

This project is a backend API built with Laravel to manage and analyze student grades. It was developed as part of a technical test for Team Fostering.

##  Features

- Normalized relational schema (Students, Profiles, Modules, Grades)
- Average grade calculation with classification:
  - **Pass**: 40%+
  - **Merit**: 60%+
  - **Distinction**: 70%+
- Filter grades by student ID(s)
- Toggle summary/classification via `summary_only` query param
- Clean architecture (Controller → Service → Repository)
- Fully documented with Swagger (OpenAPI 3.0)
- Query validation using FormRequest
- Eager loading & indexed queries for performance

##  Tech Stack

- Laravel 10
- MySQL / MariaDB
- PHP 8.x
- L5-Swagger
- PHPUnit (for optional testing)

##  Installation

```bash
git clone https://github.com/your-repo/laravel-student-grades.git
cd laravel-student-grades
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
```

##  Authentication

- JWT Auth is required for accessing the `/api/v1/students/grades` endpoint.
- Pass your token via the `Authorization: Bearer <token>` header.

## API Usage

**GET /api/v1/students/grades**

| Query Param     | Type    | Description                                     |
|------------------|---------|-------------------------------------------------|
| `students[]`     | string  | Filter by student IDs (e.g. `123021S`)         |
| `summary_only`   | boolean | If true, hides classification label            |

**Example:**

```bash
curl -X GET "https://yourdomain.test/api/v1/students/grades?students[]=123021S&summary_only=true"   -H "Authorization: Bearer {your_token}"
```

##  Sample Data

Seeder generates:
- 10 students
- 10 matching profiles
- 3 modules
- Grades for each student in each module

##  API Docs (Swagger)

Visit:

```
http://yourdomain.test/api/documentation
```

After running:

```bash
php artisan l5-swagger:generate
```
