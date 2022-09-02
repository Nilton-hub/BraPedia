<?php
// PASSWORD
/**
 * @param string $password
 * @return bool
 */
function is_password(string $password): bool
{
    if (password_get_info($password)["algo"] || mb_strlen($password) >= CONF_PASSWD_MIN_LEN && mb_strlen($password) <= CONF_PASSWD_MAX_LEN) {
        return true;
    }
    return false;
}

/**
 * @param string $password
 * @return string
 */
function password(string $password): string
{
    return password_hash($password, CONF_PASSWD_ALGO, CONF_PASSWD_OPTIONS);
}

/**
 * @param string $hash
 * @return bool
 */
function password_rehash(string $hash): bool
{
    return password_needs_rehash($hash, CONF_PASSWD_ALGO, CONF_PASSWD_OPTIONS);
}

// SECURE
/**
 * @param int $length
 * @param string $alg
 * @param bool $returnCode
 * @return string
 */
function generate_token(int $length = 10, string $alg = 'sha1', bool $returnCode = true): string
{
    $passPhrase = '';
    $length = ($length < 1 || $length > 50 ? 10 : $length);
    for ($i = 1; $i <= $length; $i++) {
        $passPhrase .= chr(rand(97, 122));
    }
    if (!$returnCode) {
        return $passPhrase;
    }
    return uniqid($alg($passPhrase . time()), true);
}

/**
 * @param string $text
 * @return string
 */
function stripped(string $text): string
{
    $strSearch = ['<scrip>', '</scrip>', '-- ', ' --', ' -- '];
    $strReplace = ['&lt;scrip&gt;', '&lt;/scrip&gt;', '', '', ''];
    return str_replace($strSearch, $strReplace, $text);
}

// URL
/**
 * @param string $path
 * @return void
 */
function redirect(string $path = ''): void
{
    header("HTTP/1.1 303 See Other");
    if (filter_var($path, FILTER_VALIDATE_URL)) {
        header("Location: {$path}");
        exit();
    }
    $url = CONF_BASE_URL . '/' . (!empty($path) && $path[0] === '/' ? substr($path, 1) : $path);
    header("Location: {$url}");
    exit();
}

/**
 * @param string $uri
 * @return string
 */
function url(string $uri = '/'): string
{
    return CONF_BASE_URL . '/' . ($uri[0] === '/' ? substr($uri, 1) : $uri);
}

// DATE
/**
 * @param string $data
 * @return string
 */
function date_portuguese(string $data): string
{
    setlocale(LC_ALL, 'pt_BR', 'pt_BR.utf-8', 'portuguese');
    return strftime($data);
}
