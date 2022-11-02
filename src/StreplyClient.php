<?php

namespace Streply\StreplyLaravel;

use Streply\Exceptions\InvalidDsnException;
use Streply\Streply;

class StreplyClient
{
    /**
     * @var string
     */
    protected string $dsn;

    /**
     * @var array
     */
    protected array $options;

    /**
     * @param string $dsn
     * @param array $options
     */
    public function __construct(string $dsn, array $options)
    {
        $this->dsn = $dsn;
        $this->options = $options;
    }

    /**
     * @throws InvalidDsnException
     */
    public function initialize()
    {
        Streply::Initialize($this->dsn, $this->options);
    }
}
