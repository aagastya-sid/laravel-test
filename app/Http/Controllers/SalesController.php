<?php

namespace App\Http\Controllers;

use Akaunting\Money\Money;
use App\Http\Requests\SalesRecordRequest;
use App\Models\Product;
use App\Models\Sales;
use Illuminate\Http\Request;

class SalesController extends Controller
{
    public function index()
    {
        $products = Product::all()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                ];
            });

        return view('coffee_sales', [
            'products' => $products,
        ]);

    }
    public function recordSale(SalesRecordRequest $request): \Illuminate\Http\JsonResponse
    {
        $product = Product::find($request->product_id);
        $quantity = $request->quantity;
        $unit_cost = $request->unit_cost;
        $order_id = $request->order_id;
        $product_id = $request->product_id;
        $shipping_cost = config('constant.shipping_cost');
        $profit_margin = $product->profit_margin;

        $sales = new Sales();
        $sales->user_id = auth()->user()->id;
        $sales->order_id = $order_id;
        $sales->quantity = $quantity;
        $sales->unit_cost = $unit_cost;
        $sales->shipping_cost = $shipping_cost;
        $sales->profit_margin = $profit_margin;
        $sales->product_id = $product_id;
        $sales->save();

        return response()->json([
            "message" => "Sales record created"
        ], 201);
    }

    public function removeRecord(Request $request)
    {
        $sales = Sales::find($request->id);
        $sales->delete();

        return response()->json([
            "message" => "Sales record deleted"
        ], 200);
    }

    function getSales(Request $request)
    {
        return Sales::with('product')->where('order_id', $request->order_id)->get()
            ->map(function ($sale) {
            return [
                'id' => $sale->id,
                'quantity' => $sale->quantity,
                'unit_cost' => $sale->unit_cost,
                'selling_price' => $this->getSellingPrice(
                    $sale->quantity,
                    $sale->unit_cost,
                    $sale->product->profit_margin
                ),
                'product' => $sale->product->name,
                'sold_at' => $sale->created_at->format('Y-m-d H:i'),
            ];
        });

    }

    public function sellingPrice(Request $request): \Illuminate\Http\JsonResponse
    {
        $product = Product::find($request->product_id);
        $quantity = $request->quantity;
        $unit_cost = $request->unit_cost;

        return response()->json([
            'selling_price' => $this->getSellingPrice($quantity, $unit_cost, $product->profit_margin)
        ]);
    }

    public function getSellingPrice($quantity, $unit_cost, $profit_margin): string
    {
        $cost = $quantity * $unit_cost;

        $cost = number_format(
            ($cost / (1 - ($profit_margin / 100)))
            + config('constant.shipping_cost'),
            2
        );

        return Money::EUR($cost)->format();
    }
}
