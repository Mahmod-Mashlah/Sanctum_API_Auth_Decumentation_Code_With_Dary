<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'name' => ['required','max:255'],
            'description' => ['required','string'],
            'priority' => ['required','in:low,medium,high'],

        ];
    }
}
