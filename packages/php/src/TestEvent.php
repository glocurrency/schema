<?php

declare(strict_types=1);

namespace GloCurrency\Schema;

class TestEvent
{
    /**
     * Schema used to validate input for creating instances of this class
     *
     * @var array
     */
    private static array $schema = [
        '$schema' => 'http://json-schema.org/draft-07/schema#',
        'title' => 'TestEvent',
        'type' => 'object',
        'properties' => [
            'type' => [
                'type' => 'string',
            ],
            'version' => [
                'type' => 'number',
            ],
            'data' => [
                'type' => 'object',
                'properties' => [
                    'timestamp' => [
                        'type' => 'string',
                        'format' => 'date-time',
                    ],
                ],
                'required' => [
                    'timestamp',
                ],
            ],
        ],
        'required' => [
            'type',
            'version',
            'data',
        ],
    ];

    /**
     * @var string
     */
    private string $type;

    /**
     * @var int|float
     */
    private int|float $version;

    /**
     * @var TestEventData
     */
    private TestEventData $data;

    /**
     * @param string $type
     * @param int|float $version
     * @param TestEventData $data
     */
    public function __construct(string $type, int|float $version, TestEventData $data)
    {
        $this->type = $type;
        $this->version = $version;
        $this->data = $data;
    }

    /**
     * @return string
     */
    public function getType() : string
    {
        return $this->type;
    }

    /**
     * @return int|float
     */
    public function getVersion() : int|float
    {
        return $this->version;
    }

    /**
     * @return TestEventData
     */
    public function getData() : TestEventData
    {
        return $this->data;
    }

    /**
     * @param string $type
     * @return self
     */
    public function withType(string $type) : self
    {
        $validator = new \JsonSchema\Validator();
        $validator->validate($type, self::$schema['properties']['type']);
        if (!$validator->isValid()) {
            throw new \InvalidArgumentException($validator->getErrors()[0]['message']);
        }

        $clone = clone $this;
        $clone->type = $type;

        return $clone;
    }

    /**
     * @param int|float $version
     * @return self
     */
    public function withVersion(int|float $version) : self
    {
        $validator = new \JsonSchema\Validator();
        $validator->validate($version, self::$schema['properties']['version']);
        if (!$validator->isValid()) {
            throw new \InvalidArgumentException($validator->getErrors()[0]['message']);
        }

        $clone = clone $this;
        $clone->version = $version;

        return $clone;
    }

    /**
     * @param TestEventData $data
     * @return self
     */
    public function withData(TestEventData $data) : self
    {
        $clone = clone $this;
        $clone->data = $data;

        return $clone;
    }

    /**
     * Builds a new instance from an input array
     *
     * @param array|object $input Input data
     * @param bool $validate Set this to false to skip validation; use at own risk
     * @return TestEvent Created instance
     * @throws \InvalidArgumentException
     */
    public static function buildFromInput(array|object $input, bool $validate = true) : TestEvent
    {
        $input = is_array($input) ? \JsonSchema\Validator::arrayToObjectRecursive($input) : $input;
        if ($validate) {
            static::validateInput($input);
        }

        $type = $input->{'type'};
        $version = str_contains((string)($input->{'version'}), '.') ? (float)($input->{'version'}) : (int)($input->{'version'});
        $data = TestEventData::buildFromInput($input->{'data'}, validate: $validate);

        $obj = new self($type, $version, $data);

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
        $output['type'] = $this->type;
        $output['version'] = $this->version;
        $output['data'] = ($this->data)->toJson();

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
        $this->data = clone $this->data;
    }
}

