<?php

use Livewire\Volt\Component;

new class extends Component {
    public $width = 20;
    public $currentStep = 1; // Track the current step
    public $userInput = ''; // User input for step 1
    public $aiResponses = []; // Store AI responses for steps 2 and 3
    public $selectedResponses = []; // Store user selections from AI responses

    protected $client;

    public function mount() {
        $this->client = Gemini::client(env('GEMINI_KEY'));
    }

    public function submitStepOne() {
        // Reinitialize client if null
        if (is_null($this->client)) {
            $this->client = Gemini::client(env('GEMINI_KEY'));
        }


        // Create a prompt for the AI to provide actionable tasks related to the user input
        $prompt = "Based on the following user input, provide three simple and actionable questions that guide the user. 
        Each question should be followed by a list of possible checkbox options related to the question. Examples in the list should be realistic and limited to 5 options per question.
        Avoid unnecessary examples or explanations. Ensure the questions do not touch on inappropriate subjects like nudity, religion, race, etc. Only provide meaningful options.";
        
        


        $response = $this->client->geminiPro()->generateContent($prompt);
        // Call the AI with user input and get responses
        if (strpos($response->candidates[0]->content->parts[0]->text, 'This input doesn\'t seem actionable') !== false) {
            $this->aiResponses = ['This input doesn’t seem actionable. Could you provide more context?'];
            $this->width += 20;
            return; // Stop further processing
        }
    
        // Extract responses
        $this->aiResponses = explode("\n", $response->candidates[0]->content->parts[0]->text);
        $this->aiResponses = array_slice($this->aiResponses, 0, 5); // Limit to 5 responses
        \Log::info($this->aiResponses);

        $this->width += 20;
        $this->currentStep = 2; // Move to step 2
    }
    

    public function submitStepTwo() {
        // Process selections from step 2 and get refined responses
        // You might want to call the AI again here based on selections
        $this->currentStep = 3; // Move to step 3
    }

    public function completeStepThree() {
        // Finalize to-do list from step 3 and display in step 4
        $this->currentStep = 4; // Move to step 4
    }
}; ?>

<div>
    <div>
        <h2>Step {{ $currentStep }}</h2>
        <div class="h-3 relative max-w-xl rounded-full overflow-hidden">
            <div class="w-full h-full bg-gray-200 absolute"></div>
            <div class="h-full bg-green-500 transition-all duration-300 absolute" style="width: {{ $width }}%"></div>
        </div>
    </div>

    <!-- Step 1 -->
    @if($currentStep == 1)
        <div class="mt-5">
            <x-input-label>
                What is on your mind?
            </x-input-label>
            <x-text-input wire:model="userInput" />
            <x-primary-button wire:click="submitStepOne" class="mt-4">
                Submit
            </x-primary-button>
        </div>
    @endif

    @if(!empty($aiResponses) && isset($aiResponses[0]) && $aiResponses[0] == 'This input doesn’t seem actionable. Could you provide more context?')
        <div class="mt-5 text-red-600">
            {{ $aiResponses[0] }}
        </div>
    @endif


    <!-- Step 2 -->
    @if($currentStep == 2)
        <div class="mt-5">
            <x-input-label>
                Choose from the following choices:
            </x-input-label>
            @foreach($aiResponses as $index => $response)
                <div class="mt-4">
                    <p>{{ $response['question'] }}</p>
                    @foreach($response['options'] as $option)
                        <input type="checkbox" name="response_{{ $index }}[]" value="{{ $option }}" id="{{ $option }}_{{ $index }}">
                        <label for="{{ $option }}_{{ $index }}">{{ $option }}</label><br>
                    @endforeach
                </div>
            @endforeach
            <x-primary-button wire:click="submitStepTwo" class="mt-4">
                Next
            </x-primary-button>
        </div>
    @endif



    <!-- Step 3 -->
    @if($currentStep == 3)
        <div class="mt-5">
            <x-input-label>
                Refined choices from AI
            </x-input-label>
            @foreach($aiResponses as $response)
                <x-text-input type="checkbox" value="{{ $response->text }}" />
                {{ $response->text }} <br>
            @endforeach
            <x-primary-button wire:click="completeStepThree" class="mt-4">
                Next
            </x-primary-button>
        </div>
    @endif

    <!-- Step 4 -->
    @if($currentStep == 4)
        <div class="mt-5">
            <x-input-label>
                Actual things to do:
            </x-input-label>
            <!-- Present the final to-do list here -->
            <p>Your final tasks will be listed here based on AI suggestions.</p>
        </div>
    @endif
</div>


