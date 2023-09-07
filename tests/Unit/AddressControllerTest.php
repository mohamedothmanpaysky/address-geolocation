<?php

namespace Tests\Unit;

use App\Http\Controllers\AddressController;
use GuzzleHttp\Client;
use Illuminate\Foundation\Testing\WithFaker;
use League\Csv\Reader;
use Tests\TestCase;

class AddressControllerTest extends TestCase
{

    /** @test */
    public function it_calculates_distances_and_sorts_results()
    {
        // Mock the GuzzleHttp client to return a predefined response
        $mockedResponse = json_encode([
            'data' => [
                [
                    'distance' => 1000, // 1 km
                ],
                [
                    'distance' => 2000, // 2 km
                ],
                [
                    'distance' => 500, // 0.5 km
                ],
            ],
        ]);
        $mockedClient = $this->mock(Client::class);
        $mockedClient->shouldReceive('get')->andReturnSelf();
        $mockedClient->shouldReceive('getBody')->andReturnSelf();
        $mockedClient->shouldReceive('getContents')->andReturn($mockedResponse);

        // Create an instance of the AddressController
        $addressController = new AddressController();

        // Set up the addresses
        $addresses = [
            [
                'name' => 'Address A',
                'address' => 'Address A, Country A',
                'coordinates' => [0, 0],
            ],
            [
                'name' => 'Address B',
                'address' => 'Address B, Country B',
                'coordinates' => [1, 1],
            ],
            [
                'name' => 'Address C',
                'address' => 'Address C, Country C',
                'coordinates' => [2, 2],
            ],
        ];

        // Call the calculateDistances method
        $result = $addressController->calculateDistances($addresses, $mockedClient);

        // Assert that the CSV file was created and contains the expected data
        $csvPath = storage_path('app/distances.csv');
        $csv = Reader::createFromPath($csvPath);
        $csv->setHeaderOffset(0);
        $csvRows = $csv->getRecords();
       
        $this->assertCount(9, $csv); // Including the header row
        $this->assertEquals(['Sortnumber', 'Distance', 'Name', 'Address'], $csv->getHeader());

        // Assert the response from the calculateDistances method
        $this->assertInstanceOf(\Symfony\Component\HttpFoundation\BinaryFileResponse::class, $result);
        $this->assertEquals('distances.csv', $result->getFile()->getFilename());
    }
}