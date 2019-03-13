<?php
namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;

class BankAccountRequest extends FormRequest
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
            'type' => 'required',
            'tag' => 'required',
            'addressline1' => 'required',
            'city' => 'required',
            'region' => 'required',
            'postal_code' => 'required',
            'country' => 'required',
            'owner_name' => 'required',
            'iban' => 'required_if:type,IBAN',
            'account_number' => 'required_if:type,US,CA,GB,OTHER',
            'aba' => 'required_if:type,US|numeric',
            'branch_code' => 'required_if:type,CA|numeric|max:5',
            'institution_number' => 'required_if:type,CA|numeric|max:4',
            'bank_name' => 'required_if:type,CA',
            'sort_code' => 'required_if:type,GB',
            'bic' => 'required_if:type,OTHER'
        ];
    }

    public function messages()
    {
        return [
            'invitation_id.required' => 'Invitation id is required',
            'invitation_id.exists' => 'You do not have access to this invitation id',
            'status.required' => 'Status is required',
            'status.numeric' => 'Status should be a numeric value',

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
