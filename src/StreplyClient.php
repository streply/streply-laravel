<?php

declare(strict_types=1);

namespace Streply\Laravel;

use Illuminate\Support\Facades\Auth;
use Streply\Exceptions\InvalidDsnException;
use Streply\Exceptions\InvalidUserException;
use Streply\Responses\Entity;
use Streply\Streply;

class StreplyClient
{
    public function __construct(
        protected string $dsn,
        protected array $options
    ) {}

    public function initialize(): void
    {
        Streply::Initialize($this->dsn, $this->options);
    }

    public function user(): void
    {
        if (!Auth::check()) {
            return;
        }

        $user = Auth::user();
        $userName = $user?->name ?? $user?->username ?? null;

        Streply::User(Auth::id(), $userName);
    }
}