<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\Repositories\TripRepository;
use App\Models\Trip;

class UpdateTripRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check() && $this->route('id') &&
               Trip::where('id', $this->route('id'))->where('user_id', Auth::id())->exists();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $tripId = $this->route('id');

        return [
            'destination' => 'required|string|max:255',
            'country_code' => 'required|string|size:2',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'type' => 'required|in:business,leisure,adventure,family,solo',
            'budget' => 'nullable|numeric|min:0|max:99999999.99',
            'travelers' => 'required|integer|min:1|max:50',
            'notes' => 'nullable|string|max:1000',
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
            'destination.required' => 'Please select a destination.',
            'country_code.required' => 'Country code is required.',
            'start_date.required' => 'Please select a start date.',
            'end_date.required' => 'Please select an end date.',
            'end_date.after_or_equal' => 'End date must be after or equal to start date.',
            'type.required' => 'Please select a trip type.',
            'type.in' => 'Invalid trip type selected.',
            'budget.numeric' => 'Budget must be a valid number.',
            'budget.min' => 'Budget cannot be negative.',
            'budget.max' => 'Budget cannot exceed 99,999,999.99 PKR.',
            'travelers.required' => 'Please specify the number of travelers.',
            'travelers.integer' => 'Number of travelers must be a whole number.',
            'travelers.min' => 'At least 1 traveler is required.',
            'travelers.max' => 'Maximum 50 travelers allowed.',
            'notes.max' => 'Notes cannot exceed 1000 characters.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Check for trip conflicts (excluding current trip)
            if ($this->has(['start_date', 'end_date'])) {
                $tripRepository = app(TripRepository::class);
                $conflicts = $tripRepository->findConflictingTrips(
                    Auth::id(),
                    $this->start_date,
                    $this->end_date,
                    $this->route('id') // Exclude current trip
                );

                if ($conflicts->count() > 0) {
                    $conflictList = $conflicts->map(function ($trip) {
                        return "{$trip->destination} ({$trip->start_date->format('M j')} - {$trip->end_date->format('M j, Y')})";
                    })->join(', ');

                    $validator->errors()->add('dates', "You have conflicting trips: {$conflictList}. Please adjust your dates.");
                }
            }
        });
    }
}
