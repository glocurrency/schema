<?php

declare(strict_types=1);

namespace GloCurrency\Schema;

class EchoEventData
{
    /**
     * Schema used to validate input for creating instances of this class
     *
     * @var array
     */
    private static array $schema = [
        'type' => 'object',
        'properties' => [
            'message' => [
                'type' => 'string',
            ],
            'timestamp' => [
                'type' => 'string',
                'format' => 'date-time',
            ],
        ],
        'required' => [
            'message',
            'timestamp',
        ],
    ];

    /**
     * @var string
     */
    private string $message;

    /**
     * @var \DateTime
     */
    private \DateTime $timestamp;

    /**
     * @param string $message
     * @param \DateTime $timestamp
     */
    public function __construct(string $message, \DateTime $timestamp)
    {
        $this->message = $message;
        $this->timestamp = $timestamp;
    }

    /**
     * @return string
     */
    public function getMessage() : string
    {
        return $this->message;
    }

    /**
     * @return \DateTime
     */
    public function getTimestamp() : \DateTime
    {
        return $this->timestamp;
    }

    /**
     * @param string $message
     * @return self
     */
    public function withMessage(string $message) : self
    {
        $validator = new \JsonSchema\Validator();
        $validator->validate($message, self::$schema['properties']['message']);
        if (!$validator->isValid()) {
            throw new \InvalidArgumentException($validator->getErrors()[0]['message']);
        }

        $clone = clone $this;
        $clone->message = $message;

        return $clone;
    }

    /**
     * @param \DateTime $timestamp
     * @return self
     */
    public function withTimestamp(\DateTime $timestamp) : self
    {
        $clone = clone $this;
        $clone->timestamp = $timestamp;

        return $clone;
    }

    /**
     * Builds a new instance from an input array
     *
     * @param array|object $input Input data
     * @param bool $validate Set this to false to skip validation; use at own risk
     * @return EchoEventData Created instance
     * @throws \InvalidArgumentException
     */
    public static function buildFromInput(array|object $input, bool $validate = true) : EchoEventData
    {
        $input = is_array($input) ? \JsonSchema\Validator::arrayToObjectRecursive($input) : $input;
        if ($validate) {
            static::validateInput($input);
        }

        $message = $input->{'message'};
        $timestamp = new \DateTime($input->{'timestamp'});

        $obj = new self($message, $timestamp);

        return $obj;
    }

    /**
     * Converts this object back to a simple array that can be JSON-serialized
     *
     * @return array Converted array
     */
    public function toJson() : array
    {
        $output = [];
        $output['message'] = $this->message;
        $output['timestamp'] = ($this->timestamp)->format(\DateTime::ATOM);

        return $output;
    }

    /**
     * Validates an input array
     *
     * @param array|object $input Input data
     * @param bool $return Return instead of throwing errors
     * @return bool Validation result
     * @throws \InvalidArgumentException
     */
    public static function validateInput(array|object $input, bool $return = false) : bool
    {
        $validator = new \JsonSchema\Validator();
        $input = is_array($input) ? \JsonSchema\Validator::arrayToObjectRecursive($input) : $input;
        $validator->validate($input, self::$schema);

        if (!$validator->isValid() && !$return) {
            $errors = array_map(function(array $e): string {
                return $e["property"] . ": " . $e["message"];
            }, $validator->getErrors());
            throw new \InvalidArgumentException(join(", ", $errors));
        }

        return $validator->isValid();
    }

    public function __clone()
    {
        $this->timestamp = clone $this->timestamp;
    }
}

