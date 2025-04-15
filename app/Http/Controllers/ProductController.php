<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Services\ExchangeRateService;


class ProductController extends Controller
{
    public function index(ExchangeRateService $exchangeService )
    {
        $products = Product::all();
        $exchangeRate = $exchangeService->getUsdToEurRate();
         return view('products.list', compact('products', 'exchangeRate'));
    }

    public function show(Request $request , ExchangeRateService $exchangeService)
    {
        $id = $request->route('product_id');
        $product = Product::find($id);
        $exchangeRate = $exchangeService->getUsdToEurRate();
        return view('products.show', compact('product', 'exchangeRate'));
    }

    /**
     * @return float
     */
    //code for etExchangeRate function in services
}
