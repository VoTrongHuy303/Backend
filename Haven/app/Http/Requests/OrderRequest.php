<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class OrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'full_name' => 'required|string',
            'phone' => 'required|string',
            'email' => 'required|email',
            'address' => 'required|string',
            'payment_method' => 'required|string',
            
        
        ];
    }

    public function messages(): array
    {
        
        return [
            // 'name_product' messages
            'name_product.required' => 'Vui lòng nhập tên sản phẩm chính.',
            'name_product.string' => 'Tên sản phẩm chính phải là chuỗi ký tự hợp lệ.',
            'name_product.max' => 'Tên sản phẩm chính không được vượt quá 255 ký tự.',
            
            'images.array' => 'Danh sách hình ảnh phải là một mảng.',
            'images.*.image' => 'Mỗi tệp phải là một hình ảnh hợp lệ.',
            'images.*.mimes' => 'Hình ảnh phải có định dạng jpeg, png, jpg, gif hoặc svg.',
            'images.*.max' => 'Dung lượng mỗi hình ảnh không được vượt quá 2MB.',
            
            // 'name' messages
            'name.required' => 'Vui lòng nhập tên cho các biến thể.',
            'name.array' => 'Tên biến thể phải là một mảng.',
            'name.*.string' => 'Mỗi tên biến thể phải là một chuỗi ký tự.',
    
            // 'price' messages
            'price.required' => 'Vui lòng nhập giá cho các biến thể.',
            'price.array' => 'Giá sản phẩm phải là một mảng.',
            'price.*.required' => 'Giá của mỗi biến thể là bắt buộc.',
            'price.*.numeric' => 'Giá của mỗi biến thể phải là một số.',
            'price.*.min' => 'Giá của mỗi biến thể không được nhỏ hơn 0.',
    
            // 'stock' messages
            'stock.required' => 'Vui lòng nhập số lượng tồn kho cho các biến thể.',
            'stock.array' => 'Số lượng tồn kho phải là một mảng.',
            'stock.*.integer' => 'Số lượng tồn kho của mỗi biến thể phải là một số nguyên.',
            'stock.*.min' => 'Số lượng tồn kho của mỗi biến thể không được nhỏ hơn 0.',
    
            // 'variant_value' messages
            'variant_value.required' => 'Vui lòng nhập giá trị biến thể.',
            'variant_value.array' => 'Giá trị biến thể phải là một mảng.',
            'variant_value.*.string' => 'Mỗi giá trị biến thể phải là chuỗi ký tự.',
    
            // 'description' messages
            'description.string' => 'Mô tả sản phẩm phải là chuỗi ký tự hợp lệ.',
    
            // 'image' messages
            'image.array' => 'Danh sách hình ảnh phải là một mảng.',
            'image.*.image' => 'Mỗi tệp phải là một hình ảnh hợp lệ.',
            'image.*.mimes' => 'Hình ảnh phải có định dạng jpeg, png, jpg, gif hoặc svg.',
            'image.*.max' => 'Dung lượng mỗi hình ảnh không được vượt quá 2MB.',
    
            // 'category_id' messages
            'category_id.required' => 'Vui lòng chọn danh mục cho sản phẩm.',
            'category_id.exists' => 'Danh mục bạn chọn không tồn tại.',
    
            // 'brand_id' messages
            'brand_id.exists' => 'Thương hiệu bạn chọn không tồn tại.',
    
          
    
            // 'discount' messages
            'discount.required' => 'Vui lòng nhập thông tin giảm giá.',
            'discount.array' => 'Thông tin giảm giá phải là một mảng.',
            'discount.*.numeric' => 'Giảm giá phải là số.',
            'discount.*.min' => 'Giảm giá không được nhỏ hơn 0%.',
            'discount.*.max' => 'Giảm giá không được lớn hơn 100%.',
        ];
    }
    
    protected function failedValidation(Validator $validator)
    {
        // Tùy chỉnh phản hồi JSON cho lỗi xác thực
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Dữ liệu không hợp lệ.',
            'errors' => $validator->errors(),
        ], 422));
    }
}
