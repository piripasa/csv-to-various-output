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

    /**
     * Check if csv file exist or not
     */
    public function testCSVFileExist()
    {
        // Assert the file was stored...
        Storage::disk('trivago')->assertExists('hotels.csv');
    }

    /**
     * Check if Redis server running or not
     */
    public function testRedisConnection()
    {
        $redis = new \Redis();
        $this->assertTrue($redis->connect(env('REDIS_HOST')));
    }

    /**
     * Check for valid csv data
     */
    public function testValidCSVFile()
    {
        $outputService = new OutputService();
        $csvValidator = $outputService->prepareData(false);
        $this->validData = $csvValidator->getValidData();
        $this->assertTrue($csvValidator->fails(), 'CSV file contains invalid data');
    }

    /**
     * File generation fail test
     */
    public function testGenerateFileValidation()
    {
        Storage::fake('trivago');

        $data = [
            'output' => ['xyz']
        ];
        $response = $this->json('POST', 'generate-file', $data);

        $this->assertEquals(422, $response->getStatusCode());
    }

    /**
     * File generation test
     */
    public function testGenerateDataFile()
    {
        Storage::fake('trivago');

        $data = [
            'output' => ['json', 'xml', 'html', 'yaml', 'sqlite']
        ];
        $response = $this->json('POST', 'generate-file', $data);

        $this->assertEquals(201, $response->getStatusCode());
    }

    /**
     * generate json file
     */
    public function testCreateJsonOutput()
    {
        $this->manageFile('json');
    }

    /**
     * generate xml file
     */
    public function testCreateXmlOutput()
    {
        $this->manageFile('xml');
    }

    /**
     * generate yaml file
     */
    public function testCreateYamlOutput()
    {
        $this->manageFile('yaml');
    }

    /**
     * generate html file
     */
    public function testCreateHtmlOutput()
    {
        $this->manageFile('html');
    }

    /**
     * generate sqlite file
     */
    public function testCreateSqliteOutput()
    {
        $this->manageFile('sqlite');
    }

    /**
     * Prepare data test
     * @return array
     */
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

    /**
     * File type test
     * @param $type
     */
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
