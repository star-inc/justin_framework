<?php

namespace JustinExample\Validators;

use JustinExample\Kernel\Context;
use JustinExample\Models\User;
use Firebase\JWT\JWT as RealJWT;
use Firebase\JWT\Key;
use Exception;

class JWT implements ValidatorInterface
{
    public Key $jwt_key;
    public string $issuer;

    public function __construct(Context $context)
    {
        $jwt_secret = $context->getConfig()->get("JWT_SECRET");
        assert(is_string($jwt_secret));
        $this->jwt_key = new Key($jwt_secret, "HS256");
        $this->issuer = $context->getRequest()->getHeader("Host");
    }

    public function issue(User $user): string
    {
        $payload = [
            "iss" => $this->issuer,
            "iat" => time(),
            "exp" => time() + 3600,
            "sub" => $user->getUuid(),
            "aud" => $this->issuer,
            "name" => $user->getUsername(),
        ];
        return RealJWT::encode(
            $payload,
            $this->jwt_key->getKeyMaterial(),
            $this->jwt_key->getAlgorithm()
        );
    }

    public function validate(mixed $value): mixed
    {
        try {
            $token = RealJWT::decode($value, $this->jwt_key);
            $user = new User();
            $user->setUuid($token->sub)->setUsername($token->name);
            if ($user->checkReady()) {
                return $user;
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
        }
        return false;
    }
}
