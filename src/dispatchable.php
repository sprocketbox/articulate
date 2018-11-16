<?php

namespace Illuminate\Foundation\Events;

if (!trait_exists('Illuminate\Foundation\Events\Dispatchable')) {
    trait Dispatchable
    {
        /**
         * Dispatch the event with the given arguments.
         *
         * @return void
         */
        public static function dispatch()
        {
            return event(new static(...func_get_args()));
        }

        /**
         * Broadcast the event with the given arguments.
         *
         * @return \Illuminate\Broadcasting\PendingBroadcast
         */
        public static function broadcast()
        {
            return app(Illuminate\Contracts\Broadcasting\Factory::class)->event(new static(...func_get_args()));
        }
    }
}
