<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class DiscountController extends Controller
{
    public function calculate(Request $request)
    {
        $request->validate([
            'orderId' => 'required|exists:orders,id'
        ]);

        $orderId = $request->input('orderId');
        $order = Order::with('customer', 'items.product')->findOrFail($orderId);

        $discounts = [];
        $totalDiscount = 0;

        // Rule: 10% discount for orders over 1000
        if ($order->total >= 1000) {
            $discountAmount = $order->total * 0.1;
            $totalDiscount += $discountAmount;
            $discounts[] = [
                'discountReason' => '10_PERCENT_OVER_1000',
                'discountAmount' => number_format($discountAmount, 2),
                'subtotal' => number_format($order->total - $discountAmount, 2)
            ];
        }

        // Rule: Buy 5, Get 1 Free (for category 2)
        foreach ($order->items as $item) {
            if ($item->product->category == 2 && $item->quantity >= 6) {
                $discountAmount = $item->unit_price;
                $totalDiscount += $discountAmount;
                $discounts[] = [
                    'discountReason' => 'BUY_5_GET_1',
                    'discountAmount' => number_format($discountAmount, 2),
                    'subtotal' => number_format($order->total - $totalDiscount, 2)
                ];
            }
        }

        // Additional discount rules can be added here

        $response = [
            'orderId' => $order->id,
            'discounts' => $discounts,
            'totalDiscount' => number_format($totalDiscount, 2),
            'discountedTotal' => number_format($order->total - $totalDiscount, 2)
        ];

        return response()->json($response);
    }
}
