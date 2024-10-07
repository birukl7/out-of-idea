<?php

use Livewire\Volt\Component;

new class extends Component {
    //
}; ?>

<div>
    <x-input-label>
        Actions to do
    </x-input-label>

    @foreach($aiResponses as $response)
        <div class="mt-5">
            <x-text-input type="checkbox" value="{{ $response }}" /> {{ $response }} <br>
        </div>
    @endforeach

    <x-primary-button wire:click="complete" class="mt-4">
        Finish
    </x-primary-button>
</div>

