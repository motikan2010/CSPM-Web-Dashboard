<?php


namespace App\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;

class CloudNewRequest extends FormRequest
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
            'name' => ['required'],
            'aws_key' => ['required', 'regex:/^[A-Z0-9]{20}$/'],
            'aws_secret' => ['required', 'regex:/^[a-zA-Z0-9+\/]{40}$/'],
        ];
    }
}
