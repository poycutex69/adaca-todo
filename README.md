# Todo API - Laravel Project

## Setup Instructions

### 1. Clone the repository
```bash
# Clone this repository and navigate into the project directory
# git clone https://github.com/poycutex69/adaca-todo
cd adaca-todo
```

### 2. Install dependencies
```bash
composer install
npm install
```

### 3. Copy and configure environment file
```bash
cp .env.example .env
# Edit .env as needed (e.g., set DB_CONNECTION, DB_DATABASE, etc.)
```

### 4. Generate application key
```bash
php artisan key:generate
```

### 5. Run migrations and seeders
```bash
php artisan migrate --seed
```

### 6. Run the development server
```bash
php artisan serve
```

The API will be available at `http://localhost:8000/api/todos`.

### 7. Run tests
```bash
php artisan test
```

---

## Todo API Endpoints

All endpoints are prefixed with `/api/todos`.

| Method | Endpoint                | Description                        | Parameters (query/body)         |
|--------|-------------------------|------------------------------------|---------------------------------|
| GET    | /api/todos              | List todos (with filters)          | `search`, `completed`, `page`, `per_page` |
| POST   | /api/todos              | Create a new todo                  | `title` (string, required, 3-100 chars), `completed` (boolean, optional) |
| GET    | /api/todos/{id}         | Get a specific todo                | -                               |
| PUT    | /api/todos/{id}         | Update a todo                      | `title` (string, 3-100 chars), `completed` (boolean)   |
| PATCH  | /api/todos/{id}         | Update a todo (partial)            | `title` (string, 3-100 chars), `completed` (boolean)   |
| DELETE | /api/todos/{id}         | Delete a todo                      | -                               |

### Query Parameters for GET /api/todos
- `search` (string): Filter todos by title keyword
- `status` (string): Filter by status (`completed` or `pending`)
- `page` (integer): Page number for pagination
- `per_page` (integer): Number of items per page (default: 10)

### Example Requests
- List all todos: `GET /api/todos`
- Search todos: `GET /api/todos?search=meeting`
- Filter by status: `GET /api/todos?status=completed`
- Filter pending: `GET /api/todos?status=pending`
- Paginate: `GET /api/todos?page=2&per_page=5`
- Create: `POST /api/todos` with JSON `{ "title": "My Task" }`
- Update: `PUT /api/todos/1` with JSON `{ "completed": true }`
- Delete: `DELETE /api/todos/1`

---

## Testing with Postman

This project includes a comprehensive Postman collection (`Todo_API.postman_collection.json`) for testing all API endpoints.

### Importing the Collection

1. **Open Postman**
2. **Import the collection:**
   - Click "Import" in the top left
   - Select "File" tab
   - Choose `Todo_API.postman_collection.json` from the project root
   - Click "Import"

### Setting Up the Environment

1. **Update the base URL:**
   - The collection uses a variable `{{base_url}}` set to `http://localhost:8000`
   - If your Laravel server runs on a different port, update this variable:
     - Click on the collection name
     - Go to "Variables" tab
     - Update the `base_url` value to match your server (e.g., `http://localhost:3000`)

### Collection Features

The collection includes:

#### **GET Endpoints:**
- **Get All Todos** - Basic retrieval with pagination
- **Get All Todos with Pagination** - Custom page size example
- **Search Todos** - Search by title keyword
- **Filter by Status - Completed** - Get only completed todos
- **Filter by Status - Pending** - Get only pending todos
- **Combined Filters** - Multiple filters together
- **Get Specific Todo** - Retrieve a single todo by ID

#### **POST Endpoints:**
- **Create Todo** - Full todo creation
- **Create Todo - Minimal** - Create with just title

#### **PUT/PATCH Endpoints:**
- **Update Todo - Full** - Complete update using PUT
- **Update Todo - Partial (PATCH)** - Partial update using PATCH

#### **DELETE Endpoints:**
- **Delete Todo** - Delete a specific todo

#### **Validation Tests:**
- **Create Todo - Missing Title** - Tests required field validation
- **Create Todo - Title Too Short** - Tests minimum 3 character validation
- **Create Todo - Title Too Long** - Tests maximum 100 character validation

