# Submissions API

This Laravel application provides a `/submit` API endpoint to accept user submissions and process them asynchronously using jobs, events, and listeners.

## Setup Instructions

1. Clone the repository.
2. Create a `.env` file from `.env.example` and configure your ports settings. Pay attention to `APP_PORT` for api, `FORWARD_DB_PORT` for database and `FORWARD_REDIS_PORT` for redis
3. Run `docker-compose up -d` to start docker env.
4. Run `./vendor/bin/sail composer install` to install all the dependencies.
5. Run `./vendor/bin/sail artisan key:generate` to generate app key.
6. Run `./vendor/bin/sail artisan migrate` to create the necessary tables.

### **Run the Queue Worker**
Make sure to run the queue worker so that jobs can be processed:
```bash
./vendor/bin/sail artisan queue:work
```

## Testing the API

You can test the `/api/submit` endpoint using a tool like Postman or cURL. Example JSON payload:

```json
{
  "name": "John Doe",
  "email": "john.doe@example.com",
  "message": "This is a test message."
}
```
The API will validate the data and respond with a `202 Accepted` status. The data will then be processed by a background job.

You can see the operation result in log file - it will contain information about added data, or the error if something went wrong.

If the data added successfully, the new line will be added to `submissions` table

### Unit tests
Run unit tests with:
```bash
  ./vendor/bin/sail artisan test
```
