<?php
/*
 * Copyright 2025 Google LLC.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/*
 * For instructions on how to run the full sample:
 *
 * @see https://github.com/GoogleCloudPlatform/php-docs-samples/tree/main/parametermanager/README.md
 */

declare(strict_types=1);

namespace Google\Cloud\Samples\ParameterManager;

// [START parametermanager_get_param_version]
// Import necessary classes for retrieve a parameter.
use Google\Cloud\ParameterManager\V1\Client\ParameterManagerClient;
use Google\Cloud\ParameterManager\V1\GetParameterVersionRequest;

/**
 * Retrieves a parameter version using the Parameter Manager SDK for GCP.
 *
 * @param string $projectId The Google Cloud Project ID (e.g. 'my-project')
 * @param string $parameterId The Parameter ID (e.g. 'my-param')
 * @param string $versionId The Version ID (e.g. 'my-param-version')
 */
function get_param_version(string $projectId, string $parameterId, string $versionId): void
{
    // Create a client for the Parameter Manager service.
    $client = new ParameterManagerClient();

    // Build the resource name of the parameter version.
    $parameterVersionName = $client->parameterVersionName($projectId, 'global', $parameterId, $versionId);

    // Prepare the request to get the parameter version.
    $request = (new GetParameterVersionRequest())
        ->setName($parameterVersionName);

    // Retrieve the parameter version using the client.
    $parameterVersion = $client->getParameterVersion($request);

    // Print the retrieved parameter version details.
    printf('Found parameter version %s with state %s' . PHP_EOL, $parameterVersion->getName(), $parameterVersion->getDisabled() ? 'disabled' : 'enabled');
    if (!($parameterVersion->getDisabled())) {
        printf('Payload: %s' . PHP_EOL, $parameterVersion->getPayload()->getData());
    }
}
// [END parametermanager_get_param_version]

// The following 2 lines are only needed to execute the samples on the CLI
require_once __DIR__ . '/../../testing/sample_helpers.php';
\Google\Cloud\Samples\execute_sample(__FILE__, __NAMESPACE__, $argv);
