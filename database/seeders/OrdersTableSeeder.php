<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Seeder;

class OrdersTableSeeder extends Seeder
{
    public function run()
    {
        $orders = [
            [
                "id" => 1,
                "customerId" => 1,
                "items" => [
                    [
                        "productId" => 102,
                        "quantity" => 10,
                        "unitPrice" => "11.28",
                        "total" => "112.80"
                    ]
                ],
                "total" => "112.80"
            ],
            [
                "id" => 2,
                "customerId" => 2,
                "items" => [
                    [
                        "productId" => 101,
                        "quantity" => 2,
                        "unitPrice" => "49.50",
                        "total" => "99.00"
                    ],
                    [
                        "productId" => 100,
                        "quantity" => 1,
                        "unitPrice" => "120.75",
                        "total" => "120.75"
                    ]
                ],
                "total" => "219.75"
            ],
            [
                "id" => 3,
                "customerId" => 3,
                "items" => [
                    [
                        "productId" => 102,
                        "quantity" => 6,
                        "unitPrice" => "11.28",
                        "total" => "67.68"
                    ],
                    [
                        "productId" => 100,
                        "quantity" => 10,
                        "unitPrice" => "120.75",
                        "total" => "1207.50"
                    ]
                ],
                "total" => "1275.18"
            ]
        ];

        foreach ($orders as $orderData) {
            $order = Order::create([
                'id' => $orderData['id'],
                'customer_id' => $orderData['customerId'],
                'total' => $orderData['total']
            ]);

            foreach ($orderData['items'] as $itemData) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $itemData['productId'],
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $itemData['unitPrice'],
                    'total' => $itemData['total']
                ]);
            }
        }
    }
}
