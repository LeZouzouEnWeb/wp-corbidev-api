<?php
// Example Pages.php returning a manifest array when included.
return [
    'slug' => 'cv',
    'display_name' => 'CV',
    'modules' => ['identity', 'experience', 'education'],
    'admin_class' => 'Admin\\Pages\\CvPage',
    'api_contract' => 'openapi.json',
];
