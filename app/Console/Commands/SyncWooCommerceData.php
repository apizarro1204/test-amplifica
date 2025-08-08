<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\WooCommerceCredential;
use App\Services\WooCommerceService;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class SyncWooCommerceData extends Command
{
    protected $signature = 'sync:woocommerce';
    protected $description = 'Sincroniza productos y pedidos de todas las tiendas WooCommerce conectadas';

    public function handle()
    {
        $this->info('Iniciando sincronización de WooCommerce...');
        $credentials = WooCommerceCredential::all();
        foreach ($credentials as $credential) {
            try {
                $service = new WooCommerceService();
                $service->connect($credential->store_url, $credential->consumer_key, $credential->consumer_secret);
                $orders = $service->getRecentOrders(30);
                $products = $service->getProducts();

                // Guardar productos
                foreach ($products as $product) {
                    $externalId = is_array($product) ? $product['id'] : $product->id;
                    $name = is_array($product) ? $product['name'] : $product->name;
                    $sku = is_array($product) ? $product['sku'] : $product->sku;
                    $price = is_array($product) ? $product['price'] : $product->price;
                    $image = '';
                    if (is_array($product) && isset($product['images'][0]['src'])) {
                        $image = $product['images'][0]['src'];
                    } elseif (!is_array($product) && isset($product->images[0]->src)) {
                        $image = $product->images[0]->src;
                    }
                    \App\Models\Product::updateOrCreate(
                        [
                            'store_id' => $credential->id,
                            'external_id' => $externalId,
                        ],
                        [
                            'name' => $name,
                            'sku' => $sku,
                            'price' => $price,
                            'image' => $image,
                            'raw_data' => $product,
                        ]
                    );
                }

                // Guardar pedidos
                foreach ($orders as $order) {
                    $externalId = is_array($order) ? $order['id'] : $order->id;
                    $customer = '';
                    if (is_array($order)) {
                        $billing = $order['billing'] ?? [];
                        $customer = trim(($billing['first_name'] ?? '') . ' ' . ($billing['last_name'] ?? ''));
                    } else {
                        $billing = $order->billing ?? null;
                        if ($billing) {
                            $customer = trim(($billing->first_name ?? '') . ' ' . ($billing->last_name ?? ''));
                        }
                    }
                    $dateCreated = is_array($order) ? $order['date_created'] : $order->date_created;
                    $status = is_array($order) ? $order['status'] : $order->status;
                    $total = is_array($order) ? $order['total'] : $order->total;
                    \App\Models\Order::updateOrCreate(
                        [
                            'store_id' => $credential->id,
                            'external_id' => $externalId,
                        ],
                        [
                            'customer_name' => $customer,
                            'date_created' => $dateCreated,
                            'status' => $status,
                            'total' => $total,
                            'raw_data' => $order,
                        ]
                    );
                }

                $this->info("Tienda: {$credential->store_url} - Productos: ".count($products).", Pedidos: ".count($orders));
            } catch (\Throwable $e) {
                Log::error('Error sincronizando tienda '.$credential->store_url.': '.$e->getMessage());
                $this->error('Error en '.$credential->store_url.': '.$e->getMessage());
            }
        }
        $this->info('Sincronización finalizada.');
    }
}
