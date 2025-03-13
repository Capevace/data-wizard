<?php

return [
    // Set the async timeout to something large as LLM calls can take a long time.
    'timeout' => 600,

    /*
     * Default output length of async processes.
     */
    'defaultOutputLength' => 1024 * 100,
];
