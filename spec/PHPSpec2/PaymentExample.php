<?php

namespace PHPSpec2 {
    class PaymentExample {
        public function getName()
        {
            return "everzet\nmduarte";
        }

        public function something()
        {
            return false;
        }

        public function isPaid()
        {
            return false;
        }

        public function brokenMethod()
        {
            throw new \Exception('Something is deeply wrong');
        }
    }
}

namespace spec\PHPSpec2 {

    use PHPSpec2\Specification;

    class PaymentExample implements Specification
    {
        function it_should_blah()
        {
            $this->object->getName()->shouldReturn("everzet\nmduarte\njakub");
        }

        function it_should_something()
        {
            $this->object->something()->shouldReturnTrue();
        }

        function it_is_paid()
        {
            $this->object->shouldBePaid();
        }

        function it_throws_exceptions()
        {
            $this->object->brokenMethod();
        }
    }
}
