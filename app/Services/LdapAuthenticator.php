<?php

namespace App\Services;

use App\Exceptions\LdapUnavailableException;

class LdapAuthenticator
{
    /**
     * Return directory profile data for a valid user, or null for invalid credentials.
     *
     * @return array{name: string, email: string, username: string}|null
     */
    public function authenticate(string $username, string $password): ?array
    {
        if (! extension_loaded('ldap')) {
            throw new LdapUnavailableException('The PHP LDAP extension is not installed.');
        }

        $config = config('ldap');
        if (empty($config['host']) || empty($config['base_dn'])) {
            throw new LdapUnavailableException('LDAP host and base DN must be configured.');
        }

        $host = $config['host'];
        if (! preg_match('/^[a-z][a-z0-9+.-]*:\/\//i', $host)) {
            $host = ($config['ssl'] ? 'ldaps://' : 'ldap://').$host.':'.$config['port'];
        } elseif (! parse_url($host, PHP_URL_PORT)) {
            $scheme = parse_url($host, PHP_URL_SCHEME);
            $hostname = parse_url($host, PHP_URL_HOST);
            $path = parse_url($host, PHP_URL_PATH) ?: '';
            $host = "{$scheme}://{$hostname}:{$config['port']}{$path}";
        }

        $connection = ldap_connect($host);
        if ($connection === false) {
            throw new LdapUnavailableException('Could not connect to the LDAP server.');
        }

        try {
            $this->configureConnection($connection, $config);
            $this->bindServiceAccount($connection, $config);

            $normalizedUsername = $this->normalizeUsername($username);
            $filter = $this->buildUserFilter($normalizedUsername, $config['login_attributes']);
            $search = ldap_search($connection, $config['base_dn'], $filter);

            if ($search === false) {
                throw new LdapUnavailableException('LDAP user search failed.', ldap_errno($connection));
            }

            $entries = ldap_get_entries($connection, $search);
            if ($entries === false) {
                throw new LdapUnavailableException('Could not read LDAP search results.', ldap_errno($connection));
            }

            if ($entries['count'] !== 1) {
                return null;
            }

            $resultEntry = ldap_first_entry($connection, $search);
            if ($resultEntry === false) {
                throw new LdapUnavailableException('Could not read the LDAP user entry.', ldap_errno($connection));
            }

            $entry = $entries[0];
            $userDn = ldap_get_dn($connection, $resultEntry);
            if ($userDn === false) {
                throw new LdapUnavailableException('Could not resolve the LDAP user DN.', ldap_errno($connection));
            }

            $profile = $this->readProfile($entry, $normalizedUsername, $config);

            if (! ldap_bind($connection, $userDn, $password)) {
                return null;
            }

            return $profile;
        } finally {
            ldap_unbind($connection);
        }
    }

    private function configureConnection($connection, array $config): void
    {
        if (
            ! ldap_set_option($connection, LDAP_OPT_PROTOCOL_VERSION, 3)
            || ! ldap_set_option($connection, LDAP_OPT_REFERRALS, 0)
        ) {
            throw new LdapUnavailableException('Could not configure the LDAP connection.', ldap_errno($connection));
        }

        if (defined('LDAP_OPT_NETWORK_TIMEOUT')) {
            if (! ldap_set_option($connection, LDAP_OPT_NETWORK_TIMEOUT, $config['timeout'])) {
                throw new LdapUnavailableException('Could not set the LDAP network timeout.', ldap_errno($connection));
            }
        }

        if ($config['tls'] && ! $config['ssl'] && ! ldap_start_tls($connection)) {
            throw new LdapUnavailableException('Could not start TLS for the LDAP connection.', ldap_errno($connection));
        }
    }

    private function bindServiceAccount($connection, array $config): void
    {
        $bindUsername = $config['bind_username'];
        $bindPassword = $config['bind_password'];
        $bound = $bindUsername
            ? ldap_bind($connection, $bindUsername, $bindPassword ?? '')
            : ldap_bind($connection);

        if (! $bound) {
            throw new LdapUnavailableException('LDAP service account bind failed.', ldap_errno($connection));
        }
    }

    private function buildUserFilter(string $username, array $attributes): string
    {
        if ($attributes === []) {
            throw new LdapUnavailableException('At least one LDAP login attribute must be configured.');
        }

        foreach ($attributes as $attribute) {
            if (! preg_match('/^[a-zA-Z][a-zA-Z0-9-]*$/D', $attribute)) {
                throw new LdapUnavailableException('LDAP login attribute configuration is invalid.');
            }
        }

        $escapedUsername = ldap_escape($username, '', LDAP_ESCAPE_FILTER);
        $filters = array_map(
            fn (string $attribute): string => "({$attribute}={$escapedUsername})",
            $attributes,
        );

        return count($filters) === 1 ? $filters[0] : '(|'.implode('', $filters).')';
    }

    private function readProfile(array $entry, string $username, array $config): array
    {
        $entry = array_change_key_case($entry, CASE_LOWER);
        $name = $this->attributeValue($entry, $config['display_name_attribute'])
            ?? $this->attributeValue($entry, 'cn')
            ?? $username;
        $email = $this->attributeValue($entry, $config['email_attribute'])
            ?? $this->attributeValue($entry, $config['principal_name_attribute'])
            ?? hash('sha256', $username).'@ldap.invalid';

        return [
            'name' => $name,
            'email' => mb_strtolower($email),
            'username' => $username,
        ];
    }

    private function attributeValue(array $entry, string $attribute): ?string
    {
        $attribute = strtolower($attribute);
        if (! isset($entry[$attribute][0]) || ! is_string($entry[$attribute][0])) {
            return null;
        }

        return trim($entry[$attribute][0]) ?: null;
    }

    private function normalizeUsername(string $username): string
    {
        $username = trim($username);
        if (str_contains($username, '\\')) {
            $username = substr($username, strrpos($username, '\\') + 1);
        }

        return mb_strtolower($username);
    }
}
