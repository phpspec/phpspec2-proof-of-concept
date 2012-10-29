<?php

namespace spec\PHPSpec2\Configuration;

use PHPSpec2\ObjectBehavior;

class Configuration extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(array(
            'default' => array(
                'foo' => 'bar',
                'extensions' => array (
                    'Some\Amazing\Extension' => null
                )
            )
        ), 'default');
    }

    function it_returns_the_parameter_from_the_default_profile()
    {
        $this->getParameter('foo')->shouldReturn('bar');
    }

    function it_returns_the_progress_format_by_default()
    {
        $this->getParameter('format')->shouldReturn('progress');
    }

    function it_complains_if_the_parameter_is_not_in_the_config()
    {
        $this->shouldThrow(
            'PHPSpec2\Configuration\ConfigurationException'
        )->duringGetParameter('not_in_config');
    }

    function it_lets_application_know_that_an_extension_is_configured()
    {
        $this->shouldHaveExtensions();
    }
}
