<?php

namespace App\Http\Controllers;

use App\Http\Requests\FavoriteRequest;
use App\Models\Favorite;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    
    public function index(){
        // Lấy danh sách sản phẩm yêu thích của user hiện tại 
        $favorites = Favorite::where('user_id', Auth::user()->id)->with('productVariant')->paginate(20);
        if($favorites->isEmpty()){
            return response()->json(['message' => 'No favorite products'], 404); 
        }
        return response()->json($favorites);
        
    }

    public function store(FavoriteRequest $request)
{
    try {
    $userId = Auth::user()->id;
    $productVariantId = $request->input('product_variant_id');

    // Kiểm tra xem mẫu mã sản phẩm đã có trong mục yêu thích chưa
    $favorite = Favorite::where('user_id', $userId)
                        ->where('product_variant_id', $productVariantId)
                        ->first();

    if ($favorite) {
        // Nếu có thì xóa
        $favorite->delete();
        return response()->json(['message' => 'Unfavorite product successfully'], 204); // No content (success)
    } else {
        // Không thì thêm
        $favorite = Favorite::create([
            'user_id' => $userId,
            'product_variant_id' => $productVariantId,
        ]);
        return response()->json($favorite, 201); // Created (success)
    }
    } catch (\Exception $e) {
        return response()->json([   
            'success' => false,
            'message' => 'Xảy ra lỗi trong quá trình yêu thích',
            'error' => $e->getMessage()
        ], 500);
    }
    
}

}