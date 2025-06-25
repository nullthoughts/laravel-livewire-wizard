<?php

namespace NullThoughts\LivewireWizard\Enums;

enum StepStatus: string
{
    case Previous = 'previous';
    case Current = 'current';
    case Next = 'next';
}
