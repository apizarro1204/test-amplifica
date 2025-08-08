<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\WooCommerceService;
use App\Models\User;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        $stores = $user->woocommerceCredentials()->get();
        $selectedStore = null;
        $ventasPorMes = collect();
        $ordersByStatus = [];
        $totalSales = 0;
        $totalOrders = 0;
        $totalProducts = 0;

        $storeId = $request->input('store_id');
        $service = new WooCommerceService();

        // Resumen por tienda
        foreach ($stores as $store) {
            try {
                $service->connect($store->store_url, $store->consumer_key, $store->consumer_secret);
                $orders = $service->getRecentOrders(365);
                $products = $service->getProducts();
                $ventas = collect($orders)->sum(function($order) {
                    return (float)(is_array($order) ? $order['total'] : $order->total);
                });
                $store->total_sales = $ventas;
                $store->total_orders = count($orders);
                $store->total_products = count($products);
            } catch (\Throwable $e) {
                $store->total_sales = 0;
                $store->total_orders = 0;
                $store->total_products = 0;
            }
        }

        // Si hay filtro de tienda
        if ($storeId) {
            $selectedStore = $stores->where('id', $storeId)->first();
            if ($selectedStore) {
                try {
                    $service->connect($selectedStore->store_url, $selectedStore->consumer_key, $selectedStore->consumer_secret);
                    $orders = $service->getRecentOrders(365);
                    $products = $service->getProducts();
                    $ventasPorMes = collect($orders)->groupBy(function($order) {
                        $date = is_array($order) ? $order['date_created'] : $order->date_created;
                        return substr($date, 0, 7); // YYYY-MM
                    })->map(function($ordersMes) {
                        return collect($ordersMes)->sum(function($order) {
                            $total = is_array($order) ? $order['total'] : $order->total;
                            return (float)$total;
                        });
                    });
                    $ordersByStatus = collect($orders)->groupBy(function($order) {
                        return is_array($order) ? $order['status'] : $order->status;
                    })->map->count()->toArray();
                    $totalSales = $selectedStore->total_sales;
                    $totalOrders = $selectedStore->total_orders;
                    $totalProducts = $selectedStore->total_products;
                } catch (\Throwable $e) {
                    // Si falla, dejar todo en cero
                }
            }
        } else {
            // Resumen global
            $totalSales = $stores->sum('total_sales');
            $totalOrders = $stores->sum('total_orders');
            $totalProducts = $stores->sum('total_products');
            // Ventas por mes y pedidos por estado globales
            $ventasPorMes = collect();
            $ordersByStatus = [];
            foreach ($stores as $store) {
                try {
                    $service->connect($store->store_url, $store->consumer_key, $store->consumer_secret);
                    $orders = $service->getRecentOrders(365);
                    $ventasPorMes = $ventasPorMes->merge(
                        collect($orders)->groupBy(function($order) {
                            $date = is_array($order) ? $order['date_created'] : $order->date_created;
                            return substr($date, 0, 7);
                        })->map(function($ordersMes) {
                            return collect($ordersMes)->sum(function($order) {
                                $total = is_array($order) ? $order['total'] : $order->total;
                                return (float)$total;
                            });
                        })
                    );
                    $ordersByStatusStore = collect($orders)->groupBy(function($order) {
                        return is_array($order) ? $order['status'] : $order->status;
                    })->map->count()->toArray();
                    foreach ($ordersByStatusStore as $status => $count) {
                        if (!isset($ordersByStatus[$status])) {
                            $ordersByStatus[$status] = 0;
                        }
                        $ordersByStatus[$status] += $count;
                    }
                } catch (\Throwable $e) {
                    // ignorar errores de tienda individual
                }
            }
            // Agrupar ventas por mes sumando meses iguales
            $ventasPorMes = $ventasPorMes->reduce(function($carry, $item, $key) {
                if (!isset($carry[$key])) $carry[$key] = 0;
                $carry[$key] += $item;
                return $carry;
            }, collect());
        }

        return view('dashboard', compact('stores', 'selectedStore', 'totalSales', 'totalOrders', 'totalProducts', 'ventasPorMes', 'ordersByStatus'));
    }
}
