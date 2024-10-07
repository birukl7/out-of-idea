<?php

use Livewire\Volt\Component;
use App\Traits\GeminiClient;

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

    use GeminiClient;

    public function mount() {
        $this->client = Gemini::client(env('GEMINI_KEY'));
    }

    public function submitStepOne() {
        // Reinitialize client if null
        if (is_null($this->client)) {
            $this->client = Gemini::client(env('GEMINI_KEY'));
        }

    // Create a prompt for the AI to provide actionable tasks related to the user input
    $prompt = "Based on the following user input, provide three simple and actionable yes/no questions that guide the user. 
    The response should be realistic and contain exactly 3 options per question in this array format: ['<answer1>', '<answer2>', '<answer3>']. 
    Focus on 'do' questions instead of 'what' questions. Avoid adding numbers before the array strings, and do not include question marks at the end of each string. 
    Ensure every opening bracket '[' is closed properly in the response, and always return the answer in the array format without any extra explanations. 
    Feel free to use an informal tone in the response. Here is the user input: " . $this->userInput;


        $this->aiResponses = $this->requestFromGemini($prompt);

        if (empty($this->aiResponses) || $this->aiResponses[0] == 'Something is wrong. Please try again.' || $this->aiResponses[0] == 'There was an error processing your request. Please try again later.') {
            $this->stepOneError = true;
            $this->responseOneNull = true;
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
        
        $prompt .= " Based on the following user answers, provide five simple and actionable suggestions that guide the user. 
        The answer should be limited to five options in the format ['<answer1>', '<answer2>', '<answer3>', '<answer4>', '<answer5>']. 
        Avoid unnecessary explanations, and make sure there are no numbers in front of the array strings. 
        Avoid question marks at the end of the array strings. 
        Ensure that the array format is maintained and correctly closed. The tone should be a bit informal but stick to the format strictly.";
        

        $this->aiResponses = $this->requestFromGemini($prompt);

        if (empty($this->aiResponses) || $this->aiResponses[0] == 'Something is wrong. Please try again.' || $this->aiResponses[0] == 'There was an error processing your request. Please try again later.') {
            $this->stepTwoError = true;
            $this->responseTwoNull = true;
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


