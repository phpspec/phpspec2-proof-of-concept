<?php

namespace spec\PHPSpec2\Formatter\Presenter\Differ;

use PHPSpec2\ObjectBehavior;

class ArrayEngine extends ObjectBehavior
{
    function it_should_support_arrays()
    {
        $this->supports(array('first'), array('second'))->shouldReturn(true);
    }

    function it_should_calculate_diff()
    {
        $this->compare(array('first'), array('second'))->shouldReturn(<<<STRING
<code>
@@ -1,1 +1,1 @@
<diff-del>-first</diff-del>
<diff-add>+second</diff-add>
</code>
STRING
        );

    }

}
