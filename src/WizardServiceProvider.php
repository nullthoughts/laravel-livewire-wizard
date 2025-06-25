<?php

namespace NullThoughts\LivewireWizard;

use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;
use NullThoughts\LaravelPackageTools\Package;
use NullThoughts\LaravelPackageTools\PackageServiceProvider;
use NullThoughts\LivewireWizard\Support\EventEmitter;
use NullThoughts\LivewireWizard\Support\StepSynth;

class WizardServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-livewire-wizard')
            ->hasViews();
    }

    public function bootingPackage()
    {
        Livewire::propertySynthesizer(StepSynth::class);
        $this->registerLivewireTestMacros();
    }

    public function registerLivewireTestMacros()
    {
        Testable::macro('emitEvents', function () {
            return new EventEmitter($this);
        });

        Testable::macro('getStepState', function (?string $step = null) {
            return $this->instance()->getCurrentStepState($step);
        });
    }
}
