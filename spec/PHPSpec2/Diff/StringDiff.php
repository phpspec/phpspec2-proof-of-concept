<?php

namespace spec\PHPSpec2\Diff;

use PHPSpec2\Specification;

class StringDiff implements Specification
{
    function it_should_support_strings()
    {
        $this->stringDiff->supports('string1', 'string2')->shouldReturn(true);
    }

    function it_should_calculate_diff()
    {
        $this->stringDiff->compare('string1', 'string2')->shouldReturn(<<<STRING
<code>
@@ -1,1 +1,1 @@
<diff-del>-string1</diff-del>
<diff-add>+string2</diff-add>
</code>
STRING
        );
    }
}
