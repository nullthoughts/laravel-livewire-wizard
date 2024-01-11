<?php

namespace Spatie\LivewireWizard\Components;

use Livewire\Component;
use Livewire\Mechanisms\ComponentRegistry;
use Spatie\LivewireWizard\Components\Concerns\StepAware;

abstract class StepComponent extends Component
{
    use StepAware;

    public ?string $wizardClassName = null;
    public array $allStepNames = [];

    public function mount($models) {
        foreach ($models as $property => $model) {
            $this->{$property} = $model;
        }
    }

    public function previousStep()
    {
        $this->dispatch('previousStep')->to($this->wizardClassName);
    }

    public function nextStep()
    {
        $this->dispatch('nextStep')->to($this->wizardClassName);
    }

    public function showStep(string $stepName)
    {
        $this->dispatch('showStep', toStepName: $stepName)->to($this->wizardClassName);
    }

    public function hasPreviousStep()
    {
        return ! empty($this->allStepNames) && $this->allStepNames[0] !== app(ComponentRegistry::class)->getName(static::class);
    }

    public function hasNextStep()
    {
        return end($this->allStepNames) !== app(ComponentRegistry::class)->getName(static::class);
    }

    public function stepInfo(): array
    {
        return [];
    }
}
