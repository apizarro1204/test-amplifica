<?php
namespace App\Http\Controllers;

use App\Models\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\WooCommerceService;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $credentialId = $request->input('credential_id');
    /** @var User $user */
    $user = Auth::user();
    $credential = $user->woocommerceCredentials()->findOrFail($credentialId);
        $service = new WooCommerceService();
        $service->connect($credential->store_url, $credential->consumer_key, $credential->consumer_secret);
        $params = [];
        if ($request->filled('date_from')) {
            $params['after'] = $request->input('date_from') . 'T00:00:00';
        }
        if ($request->filled('date_to')) {
            $params['before'] = $request->input('date_to') . 'T23:59:59';
        }
        if ($request->filled('status')) {
            $params['status'] = $request->input('status');
        }
        if ($request->filled('customer')) {
            $params['search'] = $request->input('customer');
        }
        // Usar método seguro del servicio para obtener pedidos con filtros
        $orders = $service->getRecentOrders(365); // Trae el año por defecto
        // Filtrar manualmente si hay filtros adicionales
        if (!empty($params)) {
            $orders = collect($orders)->filter(function($order) use ($params) {
                $isArray = is_array($order);
                $date = $isArray ? $order['date_created'] : $order->date_created;
                $status = $isArray ? $order['status'] : $order->status;
                $customer = $isArray ? ($order['billing']['first_name'] ?? '') : ($order->billing->first_name ?? '');
                $pass = true;
                if (isset($params['after']) && $date < $params['after']) $pass = false;
                if (isset($params['before']) && $date > $params['before']) $pass = false;
                if (isset($params['status']) && $status !== $params['status']) $pass = false;
                if (isset($params['search']) && stripos($customer, $params['search']) === false) $pass = false;
                return $pass;
            })->values();
        }
        return view('orders.index', compact('orders', 'credential'));
    }

    public function exportExcel(Request $request)
    {
        $credentialId = $request->input('credential_id');
    /** @var User $user */
    $user = Auth::user();
    $credential = $user->woocommerceCredentials()->findOrFail($credentialId);
        $service = new WooCommerceService();
        $service->connect($credential->store_url, $credential->consumer_key, $credential->consumer_secret);
        $orders = $service->getRecentOrders();

        $rows = [];
        $header = ['ID', 'Cliente', 'Fecha', 'Productos', 'Estado'];
        $rows[] = $header;
        foreach ($orders as $order) {
            $isArray = is_array($order);
            $id = $isArray ? $order['id'] : $order->id;
            $billing = $isArray ? $order['billing'] : $order->billing;
            $firstName = $isArray ? ($billing['first_name'] ?? '') : ($billing->first_name ?? '');
            $lastName = $isArray ? ($billing['last_name'] ?? '') : ($billing->last_name ?? '');
            $dateCreated = $isArray ? $order['date_created'] : $order->date_created;
            $lineItems = $isArray ? $order['line_items'] : $order->line_items;
            $status = $isArray ? $order['status'] : $order->status;
            $productos = collect($lineItems)->map(function($item) use ($isArray) {
                $name = $isArray ? $item['name'] : $item->name;
                $qty = $isArray ? $item['quantity'] : $item->quantity;
                return $name.' (x'.$qty.')';
            })->implode(", ");
            $rows[] = [$id, $firstName.' '.$lastName, $dateCreated, $productos, $status];
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        foreach ($rows as $rowIdx => $row) {
            foreach ($row as $colIdx => $value) {
                $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx + 1);
                $sheet->setCellValue($col . ($rowIdx + 1), $value);
            }
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'pedidos_'.date('Ymd_His').'.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }
}
