<?php

namespace App\Livewire;

use App\Models\QuoteRequest;
use Livewire\Attributes\On;
use Livewire\Component;

class QuoteRequestShow extends Component
{
    public QuoteRequest $quoteRequest;

    public function mount(QuoteRequest $quoteRequest)
    {
        $this->quoteRequest = $quoteRequest;
    }

    #[On('echo:quote-request.{quoteRequest.id},QuoteResponseReceived')]
    public function refreshResponses()
    {
        $this->quoteRequest->refresh();
        $this->quoteRequest->load('responses.supplier');
    }

    public function render()
    {
        return view('livewire.quote-request-show', [
            'responses' => $this->quoteRequest->responses()->with('supplier')->latest()->get(),
            'bestResponse' => $this->quoteRequest->bestResponse(),
        ]);
    }
}
