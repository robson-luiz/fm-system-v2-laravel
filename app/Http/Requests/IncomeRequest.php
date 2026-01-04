<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Income;

class IncomeRequest extends FormRequest
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
            'description' => [
                'required',
                'string',
                'max:255',
                'min:3'
            ],
            'amount' => [
                'required',
                'numeric',
                'min:0.01',
                'max:999999.99'
            ],
            'received_date' => [
                'required',
                'date',
                'after_or_equal:2020-01-01',
                'before_or_equal:' . now()->addYear()->format('Y-m-d')
            ],
            'category' => [
                'required',
                'string',
                'max:100',
                'in:' . implode(',', Income::getDefaultCategories())
            ],
            'type' => [
                'required',
                'in:fixa,variavel'
            ],
            'status' => [
                'required',
                'in:recebida,pendente'
            ],
            'source' => [
                'nullable',
                'string',
                'max:255'
            ],
            'notes' => [
                'nullable',
                'string',
                'max:1000'
            ]
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'description.required' => 'A descrição é obrigatória.',
            'description.min' => 'A descrição deve ter pelo menos 3 caracteres.',
            'description.max' => 'A descrição não pode ter mais de 255 caracteres.',
            
            'amount.required' => 'O valor é obrigatório.',
            'amount.numeric' => 'O valor deve ser um número válido.',
            'amount.min' => 'O valor deve ser maior que zero.',
            'amount.max' => 'O valor não pode ser maior que R$ 999.999,99.',
            
            'received_date.required' => 'A data de recebimento é obrigatória.',
            'received_date.date' => 'A data de recebimento deve ser uma data válida.',
            'received_date.after_or_equal' => 'A data de recebimento não pode ser anterior a 2020.',
            'received_date.before_or_equal' => 'A data de recebimento não pode ser superior a um ano no futuro.',
            
            'category.required' => 'A categoria é obrigatória.',
            'category.in' => 'A categoria selecionada é inválida.',
            
            'type.required' => 'O tipo de receita é obrigatório.',
            'type.in' => 'O tipo de receita deve ser "fixa" ou "variável".',
            
            'status.required' => 'O status é obrigatório.',
            'status.in' => 'O status deve ser "recebida" ou "pendente".',
            
            'source.max' => 'A fonte não pode ter mais de 255 caracteres.',
            'notes.max' => 'As observações não podem ter mais de 1000 caracteres.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'description' => 'descrição',
            'amount' => 'valor',
            'received_date' => 'data de recebimento',
            'category' => 'categoria',
            'type' => 'tipo',
            'status' => 'status',
            'source' => 'fonte',
            'notes' => 'observações'
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Converter amount de formato brasileiro para decimal se necessário
        if ($this->has('amount') && is_string($this->amount)) {
            $amount = $this->amount;
            
            // Remove caracteres não numéricos exceto vírgula e ponto
            $amount = preg_replace('/[^\d,.]/', '', $amount);
            
            // Se tem vírgula, assume formato brasileiro (1.000,50)
            if (str_contains($amount, ',')) {
                $amount = str_replace(['.', ','], ['', '.'], $amount);
            }
            
            $this->merge([
                'amount' => (float) $amount
            ]);
        }
    }
}
