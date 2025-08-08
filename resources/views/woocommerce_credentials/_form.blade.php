<div class="mb-4">
    <label for="store_url" class="block text-sm font-medium text-gray-700 mb-1">URL de la tienda</label>
    <input type="url" name="store_url" id="store_url" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" value="{{ old('store_url', $credential->store_url ?? '') }}" required>
    @error('store_url')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
</div>
<div class="mb-4">
    <label for="consumer_key" class="block text-sm font-medium text-gray-700 mb-1">Consumer Key</label>
    <input type="text" name="consumer_key" id="consumer_key" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" value="{{ old('consumer_key', $credential->consumer_key ?? '') }}" required>
    @error('consumer_key')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
</div>
<div class="mb-4">
    <label for="consumer_secret" class="block text-sm font-medium text-gray-700 mb-1">Consumer Secret</label>
    <input type="text" name="consumer_secret" id="consumer_secret" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" value="{{ old('consumer_secret', $credential->consumer_secret ?? '') }}" required>
    @error('consumer_secret')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
</div>
