<?php
namespace App\Http\Requests\ProJob;

use Auth;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Request;


class InviteProJobRequest extends FormRequest
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
    public function rules(Request $request)
    {
       
        return [
            'user_id' => 'required|exists:users,id',
            'pro_job_id' => 'required|exists:pro_jobs,id,user_id,' . Auth::user()->id,
            'pro_job_id' => 'unique:pro_job_invites,pro_job_id,NULL,id,user_id,'.$this->user_id,
           
            
        ];
    }

    public function messages()
    {
        return [
            'user_id.unique' => 'Invalid user',
            'pro_job_id.exists' => 'Invalid pro_job_id OR you are not owner of this job',
            'pro_job_id.unique' => 'You already invited this job to this user.',
          ];
    }
    /**
     * [failedValidation [Overriding the event validator for custom error response]]
     * @param  Validator $validator [description]
     * @return [object][object of various validation errors]
     */
    public function failedValidation(Validator $validator)
    {
        $data_error = [];
        $error = $validator->errors()->all(); #if validation fail print error messages
        foreach ($error as $key => $errors):
            $data_error['status'] = 400;
            $data_error['message'] = $errors;
        endforeach;
        //write your bussiness logic here otherwise it will give same old JSON response
        throw new HttpResponseException(response()->json($data_error));

    }
}
