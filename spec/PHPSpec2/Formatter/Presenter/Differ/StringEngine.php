<?php

namespace spec\PHPSpec2\Formatter\Presenter\Differ;

use PHPSpec2\ObjectBehavior;

class StringEngine extends ObjectBehavior
{
    function it_should_support_strings()
    {
        $this->supports('string1', 'string2')->shouldReturn(true);
    }

    function it_should_calculate_diff()
    {
        $this->compare('string1', 'string2')->shouldReturn(<<<STRING
<code>
@@ -1,1 +1,1 @@
<diff-del>-string1</diff-del>
<diff-add>+string2</diff-add>
</code>
STRING
        );
    }
}
