<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Traits\Auditable;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    use Auditable;

    public function index()
    {
        try {
            $products = Product::paginate();
            return $this->successResponse($products, 'Product list fetched');
        } catch (\Throwable $e) {
            $this->logError('Failed to fetch products', ['exception' => $e->getMessage()]);
            return $this->errorResponse('Failed to fetch products', 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'sku'           => 'required|unique:products,sku',
                'title'         => 'required|unique:products,title',
                'price'         => 'required|numeric',
                'description'   => 'required|string',
                'status'        => 'in:inactive,active,draft',
                'image'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048'
            ]);

            if ($request->hasFile('image')) {
                $validated['image'] = $request->file('image')->store('products', 'public');
            }

            $validated['id'] = Str::uuid();
            $validated['slug'] = Str::slug($validated['title']);

            $product = Product::create($validated);
            $this->logAudit('create', $product, $validated);

            return $this->successResponse($product, 'Product created', 201);

        } catch (ValidationException $e) {
            $this->logError('Validation failed during product creation', $e->errors());
            return $this->errorResponse('Validation failed', 422, $e->errors());
        } catch (\Throwable $e) {
            $this->logError('Error creating product', ['exception' => $e->getMessage()]);
            return $this->errorResponse('Failed to create product', 500);
        }
    }

    public function show($slug)
    {
        try {
            $product = Product::find($slug);

            if (!$product) {
                return $this->errorResponse('Product not found', 404);
            }

            return $this->successResponse($product, 'Product fetched');

        } catch (\Throwable $e) {
            $this->logError('Error fetching product', ['id' => $id, 'exception' => $e->getMessage()]);
            return $this->errorResponse('Failed to fetch product', 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $product = Product::find($id);

            if (!$product) {
                return $this->errorResponse('Product not found', 404);
            }

            $validated = $request->validate([
                'sku'           => 'sometimes|unique:products,sku,' . $product->id,
                'title'         => 'sometimes|unique:products,title,' . $product->id,
                'slug'          => 'sometimes|string',
                'price'         => 'sometimes|numeric',
                'description'   => 'nullable|string',
                'status'        => 'in:inactive,active,draft',
                'image'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
                'inventory_id'  => 'sometimes|uuid|unique:products,inventory_id,' . $product->id,
            ]);

            if ($request->hasFile('image')) {
                if ($product->image && Storage::disk('public')->exists($product->image)) {
                    Storage::disk('public')->delete($product->image);
                }
                $validated['image'] = $request->file('image')->store('products', 'public');
            }

            $this->logAudit('update', $product, $validated);
            $product->update($validated);

            return $this->successResponse($product, 'Product updated');

        } catch (ValidationException $e) {
            $this->logError('Validation failed during product update', $e->errors(), $product ?? null);
            return $this->errorResponse('Validation failed', 422, $e->errors());
        } catch (\Throwable $e) {
            $this->logError('Error updating product', ['exception' => $e->getMessage()], $product ?? null);
            return $this->errorResponse('Failed to update product', 500);
        }
    }

    public function destroy($id)
    {
        try {
            $product = Product::find($id);

            if (!$product) {
                return $this->errorResponse('Product not found', 404);
            }

            $this->logAudit('delete', $product, $product->toArray());

            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }

            $product->delete();

            return $this->successResponse(null, 'Product deleted');

        } catch (\Throwable $e) {
            $this->logError('Error deleting product', ['id' => $id, 'exception' => $e->getMessage()]);
            return $this->errorResponse('Failed to delete product', 500);
        }
    }
}
