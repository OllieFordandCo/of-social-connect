<?php

namespace OAuth\Common\Storage;

use OAuth\Common\Token\TokenInterface;
use OAuth\Common\Storage\Exception\TokenNotFoundException;
use OAuth\Common\Storage\Exception\AuthorizationStateNotFoundException;

/**
 * Stores a token in a PHP session.
 */
class WPDatabase implements TokenStorageInterface
{
    /**
     * @var string
     */
    protected $storageVariableName;

    /**
     * @var string
     */
    protected $stateVariableName;

    /**
     * @param bool $startSession Whether or not to start the session upon construction.
     * @param string $storageVariableName the variable name to use within the _SESSION superglobal
     * @param string $stateVariableName
     */
    public function __construct(
        $startSession = false,
        $storageVariableName = 'of_connect_oauth_token',
        $stateVariableName = 'of_connect_oauth_state'
    ) {

        $this->storageVariableName = $storageVariableName;
        $this->stateVariableName = $stateVariableName;
        $storage_option = get_option($this->storageVariableName);
		$state_option = get_option($this->stateVariableName );
		if (!$storage_option) {
            add_option($this->storageVariableName, array());
        }
        if (!$state_option) {
            add_option($this->stateVariableName , array());
        }
    }

    /**
     * {@inheritDoc}
     */
    public function retrieveAccessToken($service)
    {
        if ($this->hasAccessToken($service)) {
			$storage_option = get_option($this->storageVariableName);
            return $storage_option[$service];
        }

        throw new TokenNotFoundException('Token not found in session, are you sure you stored it?');
    }

    /**
     * {@inheritDoc}
     */
    public function storeAccessToken($service, TokenInterface $token)
    {
        $storage_option = get_option($this->storageVariableName);

        if (isset($storage_option)
            && is_array($storage_option)
        ) {
            $storage_option[$service] = $token;
			update_option($this->storageVariableName, $storage_option);
        } else {
            $storage_option = array(
                $service => $token,
            );
			update_option($this->storageVariableName, $storage_option);
        }

        // allow chaining
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function hasAccessToken($service)
    {
		$storage_option = get_option($this->storageVariableName);
        return isset($storage_option, $storage_option[$service]);
    }

    /**
     * {@inheritDoc}
     */
    public function clearToken($service)
    {
		$storage_option = get_option($this->storageVariableName);
        if (array_key_exists($service, $storage_option)) {
            unset($storage_option[$service]);
			update_option($this->storageVariableName, $storage_option);
        }

        // allow chaining
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function clearAllTokens()
    {
        delete_option( $this->storageVariableName );

        // allow chaining
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function storeAuthorizationState($service, $state)
    {
		$state_option = get_option($this->stateVariableName);
        if (isset($state_option)
            && is_array($state_option)
        ) {
            $state_option[$service] = $state;
			update_option($this->stateVariableName, $state_option);
        } else {
            $state_option = array(
                $service => $state,
            );
			update_option($this->stateVariableName, $state_option);
        }

        // allow chaining
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function hasAuthorizationState($service)
    {
		$state_option = get_option($this->stateVariableName);
        return $state_option;
    }

    /**
     * {@inheritDoc}
     */
    public function retrieveAuthorizationState($service)
    {
        if ($this->hasAuthorizationState($service)) {
			$state_option = get_option($this->stateVariableName);
            return $state_option[$service];
        }

        throw new AuthorizationStateNotFoundException('State not found in session, are you sure you stored it?');
    }

    /**
     * {@inheritDoc}
     */
    public function clearAuthorizationState($service)
    {
		$state_option = get_option($this->stateVariableName);
        if (array_key_exists($service, $state_option)) {
            unset($state_option[$service]);
			update_option($this->stateVariableName, $state_option);
        }

        // allow chaining
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function clearAllAuthorizationStates()
    {
		delete_option($this->stateVariableName);

        // allow chaining
        return $this;
    }

    public function __destruct()
    {
        session_write_close();
    }
}
