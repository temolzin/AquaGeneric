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
            // compute period from start_date (format YYYY-MM)
            try {
                $start = new \DateTime($this->input('start_date') . '-01');
                $periodMonth = (int) $start->format('n');
                $periodYear = (int) $start->format('Y');
                $ym = sprintf('%04d-%02d', $periodYear, $periodMonth);
            } catch (\Exception $e) {
                return; // let other validation handle invalid dates
            }

            // Only enforce the service-uniqueness rule when the input category itself is named exactly 'Servicio de Agua'
            $inputCategoryId = (int) $this->input('debt_category_id');
            $inputCategory = DebtCategory::find($inputCategoryId);
            if (!$inputCategory || $inputCategory->name !== 'Servicio de Agua') {
                return; // other categories are allowed regardless
            }


            // For service-category input, check existing service debts (including legacy rows without period_month)
            // Scope service categories to the water connection's locality to avoid cross-locality conflicts
            $waterConnection = \App\Models\WaterConnection::find($this->input('water_connection_id'));
            $localityId = $waterConnection->locality_id ?? null;

            if (is_null($localityId)) return;

            $serviceCategoryIds = DebtCategory::where('name', 'Servicio de Agua')
                ->where('locality_id', $localityId)
                ->pluck('id')
                ->toArray();

            if (empty($serviceCategoryIds)) return;

            $exists = Debt::where('water_connection_id', $this->input('water_connection_id'))
                ->where('locality_id', $localityId)
                ->whereIn('debt_category_id', $serviceCategoryIds)
                ->where(function ($q) use ($periodMonth, $periodYear, $ym) {
                    $q->where(function ($a) use ($periodMonth, $periodYear) {
                            $a->where('period_month', $periodMonth)
                              ->where('period_year', $periodYear);
                        })
                        ->orWhere(function ($b) use ($ym) {
                            $b->whereNull('period_month')
                              ->whereRaw("DATE_FORMAT(start_date, '%Y-%m') = ?", [$ym]);
                        });
                })
                ->exists();

            if ($exists) {
                $validator->errors()->add('debt_category_id', 'Ya existe una deuda de Servicio de Agua para este periodo (por toma).');
            }
        });
    }
}
