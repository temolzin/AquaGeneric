<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;

trait CreatesApplication
{
    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $this->generateEphemeralSecrets();

        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        return $app;
    }

    /**
     * Provide throwaway secrets for the test run.
     *
     * They are generated on every execution so no key is stored in the
     * repository. Values already defined by the environment are respected.
     *
     * @return void
     */
    protected function generateEphemeralSecrets()
    {
        $secrets = [
            'APP_KEY' => 'base64:'.base64_encode(random_bytes(32)),
            'TOKEN_SECRET_KEY' => bin2hex(random_bytes(32)),
        ];

        foreach ($secrets as $name => $value) {
            if ($this->hasEnvironmentValue($name)) {
                continue;
            }

            $_SERVER[$name] = $value;
            $_ENV[$name] = $value;
            putenv($name.'='.$value);
        }
    }

    /**
     * Determine whether the environment already defines a non-empty value.
     *
     * @param  string  $name
     * @return bool
     */
    protected function hasEnvironmentValue($name)
    {
        foreach ([$_SERVER, $_ENV] as $source) {
            if (!empty($source[$name])) {
                return true;
            }
        }

        $value = getenv($name);

        return $value !== false && $value !== '';
    }
}
