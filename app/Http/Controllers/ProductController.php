<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\WooCommerceService;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $credentialId = $request->input('credential_id');
        /** @var User $user */
        $user = Auth::user();
        $credential = $user->woocommerceCredentials()->findOrFail($credentialId);
        $service = new WooCommerceService();
        $service->connect($credential->store_url, $credential->consumer_key, $credential->consumer_secret);
        $products = $service->getProducts();
        return view('products.index', compact('products', 'credential'));
    }

    public function exportExcel(Request $request)
    {
        $credentialId = $request->input('credential_id');
        /** @var User $user */
        $user = Auth::user();
        $credential = $user->woocommerceCredentials()->findOrFail($credentialId);
        $service = new WooCommerceService();
        $service->connect($credential->store_url, $credential->consumer_key, $credential->consumer_secret);
        $products = $service->getProducts();

        // Normalizar productos a array
        $rows = [];
        $header = ['ID', 'Nombre', 'SKU', 'Precio', 'Stock', 'Categorías'];
        $rows[] = $header;
        foreach ($products as $product) {
            $isArray = is_array($product);
            $id = $isArray ? $product['id'] : $product->id;
            $name = $isArray ? $product['name'] : $product->name;
            $sku = $isArray ? $product['sku'] : $product->sku;
            $price = $isArray ? $product['price'] : $product->price;
            $stock = $isArray ? ($product['stock_quantity'] ?? '') : ($product->stock_quantity ?? '');
            $categories = $isArray ? $product['categories'] : $product->categories;
            $catNames = collect($categories)->map(function($cat) use ($isArray) {
                return is_array($cat) ? $cat['name'] : $cat->name;
            })->implode(', ');
            $rows[] = [$id, $name, $sku, $price, $stock, $catNames];
        }

        // Crear Excel
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        foreach ($rows as $rowIdx => $row) {
            foreach ($row as $colIdx => $value) {
                $cell = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx + 1) . ($rowIdx + 1);
                $sheet->setCellValue($cell, $value);
            }
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'productos_'.date('Ymd_His').'.xlsx';
        // Output
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }
}
