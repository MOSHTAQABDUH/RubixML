<?php

namespace Rubix\ML\Tests\Regressors;

use Rubix\ML\Online;
use Rubix\ML\Learner;
use Rubix\ML\DataType;
use Rubix\ML\Estimator;
use Rubix\ML\Persistable;
use Rubix\ML\Datasets\Unlabeled;
use Rubix\ML\Regressors\KNNRegressor;
use Rubix\ML\Kernels\Distance\Minkowski;
use Rubix\ML\Datasets\Generators\HalfMoon;
use Rubix\ML\CrossValidation\Metrics\RSquared;
use PHPUnit\Framework\TestCase;
use InvalidArgumentException;
use RuntimeException;

/**
 * @group Regressors
 * @covers \Rubix\ML\Regressors\KNNRegressor
 */
class KNNRegressorTest extends TestCase
{
    protected const TRAIN_SIZE = 300;
    protected const TEST_SIZE = 10;
    protected const MIN_SCORE = 0.9;

    protected const RANDOM_SEED = 0;

    /**
     * @var \Rubix\ML\Datasets\Generators\HalfMoon
     */
    protected $generator;

    /**
     * @var \Rubix\ML\Regressors\KNNRegressor
     */
    protected $estimator;

    /**
     * @var \Rubix\ML\CrossValidation\Metrics\RSquared
     */
    protected $metric;

    /**
     * @before
     */
    protected function setUp() : void
    {
        $this->generator = new HalfMoon(4., -7., 1., 90, 0.02);

        $this->estimator = new KNNRegressor(3, true, new Minkowski(3.0));

        $this->metric = new RSquared();

        srand(self::RANDOM_SEED);
    }
    
    /**
     * @test
     */
    public function build_regressor() : void
    {
        $this->assertInstanceOf(KNNRegressor::class, $this->estimator);
        $this->assertInstanceOf(Online::class, $this->estimator);
        $this->assertInstanceOf(Learner::class, $this->estimator);
        $this->assertInstanceOf(Persistable::class, $this->estimator);
        $this->assertInstanceOf(Estimator::class, $this->estimator);

        $this->assertSame(Estimator::REGRESSOR, $this->estimator->type());

        $this->assertNotContains(DataType::CATEGORICAL, $this->estimator->compatibility());
        $this->assertContains(DataType::CONTINUOUS, $this->estimator->compatibility());

        $this->assertFalse($this->estimator->trained());
    }
        
    /**
     * @test
     */
    public function train_partial_predict_proba() : void
    {
        $training = $this->generator->generate(self::TRAIN_SIZE);

        $testing = $this->generator->generate(self::TEST_SIZE);

        $folds = $training->fold(3);
        
        $this->estimator->train($folds[0]);
        $this->estimator->partial($folds[1]);
        $this->estimator->partial($folds[2]);

        $this->assertTrue($this->estimator->trained());

        $predictions = $this->estimator->predict($testing);

        $score = $this->metric->score($predictions, $testing->labels());

        $this->assertGreaterThanOrEqual(self::MIN_SCORE, $score);
    }
    
    /**
     * @test
     */
    public function train_with_unlabeled() : void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->estimator->train(Unlabeled::quick());
    }
    
    /**
     * @test
     */
    public function train_incompatible() : void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->estimator->train(Unlabeled::quick([['bad']]));
    }
    
    /**
     * @test
     */
    public function predict_untrained() : void
    {
        $this->expectException(RuntimeException::class);

        $this->estimator->predict(Unlabeled::quick());
    }
}
