<?php

namespace App\Http\Requests;

use App\ValidationMessages;
use Illuminate\Foundation\Http\FormRequest;

class RekamMedisEmergencyStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Check if user has valid token
        return session('valid_emergency_token') !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'external_employee_id' => 'required|exists:external_employees,id',
            'tanggal_periksa' => 'required|date',
            'waktu_periksa' => 'nullable|date_format:H:i',
            'status' => 'required|in:On Progress,Close',
            'keluhan' => 'required|string',
            'id_diagnosa_emergency' => 'required|exists:diagnosa_emergency,id_diagnosa_emergency',
            'terapi' => 'required|string',
            'catatan' => 'nullable|string',
            'obat_list' => 'nullable|array',
            'obat_list.*.id_obat' => 'required|exists:obat,id_obat',
            'obat_list.*.jumlah_obat' => 'nullable|integer|min:1|max:10000',
            'obat_list.*.aturan_pakai' => 'nullable|string',
        ];
    }

    /**
     * Get custom error messages for validation.
     */
    public function messages(): array
    {
        return ValidationMessages::getMessages();
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return ValidationMessages::getAttributeMessages();
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (! session('valid_emergency_token')) {
                $validator->errors()->add('token', 'Token emergency diperlukan untuk membuat rekam medis emergency.');
            }
        });
    }
}
