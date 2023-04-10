<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OrderController extends Controller
{
    public function index(): JsonResponse
    {
        $orders = Order::with('customer', 'items.product')->get();
        return response()->json($orders);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'customerId' => 'required|exists:customers,id',
            'items' => 'required|array',
            'items.*.productId' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1'
        ]);

        $items = $request->input('items');
        $customerId = $request->input('customerId');

        $total = 0;
        foreach ($items as $item) {
            $product = Product::find($item['productId']);
            if ($product->stock < $item['quantity']) {
                return response()->json(['error' => 'Stock is not enough for the product: ' . $product->name], 400);
            }
            $total += $product->price * $item['quantity'];
        }

        $order = Order::create([
            'customer_id' => $customerId,
            'total' => $total
        ]);

        foreach ($items as $item) {
            $product = Product::find($item['productId']);
            $product->stock -= $item['quantity'];
            $product->save();

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['productId'],
                'quantity' => $item['quantity'],
                'unit_price' => $product->price,
                'total' => $product->price * $item['quantity']
            ]);
        }

        return response()->json(['success' => true, 'order_id' => $order->id], Response::HTTP_CREATED);
    }

    public function destroy($id): JsonResponse
    {
        $order = Order::findOrFail($id);
        $order->items()->delete();
        $order->delete();

        return response()->json(['success' => true], Response::HTTP_NO_CONTENT);
    }
}
