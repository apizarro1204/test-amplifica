@extends('layouts.app')
@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-black mb-2">Pedidos</h1>
                    <p class="text-gray-600">{{ $credential->store_url }}</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('orders.exportExcel', ['credential_id' => $credential->id]) }}" 
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

        <!-- Filters Section -->
        <div class="bg-white rounded-xl shadow-md p-6 mb-8">
            <h3 class="text-lg font-semibold text-black mb-4">Filtros</h3>
            <form method="GET" action="{{ route('orders.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
                <input type="hidden" name="credential_id" value="{{ $credential->id }}">
                
                <div>
                    <label for="date_from" class="block text-sm font-medium text-black mb-2">Desde</label>
                    <input type="date" name="date_from" id="date_from" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-black" 
                           value="{{ request('date_from') }}">
                </div>
                
                <div>
                    <label for="date_to" class="block text-sm font-medium text-black mb-2">Hasta</label>
                    <input type="date" name="date_to" id="date_to" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-black" 
                           value="{{ request('date_to') }}">
                </div>
                
                <div>
                    <label for="customer" class="block text-sm font-medium text-black mb-2">Cliente</label>
                    <input type="text" name="customer" id="customer" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-black" 
                           value="{{ request('customer') }}" placeholder="Nombre o apellido">
                </div>
                
                <div>
                    <label for="status" class="block text-sm font-medium text-black mb-2">Estado</label>
                    <select name="status" id="status" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-black">
                        <option value="">Todos</option>
                        <option value="pending" @if(request('status')=='pending') selected @endif>Pendiente</option>
                        <option value="processing" @if(request('status')=='processing') selected @endif>Procesando</option>
                        <option value="completed" @if(request('status')=='completed') selected @endif>Completado</option>
                        <option value="cancelled" @if(request('status')=='cancelled') selected @endif>Cancelado</option>
                        <option value="refunded" @if(request('status')=='refunded') selected @endif>Reembolsado</option>
                        <option value="failed" @if(request('status')=='failed') selected @endif>Fallido</option>
                    </select>
                </div>
                
                <div class="md:col-span-2 lg:col-span-2 flex items-end space-x-3">
                    <button type="submit" 
                            class="flex-1 px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Filtrar
                    </button>
                    <a href="{{ route('orders.index', ['credential_id' => $credential->id]) }}" 
                       class="px-4 py-2 bg-gray-300 border border-transparent rounded-lg font-semibold text-sm text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Limpiar
                    </a>
                </div>
            </form>
        </div>

        <!-- Orders List -->
        @if($orders && count($orders) > 0)
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">
                                    Pedido
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">
                                    Cliente
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">
                                    Fecha
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">
                                    Productos
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">
                                    Estado
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">
                                    Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($orders as $order)
                                @php
                                    $isArray = is_array($order);
                                    $id = $isArray ? $order['id'] : $order->id;
                                    $billing = $isArray ? $order['billing'] : $order->billing;
                                    $firstName = $isArray ? ($billing['first_name'] ?? '') : ($billing->first_name ?? '');
                                    $lastName = $isArray ? ($billing['last_name'] ?? '') : ($billing->last_name ?? '');
                                    $customerName = trim($firstName . ' ' . $lastName) ?: 'Cliente sin nombre';
                                    $dateCreated = $isArray ? $order['date_created'] : $order->date_created;
                                    $lineItems = $isArray ? $order['line_items'] : $order->line_items;
                                    $status = $isArray ? $order['status'] : $order->status;
                                    
                                    // Status colors
                                    $statusColors = [
                                        'completed' => 'bg-green-100 text-green-800',
                                        'processing' => 'bg-blue-100 text-blue-800',
                                        'on-hold' => 'bg-yellow-100 text-yellow-800',
                                        'pending' => 'bg-gray-100 text-gray-800',
                                        'cancelled' => 'bg-red-100 text-red-800',
                                        'refunded' => 'bg-purple-100 text-purple-800',
                                        'failed' => 'bg-red-100 text-red-800',
                                    ];
                                    $statusColor = $statusColors[$status] ?? 'bg-gray-100 text-gray-800';
                                    
                                    // Status labels
                                    $statusLabels = [
                                        'completed' => 'Completado',
                                        'processing' => 'Procesando',
                                        'on-hold' => 'En espera',
                                        'pending' => 'Pendiente',
                                        'cancelled' => 'Cancelado',
                                        'refunded' => 'Reembolsado',
                                        'failed' => 'Fallido',
                                    ];
                                    $statusLabel = $statusLabels[$status] ?? ucfirst($status);
                                @endphp
                                <tr class="hover:bg-gray-50 transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                                    <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-black">#{{ $id }}</div>
                                                <div class="text-sm text-gray-500">Pedido {{ $id }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-black font-medium">{{ $customerName }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-black">
                                        {{ \Carbon\Carbon::parse($dateCreated)->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-black">
                                            @if(is_array($lineItems) || (is_object($lineItems) && method_exists($lineItems, 'count')))
                                                @php
                                                    $itemCount = is_array($lineItems) ? count($lineItems) : $lineItems->count();
                                                    $firstItem = null;
                                                    if ($itemCount > 0) {
                                                        $firstItem = is_array($lineItems) ? $lineItems[0] : $lineItems->first();
                                                        $firstItemName = is_array($firstItem) ? $firstItem['name'] : $firstItem->name;
                                                        $firstItemQty = is_array($firstItem) ? $firstItem['quantity'] : $firstItem->quantity;
                                                    }
                                                @endphp
                                                @if($itemCount > 0)
                                                    <div class="font-medium">{{ $firstItemName }} ({{ $firstItemQty }})</div>
                                                    @if($itemCount > 1)
                                                        <div class="text-gray-500 text-xs">+{{ $itemCount - 1 }} producto{{ $itemCount > 2 ? 's' : '' }} más</div>
                                                    @endif
                                                @else
                                                    <span class="text-gray-500">Sin productos</span>
                                                @endif
                                            @else
                                                <span class="text-gray-500">Sin productos</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusColor }}">
                                            {{ $statusLabel }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button class="text-blue-600 hover:text-blue-900 transition-colors duration-200 mr-3" title="Ver detalles">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </button>
                                        <button class="text-green-600 hover:text-green-900 transition-colors duration-200" title="Editar pedido">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Order Summary Cards -->
            <div class="mt-8 grid grid-cols-1 md:grid-cols-4 gap-6">
                @php
                    $totalOrders = count($orders);
                    $completedOrders = count(array_filter($orders, function($order) {
                        $status = is_array($order) ? $order['status'] : $order->status;
                        return $status === 'completed';
                    }));
                    $pendingOrders = count(array_filter($orders, function($order) {
                        $status = is_array($order) ? $order['status'] : $order->status;
                        return in_array($status, ['pending', 'processing', 'on-hold']);
                    }));
                    $processingOrders = count(array_filter($orders, function($order) {
                        $status = is_array($order) ? $order['status'] : $order->status;
                        return $status === 'processing';
                    }));
                @endphp
                
                <div class="bg-white overflow-hidden shadow-md rounded-xl">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Pedidos</dt>
                                    <dd class="text-lg font-medium text-black">{{ $totalOrders }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-md rounded-xl">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Completados</dt>
                                    <dd class="text-lg font-medium text-black">{{ $completedOrders }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-md rounded-xl">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Procesando</dt>
                                    <dd class="text-lg font-medium text-black">{{ $processingOrders }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-md rounded-xl">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Pendientes</dt>
                                    <dd class="text-lg font-medium text-black">{{ $pendingOrders }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- Empty State -->
            <div class="bg-white rounded-xl shadow-md p-12 text-center">
                <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="text-lg font-medium text-black mb-2">No hay pedidos disponibles</h3>
                <p class="text-gray-500 mb-6">Esta tienda no tiene pedidos registrados o no se pudieron cargar con los filtros aplicados.</p>
                <div class="space-x-3">
                    <a href="{{ route('orders.index', ['credential_id' => $credential->id]) }}" 
                       class="inline-flex items-center px-6 py-3 bg-blue-600 border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Limpiar Filtros
                    </a>
                    <a href="{{ route('woocommerce_credentials.index') }}" 
                       class="inline-flex items-center px-6 py-3 bg-gray-600 border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Volver a Tiendas
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
