<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class ProcessPurchaseRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    return [
      'amount' => ['required', 'numeric', 'min:0'],
      'transaction_reference' => ['required', 'string', 'max:255'],
    ];
  }
}
