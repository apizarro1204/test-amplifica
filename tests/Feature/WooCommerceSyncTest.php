<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;
use App\Models\WooCommerceCredential;
use App\Services\WooCommerceService;

class WooCommerceSyncTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Crear tabla solo si no existe
        if (!\Illuminate\Support\Facades\Schema::hasTable('woo_commerce_credentials')) {
            \Illuminate\Support\Facades\Schema::create('woo_commerce_credentials', function ($table) {
                $table->id();
                $table->string('store_url');
                $table->string('consumer_key');
                $table->string('consumer_secret');
                $table->timestamps();
            });
        }
    }

    public function test_sync_command_runs_without_error()
    {
        $this->artisan('sync:woocommerce')
            ->expectsOutput('Iniciando sincronización de WooCommerce...')
            ->expectsOutput('Sincronización finalizada.')
            ->assertExitCode(0);
    }

    public function test_woocommerce_service_connection_fails_with_invalid_credentials()
    {
        $service = new WooCommerceService();
        $this->expectException(\Exception::class);
        $service->connect('https://invalid-url.com', 'invalid', 'invalid');
        $service->getProducts();
    }
}
