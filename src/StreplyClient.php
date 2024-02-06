<?php

namespace Streply\StreplyLaravel;

use Illuminate\Support\Facades\Auth;
use Streply\Exceptions\InvalidDsnException;
use Streply\Exceptions\InvalidUserException;
use Streply\Responses\Entity;
use Streply\Streply;

class StreplyClient
{
    protected string $dsn;

    protected array $options;

    public function __construct(string $dsn, array $options)
    {
        $this->dsn = $dsn;
        $this->options = $options;
    }

    public function initialize(): void
    {
        Streply::Initialize($this->dsn, $this->options);
    }

	public function flush(): void
	{
		Streply::Flush();
	}

	public function user(): void
    {
        if(Auth::check()) {
            Streply::User(Auth::id(), Auth::user()->name ?? null);
        }
    }

    public function activity(string $name, array $parameters = [], ?string $flag = null): Entity
    {
        return \Streply\Activity($name, $parameters, null, $flag);
    }

	public function setRoute(?string $route): void
	{
		Streply::Properties()->setForPerformance('route', $route);
	}
}
