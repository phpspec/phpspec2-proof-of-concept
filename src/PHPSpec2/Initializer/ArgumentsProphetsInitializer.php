<?php

namespace PHPSpec2\Initializer;

use PHPSpec2\SpecificationInterface;
use PHPSpec2\Loader\Node\Example;
use PHPSpec2\Prophet\CollaboratorsCollection;
use PHPSpec2\Prophet\MockProphet;
use PHPSpec2\Mocker\MockerInterface;
use PHPSpec2\Wrapper\ArgumentsUnwrapper;
use PHPSpec2\Formatter\Presenter\PresenterInterface;

class ArgumentsProphetsInitializer implements ExampleInitializerInterface
{
    private $parametersReader;
    private $mocker;
    private $unwrapper;
    private $presenter;

    public function __construct(FunctionParametersReader $parametersReader,
                                MockerInterface $mocker, ArgumentsUnwrapper $unwrapper,
                                PresenterInterface $presenter)
    {
        $this->parametersReader = $parametersReader;
        $this->mocker           = $mocker;
        $this->unwrapper        = $unwrapper;
        $this->presenter        = $presenter;
    }

    public function getPriority()
    {
        return 0;
    }

    public function supports(SpecificationInterface $specification, Example $example)
    {
        return true;
    }

    public function initialize(SpecificationInterface $specification, Example $example,
                               CollaboratorsCollection $collaborators)
    {
        foreach ($this->parametersReader->getParameters($example) as $name => $type) {
            $subject = $type ? $this->mocker->mock($type) : $type;
            $prophet = new MockProphet(
                $subject, $this->mocker, $this->unwrapper, $this->presenter
            );
            $collaborators->set($name, $prophet);
        }
    }
}
