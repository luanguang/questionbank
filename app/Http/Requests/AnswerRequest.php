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
            'title'         =>  'required|string',
            'is_pic'        =>  'boolean',
            'score'         =>  'integer|min:0|max:10',
            'difficult'     =>  'required|in:1,2,3,4,5',
            'category_id'   =>  'required|integer|min:0',
            'paper_id'      =>  'integer|min:0',
            'choice.*'      =>  'required|string',
            'is_right'      =>  'required|integer'
        ];
    }
}
