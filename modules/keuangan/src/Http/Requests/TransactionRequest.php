<?php

namespace Modules\Keuangan\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransactionRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'category_id' => ['nullable', 'exists:categories,id'],
            'type'        => ['required', 'in:income,expense'],
            'amount'      => ['required', 'numeric', 'min:1', 'max:999999999999'],
            'description' => ['nullable', 'string', 'max:255'],
            'transaction_date' => ['required', 'date'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'type.required'             => 'Tipe transaksi wajib dipilih.',
            'type.in'                   => 'Tipe transaksi harus pemasukan atau pengeluaran.',
            'amount.required'           => 'Jumlah transaksi wajib diisi.',
            'amount.numeric'            => 'Jumlah transaksi harus berupa angka.',
            'amount.min'                => 'Jumlah transaksi minimal Rp 1.',
            'amount.max'                => 'Jumlah transaksi terlalu besar.',
            'description.max'           => 'Deskripsi maksimal 255 karakter.',
            'transaction_date.required' => 'Tanggal transaksi wajib diisi.',
            'transaction_date.date'     => 'Format tanggal tidak valid.',
            'category_id.exists'        => 'Kategori yang dipilih tidak ditemukan.',
        ];
    }
}
