<?php

class PetFeature
{
    private $id;
    private $name;
    private $value;

    public function __construct(int $id, ?string $name, int|bool|null $value)
    {
        $this->id = $id;
        $this->name = $name;
        $this->setValue($value);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getValue(): bool
    {
        return $this->value;
    }

    public function setValue(int|bool|null $value): void
    {
        if (is_numeric($value)) {
            $this->value = $value == 2 ? true : false;
        } else {
            $this->value = $value;
        }
    }
}
