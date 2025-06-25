<?php

namespace NullThoughts\LivewireWizard\Components;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\Mechanisms\ComponentRegistry;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionObject;
use NullThoughts\LivewireWizard\Attributes\ModelProperty;
use NullThoughts\LivewireWizard\Components\Concerns\MountsWizard;
use NullThoughts\LivewireWizard\Exceptions\InvalidStepComponent;
use NullThoughts\LivewireWizard\Exceptions\NoNextStep;
use NullThoughts\LivewireWizard\Exceptions\NoPreviousStep;
use NullThoughts\LivewireWizard\Exceptions\NoStepsReturned;
use NullThoughts\LivewireWizard\Exceptions\StepDoesNotExist;

abstract class WizardComponent extends Component
{
    use MountsWizard;

    public ?string $currentStepName = null;

    /** @return <int, class-string<StepComponent> */
    abstract public function steps(): array;

    public function stepNames(): Collection
    {
        $steps = collect($this->steps())
            ->each(function (string $stepClassName) {
                if (! is_a($stepClassName, StepComponent::class, true)) {
                    throw InvalidStepComponent::doesNotExtendStepComponent(static::class, $stepClassName);
                }
            })
            ->map(function (string $stepClassName) {
                $alias = app(ComponentRegistry::class)->getName($stepClassName);

                if (is_null($alias)) {
                    throw InvalidStepComponent::notRegisteredWithLivewire(static::class, $stepClassName);
                }

                return $alias;
            });

        if ($steps->isEmpty()) {
            throw NoStepsReturned::make(static::class);
        }

        return $steps;
    }

    #[On('previousStep')]
    public function previousStep()
    {
        $previousStep = collect($this->stepNames())
            ->before(fn (string $step) => $step === $this->currentStepName);

        if (! $previousStep) {
            throw NoPreviousStep::make(self::class, $this->currentStepName);
        }

        $this->showStep($previousStep);
    }

    #[On('nextStep')]
    public function nextStep()
    {
        $nextStep = collect($this->stepNames())
            ->after(fn (string $step) => $step === $this->currentStepName);

        if (! $nextStep) {
            throw NoNextStep::make(self::class, $this->currentStepName);
        }

        $this->showStep($nextStep);
    }

    #[On('showStep')]
    public function showStep($toStepName)
    {
        if (! $this->stepNames()->contains($toStepName)) {
            throw StepDoesNotExist::doesNotHaveState($toStepName);
        }
    
        $this->currentStepName = $toStepName;
    }

    public function render()
    {
        $class = new ReflectionClass($this);

        $models = collect($class->getMethods())->filter(function($method) {
            return $method->getAttributes(ModelProperty::class);
        })->mapWithKeys(function ($method) {
            $property = $method->name;

            return [$property => $this->{$property}];
        })->all();

        return view('livewire-wizard::wizard', compact('models'));
    }
}
