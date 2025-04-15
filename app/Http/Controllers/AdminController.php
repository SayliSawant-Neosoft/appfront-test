<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Jobs\SendPriceChangeNotification;
use App\Repositories\ProductRepositoryInterface;
use App\Services\ProductService;
use App\Http\Requests\StoreProductRequest;

class AdminController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function loginPage()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        if (Auth::attempt($request->except('_token'))) {
            return redirect()->route('admin.products');
        }
        return redirect()->back()->with('error', 'Invalid login credentials');
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }

    public function products()
    {
        $products = $this->productService->all();
        return view('admin.products', compact('products'));
    }

    public function editProduct($id)
    {
        $product = $this->productService->getProductById($id);
        return view('admin.edit_product', compact('product'));
    }
    //Removed Previous Code for updateProduct

    public function updateProduct(StoreProductRequest $request,  $id)
    {
        try {
            $this->productService->updateProduct($id, $request->validated());
            return redirect()->route('admin.products')->with('success', 'Product updated successfully');
        } catch (Exception $e) {
            return back()->withErrors($e->getMessage());
        }
    }

    public function deleteProduct($id)
    {
        $this->productService->delete($id);
        return redirect()->route('admin.products')->with('success', 'Product deleted successfully');
    }

    public function addProductForm()
    {
        return view('admin.add_product');
    }
    //Removed Previous Code for addProduct

    public function addProduct(StoreProductRequest $request)
    {
        try {
            $this->productService->create($request->validated());
            return redirect()->route('admin.products')->with('success', 'Product added successfully');;
        } catch (Exception $e) {
            return back()->withErrors($e->getMessage());
        }
    
    }
}
