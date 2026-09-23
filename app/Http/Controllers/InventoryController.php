<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $inventory = Inventory::where('user_id', $request->user()->id)
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $inventory
        ]);
    }

    public function show(Request $request, $id)
    {
        $item = Inventory::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Inventory item not found.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $item
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'cost' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
        ]);

        $profit = $validated['price'] - $validated['cost'];

        $status = match (true) {
            $validated['stock'] <= 0 => 'Out of Stock',
            $validated['stock'] <= 5 => 'Low Stock',
            default => 'In Stock',
        };

        $item = Inventory::create([
            'user_id' => $request->user()->id,
            'product' => $validated['product'],
            'category' => $validated['category'],
            'price' => $validated['price'],
            'cost' => $validated['cost'],
            'profit' => $profit,
            'stock' => $validated['stock'],
            'status' => $status,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Inventory item created successfully.',
            'data' => $item
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $item = Inventory::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Inventory item not found.'
            ], 404);
        }

        $validated = $request->validate([
            'product' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'cost' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
        ]);

        $profit = $validated['price'] - $validated['cost'];

        $status = match (true) {
            $validated['stock'] <= 0 => 'Out of Stock',
            $validated['stock'] <= 5 => 'Low Stock',
            default => 'In Stock',
        };

        $item->update([
            'product' => $validated['product'],
            'category' => $validated['category'],
            'price' => $validated['price'],
            'cost' => $validated['cost'],
            'profit' => $profit,
            'stock' => $validated['stock'],
            'status' => $status,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Inventory item updated successfully.',
            'data' => $item
        ]);
    }

    public function delete(Request $request, $id)
    {
        $item = Inventory::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Inventory item not found.'
            ], 404);
        }

        $item->delete();

        return response()->json([
            'success' => true,
            'message' => 'Inventory item deleted successfully.'
        ]);
    }
}