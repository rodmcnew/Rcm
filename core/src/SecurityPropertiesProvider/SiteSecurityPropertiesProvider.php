<?php

namespace Rcm\SecurityPropertiesProvider;

use Rcm\Acl\SecurityPropertiesProviderInterface;
use Rcm\Acl2\SecurityPropertyConstants;

class SiteSecurityPropertiesProvider implements SecurityPropertiesProviderInterface
{
    public function findSecurityProperties($data): array
    {
        if (!array_key_exists('countryIso3', $data)) {
            throw new NotAllowedBySecurityPropGenerationFailure('countryIso3 not passed.');
        }

        return [
            'type' => SecurityPropertyConstants::TYPE_SITE,
            SecurityPropertyConstants::CONTENT_TYPE_KEY => SecurityPropertyConstants::CONTENT_TYPE_SITE,
            'country' => $data['countryIso3']
        ];
    }
}
