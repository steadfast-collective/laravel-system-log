<?php

namespace SteadfastCollective\LaravelSystemLog\Contracts;

interface SystemLoggableContract
{
    public function getInternalId(): ?string;

    public function getInternalType(): string;

    public function getExternalId(): ?string;

    public function getExternalType(): string;
}
