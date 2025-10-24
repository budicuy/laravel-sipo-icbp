<?php

namespace App\Http\Requests;

use App\ValidationMessages;
use Illuminate\Foundation\Http\FormRequest;

class RekamMedisStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'id_keluarga' => 'required|exists:keluarga,id_keluarga',
            'tanggal_periksa' => 'required|date',
            'waktu_periksa' => 'nullable|date_format:H:i',
            'status' => 'required|in:On Progress,Close',
            'jumlah_keluhan' => 'required|integer|min:1|max:3',

            // Validasi untuk setiap keluhan
            'keluhan.*.id_diagnosa' => 'required|exists:diagnosa,id_diagnosa',
            'keluhan.*.terapi' => 'required|in:Obat,Lab,Istirahat',
            'keluhan.*.keterangan' => 'nullable|string',
            'keluhan.*.obat_list' => 'nullable|array',
            'keluhan.*.obat_list.*.id_obat' => 'required|exists:obat,id_obat',
            'keluhan.*.obat_list.*.jumlah_obat' => 'nullable|integer|min:1|max:10000',
            'keluhan.*.obat_list.*.aturan_pakai' => 'nullable|string',
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
}
