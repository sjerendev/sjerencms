<?php

use App\Support\TextMotion;
use Illuminate\Support\Facades\Blade;

it('prints nothing when motion is off or missing', function ($settings) {
    expect((string) TextMotion::attributes($settings))->toBe('');
})->with([null, [[]], [['enabled' => false, 'from' => 'left']]]);

it('fills in the defaults when motion is turned on', function () {
    expect(TextMotion::normalize(['enabled' => true]))->toEqual([
        'from' => 'bottom', 'distance' => 24, 'split' => 'none', 'stagger' => 0.06,
        'duration' => 0.8, 'delay' => 0, 'ease' => 'soft', 'mask' => false,
    ]);
});

it('replaces invalid values and clamps numbers', function () {
    expect(TextMotion::normalize(['enabled' => true, 'from' => 'diagonal', 'distance' => 9999,
        'split' => 'sentences', 'duration' => '0', 'delay' => -2, 'ease' => 'wobbly']))
        ->toMatchArray(['from' => 'bottom', 'distance' => 400, 'split' => 'none',
            'duration' => 0.1, 'delay' => 0, 'ease' => 'soft']);
});

it('prints the motion attributes with escaped json', function () {
    $html = (string) TextMotion::attributes(['enabled' => true, 'split' => 'words']);

    expect($html)->toStartWith(' data-motion="text" data-text-motion="')
        ->and($html)->toContain('&quot;split&quot;:&quot;words&quot;');
});

it('reads the key from $motion in a view, and works without it', function () {
    expect(Blade::render('<p @textMotion(\'intro_one\')>x</p>', ['motion' => ['intro_one' => ['enabled' => true]]]))
        ->toContain('<p  data-motion="text"');
    expect(Blade::render('<p @textMotion(\'intro_one\')>x</p>'))->toBe('<p >x</p>');
});
