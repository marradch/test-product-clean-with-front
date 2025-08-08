<?php

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;

class ProductApiTest extends TestCase
{
    private Client $client;

    protected function setUp(): void
    {
        $this->client = new Client(['base_uri' => 'http://localhost:80']);
    }

    public function testCreateProductSuccessfully(): void
    {
        $response = $this->client->post('/products', [
            'json' => [
                'name' => 'Test Product',
                'price' => 123.45,
                'category_id' => 1,
                'status' => 'available',
                'attributes' => [
                    'color' => 'black',
                    'size' => 'M'
                ]
            ]
        ]);

        $this->assertEquals(201, $response->getStatusCode());
        $data = json_decode($response->getBody(), true);

        $this->assertArrayHasKey('id', $data);
        $this->assertIsInt($data['id']);
    }
}