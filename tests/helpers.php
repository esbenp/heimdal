<?php

class TestReporter implements Optimus\Heimdal\Reporters\ReporterInterface
{
    private $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function report(Exception $e)
    {
        return sprintf('%s: %s', $e->getMessage(), $this->config['test']);
    }
}

function getConfigStub()
{
    $config = require __DIR__.'/../src/config/optimus.heimdal.php';

    $reporterMock = \Mockery::mock('ReporterClass');

    $config['reporters'] = [
        'test' => [
            'class' => TestReporter::class,
            'config' => [
                'test' => 1234
            ]
        ],
        'test2' => [
            'class' => TestReporter::class,
            'config' => [
                'test' => 4321
            ]
        ]
    ];

    return $config;
}
