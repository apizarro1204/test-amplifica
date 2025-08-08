@extends('layouts.app')
@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-black mb-2">Dashboard de la tienda</h1>
                <p class="text-gray-600">{{ $credential->store_url }}</p>
            </div>
            <a href="{{ route('woocommerce_credentials.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Volver a Tiendas
            </a>
        </div>

        <!-- Métricas principales -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-md p-6 flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <div class="text-sm text-gray-500">Ventas totales (año)</div>
                    <div class="text-2xl font-bold text-black">
                        ${{ number_format((is_array($ventasPorMes) ? array_sum($ventasPorMes) : $ventasPorMes->sum()), 2) }}
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-md p-6 flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <div class="text-sm text-gray-500">Mes con más ventas</div>
                    <div class="text-2xl font-bold text-black">
                        @php $maxMes = collect($ventasPorMes)->sortDesc()->keys()->first(); @endphp
                        {{ $maxMes ?? '-' }}
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-md p-6 flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-8 w-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <div class="text-sm text-gray-500">Producto más vendido</div>
                    <div class="text-2xl font-bold text-black">
                        {{ $productosVendidos[0]['name'] ?? '-' }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráfico de ventas por mes (barras simples) -->
        <div class="bg-white rounded-xl shadow-md p-6 mb-8">
            <h3 class="text-lg font-semibold text-black mb-4">Total de ventas por mes (último año)</h3>
            <div class="overflow-x-auto">
                <div class="flex items-end space-x-2 h-48">
                    @php $max = is_array($ventasPorMes) ? max($ventasPorMes) : $ventasPorMes->max(); @endphp
                    @foreach($ventasPorMes as $mes => $total)
                        @php $height = $max > 0 ? intval(($total/$max)*160) : 0; @endphp
                        <div class="flex flex-col items-center justify-end">
                            <div class="bg-blue-500 w-8 rounded-t-lg" style="height: {{ $height }}px" title="${{ number_format($total,2) }}"></div>
                            <div class="text-xs text-black mt-2">{{ $mes }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Tabla de productos más vendidos -->
        <div class="bg-white rounded-xl shadow-md p-6">
            <h3 class="text-lg font-semibold text-black mb-4">Productos más vendidos</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">Producto</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">Cantidad Vendida</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($productosVendidos as $prod)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-black">{{ $prod['name'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-black">{{ $prod['qty'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
