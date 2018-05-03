<?php

namespace Tests\Unit;

use App\Repositories\OutputFactory;
use App\Repositories\OutputRepository;
use App\Services\OutputService;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DataTest extends TestCase
{
    protected $validData;

    public function setUp()
    {
        parent::setUp();
    }

    public function testCSVFileExist()
    {
        // Assert the file was stored...
        Storage::disk('trivago')->assertExists('hotels.csv');
    }

    public function testRedisConnection()
    {
        $redis = new \Redis();
        $this->assertTrue($redis->connect(env('REDIS_HOST')));
    }

    public function testValidCSVFile()
    {
        $outputService = new OutputService();
        $csvValidator = $outputService->prepareData(false);
        $this->validData = $csvValidator->getValidData();
        $this->assertTrue($csvValidator->fails(), 'CSV file contains invalid data');
    }

    public function testGenerateFileValidation()
    {
        Storage::fake('trivago');

        $data = [
            'output' => ['xyz']
        ];
        $response = $this->json('POST', 'generate-file', $data);

        $this->assertEquals(422, $response->getStatusCode());
    }

    public function testGenerateDataFile()
    {
        Storage::fake('trivago');

        $data = [
            'output' => ['json', 'xml', 'html', 'yaml', 'sqlite']
        ];
        $response = $this->json('POST', 'generate-file', $data);

        $this->assertEquals(201, $response->getStatusCode());
    }

    public function testCreateJsonOutput()
    {
        $this->manageFile('json');
    }

    public function testCreateXmlOutput()
    {
        $this->manageFile('xml');
    }

    public function testCreateYamlOutput()
    {
        $this->manageFile('yaml');
    }

    public function testCreateHtmlOutput()
    {
        $this->manageFile('html');
    }

    public function testCreateSqliteOutput()
    {
        $this->manageFile('sqlite');
    }

    private function prepareData()
    {
        $data = [];

        $faker = \Faker\Factory::create();
        for ($i=0; $i < 10; $i++) {
            array_push($data, [
                'name' => $faker->name,
                'address' => $faker->address,
                'stars' => rand(1, 5),
                'contact' => $faker->streetAddress,
                'phone' => $faker->phoneNumber,
                'uri' => $faker->url,
            ]);
        }

        return array_values($data);
    }

    private function manageFile($type)
    {
        Storage::fake('trivago');

        $outputRepository = new OutputRepository();

        $fileName = 'test_' . $type . '_' . time();
        $outputRepository->setFileName($fileName)
            ->setData($this->prepareData())
            ->save(OutputFactory::processOutput($type));

        $file = $fileName . '.' . $type;

        if ($type == 'sqlite') {
            $file = $fileName . '.sqlite3';
        }

        if ($type == 'yaml') {
            $file = $fileName . '.yml';
        }

        Storage::disk('trivago')->assertExists($file);
    }
}
