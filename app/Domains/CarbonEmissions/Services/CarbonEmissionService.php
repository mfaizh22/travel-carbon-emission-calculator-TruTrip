<?php

namespace App\Domains\CarbonEmissions\Services;

use App\Domains\CarbonEmissions\Services\CarbonEmissionServiceInterface;
use App\Externals\CarbonEmissions\Services\CarbonEmissionProviderServiceInterface;

class CarbonEmissionService implements CarbonEmissionServiceInterface
{
    protected $carbonEmissionProvider;

    public function __construct(CarbonEmissionProviderServiceInterface $carbonEmissionProvider)
    {
        $this->carbonEmissionProvider = $carbonEmissionProvider;
    }

    public function getCarbonEmissionCalculation($data)
    {
        // Process the data if needed before passing to the provider
        // For example, format validation, data enrichment, etc.

        // Delegate the actual calculation to the provider (Squake in this case)
        $result = $this->carbonEmissionProvider->getCarbonEmissionCalculation($data);

        // Process the result if needed before returning
        // For example, data formatting, additional calculations, etc.

        return $result;
    }
}
