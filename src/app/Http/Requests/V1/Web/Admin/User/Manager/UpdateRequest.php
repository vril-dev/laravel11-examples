<?php

namespace App\Http\Requests\V1\Web\Admin\User\Manager;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Database\Query\Builder;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return boolean
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'id' => [
                'required',
                Rule::prohibitedIf((int) $this->id === user('id')),
                Rule::exists(User::class)
                    ->where(function(Builder $query) {
                        return $query->where('role_id', '>=', user('role_id'));
                    }),
            ],
            'name' => [
                'required',
            ],
            'email' => [
                'required',
                'max:255',
                Rule::unique(User::class)
                    ->ignore($this->id),
                'email:rfc,dns',
            ],
            'role_id' => [
                'required',
                'exists:App\Models\Role,id'
            ],
            'is_login_prohibited' => [
                'required',
                'boolean',
            ],
            'is_restore' => [
                'required',
                'boolean',
            ],
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    public function prepareForValidation(): void
    {
        $this->merge($this->route()->parameteers());
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'id.prohibited' => __('You cannot choose yourself.'),
        ];
    }

}
