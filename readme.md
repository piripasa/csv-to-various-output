## Generate various output file from csv

###Framework & tools

- Laravel 5.5 (PHP framework)
- Redis (for maintaining queue job) 
- Composer (for installing dependencies)

Queue job is used to avoid request timeout. I have used dns check to validate uri and its a quite long process(for me it took almost 1 hour). Based on this thinking I put the generate process in a queue job then just need to run `php artisan queue:work redis` to process the queue.  

Input & Output files directory: /trivago_files

Unit test cases: /tests/Unit/

There will be generated validation error log file too.


### Installation
This is a dockerized application. Do the following

Make sure: 
* `docker` & `docker-compose` installed in your PC.

To do:

- `cd trivago/` into the trivago project root directory.
- Run `docker-compose up -d --build`
- Run `sudo docker exec -it trivago_php_1 /bin/sh`
- Run `composer install`
- Run `cp .env.docker .env`
- Change values in .env as your need. like to change the value of TAKE_CSV_ROWS to 100
- Run `chgrp -R www-data storage bootstrap/cache`
- Run `chmod -R ug+rwx storage bootstrap/cache`
- Run `vendor/bin/phpunit` for PHPUnit test
- Open your browser & hit to `http://127.0.0.1:8000`.
- Select options then click on Generate button
- Run `php artisan queue:work redis`
- Check 'trivago_files' folder

####Without Docker
Make Sure you have installed inyour PC:

- PHP >= 7.0.0
- OpenSSL PHP Extension
- PDO PHP Extension
- Mbstring PHP Extension
- Tokenizer PHP Extension
- XML PHP Extension
- Composer (https://getcomposer.org/)
- Redis

To do:

- Run `composer install`
- Run `cp .env.example .env` 
- Change values in .env as your need. like to change the value of TAKE_CSV_ROWS to 100 
- Run `sudo chgrp -R www-data storage bootstrap/cache`
- Run `sudo chmod -R ug+rwx storage bootstrap/cache`
- Run `vendor/bin/phpunit` for PHPUnit test
- Run `php artisan serve`
- Open your browser & hit to `http://127.0.0.1:8000`.
- Select options then click on Generate button
- Run `php artisan queue:work redis`
- Check 'trivago_files' folder


####**** Don't forget to run following artisan command to process the queue after making file generate request
`php artisan queue:work redis`


### extend to new output formats
Step 1: Create a class file in App\Repositories directory called 'TextOutput' and implements it with 'OutputInterface'

```
 namespace App\Repositories;
 
 class TextOutput extends Output implements OutputInterface
 {
     public function saveData($fileName, $data)
     {
         $this->writeToFile($fileName . '.txt', $data);
     }
 }
```
 
Step 2: Add following code to switch case of OutputFactory.php in App\Repositories directory

```
case 'text':
	return new TextOutput();
	break;
```

Step 3: Add `text` in output dropdown in `resources\views\welcome.blade.php`

Step 4: Add `text` in `app\Http\Requests\GenerateFileRequest.php`

