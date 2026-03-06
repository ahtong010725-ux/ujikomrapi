<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
{
    return [
        'nisn' => ['required', 'string', 'max:20'],

        'name' => ['required', 'string', 'max:255'],


        

        'kelas' => ['required', 'string', 'max:50'],

        'phone' => ['required', 'string', 'max:20'],

        'tanggal_lahir' => ['required', 'date'],

        'jenis_kelamin' => ['required', 'in:Laki-laki,Perempuan'],

        'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
    ];
}

}
