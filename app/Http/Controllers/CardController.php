<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class CardController extends Controller
{
    public function viewCard()
    {
        $user = Auth::user();

        // get card count and total
        $card = Card::where('user_id', $user->id)->where('status', 'active')->with('items.product')->first();

        if (!$card) {
            return response()->json([
                'message' => 'No active card found',
            ], 200);
        }

        $cardItems = $card ? $card->items : [];
        $total = 0;
        $count = 0;

        foreach ($cardItems as $item) {
            $total += $item->price * $item->quantity;
            $count += $item->quantity;
            $item->product->image = $item->product->image ? asset('storage/' . $item->product->image) : null;
        }

        return response()->json([
            'card' => $cardItems,
            'total' => $total,
            'count' => $count,
        ]);
    }


    public function addToCard(Request $request)
    {
        $request->validate([
            'product_id' => 'required',
            'quantity' => 'required|integer|min:1',
        ]);

        $user = Auth::user();
        // add product to card and items
        $card = Card::firstOrCreate(['user_id' => $user->id, 'status' => 'active']);
        // where user_id = $user->id and status = 'active'
        $cardItem = $card->items()->where('product_id', $request->product_id)->first();

        if ($cardItem) {
            $cardItem->quantity += $request->quantity;
            $cardItem->save();
        } else {
            $cardItem = $card->items()->create([
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
                'price' => Product::find($request->product_id)->price,
                'status' => 'active',
            ]);
        }
        return response()->json(['message' => 'Product added to card successfully']);
    }

    public function removeFromCard($productId)
    {
        $user = Auth::user();

        //remove cart from by card id
        $card = Card::where('user_id', $user->id)->where('status', 'active')->first();
        $cardItem = $card->items()->where('product_id', $productId)->first();

        if ($cardItem) {
            $cardItem->delete();
        }

        return response()->json(['message' => 'Product removed from card successfully']);
    }
}
