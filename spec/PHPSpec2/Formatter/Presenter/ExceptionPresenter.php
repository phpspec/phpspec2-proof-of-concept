<?php

namespace spec\PHPSpec2\Formatter\Presenter;

use PHPSpec2\ObjectBehavior;
use PHPSpec2\Exception\Example\NotEqualException;
use PHPSpec2\Exception\Exception;
use PHPSpec2\Exception\Example\MockerException;

class ExceptionPresenter extends ObjectBehavior
{
    /**
     * @param PHPSpec2\Formatter\Presenter\ValuePresenter $valuePresenter
     * @param PHPSpec2\Formatter\Presenter\Differ\Differ $differ
     */
    function let($valuePresenter, $differ)
    {
        $this->beConstructedWith($valuePresenter, $differ);
    }

    function it_should_show_the_stacktrace_of_an_exception($valuePresenter)
    {
        $exc = new Exception('personal Exception');
        $valuePresenter->presentValue->willReturn('personal Exception');
        $valuePresenter->presentValue->willReturn('"stacktrace"');

        $this->presentExceptionStackTrace($exc)->shouldBeLike('
 0 spec/PHPSpec2/Formatter/Presenter/ExceptionPresenter.php:23
   throw new PHPSpec2\Exception\Exception(personal Exception)
 1 [internal]
   spec\PHPSpec2\Formatter\Presenter\ExceptionPresenter->it_should_show_the_stacktrace_of_an_exception("stacktrace")
');
    }

    function it_should_return_a_string_for_different_kind_of_exception($valuePresenter, $mockerException)
    {
        throw new \PHPSpec2\Exception\Example\PendingException('Error here, "PHP Fatal error:  Call to undefined method Mockery\Exception::setCause() in Formatter/PrettyFormatter.php on line 129"');
        $mockerException->beAMockOf('\PHPSpec2\Exception\Example\MockerException');
        $mockerException->beConstructedWith('Message');

        $mockerException->setCause('a');
        $mockerException->getCode->willReturn(array());
        $mockerException->getTrace->willReturn(array());
        $mockerException->getFile->willReturn('filename');
        $mockerException->getLine->willReturn(23);

        $valuePresenter->presentValue->willReturn($mockerException);
        $this->presentException($mockerException, false)->shuldReturn('AA');
    }

    function it_should_compare_the_exception_with_diff($differ)
    {
        $actual = new Exception();
        $expected = new Exception();

        $notEqualException = new NotEqualException('message', $actual, $expected);

        $differ->compare($actual, $expected)->shouldBeCalled();
        $this->presentExceptionDifference($notEqualException)->shouldReturn(null);
    }


    function it_should_show_the_trace_function($valuePresenter)
    {
        $valuePresenter->presentValue->willReturn('2');
        $this->presentExceptionTraceFunction('function', array('2'))->shouldBeLike("   function(2)\n");
    }


    function it_should_show_the_trace_method($valuePresenter)
    {
        $valuePresenter->presentValue->willReturn('3');
        $this->presentExceptionTraceMethod('class', 'type', 'method', array('3'))->shouldBeLike("   classtypemethod(3)\n");
    }




}
