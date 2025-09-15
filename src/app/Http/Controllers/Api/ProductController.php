<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Log;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Product::with(['images','categories'])->paginate(10);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'string|max:255',
            'description' => 'string',
            'price' => 'nullable',
            'stock' => 'nullable'
        ]);

        $product = Product::create($request->only(['name', 'description', 'price', 'stock']));

        if ($request->filled('categories')) {
            $product->categories()->sync($request->categories);
        }
    
        Log::info('1', $request->hasFile('images'));

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('uploads', 'public');
                $product->images()->create(['path' => $path]);
            }
        }
        
        return response()->json($product->load(['images', 'categories']), 201);

    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return $product->load(['images', 'categories']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $product->update($request->only(['name', 'description', 'price', 'stock']));

        $product->categories()->sync($request->input('categories',[]));

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('uploads', 'public');
                $product->images()->create(['path' => $path]);
            }
        }

        return response()->json($product->load(['images', 'categories']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        foreach ($product->images as $img) {
            \Storage::disk('public')->delete($img->path);
            $img->delete();
        }

        $product->categories()->detach();
        $product->delete();
        return response()->json(null, 204);
    }
}
