<?php

namespace Rubix\ML\Tests\CrossValidation\Metrics;

use Rubix\ML\Estimator;
use Rubix\ML\CrossValidation\Metrics\Metric;
use Rubix\ML\CrossValidation\Metrics\Completeness;
use PHPUnit\Framework\TestCase;
use Generator;

/**
 * @group Metrics
 * @covers \Rubix\ML\CrossValidation\Metrics\Completeness
 */
class CompletenessTest extends TestCase
{
    /**
     * @var \Rubix\ML\CrossValidation\Metrics\Completeness
     */
    protected $metric;

    /**
     * @before
     */
    protected function setUp() : void
    {
        $this->metric = new Completeness();
    }

    /**
     * @test
     */
    public function build_metric() : void
    {
        $this->assertInstanceOf(Completeness::class, $this->metric);
        $this->assertInstanceOf(Metric::class, $this->metric);
    }

    /**
     * @test
     */
    public function range() : void
    {
        $expected = [0.0, 1.0];

        $this->assertEquals($expected, $this->metric->range());
    }

    /**
     * @test
     */
    public function compatibility() : void
    {
        $expected = [
            Estimator::CLUSTERER,
        ];

        $this->assertEquals($expected, $this->metric->compatibility());
    }

    /**
     * @test
     * @dataProvider scoreProvider
     *
     * @param (string|int)[] $predictions
     * @param (string|int)[] $labels
     * @param float $expected
     */
    public function score(array $predictions, array $labels, float $expected) : void
    {
        [$min, $max] = $this->metric->range();

        $score = $this->metric->score($predictions, $labels);

        $this->assertThat(
            $score,
            $this->logicalAnd(
                $this->greaterThanOrEqual($min),
                $this->lessThanOrEqual($max)
            )
        );

        $this->assertEquals($expected, $score);
    }

    /**
     * @return \Generator<array>
     */
    public function scoreProvider() : Generator
    {
        yield [
            [0, 1, 1, 0, 1],
            ['lamb', 'lamb', 'wolf', 'wolf', 'wolf'],
            0.5833333333333333,
        ];

        yield [
            [0, 0, 1, 1, 1],
            ['lamb', 'lamb', 'wolf', 'wolf', 'wolf'],
            1.0,
        ];

        yield [
            [1, 1, 0, 0, 0],
            ['lamb', 'lamb', 'wolf', 'wolf', 'wolf'],
            1.0,
        ];

        yield [
            [0, 1, 2, 3, 4],
            ['lamb', 'lamb', 'wolf', 'wolf', 'wolf'],
            0.41666666666666663,
        ];

        yield [
            [0, 0, 0, 0, 0],
            ['lamb', 'lamb', 'wolf', 'wolf', 'wolf'],
            1.0,
        ];
    }
}
