<?php

declare(strict_types=1);

use PerfectDrive\Referable\ReferableFinder\ReferableFinder;

it('skips classes that fail to autoload and still returns valid classes', function () {
    // Point the finder at a directory containing both a valid Referable
    // (tests/Enums) and a known-broken class (tests/BrokenAutoload/
    // BrokenModel extends a non-existent parent). The finder must catch
    // the autoload failure, skip the broken class, and still return the
    // valid ones — instead of crashing the entire route-registration
    // pass.
    //
    // The triggered autoload writes to error_log() (the finder's silent-
    // catch breadcrumb); we redirect it to a temp file so it doesn't leak
    // into Pest's stderr and mark the test "risky".
    $tmpLog = tempnam(sys_get_temp_dir(), 'referable-test-');
    if ($tmpLog === false) {
        $this->fail('Unable to create temp file for error_log redirection.');
    }
    $originalErrorLog = ini_set('error_log', $tmpLog);

    try {
        $result = ReferableFinder::all(
            directories: [
                __DIR__.'/Enums',
                __DIR__.'/BrokenAutoload',
            ],
            basePath: realpath(__DIR__),
            baseNamespace: 'PerfectDrive\\Referable\\Tests',
        );

        expect($result->all())
            ->toContain('PerfectDrive\Referable\Tests\Enums\BasicReferableEnum')
            ->toContain('PerfectDrive\Referable\Tests\Enums\LabelReferableEnum')
            ->not->toContain('PerfectDrive\Referable\Tests\BrokenAutoload\BrokenModel');
    } finally {
        ini_set('error_log', $originalErrorLog === false ? '' : $originalErrorLog);
        @unlink($tmpLog);
    }
});
