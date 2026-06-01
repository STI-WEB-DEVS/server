<?php
 
namespace App\Http\Requests;
 
use Illuminate\Foundation\Http\FormRequest;
 
class OrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Ensure this is set to true so it allows the request through
    }
 
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            //  REMOVED 'customer_uuid' rule since it's now handled implicitly by auth()
 
            'items'                  => 'required|array|min:1',
            'items.*.product_uuid'   => 'required|string',
            'items.*.quantity'       => 'required|integer|min:1',
        ];
    }
}