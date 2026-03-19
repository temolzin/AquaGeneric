<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\DebtCategory;
use App\Models\Debt;

class StoreDebtRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check();
    }

    public function rules()
    {
        return [
            'water_connection_id' => ['required', 'exists:water_connections,id'],
            // inputs are type="month" (YYYY-MM); accept YYYY-MM format
            'start_date' => ['required', 'date_format:Y-m'],
            'end_date' => ['required', 'date_format:Y-m'],
            'amount' => ['required', 'numeric'],
            'debt_category_id' => ['required', 'exists:debt_categories,id'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $serviceId = DebtCategory::where('name', 'Servicio de Agua')->value('id');
            if (!$serviceId) return;

            $inputCategory = (int) $this->input('debt_category_id');
            if ($inputCategory !== (int) $serviceId) return;

            // compute period from start_date (format YYYY-MM)
            try {
                $start = new \DateTime($this->input('start_date') . '-01');
                $periodMonth = (int) $start->format('n');
                $periodYear = (int) $start->format('Y');
            } catch (\Exception $e) {
                return; // let other validation handle invalid dates
            }

            $exists = Debt::where('water_connection_id', $this->input('water_connection_id'))
                ->where('debt_category_id', $serviceId)
                ->where('period_month', $periodMonth)
                ->where('period_year', $periodYear)
                ->exists();

            if ($exists) {
                $validator->errors()->add('debt_category_id', 'Ya existe una deuda de Servicio de Agua para este periodo (por toma).');
            }
        });
    }
}
