# Project Setup Guide

This file explains how to set up and run this Laravel project on a new machine.


## Projecy specifications and Supported Channels

A Real time multi-channel conversation management system, for handling clients over social media. When messages come in they are processed immediately with live UI updates (No page refresh required), this makes the app less manual and giving it streamlined workflow and modern user experience The current status of channels are as follows:

Working:
- Telegram - fully implemented, can send and receive messages
- Implemented as the main channels will be updated on demand

To be implemented:
- WhatsApp - webhook endpoint exists, processing not yet implemented
- SMS - webhook endpoint exists, processing not yet implemented
- Any other channel depending on clients user demand 

The webhook endpoint accepts requests for all three channels at /api/webhook/{channel} where channel is a variable that stores the type of channell: telegram, whatsapp, or simulator. Currently only Telegram messages are fully processed.

## Requirements

- PHP 8.3 or higher
- Composer 2.x
- Node.js 18.x or higher
- npm 9.x or higher
- MySQL 8.0 or higher
- ngrok (for Telegram webhooks)


## Step 1: Git Clone then Install Dependencies

Clone the repository for https cloning you will to copy your  PAT inside the "git clone" url and navigate to the project folder:

```
git clone <repository-url>
cd myApp
```

Install PHP dependencies:

```
composer install
```

If Laravel asks for user's input to run the database migrations during intallation select No. You need to configure the database first in the next steps.

Install JavaScript dependencies:

```
npm install
```


## Step 2: Environment Setup

Copy the example environment file:

```
cp .env.example .env
```

Generate the application's key:

```
php artisan key:generate
```

Open the .env file and update these values:

### Database Settings

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=myapp
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### Telegram Settings

```
TELEGRAM_BOT_TOKEN=your_telegram_bot_token
TELEGRAM_BOT_NAME=your_bot_name
TELEGRAM_WEBHOOK_SECRET=your_random_32_char_secret
```

> **Security Note:** You must generate a random string for `TELEGRAM_WEBHOOK_SECRET`. This acts as a private key between your app and Telegram to prevent spam. You can generate one using: `php -r "echo bin2hex(random_bytes(16));"`

### WebSocket Settings (Reverb)

```
BROADCAST_CONNECTION=reverb
REVERB_APP_ID=123456
REVERB_APP_KEY=myapp_key
REVERB_APP_SECRET=myapp_secret
REVERB_HOST=127.0.0.1
REVERB_PORT=8080
REVERB_SCHEME=http

VITE_REVERB_APP_KEY=myapp_key
VITE_REVERB_HOST=127.0.0.1
VITE_REVERB_PORT=8080
VITE_REVERB_SCHEME=http
```

### Application URL

```
APP_URL=http://localhost:8001 use the 8001 port since I was facing unexplained issues on local host using 8000 port

Run php artisan serve --port=8001
```


## Step 3: Database Setup

Create the MySQL database:

```
mysql -u root -p
```

Run these SQL commands:

```
CREATE DATABASE myapp;
EXIT;
```

Run the migrations:

```
php artisan migrate
```

Seed the database with a test user:

```
php artisan db:seed
```

This creates a user with:
- Email: test@example.com
- Password: password


## Step 4: Build Frontend Assets

For production:

```
npm run build
```

```
npm run dev
```


## Step 5: Running the Application

You need to run 4 services. Open 4 terminal windows:

Terminal 1 - Web Server (use port 8001):
```
php artisan serve --port=8001
```

Terminal 2 - Queue Worker:For processing incoming webhooks. currently I have defined two custom queues: 1. webhook, 2. notifications
```
php artisan queue:work --queue=webhook,notifications,default   
```

Terminal 3 - WebSocket Server:
```
php artisan reverb:start --debug for a detailed output
```

Terminal 4 - Vite (for development):
```
npm run dev
```


## Step 6: Telegram Bot Setup

You will need to create your own Telegram bot to be able to send and receive messages.

### Create a Bot with BotFather

1. Open Telegram app on your phone or desktop
2. Search for @BotFather and open a chat with it
3. Send the command: /newbot
4. BotFather asks for a name - enter any name like "My Support Bot"
5. BotFather asks for a username - must end in "bot" (example: my_support_bot)
6. BotFather gives you a token that looks like this: 7894561230:AAHxYz123abc456DEF789ghi

Copy the token provided and add it to your .env file in the telegram secion:
```
TELEGRAM_BOT_TOKEN=7894561230:AAHxYz123abc456DEF789ghi
TELEGRAM_BOT_NAME=my_support_bot
```

### Start ngrok

Open a new terminal and run:
```
ngrok http 8001
```

ngrok will show output like this:
```
Forwarding   https://a1b2c3d4.ngrok.io -> http://localhost:8001
```

Copy the https URL (example: https://a1b2c3d4.ngrok.io)

### Register the ngrok url with telegram to direct telegram traffic to the application

Run this curl command. Replace YOUR_BOT_TOKEN with your actual token and YOUR_NGROK_URL with your ngrok URL:

```
curl -X POST "https://api.telegram.org/botYOUR_BOT_TOKEN/setWebhook" \
  -H "Content-Type: application/json" \
  -d '{"url": "YOUR_NGROK_URL/api/webhook/telegram"}'
```

Example:
```
curl -X POST "https://api.telegram.org/bot7894561230:AAHxYz123abc456DEF789ghi/setWebhook" \
  -H "Content-Type: application/json" \
  -d '{"url": "https://a1b2c3d4.ngrok.io/api/webhook/telegram"}'
```

If everything is done coorrectly this Success response should shows on the terminal:
```
{"ok":true,"result":true,"description":"Webhook was set"}
```

### Now verify Webhook is Set

```
curl "https://api.telegram.org/botYOUR_BOT_TOKEN/getWebhookInfo"
```

### Test It with real input

1. Open Telegram and search for your bot username
2. Send a message to your bot
3. Check the admin panel at http://localhost:8001/admin to see the message

### Note

Every time you restart ngrok, you get a new URL. You must register the webhook again with the new URL each time.


## Admin Panel

Access the admin panel at:

```
http://localhost:8001/admin
```

Login with:
- Email: test@example.com
- Password: password


## Testing with Postman

You can also test the webhook without Telegram by sending a POST request to:

```
POST http://localhost:8001/api/webhook/telegram
```

Headers:
```
Content-Type: application/json
```

Example JSON body (simulates a Telegram message):

```
{
    "message": {
        "message_id": "2026",
        "text": "I am a winner",
        "from": {
            "id": 5137509,
            "first_name": "Lewis",
            "last_name": "wege"
        }
 
    }
}
```
To test with different customers just change the "id" to a different value then edit "first_name"  and "last_name" to your liking


Expected response:
```
{
  "message": "Received"
}
```

Status code: 202


## Notes for Testing

- Change from.id and chat.id to create a new customer
- Use the same from.id to add messages to an existing conversation
- Increment message_id for each request
- Make sure the queue worker is running to process webhooks
- Check the admin panel to see the messages appear in real time


## Creating Additional Admin Users

Use tinker to create more users:

```
php artisan tinker
```

Then run:

```
\App\Models\User::create([
    'name' => 'New Admin',
    'email' => 'admin@example.com',
    'password' => bcrypt('your-password'),
]);
```


## Troubleshooting

Class not found errors:
```
composer dump-autoload
php artisan config:clear
php artisan cache:clear
```

Queue not processing:
```
php artisan queue:work --queue=webhook,notifications,default
```

Check failed jobs:
```
php artisan queue:failed
```

Retry failed jobs:
```
php artisan queue:retry all
```

View logs:
```
tail -f storage/logs/laravel.log
```

Clear all caches:
```
php artisan optimize:clear
```

Fresh database reset (warning: deletes all data):
```
php artisan migrate:fresh --seed
```
