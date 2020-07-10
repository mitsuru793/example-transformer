<?php
declare(strict_types=1);

namespace Php\Library\UIFaces;

final class Parameters
{
    public const GENDERS = ['male', 'female'];

    public int $limit;

    /** @var string[] */
    public array $genders;

    public bool $random;

    public int $fromAge;

    public int $toAge;

    /** @var string[] */
    public array $hairColors;

    /** @var string[] */
    public array $emotions;

    public function toHttpQuery(): string
    {
        $params = [];
        if (!empty($this->limit)) {
            $params['limit'] = $this->limit;
        }

        if (!empty($this->genders)) {
            $params['gender'] = $this->genders;
        }

        if (!empty($this->random)) {
            $params['random'] = $this->random;
        }

        if (!empty($this->fromAge)) {
            $params['from_age'] = $this->fromAge;
        }

        if (!empty($this->toAge)) {
            $params['to_age'] = $this->toAge;
        }

        if (!empty($this->hairColors)) {
            $params['hairColor'] = $this->hairColors;
        }

        if (!empty($this->emotions)) {
            $params['emotion'] = $this->emotions;
        }
        return http_build_query($params);
    }

    public function limit(int $value): self
    {
        $this->limit = $value;
        return $this;
    }

    /**
     * @param string[] $values
     */
    public function genders(array $values): self
    {
        $count = count($values);
        if ($count > 2) {
            $message = "The length of genders must be less than equal 2, but $count.";
            throw new \InvalidArgumentException($message);
        }

        foreach ($values as $value) {
            if (!in_array($value, self::GENDERS)) {
                $message = sprintf('Gender must be one of [%s].' . implode(', ', self::GENDERS));
                throw new \InvalidArgumentException($message);
            }
        }

        $this->genders = $values;
        return $this;
    }

    public function random(bool $value): self
    {
        $this->random = $value;
        return $this;
    }

    public function fromAge(int $value): self
    {
        $this->validateAge($value);
        $this->fromAge = $value;
        return $this;
    }

    public function toAge(int $value): self
    {
        $this->validateAge($value);
        $this->toAge = $value;
        return $this;
    }

    /**
     * @param string[] $values
     */
    public function hairColors(array $values): self
    {
        $this->hairColors = $values;
        return $this;
    }

    /**
     * @param string[] $values
     */
    public function emotions(array $values): self
    {
        $this->emotions = $values;
        return $this;
    }

    private function validateAge(int $value): void
    {
        if ($value < 0) {
            $message = "Age must be positive number, but $value.";
            throw new \InvalidArgumentException($message);
        }
    }
}
