<?php

namespace App\Http\Controllers;

use Akaunting\Money\Money;
use App\Http\Requests\SalesRecordRequest;
use App\Models\Sales;
use Illuminate\Http\Request;

class SalesController extends Controller
{
    public function recordSale(SalesRecordRequest $request): \Illuminate\Http\JsonResponse
    {
        $quantity = $request->quantity;
        $unit_cost = $request->unit_cost;
        $order_id = $request->order_id;
        $shipping_cost = config('constant.shipping_cost');
        $profit_margin = config('constant.profit_margin');

        $sales = new Sales();
        $sales->user_id = auth()->user()->id;
        $sales->order_id = $order_id;
        $sales->quantity = $quantity;
        $sales->unit_cost = $unit_cost;
        $sales->shipping_cost = $shipping_cost;
        $sales->profit_margin = $profit_margin;
        $sales->save();

        return response()->json([
            "message" => "Sales record created"
        ], 201);
    }

    function getSales(Request $request)
    {
        return Sales::where('order_id', $request->order_id)->get()
            ->map(function ($sale) {
            return [
                'quantity' => $sale->quantity,
                'unit_cost' => $sale->unit_cost,
                'selling_price' => $this->getSellingPrice(
                    $sale->quantity,
                    $sale->unit_cost
                ),
            ];
        });

    }

    public function sellingPrice(Request $request): \Illuminate\Http\JsonResponse
    {
        $quantity = $request->quantity;
        $unit_cost = $request->unit_cost;

        return response()->json([
            'selling_price' => $this->getSellingPrice($quantity, $unit_cost)
        ]);
    }

    public function getSellingPrice($quantity, $unit_cost): string
    {
        $cost = $quantity * $unit_cost;

        $cost = number_format(
            ($cost / (1 - (config('constant.profit_margin') / 100)))
            + config('constant.shipping_cost'),
            2
        );

        return Money::EUR($cost)->format();
    }
}
