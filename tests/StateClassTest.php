<?php

use Livewire\Livewire;
use NullThoughts\LivewireWizard\Tests\TestSupport\Components\Steps\FirstStepComponent;
use NullThoughts\LivewireWizard\Tests\TestSupport\Components\Steps\SecondStepComponent;
use NullThoughts\LivewireWizard\Tests\TestSupport\Components\WizardWithCustomStateObject;
use NullThoughts\LivewireWizard\Tests\TestSupport\Components\WizardWithInitialState;
use NullThoughts\LivewireWizard\Tests\TestSupport\Components\WizardWithInvalidCustomStateObject;
use NullThoughts\LivewireWizard\Tests\TestSupport\State\CustomState;

it('can mount a wizard with a custom state class', function () {
    Livewire::test(WizardWithCustomStateObject::class)->assertSuccessful();
});

it('will throw an exception when a wizard uses an invalid custom state class', function () {
    Livewire::test(WizardWithInvalidCustomStateObject::class);
})->throws('invalid state class');

it('can use a custom state class', function () {
    $wizard = Livewire::test(WizardWithCustomStateObject::class, [
        'stateClassName' => CustomState::class,
    ]);

    $wizard
        ->assertSuccessful()
        ->assertSeeText('foo method: bar')
        ->assertSeeText('state get: stepPropertyValue');

    $currentStepState = $wizard->jsonContent('currentStepState');
    expect($currentStepState['stepPropertyName'])->toBe('stepPropertyValue');

    $allStepState = $wizard->jsonContent('allStepState');
    expect($allStepState)->toHaveKey('custom-state-step');
});

it('can load state from different steps', function () {
    $wizard = Livewire::test(WizardWithInitialState::class, ['order' => 1029])
        ->assertSuccessful();

    Livewire::test(FirstStepComponent::class, $wizard->getStepState())
        ->call('nextStep')
        ->assertDispatched('nextStep')
        ->emitEvents()->in($wizard);

    Livewire::test(SecondStepComponent::class, $wizard->getStepState())
        ->tap(function (\Livewire\Features\SupportTesting\Testable $testableLivewire) {
            $livewireComponent = $testableLivewire->instance();
            $state = $livewireComponent->state()->forStep('first-step');
            expect($state['order'])->toBe(1029);
        });
});
