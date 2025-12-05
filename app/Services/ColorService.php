<?php

namespace App\Services;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ColorService
{
    protected $imageManager;

    public function __construct()
    {
        $this->imageManager = new ImageManager(new Driver());
    }

    /**
     * Convert hex to RGB
     */
    public function hexToRgb(string $hex): array
    {
        $hex = str_replace('#', '', $hex);

        if (strlen($hex) == 3) {
            $r = hexdec(str_repeat(substr($hex, 0, 1), 2));
            $g = hexdec(str_repeat(substr($hex, 1, 1), 2));
            $b = hexdec(str_repeat(substr($hex, 2, 1), 2));
        } else {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
        }

        return ['r' => $r, 'g' => $g, 'b' => $b];
    }

    /**
     * Convert RGB to HEX
     */
    public function rgbToHex(array $rgb): string
    {
        return sprintf("#%02x%02x%02x", $rgb['r'], $rgb['g'], $rgb['b']);
    }

    /**
     * Convert RGB to HSL
     */
    public function rgbToHsl(array $rgb): array
    {
        $r = $rgb['r'] / 255;
        $g = $rgb['g'] / 255;
        $b = $rgb['b'] / 255;

        $max = max($r, $g, $b);
        $min = min($r, $g, $b);
        $h = $s = $l = ($max + $min) / 2;

        if ($max == $min) {
            $h = $s = 0;
        } else {
            $d = $max - $min;
            $s = $l > 0.5 ? $d / (2 - $max - $min) : $d / ($max + $min);

            switch ($max) {
                case $r:
                    $h = ($g - $b) / $d + ($g < $b ? 6 : 0);
                    break;
                case $g:
                    $h = ($b - $r) / $d + 2;
                    break;
                case $b:
                    $h = ($r - $g) / $d + 4;
                    break;
            }

            $h /= 6;
        }

        return [
            'h' => round($h * 360),
            's' => round($s * 100),
            'l' => round($l * 100)
        ];
    }

    /**
     * Convert RGB to CMYK
     */
    public function rgbToCmyk(array $rgb): array
    {
        $r = $rgb['r'] / 255;
        $g = $rgb['g'] / 255;
        $b = $rgb['b'] / 255;

        $k = 1 - max($r, $g, $b);

        if ($k == 1) {
            return ['c' => 0, 'm' => 0, 'y' => 0, 'k' => 100];
        }

        $c = (1 - $r - $k) / (1 - $k);
        $m = (1 - $g - $k) / (1 - $k);
        $y = (1 - $b - $k) / (1 - $k);

        return [
            'c' => round($c * 100),
            'm' => round($m * 100),
            'y' => round($y * 100),
            'k' => round($k * 100)
        ];
    }

    /**
     * Mix two colors
     */
    public function mixColors(string $hex1, string $hex2, float $ratio = 0.5): array
    {
        $rgb1 = $this->hexToRgb($hex1);
        $rgb2 = $this->hexToRgb($hex2);

        $mixedRgb = [
            'r' => round($rgb1['r'] * (1 - $ratio) + $rgb2['r'] * $ratio),
            'g' => round($rgb1['g'] * (1 - $ratio) + $rgb2['g'] * $ratio),
            'b' => round($rgb1['b'] * (1 - $ratio) + $rgb2['b'] * $ratio)
        ];

        $mixedHex = $this->rgbToHex($mixedRgb);
        $mixedHsl = $this->rgbToHsl($mixedRgb);
        $mixedCmyk = $this->rgbToCmyk($mixedRgb);

        return [
            'hex' => $mixedHex,
            'rgb' => $mixedRgb,
            'hsl' => $mixedHsl,
            'cmyk' => $mixedCmyk
        ];
    }

    /**
     * Extract colors from image
     */
    public function extractFromImage($image, int $colorCount = 5): array
    {
        $img = $this->imageManager->read($image);
        $img->resize(100, 100); // Resize for faster processing

        $width = $img->width();
        $height = $img->height();

        $colors = [];

        // Sample pixels
        for ($x = 0; $x < $width; $x += 10) {
            for ($y = 0; $y < $height; $y += 10) {
                $pixelColor = $img->pickColor($x, $y);
                $hex = sprintf("#%02x%02x%02x", $pixelColor[0], $pixelColor[1], $pixelColor[2]);

                if (!isset($colors[$hex])) {
                    $colors[$hex] = 0;
                }
                $colors[$hex]++;
            }
        }

        // Sort by frequency and get top colors
        arsort($colors);
        $topColors = array_slice(array_keys($colors), 0, $colorCount);

        $result = [];
        foreach ($topColors as $hex) {
            $rgb = $this->hexToRgb($hex);
            $result[] = [
                'hex' => $hex,
                'rgb' => $rgb,
                'hsl' => $this->rgbToHsl($rgb),
                'cmyk' => $this->rgbToCmyk($rgb),
                'percentage' => round(($colors[$hex] / array_sum($colors)) * 100, 2)
            ];
        }

        return $result;
    }

    /**
     * Find similar colors
     */
    public function findSimilar(string $hex, float $threshold = 20): array
    {
        $targetRgb = $this->hexToRgb($hex);
        $similar = [];

        $colors = \App\Models\Color::all();

        foreach ($colors as $color) {
            $colorRgb = $this->hexToRgb($color->hex);

            // Calculate Euclidean distance in RGB space
            $distance = sqrt(
                pow($targetRgb['r'] - $colorRgb['r'], 2) +
                pow($targetRgb['g'] - $colorRgb['g'], 2) +
                pow($targetRgb['b'] - $colorRgb['b'], 2)
            );

            if ($distance <= $threshold) {
                $similar[] = [
                    'color' => $color,
                    'distance' => $distance
                ];
            }
        }

        // Sort by distance
        usort($similar, function ($a, $b) {
            return $a['distance'] <=> $b['distance'];
        });

        return $similar;
    }

    /**
     * Generate shades (adding black)
     */
    public function generateShades(string $hex, int $steps = 10): array
    {
        $rgb = $this->hexToRgb($hex);
        $shades = [];

        for ($i = 0; $i <= $steps; $i++) {
            $ratio = $i / $steps;
            $shadeRgb = [
                'r' => round($rgb['r'] * (1 - $ratio)),
                'g' => round($rgb['g'] * (1 - $ratio)),
                'b' => round($rgb['b'] * (1 - $ratio))
            ];

            $shades[] = [
                'hex' => $this->rgbToHex($shadeRgb),
                'rgb' => $shadeRgb,
                'percentage' => $ratio * 100
            ];
        }

        return $shades;
    }

    /**
     * Generate tints (adding white)
     */
    public function generateTints(string $hex, int $steps = 10): array
    {
        $rgb = $this->hexToRgb($hex);
        $tints = [];

        for ($i = 0; $i <= $steps; $i++) {
            $ratio = $i / $steps;
            $tintRgb = [
                'r' => round($rgb['r'] + (255 - $rgb['r']) * $ratio),
                'g' => round($rgb['g'] + (255 - $rgb['g']) * $ratio),
                'b' => round($rgb['b'] + (255 - $rgb['b']) * $ratio)
            ];

            $tints[] = [
                'hex' => $this->rgbToHex($tintRgb),
                'rgb' => $tintRgb,
                'percentage' => $ratio * 100
            ];
        }

        return $tints;
    }

    /**
     * Generate monochromatic colors
     */
    public function generateMonochromatic(string $hex, int $variations = 5): array
    {
        $hsl = $this->hexToHsl($hex);
        $monochromatic = [];

        $lightnessSteps = 100 / ($variations + 1);

        for ($i = 1; $i <= $variations; $i++) {
            $lightness = $lightnessSteps * $i;
            $newHsl = [
                'h' => $hsl['h'],
                's' => $hsl['s'],
                'l' => $lightness
            ];

            $newRgb = $this->hslToRgb($newHsl);
            $newHex = $this->rgbToHex($newRgb);

            $monochromatic[] = [
                'hex' => $newHex,
                'rgb' => $newRgb,
                'hsl' => $newHsl,
                'lightness' => $lightness
            ];
        }

        return $monochromatic;
    }

    /**
     * Generate analogous colors
     */
    public function generateAnalogous(string $hex, int $colors = 3, int $angle = 30): array
    {
        $hsl = $this->hexToHsl($hex);
        $analogous = [];

        // Center color
        $analogous[] = [
            'hex' => $hex,
            'hsl' => $hsl,
            'position' => 'center'
        ];

        // Generate colors on both sides
        for ($i = 1; $i <= floor(($colors - 1) / 2); $i++) {
            // Left side
            $leftHsl = $hsl;
            $leftHsl['h'] = ($hsl['h'] - ($angle * $i) + 360) % 360;
            $leftRgb = $this->hslToRgb($leftHsl);

            $analogous[] = [
                'hex' => $this->rgbToHex($leftRgb),
                'hsl' => $leftHsl,
                'rgb' => $leftRgb,
                'position' => 'left-' . $i
            ];

            // Right side
            $rightHsl = $hsl;
            $rightHsl['h'] = ($hsl['h'] + ($angle * $i)) % 360;
            $rightRgb = $this->hslToRgb($rightHsl);

            $analogous[] = [
                'hex' => $this->rgbToHex($rightRgb),
                'hsl' => $rightHsl,
                'rgb' => $rightRgb,
                'position' => 'right-' . $i
            ];
        }

        // Sort by hue
        usort($analogous, function ($a, $b) {
            return $a['hsl']['h'] <=> $b['hsl']['h'];
        });

        return $analogous;
    }

    /**
     * Generate complementary color
     */
    public function generateComplementary(string $hex): array
    {
        $hsl = $this->hexToHsl($hex);

        // Complementary is 180 degrees opposite
        $complementaryHsl = [
            'h' => ($hsl['h'] + 180) % 360,
            's' => $hsl['s'],
            'l' => $hsl['l']
        ];

        $complementaryRgb = $this->hslToRgb($complementaryHsl);

        return [
            'original' => [
                'hex' => $hex,
                'hsl' => $hsl,
                'rgb' => $this->hexToRgb($hex)
            ],
            'complementary' => [
                'hex' => $this->rgbToHex($complementaryRgb),
                'hsl' => $complementaryHsl,
                'rgb' => $complementaryRgb
            ]
        ];
    }

    /**
     * Generate triadic colors
     */
    public function generateTriadic(string $hex): array
    {
        $hsl = $this->hexToHsl($hex);

        $triadic = [];
        $triadic[] = [
            'hex' => $hex,
            'hsl' => $hsl,
            'position' => 'primary'
        ];

        // Second color (120 degrees)
        $secondHsl = [
            'h' => ($hsl['h'] + 120) % 360,
            's' => $hsl['s'],
            'l' => $hsl['l']
        ];
        $secondRgb = $this->hslToRgb($secondHsl);

        $triadic[] = [
            'hex' => $this->rgbToHex($secondRgb),
            'hsl' => $secondHsl,
            'rgb' => $secondRgb,
            'position' => 'secondary'
        ];

        // Third color (240 degrees)
        $thirdHsl = [
            'h' => ($hsl['h'] + 240) % 360,
            's' => $hsl['s'],
            'l' => $hsl['l']
        ];
        $thirdRgb = $this->hslToRgb($thirdHsl);

        $triadic[] = [
            'hex' => $this->rgbToHex($thirdRgb),
            'hsl' => $thirdHsl,
            'rgb' => $thirdRgb,
            'position' => 'tertiary'
        ];

        return $triadic;
    }

    /**
     * Generate split complementary colors
     */
    public function generateSplitComplementary(string $hex, int $angle = 30): array
    {
        $hsl = $this->hexToHsl($hex);

        $splitComp = [];
        $splitComp[] = [
            'hex' => $hex,
            'hsl' => $hsl,
            'position' => 'primary'
        ];

        // Left split
        $leftHsl = [
            'h' => ($hsl['h'] + 180 - $angle + 360) % 360,
            's' => $hsl['s'],
            'l' => $hsl['l']
        ];
        $leftRgb = $this->hslToRgb($leftHsl);

        $splitComp[] = [
            'hex' => $this->rgbToHex($leftRgb),
            'hsl' => $leftHsl,
            'rgb' => $leftRgb,
            'position' => 'left'
        ];

        // Right split
        $rightHsl = [
            'h' => ($hsl['h'] + 180 + $angle) % 360,
            's' => $hsl['s'],
            'l' => $hsl['l']
        ];
        $rightRgb = $this->hslToRgb($rightHsl);

        $splitComp[] = [
            'hex' => $this->rgbToHex($rightRgb),
            'hsl' => $rightHsl,
            'rgb' => $rightRgb,
            'position' => 'right'
        ];

        return $splitComp;
    }

    /**
     * Generate tetradic (rectangular) colors
     */
    public function generateTetradic(string $hex, int $angle = 90): array
    {
        $hsl = $this->hexToHsl($hex);

        $tetradic = [];
        $tetradic[] = [
            'hex' => $hex,
            'hsl' => $hsl,
            'position' => 'primary'
        ];

        // Second color
        $secondHsl = [
            'h' => ($hsl['h'] + $angle) % 360,
            's' => $hsl['s'],
            'l' => $hsl['l']
        ];
        $secondRgb = $this->hslToRgb($secondHsl);

        $tetradic[] = [
            'hex' => $this->rgbToHex($secondRgb),
            'hsl' => $secondHsl,
            'rgb' => $secondRgb,
            'position' => 'secondary'
        ];

        // Third color (opposite of primary)
        $thirdHsl = [
            'h' => ($hsl['h'] + 180) % 360,
            's' => $hsl['s'],
            'l' => $hsl['l']
        ];
        $thirdRgb = $this->hslToRgb($thirdHsl);

        $tetradic[] = [
            'hex' => $this->rgbToHex($thirdRgb),
            'hsl' => $thirdHsl,
            'rgb' => $thirdRgb,
            'position' => 'tertiary'
        ];

        // Fourth color (opposite of second)
        $fourthHsl = [
            'h' => ($secondHsl['h'] + 180) % 360,
            's' => $hsl['s'],
            'l' => $hsl['l']
        ];
        $fourthRgb = $this->hslToRgb($fourthHsl);

        $tetradic[] = [
            'hex' => $this->rgbToHex($fourthRgb),
            'hsl' => $fourthHsl,
            'rgb' => $fourthRgb,
            'position' => 'quaternary'
        ];

        return $tetradic;
    }

    /**
     * Generate square colors (special case of tetradic with 90-degree spacing)
     */
    public function generateSquare(string $hex): array
    {
        return $this->generateTetradic($hex, 90);
    }

    /**
     * Convert HEX to HSL
     */
    public function hexToHsl(string $hex): array
    {
        $rgb = $this->hexToRgb($hex);
        return $this->rgbToHsl($rgb);
    }

    /**
     * Convert HSL to RGB
     */
    public function hslToRgb(array $hsl): array
    {
        $h = $hsl['h'] / 360;
        $s = $hsl['s'] / 100;
        $l = $hsl['l'] / 100;

        if ($s == 0) {
            $r = $g = $b = $l;
        } else {
            $q = $l < 0.5 ? $l * (1 + $s) : $l + $s - $l * $s;
            $p = 2 * $l - $q;

            $r = $this->hueToRgb($p, $q, $h + 1/3);
            $g = $this->hueToRgb($p, $q, $h);
            $b = $this->hueToRgb($p, $q, $h - 1/3);
        }

        return [
            'r' => round($r * 255),
            'g' => round($g * 255),
            'b' => round($b * 255)
        ];
    }

    private function hueToRgb($p, $q, $t): float
    {
        if ($t < 0) $t += 1;
        if ($t > 1) $t -= 1;

        if ($t < 1/6) return $p + ($q - $p) * 6 * $t;
        if ($t < 1/2) return $q;
        if ($t < 2/3) return $p + ($q - $p) * (2/3 - $t) * 6;

        return $p;
    }

    /**
     * Get all color harmonies for a given color
     */
    public function getAllHarmonies(string $hex): array
    {
        return [
            'shades' => $this->generateShades($hex),
            'tints' => $this->generateTints($hex),
            'monochromatic' => $this->generateMonochromatic($hex),
            'analogous' => $this->generateAnalogous($hex),
            'complementary' => $this->generateComplementary($hex),
            'triadic' => $this->generateTriadic($hex),
            'split_complementary' => $this->generateSplitComplementary($hex),
            'tetradic' => $this->generateTetradic($hex),
            'square' => $this->generateSquare($hex)
        ];
    }

    /**
     * Calculate color brightness
     */
    public function calculateBrightness(string $hex): float
    {
        $rgb = $this->hexToRgb($hex);
        return (0.299 * $rgb['r'] + 0.587 * $rgb['g'] + 0.114 * $rgb['b']) / 255;
    }

    /**
     * Check if text should be dark or light on this background
     */
    public function getTextColor(string $bgHex): string
    {
        $brightness = $this->calculateBrightness($bgHex);
        return $brightness > 0.5 ? '#000000' : '#FFFFFF';
    }
}
