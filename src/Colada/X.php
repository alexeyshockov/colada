<?php

namespace Colada {
    class X
    {
        /**
         * Register \x() function for quick access to "future variables".
         */
        public static function registerFunction()
        {

        }
    }
}

namespace {
    /**
     * Some useful examples:
     *
     * <code>
     * <?php
     *
     * $collection->filter(x()->getName()->startsWith('Test'));
     * </code>
     *
     * vs.
     *
     * <code>
     * <?php
     *
     * $collection->filter(
     *     function($user) { return StringHelper::startsWith($user->getName(), 'Test'); }
     * );
     * </code>
     *
     * P.S. Fucking gettext... _()...
     */
    // TODO Another idea for registering this function?
    function x()
    {
        if (func_num_args()) {
            return new \Colada\X\Value(func_get_arg(0));
        }

        return new \Colada\X\FutureValue();
    }
}
