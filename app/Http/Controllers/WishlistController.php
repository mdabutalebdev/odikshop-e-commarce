<?php

namespace App\Http\Controllers;

use App\Services\Wishlist;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function __construct(private Wishlist $wishlist) {}

    public function index()
    {
        return view('wishlist', [
            'items' => $this->wishlist->items(),
        ]);
    }

    public function toggle(Request $request)
    {
        $id = (int) $request->input('product_id');
        $active = $this->wishlist->toggle($id);

        return response()->json([
            'ok' => true,
            'active' => $active,
            'count' => $this->wishlist->count(),
        ]);
    }

    public function remove(Request $request)
    {
        $this->wishlist->remove((int) $request->input('product_id'));

        return back();
    }
}
