<?php

namespace src\core;

/**
 * Class Session
 * @package src\core
 * @author Nilton Duarte <tvirapegubeco@gmail.com>
 */
class Session
{
    /**
     * Session construct
     */
    public function __construct()
    {
        if (!session_id()) {
            session_save_path(SESSION_PATH);
            session_start();
        }
    }

    /**
     * @param $name
     * @return mixed|null
     */
    public function __get($name)
    {
        if (!empty($_SESSION[$name])) {
            return $_SESSION[$name];
        }
        return null;
    }

    /**
     * @param $name
     * @return bool
     */
    public function __isset($name): bool
    {
        return $this->has($name);
    }

    /**
     * @return object|null
     */
    public function all(): ?object
    {
        return (object)$_SESSION;
    }

    /**
     * @param string $key
     * @param $value
     * @return $this
     */
    public function set(string $key, $value): self
    {
        $_SESSION[$key] = (is_array($value) ? (object)$value : $value);
        return $this;
    }

    /**
     * @param string $key
     * @return $this
     */
    public function unset(string $key): self
    {
        if (!empty($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
        return $this;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    /**
     * @return $this
     */
    public function regenerate(): self
    {
        session_regenerate_id(true);
        return $this;
    }

    /**
     * @return $this
     */
    public function destroy(): self
    {
        session_unset();
        session_destroy();
        return $this;
    }

    /**
     * @return Message
     */
    public function flash(): ?Message
    {
        if ($this->has('flash')){
            $flash = $this->flash;
            $this->unset('flash');
            return $flash;
        }
        return null;
    }
}
