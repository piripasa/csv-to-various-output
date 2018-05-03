<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GenerateFileRequest extends FormRequest
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
            'output' => 'required|array|in:json,xml,html,yaml,sqlite',
//            'sort_order' => 'required_with:sort',
//            'filter_value' => 'required_with:filter',
        ];
    }
}
