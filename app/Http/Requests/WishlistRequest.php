<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WishlistRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'target_amount' => 'required|numeric|min:0.01',
            'current_amount' => 'nullable|numeric|min:0',
            'priority' => 'required|in:baixa,media,alta',
            'target_date' => 'nullable|date|after:today',
            'status' => 'nullable|in:em_progresso,concluida,cancelada',
            'notes' => 'nullable|string|max:5000',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'name.required' => 'O nome do objetivo é obrigatório.',
            'name.max' => 'O nome deve ter no máximo 255 caracteres.',
            'target_amount.required' => 'O valor alvo é obrigatório.',
            'target_amount.numeric' => 'O valor alvo deve ser um número válido.',
            'target_amount.min' => 'O valor alvo deve ser maior que zero.',
            'current_amount.numeric' => 'O valor atual deve ser um número válido.',
            'current_amount.min' => 'O valor atual não pode ser negativo.',
            'priority.required' => 'A prioridade é obrigatória.',
            'priority.in' => 'Prioridade inválida. Escolha: baixa, média ou alta.',
            'target_date.date' => 'A data alvo deve ser uma data válida.',
            'target_date.after' => 'A data alvo deve ser uma data futura.',
            'status.in' => 'Status inválido.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        // Converter valores monetários do formato brasileiro para float
        if ($this->has('target_amount')) {
            $this->merge([
                'target_amount' => $this->convertMoneyToFloat($this->target_amount)
            ]);
        }

        if ($this->has('current_amount')) {
            $this->merge([
                'current_amount' => $this->convertMoneyToFloat($this->current_amount)
            ]);
        }
    }

    /**
     * Converter valor monetário para float
     *
     * @param string $value
     * @return float
     */
    private function convertMoneyToFloat($value)
    {
        if (empty($value)) {
            return 0;
        }

        // Remove R$, espaços e pontos (separador de milhar)
        $value = str_replace(['R$', ' ', '.'], '', $value);
        
        // Substitui vírgula por ponto (separador decimal)
        $value = str_replace(',', '.', $value);

        return (float) $value;
    }
}

