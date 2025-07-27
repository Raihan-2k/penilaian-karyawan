<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        $user = $this->user();

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
        ];

        if ($user->employee) {
            $employee = $user->employee;
            $rules = array_merge($rules, [
                'nip' => ['required', 'string', 'digits:10', Rule::unique('employees')->ignore($employee->id)],
                'position' => ['required', 'string', 'max:255'],
                'hire_date' => ['required', 'date'],
                'pendidikan_terakhir' => ['nullable', 'string', 'max:255'],
                'nomor_telepon' => ['nullable', 'string', 'max:20'],
                'tanggal_lahir' => ['nullable', 'date'],
                // 'department' => ['nullable', 'string', 'max:255'], // HAPUS BARIS INI
            ]);
        }

        return $rules;
    }
}
