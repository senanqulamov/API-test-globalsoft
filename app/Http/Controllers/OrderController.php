<?php
namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    // Show orders with status filter
    public function index(Request $request)
    {
         $status = $request->query('status');
         $orders = Order::when($status, function($query, $status){
             if($status === 'active'){
                 $query->where('status', 'active');
             } elseif($status === 'deleted'){
                 $query->where('status', 'deleted');
             }
         })->paginate(10);

         return response()->json($orders);
    }

    // Show order with given id
    public function show($id)
    {
         $order = Order::with('items.product', 'customer')->findOrFail($id);
         return response()->json($order);
    }

    // Create a new order
    public function create(Request $request)
    {
         $validated = $request->validate([
             'customer_id' => 'required|exists:customers,id',
             'products' => 'required|array',
             'products.*.id' => 'required|exists:products,id',
             'products.*.quantity' => 'required|integer|min:1'
         ]);

         // Create a new order
         $order = Order::create([
              'customer_id' => $validated['customer_id'],
              'status' => 'active',
              'total_amount' => 0,
              'paid' => false
         ]);

         foreach($validated['products'] as $prod){
             $unitPrice = 0;
             OrderItem::create([
                  'order_id' => $order->id,
                  'product_id' => $prod['id'],
                  'quantity' => $prod['quantity'],
                  'unit_price' => $unitPrice
             ]);
         }

         $order->calculateTotal();
         return response()->json($order, 201);
    }

    // Updating order
    public function update(Request $request, $id)
    {
         $order = Order::findOrFail($id);

         // If paid , will not be updated
         if($order->paid){
             return response()->json(['error' => 'A paid order cannot be updated'], 400);
         }

         $validated = $request->validate([
             'products' => 'required|array',
             'products.*.id' => 'required|exists:products,id',
             'products.*.quantity' => 'required|integer|min:1'
         ]);

         $order->items()->delete();
         foreach($validated['products'] as $prod){
             $unitPrice = 0;
             OrderItem::create([
                  'order_id' => $order->id,
                  'product_id' => $prod['id'],
                  'quantity' => $prod['quantity'],
                  'unit_price' => $unitPrice
             ]);
         }

         $order->calculateTotal();
         return response()->json($order);
    }

    public function delete($id)
    {
         $order = Order::findOrFail($id);

         //  if Paid status is true, then it will not be deleted
         if($order->paid){
             return response()->json(['error' => 'A paid order cannot be deleted'], 400);
         }
         $order->status = 'deleted';
         $order->save();
         return response()->json(['message' => 'Order has been marked as deleted']);
    }
}
