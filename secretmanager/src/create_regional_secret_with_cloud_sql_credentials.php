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

// [START secretmanager_create_regional_secret_with_cloud_sql_credentials]
use Google\Cloud\SecretManager\V1\CreateSecretRequest;
use Google\Cloud\SecretManager\V1\Secret;
use Google\Cloud\SecretManager\V1\Secret\SecretType;
use Google\Cloud\SecretManager\V1\Client\SecretManagerServiceClient;

/**
 * Create a new secret with the Cloud SQL DB credentials secret type. This
 * type is required to enable Secret Manager's automatic rotation of Cloud
 * SQL passwords. It can only be set when the secret is created, and the
 * secret's location must match the region of the target Cloud SQL instance.
 *
 * @param string $projectId Your Google Cloud Project ID (e.g. 'my-project')
 * @param string $locationId Location of the secret; must match the Cloud SQL
 *     instance's region (e.g. 'us-central1')
 * @param string $secretId  Your secret ID (e.g. 'my-secret')
 */
function create_regional_secret_with_cloud_sql_credentials(string $projectId, string $locationId, string $secretId): void
{
    // Specify regional endpoint.
    $options = ['apiEndpoint' => "secretmanager.$locationId.rep.googleapis.com"];

    // Create the Secret Manager client.
    $client = new SecretManagerServiceClient($options);

    // Build the resource name of the parent project.
    $parent = $client->locationName($projectId, $locationId);

    $secret = new Secret([
        'secret_type' => SecretType::CLOUD_SQL_DB_CREDENTIALS,
    ]);

    $request = CreateSecretRequest::build($parent, $secretId, $secret);

    // Create the secret.
    $newSecret = $client->createSecret($request);

    printf('Created secret: %s%s', $newSecret->getName(), PHP_EOL);

    // This built-in identity is what you grant Cloud SQL IAM permissions to,
    // so that Secret Manager can rotate the database password on its behalf.
    printf(
        'Grant this identity Cloud SQL IAM permissions to enable rotation: %s%s',
        $newSecret->getPolicyMember()->getIamPolicyUidPrincipal(),
        PHP_EOL
    );
}
// [END secretmanager_create_regional_secret_with_cloud_sql_credentials]

// The following 2 lines are only needed to execute the samples on the CLI
require_once __DIR__ . '/../../testing/sample_helpers.php';
\Google\Cloud\Samples\execute_sample(__FILE__, __NAMESPACE__, $argv);
