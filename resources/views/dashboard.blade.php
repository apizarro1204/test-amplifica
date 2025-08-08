<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>

        <div class="py-8 bg-gray-50 min-h-screen">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Selector de tienda -->
                <form method="GET" action="{{ route('dashboard') }}" class="mb-8 flex flex-col md:flex-row md:items-center md:space-x-4 space-y-2 md:space-y-0">
                    <label for="store_id" class="block text-sm font-medium text-black">Ver datos de:</label>
                    <select name="store_id" id="store_id" onchange="this.form.submit()" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-black">
                        <option value="">Todas las tiendas</option>
                        @if(isset($stores))
                        @foreach($stores as $store)
                        <option value="{{ $store->id }}" @if(isset($selectedStore) && $selectedStore && $selectedStore->id == $store->id) selected @endif>
                            {{ $store->store_url }}
                        </option>
                        @endforeach
                        @endif
                    </select>
                </form>

                <!-- Métricas principales -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-white rounded-xl shadow-md p-6 flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <div class="text-sm text-gray-500">Ventas totales</div>
                            <div class="text-2xl font-bold text-black">${{ number_format($totalSales ?? 0, 2) }}</div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow-md p-6 flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <div class="text-sm text-gray-500">Total pedidos</div>
                            <div class="text-2xl font-bold text-black">{{ $totalOrders ?? 0 }}</div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow-md p-6 flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <div class="text-sm text-gray-500">Productos activos</div>
                            <div class="text-2xl font-bold text-black">{{ $totalProducts ?? 0 }}</div>
                        </div>
                    </div>
                </div>

                <!-- Gráfico de ventas por mes (barras simples) -->
                <div class="bg-white rounded-xl shadow-md p-6 mb-8">
                    <h3 class="text-lg font-semibold text-black mb-4">Ventas por mes</h3>
                    <div class="overflow-x-auto">
                        @php $max = isset($ventasPorMes) ? (is_array($ventasPorMes) ? max($ventasPorMes) : $ventasPorMes->max()) : 0; @endphp
                        <div class="flex items-end space-x-2 h-48">
                            @if(isset($ventasPorMes))
                            @foreach($ventasPorMes as $mes => $total)
                            @php $height = $max > 0 ? intval(($total/$max)*160) : 0; @endphp
                            <div class="flex flex-col items-center justify-end">
                                <div class="bg-blue-500 w-8 rounded-t-lg" style="height: {{ $height }}px" title="${{ number_format($total,2) }}"></div>
                                <div class="text-xs text-black mt-2">{{ $mes }}</div>
                            </div>
                            @endforeach
                            @else
                            <div class="text-gray-500">No hay datos de ventas.</div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Gráfico de pedidos por estado (barra horizontal simple) -->
                <div class="bg-white rounded-xl shadow-md p-6 mb-8">
                    <h3 class="text-lg font-semibold text-black mb-4">Pedidos por estado</h3>
                    <div class="space-y-2">
                        @if(isset($ordersByStatus) && count($ordersByStatus))
                        @php $maxStatus = max($ordersByStatus); @endphp
                        @foreach($ordersByStatus as $status => $count)
                        @php $width = $maxStatus > 0 ? intval(($count/$maxStatus)*100) : 0; @endphp
                        <div class="flex items-center">
                            <div class="w-32 text-sm text-black">{{ ucfirst($status) }}</div>
                            <div class="flex-1 bg-gray-200 rounded h-4 mx-2">
                                <div class="bg-green-500 h-4 rounded" style="width: {{ $width }}%"></div>
                            </div>
                            <div class="w-8 text-right text-black">{{ $count }}</div>
                        </div>
                        @endforeach
                        @else
                        <div class="text-gray-500">No hay datos de pedidos.</div>
                        @endif
                    </div>
                </div>

                <!-- Tabla resumen de tiendas -->
                <div class="bg-white rounded-xl shadow-md p-6">
                    <h3 class="text-lg font-semibold text-black mb-4">Tiendas conectadas</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">Tienda</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">Ventas</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">Pedidos</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">Productos</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @if(isset($stores))
                                @foreach($stores as $store)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-black">{{ $store->store_url }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-black">${{ number_format($store->total_sales ?? 0, 2) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-black">{{ $store->total_orders ?? 0 }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-black">{{ $store->total_products ?? 0 }}</td>
                                </tr>
                                @endforeach
                                @else
                                <tr>
                                    <td colspan="4" class="text-gray-500">No hay tiendas conectadas.</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </x-slot>

</x-app-layout>