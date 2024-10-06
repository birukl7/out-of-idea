<?php

use Livewire\Volt\Component;

new class extends Component {
    public $width = 25;
    public $currentStep = 1; // Track the current step
    public $userInput = ''; // User input for step 1
    public $aiResponses = []; // Store AI responses for steps 2 and 3
    public $selectedResponses = []; // Store user selections from AI responses
    public $choice1 = 'yes';
    public $choice2 = 'yes';
    public $choice3 = 'yes';
    public $stepOneError=false;
    public $stepTwoError=false;
    public $responseOneNull=false;
    public $responseTwoNull=false;

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
        Avoid unnecessary explanations. Avoids numbers infront of the array strings. Avoid question marks at the end of each array strings. Ensure you closed the bracket '[' you open.  Ensure you always use the array string in your response. Feel free to be informal.  Here is the user input: ".$this->userInput;
        
        
        try {
            $response = $this->client->geminiPro()->generateContent($prompt);
        
            // Check if the response contains 'candidates' and if 'candidates' is not null
            if (isset($response->candidates[0]) && $response->candidates[0] !== null) {
                
                // Get the AI response content
                $responseText = $response->candidates[0]->content->parts[0]->text ?? null;
                
                if ($responseText !== null) {
                    // Convert single quotes to double quotes to ensure valid JSON
                    $validJsonString = str_replace("'", '"', $responseText);
        
                    // Attempt to decode the JSON string
                    $this->aiResponses = json_decode($validJsonString, true);
        
                } else {
                    // Handle the case where response text is null
                    $this->aiResponses = ['Something is wrong. Please try again.'];
                    $this->responseOneNull = true;
                }
            } else {
                // Handle cases where 'candidates' are not present or are null
                $this->aiResponses = ['No valid response. Please try again.'];
            }
        
        } catch (\Exception $e) {
            // Log the exception and set a user-friendly error message
            \Log::error('Gemini API error: ' . $e->getMessage());
            $this->aiResponses = ['There was an error processing your request. Please try again later.'];
            $this->stepOneError = true;
        }

        $this->width = 50;
        $this->currentStep = 2; // Move to step 2
    }
    

    public function submitStepTwo() {
        // Process selections from step 2 and get refined responses
        // You might want to call the AI again here based on selections
        if (is_null($this->client)) {
            $this->client = Gemini::client(env('GEMINI_KEY'));
        }
        $prompt = '';

        if ($this->choice1 == 'yes'){
            $prompt = $prompt. ' A user says yes to this question: '.$this->aiResponses[0];
        } elseif($this->choice1 == 'no'){
            $prompt = $prompt. ' A user says no to this question: '.$this->aiResponses[0];
        }

        if ($this->choice2 == 'yes'){
            $prompt = $prompt. ' A user says yes to this question: '.$this->aiResponses[1];
        } elseif($this->choice2 == 'no'){
            $prompt = $prompt. ' A user says no to this question: '.$this->aiResponses[1];
        }

        if ($this->choice3 == 'yes'){
            $prompt = $prompt. ' A user says yes to this question: '.$this->aiResponses[2];
        } elseif($this->choice3 == 'no'){
            $prompt = $prompt. ' A user says no to this question: '.$this->aiResponses[2];
        }

        // dd($this->choice1, $this->choice2, $this->choice3);

        $prompt = $prompt. " Based on the following user answers, provide five simple to do things according to the user's input that guide the user. 
        The answer should be realistic and limited to five options per question and in array format like this ['<your answer>','<your answer>','<your answer>'].
        Avoid unnecessary explanations. Avoids numbers infront of the array strings. Avoid question marks at the end of each array strings. Ensure you closed the bracket '[' you open.  Ensure you always use the array string in your response. Answer a bit informal reponses but keep the format" ;

        try {
            $response = $this->client->geminiPro()->generateContent($prompt);
        
            // Check if the response contains 'candidates' and if 'candidates' is not null
            if (isset($response->candidates[0]) && $response->candidates[0] !== null) {
                
                // Get the AI response content
                $responseText = $response->candidates[0]->content->parts[0]->text ?? null;
                
                if ($responseText !== null) {
                    // Convert single quotes to double quotes to ensure valid JSON
                    $validJsonString = str_replace("'", '"', $responseText);
        
                    // Attempt to decode the JSON string
                    $this->aiResponses = json_decode($validJsonString, true);
        
                } else {
                    // Handle the case where response text is null
                    $this->aiResponses = ['Something is wrong. Please try again.'];
                    $this->responseTwoNull = true;
                }
            } else {
                // Handle cases where 'candidates' are not present or are null
                $this->aiResponses = ['No valid response. Please try again.'];
            }
        
        } catch (\Exception $e) {
            // Log the exception and set a user-friendly error message
            \Log::error('Gemini API error: ' . $e->getMessage());
            $this->aiResponses = ['There was an error processing your request. Please try again later.'];
            $this->stepTwoError = true;
        }

        $this->width = 75;
        $this->currentStep = 3; // Move to step 2
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
                    <input type="radio"  wire:model="choice{{$index+1}}" name="reponse{{$index}}" value="yes" /> <label>Yes</label><br>
                    <input type="radio" wire:model="choice{{$index+1}}" value="no" name="reponse{{$index}}" /> <label>No</label><br>
                </div>
            @endforeach
            <!-- <x-primary-button wire:click="submitStepTwo" class="mt-4">
                Previous
            </x-primary-button> -->
            <x-primary-button wire:click="submitStepTwo" class="mt-4">
                Next
            </x-primary-button>
        </div>
    @elseif(($currentStep == 2 && $stepOneError == True) || ($currentStep ==2 && $responseOneNUll) )
        <div class="mt-5">
            <div class="mt-4">
                @foreach ($aiResponses as $response )
                    <p>{{$response}}</p>
                @endforeach
               
            </div>
        </div>
    @endif

    <!-- Step 3 -->
    @if($currentStep == 3 && $stepTwoError == false)
        <div class="mt-5">
            <x-input-label>
                Actions to do
            </x-input-label>
            @foreach($aiResponses as $index => $response)
                <div class="mt-5">
                    <x-text-input type="checkbox" value="{{$response}}" />
                    {{$response}} <br>
                </div>
            @endforeach
            <x-primary-button wire:click="completeStepThree" class="mt-4">
                Finish
            </x-primary-button>
        </div>
    @elseif(($currentStep == 3 && $stepTwoError == True) || ($currentStep == 3 && $responseTwoNUll) )
        <div class="mt-5">
            <div class="mt-4">
                @foreach ($aiResponses as $response )
                    <p>{{$response}}</p>
                @endforeach
               
            </div>
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


