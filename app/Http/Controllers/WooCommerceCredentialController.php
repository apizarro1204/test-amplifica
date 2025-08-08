<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\StoreWooCommerceCredentialRequest;
use Illuminate\Support\Facades\Auth;

class WooCommerceCredentialController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();
        $credentials = $user->woocommerceCredentials()->get();
        return view('woocommerce_credentials.index', compact('credentials'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('woocommerce_credentials.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreWooCommerceCredentialRequest $request)
    {
        /** @var User $user */
        $user = Auth::user();
        $user->woocommerceCredentials()->create($request->validated());
        return redirect()->route('woocommerce_credentials.index')->with('success', 'Tienda WooCommerce agregada correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        /** @var User $user */
        $user = Auth::user();
        $credential = $user->woocommerceCredentials()->findOrFail($id);
        $credential->delete();
        return redirect()->route('woocommerce_credentials.index')->with('success', 'Tienda eliminada correctamente.');
    }
}
