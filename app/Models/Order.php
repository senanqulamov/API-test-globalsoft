<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['customer_id', 'status', 'total_amount', 'paid'];

    public function customer(){
        return $this->belongsTo(Customer::class);
    }

    public function items(){
        return $this->hasMany(OrderItem::class);
    }

    // Calculating the total amount of the order
    public function calculateTotal(){
         $total = 0;
         foreach($this->items as $item){
             $total += $item->quantity * $item->unit_price;
         }
         $this->total_amount = $total;
         $this->save();
         return $total;
    }
}
