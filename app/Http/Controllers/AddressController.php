<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use League\Csv\Writer;

class AddressController extends Controller
{
    public function calculateDistances()
    {
        $addresses = [
            [
                'name' => 'Adchieve HQ',
                'address' => "Sint Janssingel 92, 5211 DA 's-Hertogenbosch, The Netherlands",
                'coordinates' => [51.690222, 5.293713],
            ],
            [
                'name' => 'Eastern Enterprise B.V.',
                'address' => 'Deldenerstraat 70, 7551AH Hengelo, The Netherlands',
                'coordinates' => [52.258824, 6.792500],
            ],
            [
                'name' => 'Eastern Enterprise',
                'address' => '46/1 Office no 1 Ground Floor , Dada House , Inside dada silk mills compound, Udhana Main Rd, near Chhaydo Hospital, Surat, 394210, India',
                'coordinates' => [21.210933, 72.874884],
            ],
            [
                'name' => 'Adchieve Rotterdam',
                'address' => 'Weena 505, 3013 AL Rotterdam, The Netherlands',
                'coordinates' => [51.922165, 4.472371],
            ],
            [
                'name' => 'Sherlock Holmes',
                'address' => '221B Baker St., London, United Kingdom',
                'coordinates' => [51.523767, -0.158156],
            ],
            [
                'name' => 'The White House',
                'address' => '1600 Pennsylvania Avenue, Washington, D.C., USA',
                'coordinates' => [38.897676, -77.036530],
            ],
            [
                'name' => 'The Empire State Building',
                'address' => '350 Fifth Avenue, New York City, NY 10118',
                'coordinates' => [40.748817, -73.985428],
            ],
            [
                'name' => 'The Pope',
                'address' => 'Saint Martha House, 00120 Citta del Vaticano, Vatican City',
                'coordinates' => [41.902847, 12.453391],
            ],
            [
                'name' => 'Neverland',
                'address' => '5225 Figueroa Mountain Road, Los Olivos, Calif. 93441, USA',
                'coordinates' => [34.716556, -120.136743],
            ],
        ];

        $adchieveHQCoordinates = [51.690222, 5.293713];
        $sortedDistances = [];

        foreach ($addresses as $address) {
            $distance = $this->calculateDistance($adchieveHQCoordinates, $address['coordinates']);
            $sortedDistances[$address['name']] = $distance;
        }

        asort($sortedDistances);
        
        $csvPath = storage_path('app/distances.csv');
        
        $csv = Writer::createFromPath($csvPath, 'w+');
        $csv->insertOne(['Sortnumber', 'Distance', 'Name', 'Address']);
        
        $sortNumber = 1;
        foreach ($sortedDistances as $name => $distance) {
            $csv->insertOne([$sortNumber, $distance, $name, $addresses[array_search($name, array_column($addresses, 'name'))]['address']]);
            $sortNumber++;
        }

        return response()->download($csvPath, 'distances.csv');
    }

    private function calculateDistance($origin, $destination)
    {
        $client = new Client();
        $apiKey = env('POSITIONSTACK_API_KEY');
        $apiUrl = env('POSITIONSTACK_API_URL');
        $url = "$apiUrl/reverse?access_key=$apiKey&query=$origin[0],$destination[0]";

        $response = $client->get($url);
        $data = json_decode($response->getBody()->getContents(), true);

        return number_format($data['data'][0]['distance'] / 1000, 2) . ' km';
    }
}