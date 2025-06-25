<?php

namespace NullThoughts\LivewireWizard\Attributes;

use Attribute;
use Livewire\Features\SupportAttributes\Attribute as LivewireAttribute;
use ReflectionMethod;

#[Attribute(Attribute::TARGET_METHOD)]
class ModelProperty extends LivewireAttribute
{
    public function boot()
    {
        $reflectionMethod = new ReflectionMethod($this->component, $this->getName());
        $value = $reflectionMethod->invoke($this->component);

        data_set($this->component, $this->levelName, $value);
    }
}