<?php

namespace App\Services;

use Automattic\WooCommerce\Client;
use Carbon\Carbon;

class WooCommerceService
{
    protected $client;

    public function connect($storeUrl, $consumerKey, $consumerSecret)
    {
        $this->client = new Client(
            rtrim($storeUrl, '/'),
            $consumerKey,
            $consumerSecret,
            [
                'version' => 'wc/v3',
                'verify_ssl' => false,
            ]
        );
        return $this;
    }

    public function getProducts()
    {
        return $this->client->get('products');
    }

    public function getRecentOrders($days = 30)
    {
        $after = Carbon::now()->subDays($days)->toIso8601String();
        return $this->client->get('orders', [
            'after' => $after,
        ]);
    }
}
