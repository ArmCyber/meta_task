# Meta Task

## Requirements
1. PHP 8.3
2. MySQL 8
3. Node.js v20 
4. Composer

## Installation
1. Clone this repository
2. Copy `.env.example` to `.env` and configure
3. Run `composer install`
4. Run `php artisan migrate`
5. Run `php artisan key:generate`
6. Run `npm i`
7. Run `npm run build`
8. To run the application in the local environment, run `php artisan serve`

## Notes
1. I used jQuery for easy DOM manipulation without using a framework like Vue.js, but I can proceed with the task with
Vue.js or Vanilla.js (pure JavaScript) as well.
2. I found some packages which works pretty well for chunked file uploads, but as I think you're looking for some
custom solution, I preferred to write the code from scratch.
3. I preferred to also add a DB table for easier tracking/monitoring file uploads.
4. Additionally, I also added a cron job for each hour to remove incomplete uploads which wasn't changed in last
one hour, but I didn't add the cron configuration step in the Installation section as this is an additional step.
