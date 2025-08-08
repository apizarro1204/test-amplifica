<?php

namespace App\Http\Controllers;

use App\Models\User;

use App\Models\ErrorLog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\WooCommerceService;
use Illuminate\Support\Facades\Auth;

class StoreDashboardController extends Controller
{
    public function index(Request $request)
    {
    $credentialId = $request->input('credential_id');
    /** @var User $user */
    $user = Auth::user();
    $credential = $user->woocommerceCredentials()->findOrFail($credentialId);
        $ventasPorMes = collect();
        $productosVendidos = collect();
        try {
            $service = new WooCommerceService();
            $service->connect($credential->store_url, $credential->consumer_key, $credential->consumer_secret);
            $orders = $service->getRecentOrders(365); // Último año
            $products = $service->getProducts();

            // Total ventas por mes
            $ventasPorMes = collect($orders)->groupBy(function($order) {
                $date = is_array($order) ? $order['date_created'] : $order->date_created;
                return substr($date, 0, 7); // YYYY-MM
            })->map(function($ordersMes) {
                return collect($ordersMes)->sum(function($order) {
                    $total = is_array($order) ? $order['total'] : $order->total;
                    return (float)$total;
                });
            });

            // Productos más vendidos
            $productosVendidos = collect($orders)->flatMap(function($order) {
                $lineItems = is_array($order) ? $order['line_items'] : $order->line_items;
                return collect($lineItems)->map(function($item) {
                    $isArray = is_array($item);
                    $id = $isArray ? $item['product_id'] : $item->product_id;
                    $name = $isArray ? $item['name'] : $item->name;
                    $qty = $isArray ? $item['quantity'] : $item->quantity;
                    return ['id' => $id, 'name' => $name, 'qty' => $qty];
                });
            })->groupBy('id')->map(function($items, $id) {
                $name = $items->first()['name'];
                $totalQty = $items->sum('qty');
                return ['name' => $name, 'qty' => $totalQty];
            })->sortByDesc('qty')->take(10);
        } catch (\Throwable $e) {
            ErrorLog::create([
                'user_id' => Auth::id(),
                'store_id' => $credential->id,
                'action' => 'dashboard_metrics',
                'message' => $e->getMessage(),
                'context' => json_encode([
                    'trace' => $e->getTraceAsString(),
                ]),
            ]);
            return view('dashboard.store', [
                'credential' => $credential,
                'ventasPorMes' => $ventasPorMes,
                'productosVendidos' => $productosVendidos,
                'error' => 'Ocurrió un error al obtener los datos de la tienda.'
            ]);
        }
        return view('dashboard.store', compact('credential', 'ventasPorMes', 'productosVendidos'));
    }
}
