<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AnswerRequest extends FormRequest
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
            'title'                     =>  'required|string',
            'is_pic'                    =>  'required|boolean',
            'choice_num'                =>  'required|integer|min:0',
            'category_id'               =>  'required|integer|min:0',
            'choice.*.choice'           =>  'required|string',
            'choice.*.question_id'      =>  'required|integer|min:0',
            'choice.*.is_right'         =>  'required|integer|min:0',
            'choice.*.detail'           =>  'string'
        ];
    }
}
