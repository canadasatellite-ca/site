<?php
namespace CanadaSatellite\DynamicsIntegration\Enums\Sims;

/**
 * Network status filter values
 *
 * Class FilterNetworkStatus
 * @package DynamicsIntegration\Enums\Sims
 */
class FilterNetworkStatus
{
    const RequestParam = 'filterNetworkStatus';

    const None = '0';
    const Active = '1';
    const Issued = '2';
    const Expired = '3';
    const Deactivated = '4';
}