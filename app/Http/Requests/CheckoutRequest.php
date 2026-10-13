<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'regex:/^(0|\+84)[0-9]{8,10}$/'],
            'email' => ['nullable', 'email', 'max:150'],

            'address' => ['required', 'string', 'max:255'],
            'province_id' => ['required', 'integer'],
            'province_name' => ['required', 'string', 'max:120'],
            'district_id' => ['required', 'integer'],
            'district_name' => ['required', 'string', 'max:120'],
            'ward_code' => ['required', 'string', 'max:50'],
            'ward_name' => ['required', 'string', 'max:120'],

            'note' => ['nullable', 'string', 'max:500'],
            'payment_method' => ['required', 'in:cod,momo'],
        ];
    }

    public function messages(): array
    {
        return [
            'customer_name.required' => 'Vui lòng nhập họ tên.',
            'phone.required' => 'Vui lòng nhập số điện thoại.',
            'phone.regex' => 'Số điện thoại không hợp lệ.',
            'address.required' => 'Vui lòng nhập địa chỉ nhận hàng.',
            'province_id.required' => 'Vui lòng chọn tỉnh/thành phố.',
            'district_id.required' => 'Vui lòng chọn quận/huyện.',
            'ward_code.required' => 'Vui lòng chọn phường/xã.',
            'payment_method.required' => 'Vui lòng chọn phương thức thanh toán.',
            'payment_method.in' => 'Phương thức thanh toán không hợp lệ.',
        ];
    }
}
