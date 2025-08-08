@extends('layouts.app')
@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-black mb-2">Productos</h1>
                    <p class="text-gray-600">{{ $credential->store_url }}</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('products.exportExcel', ['credential_id' => $credential->id]) }}" 
                       class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-md">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Exportar Excel
                    </a>
                    <a href="{{ route('woocommerce_credentials.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Volver a Tiendas
                    </a>
                </div>
            </div>
        </div>

        <!-- Products Grid -->
        @if($products && count($products) > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($products as $product)
                    @php
                        $isArray = is_array($product);
                        $name = $isArray ? $product['name'] : $product->name;
                        $sku = $isArray ? $product['sku'] : $product->sku;
                        $price = $isArray ? $product['price'] : $product->price;
                        $images = $isArray ? $product['images'] : $product->images;
                        $status = $isArray ? $product['status'] : $product->status;
                        $stock = $isArray ? ($product['stock_quantity'] ?? 0) : ($product->stock_quantity ?? 0);
                        $imgSrc = '';
                        if (isset($images[0])) {
                            $imgSrc = $isArray ? $images[0]['src'] ?? '' : ($images[0]->src ?? '');
                        }
                    @endphp
                    <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300">
                        <!-- Product Image -->
                        <div class="aspect-w-1 aspect-h-1 bg-gray-200 relative h-48">
                            @if($imgSrc)
                                <img src="{{ $imgSrc }}" alt="{{ $name }}" class="w-full h-full object-cover">
                            @else
                                <div class="flex items-center justify-center h-full">
                                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            @endif
                            <!-- Status Badge -->
                            <div class="absolute top-2 right-2">
                                @if($status === 'publish')
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        Activo
                                    </span>
                                @else
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                        {{ ucfirst($status) }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Product Info -->
                        <div class="p-4">
                            <h3 class="font-semibold text-black text-lg mb-2 line-clamp-2">{{ $name }}</h3>
                            
                            <div class="space-y-2">
                                @if($sku)
                                    <div class="flex items-center text-sm">
                                        <span class="text-gray-500 mr-2">SKU:</span>
                                        <span class="font-mono text-black bg-gray-100 px-2 py-1 rounded text-xs">{{ $sku }}</span>
                                    </div>
                                @endif
                                
                                <div class="flex items-center justify-between">
                                    <div class="text-2xl font-bold text-black">
                                        ${{ number_format((float)$price, 2) }}
                                    </div>
                                    @if($stock > 0)
                                        <span class="text-sm text-green-600 font-medium">{{ $stock }} en stock</span>
                                    @else
                                        <span class="text-sm text-red-600 font-medium">Sin stock</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- Empty State -->
            <div class="bg-white rounded-xl shadow-md p-12 text-center">
                <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
                <h3 class="text-lg font-medium text-black mb-2">No hay productos disponibles</h3>
                <p class="text-gray-500 mb-6">Esta tienda no tiene productos registrados o no se pudieron cargar.</p>
                <a href="{{ route('woocommerce_credentials.index') }}" 
                   class="inline-flex items-center px-6 py-3 bg-blue-600 border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Volver a Tiendas
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
