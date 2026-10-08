<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Thrown by RashiAvarewaseUserProvisioner when an SSO identity can't be
 * mapped to a rashi user. Its message is safe to return to the client,
 * unlike other RuntimeExceptions (e.g. QueryException) that may leak SQL.
 */
class SsoProvisioningException extends RuntimeException {}
