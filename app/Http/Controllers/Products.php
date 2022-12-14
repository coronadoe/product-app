<?php
namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Jobs\AddProduct;
use App\Jobs\AddProductCategories;
use App\Jobs\DeleteProduct;
use App\Jobs\DeleteProductCategories;
use App\Jobs\UpdateProduct;
use App\Models\Product;
use App\Traits\ProductFormat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;

class Products extends Controller
{
    use ProductFormat;

    /**
     * Display the list of products
     */
    public function index()
    {
        $products = Product::all();

        $productList = $products->map(function($product) {
            return $this->getProductFormat($product);
        });

        return response()->json(['products' => $productList]);
    }

    /**
     * Insert a new Product 
     */
    public function store(ProductRequest $request)
    {
        if ($request->validated()) {
            dispatch(new AddProduct($request->all()));
            return response()->json(['Success' => 'Product Created Successfully']);
        }

        return response()->json(['Success' => 'Product wa not Created']);
    }

    /**
     * Retrieve the details of the product
     */
    public function show(Request $request)
    {
        $product = Product::find($request->get('id'));
        if (!empty($product)) {
            return response()->json(['product' => $this->getProductFormat($product)]);
        } 
        
        return response()->json(['product' => "No product found"]);
    }

    /**
     * Update an existing product
     */
    public function update(ProductRequest $request)
    {
        if ($request->validated()) {
            $product = Product::find($request->get('id'));

            if (empty($product)) {
                return response()->json(['product' => "No product found"]);
            }

            Bus::chain([
                new UpdateProduct($product, $request->all()),
                new DeleteProductCategories($product),
                new AddProductCategories($product, $request->all())
            ])->dispatch();

            return response()->json(['success' => "Product Updated Successfully"]);
        }

        return response()->json(['fail' => "Product was not updated"]);
    }

    /**
     * Destroy an existing product
     */
    public function destroy(Request $request)
    {
        $product = Product::find($request->get('id'));
        if (empty($product)) {
            return response()->json(['product' => "No product found"]);
        }

        Bus::chain([
            new DeleteProductCategories($product),
            new DeleteProduct($product),
        ])->dispatch();

        return response()->json(['success' => "Product deleted Successfully"]);
    }
}