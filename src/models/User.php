<?php

namespace src\models;

use InvalidArgumentException;
use src\core\Model as SuperModel;
use src\models\Model as ModelInterface;

/**
 * @author Nilton Duarte <tvirapegubeco@gmail.com>
 */
class User implements Model
{
    /** @var string $entity database table */
    protected static string $entity = 'users';

    /** @var array|string[] no update or create in this columns */
    protected static array $safe = ['id', 'created_at', 'updated_at'];

    /** @var int|null primary key */
    private ?int $id;

    /** @var null|string $name user name column */
    private ?string $name;

    /** @var null|string $email user email column */
    private ?string $email;

    /** @var null|string user password column */
    private ?string $password;

    /** @var null|string $token user token column  */
    private string $token;

    /** @var null|string $photo user profile picture path */
    private ?string $photo;

    /**
     * @param int $id
     * @return $this
     */
    public function isId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function isName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @param string $email
     * @return User
     * @throws InvalidArgumentException
     */
    public function isEmail(string $email)
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->email = $email;
            return $this;
        }
        throw new InvalidArgumentException("O email informado é inválido!");
    }

    /**
     * @param string $password
     * @return $this
     * @throws InvalidArgumentException
     */
    public function isPassword(string $password): User
    {
        if (!is_password($password)) {
            throw new InvalidArgumentException("A senha precisa ter entre " . CONF_PASSWD_MIN_LEN . " e " . CONF_PASSWD_MAX_LEN . " caracteres.");
        }
        if (is_null(password_get_info($password)['algo'])) {
            $this->password = password($password);
            return $this;
        }
        $this->password = $password;
        return $this;
    }

    /**
     * @param string $token
     * @return $this
     */
    public function isToken(string $token): self
    {
        $this->token = $token;
        return $this;
    }

    /**
     * @param null|string $photo
     * @return $this
     */
    public function isPhoto(?string $photo): self
    {
        $this->photo = $photo;
        return $this;
    }

    /**
     * @return null|string
     */
    public function photo(): ?string
    {
        return ($this->photo ?? null);
    }
    /**
     * @return string
     */
    public static function entity(): string
    {
        return self::$entity;
    }

    /**
     * @return array|string[]
     */
    public static function safe(): array
    {
        return self::$safe;
    }

    /**
     * @return int|null
     */
    public function id(): ?int
    {
        return ($this->id ?? null);
    }

    /**
     * @return null|string
     */
    public function name(): ?string
    {
        return ($this->name ?? null);
    }

    /**
     * @return null|string
     */
    public function email(): ?string
    {
        return ($this->email ?? null);
    }

    /**
     * @return null|string
     */
    public function password(): ?string
    {
        return ($this->password ?? null);
    }

    /**
     * @return string|null
     */
    public function token(): ?string
    {
        return ($this->token ?? null);
    }
}
