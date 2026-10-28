<?php

return [
    'enabled' => (bool) env('LDAP_ENABLED', env('LDAP_HOST')),
    'host' => env('LDAP_HOST'),
    'port' => (int) env('LDAP_PORT', env('LDAP_SSL', false) ? 636 : 389),
    'base_dn' => env('LDAP_BASE_DN'),
    'bind_username' => env('LDAP_USERNAME'),
    'bind_password' => env('LDAP_PASSWORD'),
    'ssl' => (bool) env('LDAP_SSL', false),
    'tls' => (bool) env('LDAP_TLS', false),
    'allow_insecure' => (bool) env('LDAP_ALLOW_INSECURE', false),
    'timeout' => (int) env('LDAP_TIMEOUT', 5),
    'login_attributes' => array_filter(array_map(
        'trim',
        explode(',', env('LDAP_LOGIN_ATTRIBUTES', 'sAMAccountName,userPrincipalName,mail'))
    )),
    'display_name_attribute' => env('LDAP_DISPLAY_NAME_ATTRIBUTE', 'displayName'),
    'email_attribute' => env('LDAP_EMAIL_ATTRIBUTE', 'mail'),
    'principal_name_attribute' => env('LDAP_PRINCIPAL_NAME_ATTRIBUTE', 'userPrincipalName'),
];
