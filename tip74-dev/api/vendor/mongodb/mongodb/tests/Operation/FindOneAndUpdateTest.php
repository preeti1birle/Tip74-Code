<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Operation\FindOneAndUpdate;

class FindOneAndUpdateTest extends TestCase
{
    /**
     * @dataProvider provideInvalidDocumentValues
     */
    public function testConstructorFilterArgumentTypeCheck($filter)
    {
        $this->expectException(InvalidArgumentException::class);
        new FindOneAndUpdate($this->getDatabaseName(), $this->getCollectionName(), $filter, []);
    }

    /**
     * @dataProvider provideInvalidDocumentValues
     */
    public function testConstructorUpdateArgumentTypeCheck($update)
    {
        $this->expectException(InvalidArgumentException::class);
        new FindOneAndUpdate($this->getDatabaseName(), $this->getCollectionName(), [], $update);
    }

    public function testConstructorUpdateArgumentRequiresOperators()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('First key in $update argument is not an update operator');
        new FindOneAndUpdate($this->getDatabaseName(), $this->getCollectionName(), [], []);
    }

    /**
     * @dataProvider provideInvalidConstructorOptions
     */
    public function testConstructorOptionTypeChecks(array $options)
    {
        $this->expectException(InvalidArgumentException::class);
        new FindOneAndUpdate($this->getDatabaseName(), $this->getCollectionName(), [], ['$set' => ['x' => 1]], $options);
    }

    public function provideInvalidConstructorOptions()
    {
        $options = [];

        foreach ($this->getInvalidDocumentValues() as $value) {
            $options[][] = ['projection' => $value];
        }

        foreach ($this->getInvalidIntegerValues() as $value) {
            $options[][] = ['returnDocument' => $value];
        }

        return $options;
    }

    /**
     * @dataProvider provideInvalidConstructorReturnDocumentOptions
     */
    public function testConstructorReturnDocumentOption($returnDocument)
    {
        $this->expectException(InvalidArgumentException::class);
        new FindOneAndUpdate($this->getDatabaseName(), $this->getCollectionName(), [], [], ['returnDocument' => $returnDocument]);
    }

    public function provideInvalidConstructorReturnDocumentOptions()
    {
        return $this->wrapValuesForDataProvider([-1, 0, 3]);
    }
}
