<?php
/**
 * Created by PhpStorm.
 * User: piripasa
 * Date: 16/2/18
 * Time: 6:25 PM
 */

namespace App\Repositories;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use PDO;

class SqliteOutput extends Output implements OutputInterface
{
    public function saveData($fileName, $data)
    {
        $storagePath = Storage::disk('trivago')->getDriver()->getAdapter()->getPathPrefix();

        try {
            // Create (connect to) SQLite database in file
            $file_db = new PDO('sqlite:' . $storagePath . $fileName . '.sqlite3');

            $file_db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

            // Create table hotels
            $file_db->exec("CREATE TABLE IF NOT EXISTS hotels (
                                      name varchar not null,
                                      address varchar not null,
                                      stars integer not null,
                                      contact varchar not null,
                                      phone varchar not null,
                                      uri varchar not null)");

            // Prepare INSERT statement to SQLite3 file db
            $insert = "INSERT INTO hotels (name, address, stars, contact, phone, uri) 
                      VALUES (:name, :address, :stars, :contact, :phone, :uri)";

            $stmt = $file_db->prepare($insert);

            // Bind parameters to statement variables
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':address', $address);
            $stmt->bindParam(':stars', $stars);
            $stmt->bindParam(':contact', $contact);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':uri', $uri);

            foreach ($data as $key => $hotel) {
                if (array_key_exists('name', $hotel)) {
                    $name = $hotel['name'];
                    $address = $hotel['address'];
                    $stars = $hotel['stars'];
                    $contact = $hotel['contact'];
                    $phone = $hotel['phone'];
                    $uri = $hotel['uri'];

                    $stmt->execute();
                }
                else {
                    foreach ($hotel as $hotel2) {
                        $name = $hotel2['name'];
                        $address = $hotel2['address'];
                        $stars = $hotel2['stars'];
                        $contact = $hotel2['contact'];
                        $phone = $hotel2['phone'];
                        $uri = $hotel2['uri'];

                        $stmt->execute();
                    }
                }
            }

            $file_db = null;
        }
        catch(\PDOException $e) {
            Log::error($e->getMessage());
        }
    }
}