<?php

return [

    /*
    |--------------------------------------------------------------------------
    | LinkedIn API Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for posting to LinkedIn company pages via the Marketing API.
    | Requires "Share on LinkedIn" product and w_organization_social scope.
    |
    */

    'client_id' => env('LINKEDIN_CLIENT_ID'),
    'client_secret' => env('LINKEDIN_CLIENT_SECRET'),
    'access_token' => env('LINKEDIN_ACCESS_TOKEN'),
    'redirect_uri' => env('LINKEDIN_REDIRECT_URI'),

    /*
    |--------------------------------------------------------------------------
    | Organization ID (Company Page)
    |--------------------------------------------------------------------------
    |
    | The numeric organization ID for your LinkedIn company page. Find it in
    | your company page URL (e.g. linkedin.com/company/111716554 → 111716554)
    | or via the organizationalEntityAcls API.
    |
    */

    'organization_id' => env('LINKEDIN_ORGANIZATION_ID', '111716554'),

    /*
    |--------------------------------------------------------------------------
    | API Version
    |--------------------------------------------------------------------------
    |
    | LinkedIn API version in YYYYMM format. See:
    | https://learn.microsoft.com/en-us/linkedin/marketing/versioning
    |
    */

    'api_version' => env('LINKEDIN_API_VERSION', '202508'),

];
