<?php

namespace App\Http\Requests\DiscussionReply;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
            'description' => [
                'required',
                function ($attribute, $value, $fail) {
                    $comment = trim_editor($value);;

                    if ($comment == '') {
                        $fail($attribute . ' ' . __('app.required'));
                    }
                }
            ]
        ];
    }

}
