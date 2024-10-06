<?php

use Livewire\Volt\Component;

new class extends Component {
    public $width = 20;
    public $currentStep = 1; // Track the current step
    public $userInput = ''; // User input for step 1
    public $aiResponses = []; // Store AI responses for steps 2 and 3
    public $selectedResponses = []; // Store user selections from AI responses
    public $choice1;
    public $choice2;
    public $choice3;
    public $stepOneError=false;

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
        The answer should be realistic and limited to 3 options per question and in array format like this ['<your answer>','<your answer>','<your answer>']. Make the questions suitable for yes or no. Avoid what question instead prefer do questions.
        Avoid unnecessary explanations. Avoids numbers infront of the array strings. Avoid question marks at the end of each array strings. Ensure you closed the bracket '[' you open.  Ensure you always use the array string in your response.  Here is the user input: ".$this->userInput;
        
        
        try {
            $response = $this->client->geminiPro()->generateContent($prompt);
    
            // Check if the response contains 'candidates'
            if (isset($response->candidates[0])) {
                $validJsonString = str_replace("'", '"', $response->candidates[0]->content->parts[0]->text);
    
                // Attempt to decode the JSON string
                $this->aiResponses = json_decode($validJsonString, true);
    
                // Check for inappropriate content response
                if (isset($this->aiResponses[0]) && $this->aiResponses[0] === 'inappropriate') {
                    $this->aiResponses = ['This topic is inappropriate. Please provide a different input.'];
                }
            } else {
                // Handle cases where candidates are not present
                $this->aiResponses = ['No valid response from AI. Please try again.'];
            }
        } catch (\Exception $e) {
            // Log the exception and set a user-friendly error message
            \Log::error('Gemini API error: ' . $e->getMessage());
            $this->aiResponses = ['There was an error processing your request. Please try again later.'];
            $this->stepOneError = true;
        }

        $this->width += 20;
        $this->currentStep = 2; // Move to step 2
    }
    

    public function submitStepTwo() {
        // Process selections from step 2 and get refined responses
        // You might want to call the AI again here based on selections
        if (is_null($this->client)) {
            $this->client = Gemini::client(env('GEMINI_KEY'));
        }
        $propmt = '';

        if ($this->choice1){
            $propmt += ' A user says yes to this question: '.$this->aiResponses[0];
        }

        if ($this->choice2){
            $propmt += ' A user says yes to this question: '.$this->aiResponses[0];
        }

        if ($this->choice3){
            $propmt += ' A user says yes to this question: '.$this->aiResponses[0];
        }

        dd($this->choice1, $this->choice2, $this->choice3);

        $propmt += 'Based on these user answers';


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
            <x-text-input wire:model="userInput"  />
            <x-primary-button wire:click="submitStepOne" class="mt-4">
                Next
            </x-primary-button>
        </div>
    @endif

    <!-- Step 2 -->
    @if($currentStep == 2 && $stepOneError == false)
        <div class="mt-5">
            <x-input-label>
                Choose from the following choices:
            </x-input-label>
            @foreach($aiResponses as $index => $response)
                <div class="mt-4">
                    <p>{{ $response }}</p>
                    <input type="radio"  wire:model="choice{{$index+1}}" value="yes" /> <label>Yes</label><br>
                    <input type="radio" wire:model="choice{{$index+1}}" value="no" /> <label>No</label><br>
                </div>
            @endforeach
            <!-- <x-primary-button wire:click="submitStepTwo" class="mt-4">
                Previous
            </x-primary-button> -->
            <x-primary-button wire:click="submitStepTwo" class="mt-4">
                Next
            </x-primary-button>
        </div>
    @else
        <div class="mt-5">
            <div class="mt-4">
                @foreach ($aiResponses as $response )
                    <p>{{$response}}</p>
                @endforeach
               
            </div>
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


