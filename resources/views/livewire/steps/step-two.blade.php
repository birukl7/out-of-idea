<?php

use Livewire\Volt\Component;
use App\Traits\GeminiClient;

new class extends Component {
    use GeminiClient;

    public $aiResponses = [];
    public $choice1 = 'yes';
    public $choice2 = 'yes';
    public $choice3 = 'yes';
    public $stepTwoError = false;
    public $responseTwoNull = false;

    public function submit()
    {
        $prompt = '';
        if ($this->choice1 == 'yes') {
            $prompt .= ' A user says yes to this question: ' . $this->aiResponses[0];
        } elseif ($this->choice1 == 'no') {
            $prompt .= ' A user says no to this question: ' . $this->aiResponses[0];
        }

        if ($this->choice2 == 'yes') {
            $prompt .= ' A user says yes to this question: ' . $this->aiResponses[1];
        } elseif ($this->choice2 == 'no') {
            $prompt .= ' A user says no to this question: ' . $this->aiResponses[1];
        }

        if ($this->choice3 == 'yes') {
            $prompt .= ' A user says yes to this question: ' . $this->aiResponses[2];
        } elseif ($this->choice3 == 'no') {
            $prompt .= ' A user says no to this question: ' . $this->aiResponses[2];
        }

        $prompt .= " Based on the user's answers, provide five simple to-do things in array format ['<your answer>','<your answer>','<your answer>'].";

        // Use the trait's method to request from Gemini
        $this->aiResponses = $this->requestFromGemini($prompt);

        if (empty($this->aiResponses) || $this->aiResponses[0] == 'Something is wrong. Please try again.') {
            $this->stepTwoError = true;
        } else {
            $this->emit('goToStep', 3);
            $this->emit('updateWidth', 75);
        }
    }
}; ?>

<div>
    <x-input-label>
        Choose from the following choices:
    </x-input-label>

    @foreach($aiResponses as $index => $response)
        <div class="mt-4">
            <p>{{ $response }}</p>
            <input type="radio" wire:model="choice{{ $index+1 }}" value="yes" /> <label>Yes</label><br>
            <input type="radio" wire:model="choice{{ $index+1 }}" value="no" /> <label>No</label><br>
        </div>
    @endforeach

    <x-primary-button wire:click="submit" class="mt-4">
        Next
    </x-primary-button>
</div>

