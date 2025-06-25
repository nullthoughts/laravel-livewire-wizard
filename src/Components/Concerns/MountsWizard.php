<?php

namespace NullThoughts\LivewireWizard\Components\Concerns;

trait MountsWizard
{
    public function mountMountsWizard(?string $showStep = null)
    {
        $stepName = $showStep ?? $this->currentStepName ?? $this->stepNames()->first();

        // $this->model = $this->model();
        $this->showStep($stepName);
    }
}
