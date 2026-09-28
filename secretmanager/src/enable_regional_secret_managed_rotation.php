<?php
/*
 * Copyright 2026 Google LLC.
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
 * @see https://github.com/GoogleCloudPlatform/php-docs-samples/tree/main/secretmanager/README.md
 */

declare(strict_types=1);

namespace Google\Cloud\Samples\SecretManager;

// [START secretmanager_enable_regional_secret_managed_rotation]
use Google\Cloud\SecretManager\V1\EnableManagedRotationRequest;
use Google\Cloud\SecretManager\V1\EnableManagedRotationRequest\CloudSQLSingleUserCredentials;
use Google\Cloud\SecretManager\V1\Client\SecretManagerServiceClient;

/**
 * Enable managed rotation for a Cloud SQL DB credentials secret. This links
 * the secret to a Cloud SQL instance and database user, and can only be
 * called once per secret. It adds the secret's first version and sets the
 * matching password on the Cloud SQL user, taking the place of a manually
 * added secret version, which this secret type doesn't support. Afterwards,
 * use rotate_regional_secret.php to trigger further rotations.
 *
 * $instanceId is the bare Cloud SQL instance ID (e.g. "my-instance") -- not a
 * connection name. Neither the project nor the region should be included:
 * passing "PROJECT_ID:INSTANCE_ID" (as gcloud's own
 * `enable-managed-rotation --help` examples misleadingly show) or the full
 * "PROJECT_ID:LOCATION_ID:INSTANCE_ID" connection name both fail -- the
 * service already knows the project from the secret's own path, and prepends
 * it internally, so a qualified value ends up double-prefixed.
 *
 * @param string $projectId Your Google Cloud Project ID (e.g. 'my-project')
 * @param string $locationId Location of the secret (e.g. 'us-central1')
 * @param string $secretId  ID of the Cloud SQL DB credentials secret to enable rotation on
 * @param string $instanceId Bare ID of the Cloud SQL instance (no project or region prefix)
 * @param string $username Username of the Cloud SQL database user
 */
function enable_regional_secret_managed_rotation(string $projectId, string $locationId, string $secretId, string $instanceId, string $username): void
{
    // Specify regional endpoint.
    $options = ['apiEndpoint' => "secretmanager.$locationId.rep.googleapis.com"];

    // Create the Secret Manager client.
    $client = new SecretManagerServiceClient($options);

    // Build the resource name of the secret.
    $parent = $client->projectLocationSecretName($projectId, $locationId, $secretId);

    $credentials = new CloudSQLSingleUserCredentials([
        'instance_id' => $instanceId,
        'username' => $username,
        // Leaving password unset lets Secret Manager generate a secure
        // password itself.
    ]);

    $request = (new EnableManagedRotationRequest())
        ->setParent($parent)
        ->setCloudSqlSingleUserCredentials($credentials);

    // Enable managed rotation.
    $version = $client->enableManagedRotation($request);

    printf('Enabled managed rotation, created secret version: %s%s', $version->getName(), PHP_EOL);
}
// [END secretmanager_enable_regional_secret_managed_rotation]

// The following 2 lines are only needed to execute the samples on the CLI
require_once __DIR__ . '/../../testing/sample_helpers.php';
\Google\Cloud\Samples\execute_sample(__FILE__, __NAMESPACE__, $argv);
