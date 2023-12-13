<?php

namespace App\Http\Controllers;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Resources\ProductResource;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreProductRequest;
use App\Traits\HttpResponses;

class ProductController extends Controller
{
    
    use HttpResponses;


    public function index()
    {
        return ProductResource::collection(
            Product::where('user_id', Auth::user()->id)->get()
        );
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        $request->validated($request->all());

        $product = Product::create([
            'user_id' => Auth::user()->id,
            'name' =>$request->name,
            'description' => $request->description,
            'priority' => $request->priority,
            'weight' => $request->weight,
            'size' => $request->size,
            'model' => $request->model,

        ]);

        return new ProductResource($product);
    }
    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {

        return $this->isNotAuthorized($product) ? $this->isNotAuthorized($product) : new ProductResource($product);

        
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {

        if(Auth::user()->id !== $product->user_id){
            return $this->error('', 'You are not authorized to make this request', 403);
        }

        $product->update($request->all());
        return new ProductResource($product);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        return $this->isNotAuthorized($product) ? $this->isNotAuthorized($product) : $product->delete();
    }

    private function isNotAuthorized($product)
    {
        if(Auth::user()->id !== $product->user_id){
            return $this->error('', 'You are not authorized to make this request', 403);
        }
    }

}
