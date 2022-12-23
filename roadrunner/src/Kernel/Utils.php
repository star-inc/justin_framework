<?php
// Justin PHP Framework
// It's a portable framework for PHP 8.0+, powered by open source community.
// Licensed under the MIT License. (https://ncurl.xyz/s/2ltII6Ang)
// (c) 2022 Star Inc. (https://starinc.xyz)

namespace JustinExample\Kernel;

class Utils
{
    public static function posixTimestamp(): int
    {
        return time();
    }

    // https://www.uuidgenerator.net/dev-corner/php
    public static function randomUUID(): string
    {
        // Generate 16 bytes (128 bits) of random data or use the data passed into the function.
        $data = self::randomBytes(16);
        assert(strlen($data) == 16);

        // Set version to 0100
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        // Set bits 6-7 to 10
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

        // Output the 36 character UUID.
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    public static function randomBytes(int $length, bool $base64 = false): string
    {
        $bytes = random_bytes($length);
        return $base64 ? base64_encode($bytes) : $bytes;
    }

    public static function randomNumber()
    {
        return random_int(0, PHP_INT_MAX);
    }

    public static function passwordHash(string $password): string
    {
        $password = hash("sha256", $password);
        return password_hash($password, PASSWORD_BCRYPT);
    }

    public static function passwordVerify(string $password, string $hash): bool
    {
        $password = hash("sha256", $password);
        return password_verify($password, $hash);
    }
}
