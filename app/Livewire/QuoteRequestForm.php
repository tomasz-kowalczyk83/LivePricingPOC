<?php

namespace App\Livewire;

use App\Jobs\ProcessQuoteRequest;
use App\Models\QuoteRequest;
use Livewire\Component;

class QuoteRequestForm extends Component
{
    public $buyer_name = '';
    public $buyer_email = '';
    public $part_description = '';
    public $vehicle_year = '';
    public $vehicle_make = '';
    public $vehicle_model = '';

    protected $rules = [
        'buyer_name' => 'required|string|max:255',
        'buyer_email' => 'required|email|max:255',
        'part_description' => 'required|string',
        'vehicle_year' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
        'vehicle_make' => 'nullable|string|max:255',
        'vehicle_model' => 'nullable|string|max:255',
    ];

    public function submit()
    {
        $this->validate();

        $vehicleInfo = null;
        if ($this->vehicle_year || $this->vehicle_make || $this->vehicle_model) {
            $vehicleInfo = array_filter([
                'year' => $this->vehicle_year,
                'make' => $this->vehicle_make,
                'model' => $this->vehicle_model,
            ]);
        }

        $quoteRequest = QuoteRequest::create([
            'buyer_name' => $this->buyer_name,
            'buyer_email' => $this->buyer_email,
            'part_description' => $this->part_description,
            'vehicle_info' => $vehicleInfo,
        ]);

        ProcessQuoteRequest::dispatch($quoteRequest);

        return redirect()->route('quote-request.show', $quoteRequest);
    }

    public function render()
    {
        return view('livewire.quote-request-form');
    }
}
