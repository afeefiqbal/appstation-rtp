# Real-Time Bidding (RTB) Backend – Laravel Application


## 🛠 Tech Stack
- Laravel (Latest)
- MySQL / PostgreSQL
- Laravel Sanctum (Authentication)
- Laravel Queue (Redis or Database)
- Laravel Scheduler
- Docker & Docker Compose

---

## 🚀 Setup Instructions

### 1. Clone the repository

```bash
git clone https://github.com/afeefiqbal/appstation-rtp.git
cd laravel-rtb-app
```
### 2. Copy Environment File

```bash
cp .env.example .env
```
### 3. Start Docker
```bash
docker-compose up -d --build
```
### 4. Install Laravel Dependencies
```bash
docker exec -it app bash
composer install
php artisan key:generate
```

### 5. Run Migrations & Seeders
```bash
php artisan migrate --seed
```
##  Running Queue & Scheduler
```bash
php artisan queue:work
php artisan schedule:run
```
### 🧠 Approach Explanation
- Authentication is handled via Laravel Sanctum to protect all API routes.

- Ad Slots can be created and managed by Admins, with real-time bidding by users.

- Bidding Process is queued using Laravel's queue system for efficient processing and validation.

- Automatic Evaluation runs via Laravel’s Scheduler to award the winning bid after a slot closes.

-Docker handles consistent setup of Laravel, MySQL, and Nginx services.


## 📮 API Endpoints

### 🔑 Authentication

| Method | Endpoint        | Description    |
|--------|-----------------|----------------|
| POST   | /api/login      | Login user     |
| POST   | /api/register   | Register user  |
| POST   | /api/logout     | Logout user    |

---

### 📢 Ad Slots

| Method | Endpoint                       | Description                                     |
|--------|--------------------------------|-------------------------------------------------|
| GET    | /api/ad-slots                  | List all ad slots (optional `?status=open`)     |
| GET    | /api/ad-slots/{slot_id}/bids   | Get all bid details inside the slot             |
| POST   | /api/ad-slots                  | Create a new ad slot (Admin only)               |
| PATCH  | /api/ad-slots/{slot_id}/status | updating status of slot (Admin only)            | 
| PUT    | /api/ad-slots/{slot_id}        | updating Details of slot  (Admin only)          | 


---

### 🏷️ Bidding

| Method | Endpoint                               | Description                             |
|--------|----------------------------------------|-----------------------------------------|
| POST   | /api/ad-slots/{slot_id}/bids           | Place a bid on a specific ad slot       |
| GET    | /api/ad-slots/{slot_id}/winner         | View winning bid after awarded          |

---

### 👤 User

| Method | Endpoint                 | Description                             |
|--------|--------------------------|-----------------------------------------|
| GET    | /api/user/bids           | View logged-in user's bid history       |



### 🔐 Sample User Credentials


| Role/Name   | Email           | Password       |
|-------------|-----------------|----------------|
| Admin       | admin@rtb.com   | password       |
| Bidder One  | user@rtb.com    | password       |

