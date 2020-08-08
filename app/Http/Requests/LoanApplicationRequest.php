<?php

namespace App\Http\Requests;

use App\Rules\Money;
use Illuminate\Foundation\Http\FormRequest;

class LoanApplicationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'description' => ['required', 'min:5'],
            'total' => ['required', 'numeric', new Money()],
            'currency' => ['filled'],
            'term_ended_at' => ['required', 'date', 'after:tomorrow'],
        ];
    }
}
