<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ColorController extends Controller
{
    protected $imageManager;

    public function __construct()
    {
        $this->imageManager = new ImageManager(new Driver());
    }

    public function extract(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'color_count' => 'nullable|integer|min:3|max:12',
            'accuracy' => 'nullable|in:low,medium,high'
        ]);

        try {
            $image = $request->file('image');
            $colorCount = $request->input('color_count', 5);
            $accuracy = $request->input('accuracy', 'medium');

            // Save the image
            $path = $image->store('uploads/images', 'public');

            // Extract colors
            $colors = $this->extractColorsFromImage($image, $colorCount, $accuracy);

            return response()->json([
                'success' => true,
                'colors' => $colors,
                'image_url' => Storage::url($path),
                'message' => 'Successfully extracted ' . count($colors) . ' colors'
            ]);

        } catch (\Exception $e) {
            \Log::error('Color extraction error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error extracting colors: ' . $e->getMessage()
            ], 500);
        }
    }

    private function extractColorsFromImage($image, $colorCount = 5, $accuracy = 'medium')
    {
        // Create image instance
        $img = $this->imageManager->read($image);

        // Resize based on accuracy
        switch ($accuracy) {
            case 'low':
                $img->resize(50, 50);
                $step = 5;
                break;
            case 'high':
                $img->resize(200, 200);
                $step = 2;
                break;
            default: // medium
                $img->resize(100, 100);
                $step = 4;
                break;
        }

        $width = $img->width();
        $height = $img->height();
        $colors = [];
        $totalPixels = 0;

        // Sample pixels
        for ($x = 0; $x < $width; $x += $step) {
            for ($y = 0; $y < $height; $y += $step) {
                // Get pixel color - trả về đối tượng Color
                $colorObject = $img->pickColor($x, $y);

                // Lấy giá trị RGB từ đối tượng Color
                // Sử dụng ->value() để lấy giá trị số từ Channel object
                $r = $colorObject->red()->value();
                $g = $colorObject->green()->value();
                $b = $colorObject->blue()->value();

                // Convert to hex
                $hex = sprintf("#%02x%02x%02x", $r, $g, $b);

                // Simplify color to reduce variations
                $simplifiedColor = $this->simplifyColor($hex);

                if (!isset($colors[$simplifiedColor])) {
                    $colors[$simplifiedColor] = 0;
                }
                $colors[$simplifiedColor]++;
                $totalPixels++;
            }
        }

        // Sort by frequency and get top colors
        arsort($colors);
        $topColors = array_slice(array_keys($colors), 0, $colorCount);

        $result = [];
        foreach ($topColors as $hex) {
            $rgb = $this->hexToRgb($hex);
            $percentage = round(($colors[$hex] / $totalPixels) * 100, 1);

            $result[] = [
                'hex' => $hex,
                'rgb' => $rgb,
                'percentage' => $percentage
            ];
        }

        return $result;
    }

    private function simplifyColor($hex)
    {
        // Group similar colors by rounding RGB values
        $rgb = $this->hexToRgb($hex);

        // Round to nearest 32 to reduce color variations (32 steps in 0-255 range)
        $roundedRgb = [
            'r' => round($rgb['r'] / 32) * 32,
            'g' => round($rgb['g'] / 32) * 32,
            'b' => round($rgb['b'] / 32) * 32
        ];

        return $this->rgbToHex($roundedRgb['r'], $roundedRgb['g'], $roundedRgb['b']);
    }

    private function hexToRgb($hex)
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

    private function rgbToHex($r, $g, $b)
    {
        return '#' . sprintf("%02x%02x%02x", $r, $g, $b);
    }

    public function convert(Request $request)
    {
        $request->validate([
            'color' => 'required',
            'from' => 'required|in:hex,rgb,hsl',
            'to' => 'required|in:hex,rgb,hsl'
        ]);

        $color = $request->color;
        $from = $request->from;
        $to = $request->to;

        $result = $this->convertColor($color, $from, $to);

        return response()->json([
            'success' => true,
            'result' => $result
        ]);
    }

    private function convertColor($color, $from, $to)
    {
        if ($from === $to) {
            return $color;
        }

        if ($from === 'hex' && $to === 'rgb') {
            $rgb = $this->hexToRgb($color);
            return "rgb({$rgb['r']}, {$rgb['g']}, {$rgb['b']})";
        }

        if ($from === 'hex' && $to === 'hsl') {
            $rgb = $this->hexToRgb($color);
            $hsl = $this->rgbToHsl($rgb['r'], $rgb['g'], $rgb['b']);
            return "hsl({$hsl['h']}, {$hsl['s']}%, {$hsl['l']}%)";
        }

        if ($from === 'rgb' && $to === 'hex') {
            preg_match('/rgb\((\d+),\s*(\d+),\s*(\d+)\)/', $color, $matches);
            if (count($matches) === 4) {
                $hex = $this->rgbToHex($matches[1], $matches[2], $matches[3]);
                return $hex;
            }
        }

        if ($from === 'rgb' && $to === 'hsl') {
            preg_match('/rgb\((\d+),\s*(\d+),\s*(\d+)\)/', $color, $matches);
            if (count($matches) === 4) {
                $hsl = $this->rgbToHsl($matches[1], $matches[2], $matches[3]);
                return "hsl({$hsl['h']}, {$hsl['s']}%, {$hsl['l']}%)";
            }
        }

        if ($from === 'hsl' && $to === 'hex') {
            preg_match('/hsl\((\d+),\s*(\d+)%,\s*(\d+)%\)/', $color, $matches);
            if (count($matches) === 4) {
                $rgb = $this->hslToRgb($matches[1], $matches[2], $matches[3]);
                $hex = $this->rgbToHex($rgb['r'], $rgb['g'], $rgb['b']);
                return $hex;
            }
        }

        if ($from === 'hsl' && $to === 'rgb') {
            preg_match('/hsl\((\d+),\s*(\d+)%,\s*(\d+)%\)/', $color, $matches);
            if (count($matches) === 4) {
                $rgb = $this->hslToRgb($matches[1], $matches[2], $matches[3]);
                return "rgb({$rgb['r']}, {$rgb['g']}, {$rgb['b']})";
            }
        }

        return $color;
    }

    private function rgbToHsl($r, $g, $b)
    {
        $r /= 255; $g /= 255; $b /= 255;
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

    private function hslToRgb($h, $s, $l)
    {
        $h /= 360;
        $s /= 100;
        $l /= 100;

        if ($s == 0) {
            $r = $g = $b = $l;
        } else {
            $hue2rgb = function($p, $q, $t) {
                if ($t < 0) $t += 1;
                if ($t > 1) $t -= 1;
                if ($t < 1/6) return $p + ($q - $p) * 6 * $t;
                if ($t < 1/2) return $q;
                if ($t < 2/3) return $p + ($q - $p) * (2/3 - $t) * 6;
                return $p;
            };

            $q = $l < 0.5 ? $l * (1 + $s) : $l + $s - $l * $s;
            $p = 2 * $l - $q;

            $r = $hue2rgb($p, $q, $h + 1/3);
            $g = $hue2rgb($p, $q, $h);
            $b = $hue2rgb($p, $q, $h - 1/3);
        }

        return [
            'r' => round($r * 255),
            'g' => round($g * 255),
            'b' => round($b * 255)
        ];
    }

    public function search(Request $request)
    {
        $query = $request->input('query', '');
        $category = $request->input('category', '');
        $sort = $request->input('sort', 'name');
        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 12);

        // Get color database
        $colors = $this->getColorDatabase();

        // Filter colors
        $filtered = collect($colors)->filter(function($color) use ($query, $category) {
            $matches = true;

            if ($query) {
                $matches = $matches && (
                    stripos($color['name'], $query) !== false ||
                    stripos($color['hex'], $query) !== false
                );
            }

            if ($category) {
                $matches = $matches && ($color['category'] === $category);
            }

            return $matches;
        });

        // Sort colors
        switch ($sort) {
            case 'name_desc':
                $filtered = $filtered->sortByDesc('name');
                break;
            case 'hex':
                $filtered = $filtered->sortBy('hex');
                break;
            case 'category':
                $filtered = $filtered->sortBy('category');
                break;
            case 'popular':
                // For demo, random sort for "popular"
                $filtered = $filtered->shuffle();
                break;
            default: // name
                $filtered = $filtered->sortBy('name');
                break;
        }

        // Paginate
        $total = $filtered->count();
        $paginated = $filtered->forPage($page, $perPage)->values();

        return response()->json([
            'success' => true,
            'colors' => $paginated,
            'count' => $paginated->count(),
            'total' => $total,
            'total_pages' => ceil($total / $perPage),
            'current_page' => $page
        ]);
    }

    private function getColorDatabase()
    {
        return [
            // Red colors
            ['name' => 'Indian Red', 'hex' => '#CD5C5C', 'rgb' => '205,92,92', 'category' => 'Red'],
            ['name' => 'Light Coral', 'hex' => '#F08080', 'rgb' => '240,128,128', 'category' => 'Red'],
            ['name' => 'Salmon', 'hex' => '#FA8072', 'rgb' => '250,128,114', 'category' => 'Red'],
            ['name' => 'Dark Salmon', 'hex' => '#E9967A', 'rgb' => '233,150,122', 'category' => 'Red'],
            ['name' => 'Light Salmon', 'hex' => '#FFA07A', 'rgb' => '255,160,122', 'category' => 'Red'],
            ['name' => 'Crimson', 'hex' => '#DC143C', 'rgb' => '220,20,60', 'category' => 'Red'],
            ['name' => 'Fire Brick', 'hex' => '#B22222', 'rgb' => '178,34,34', 'category' => 'Red'],
            ['name' => 'Dark Red', 'hex' => '#8B0000', 'rgb' => '139,0,0', 'category' => 'Red'],

            // Pink colors
            ['name' => 'Pink', 'hex' => '#FFC0CB', 'rgb' => '255,192,203', 'category' => 'Pink'],
            ['name' => 'Light Pink', 'hex' => '#FFB6C1', 'rgb' => '255,182,193', 'category' => 'Pink'],
            ['name' => 'Hot Pink', 'hex' => '#FF69B4', 'rgb' => '255,105,180', 'category' => 'Pink'],
            ['name' => 'Deep Pink', 'hex' => '#FF1493', 'rgb' => '255,20,147', 'category' => 'Pink'],
            ['name' => 'Medium Violet Red', 'hex' => '#C71585', 'rgb' => '199,21,133', 'category' => 'Pink'],
            ['name' => 'Pale Violet Red', 'hex' => '#DB7093', 'rgb' => '219,112,147', 'category' => 'Pink'],

            // Orange colors
            ['name' => 'Coral', 'hex' => '#FF7F50', 'rgb' => '255,127,80', 'category' => 'Orange'],
            ['name' => 'Tomato', 'hex' => '#FF6347', 'rgb' => '255,99,71', 'category' => 'Orange'],
            ['name' => 'Orange Red', 'hex' => '#FF4500', 'rgb' => '255,69,0', 'category' => 'Orange'],
            ['name' => 'Dark Orange', 'hex' => '#FF8C00', 'rgb' => '255,140,0', 'category' => 'Orange'],
            ['name' => 'Orange', 'hex' => '#FFA500', 'rgb' => '255,165,0', 'category' => 'Orange'],

            // Yellow colors
            ['name' => 'Gold', 'hex' => '#FFD700', 'rgb' => '255,215,0', 'category' => 'Yellow'],
            ['name' => 'Yellow', 'hex' => '#FFFF00', 'rgb' => '255,255,0', 'category' => 'Yellow'],
            ['name' => 'Light Yellow', 'hex' => '#FFFFE0', 'rgb' => '255,255,224', 'category' => 'Yellow'],
            ['name' => 'Lemon Chiffon', 'hex' => '#FFFACD', 'rgb' => '255,250,205', 'category' => 'Yellow'],
            ['name' => 'Light Goldenrod Yellow', 'hex' => '#FAFAD2', 'rgb' => '250,250,210', 'category' => 'Yellow'],
            ['name' => 'Papaya Whip', 'hex' => '#FFEFD5', 'rgb' => '255,239,213', 'category' => 'Yellow'],
            ['name' => 'Moccasin', 'hex' => '#FFE4B5', 'rgb' => '255,228,181', 'category' => 'Yellow'],
            ['name' => 'Peach Puff', 'hex' => '#FFDAB9', 'rgb' => '255,218,185', 'category' => 'Yellow'],
            ['name' => 'Pale Goldenrod', 'hex' => '#EEE8AA', 'rgb' => '238,232,170', 'category' => 'Yellow'],
            ['name' => 'Khaki', 'hex' => '#F0E68C', 'rgb' => '240,230,140', 'category' => 'Yellow'],
            ['name' => 'Dark Khaki', 'hex' => '#BDB76B', 'rgb' => '189,183,107', 'category' => 'Yellow'],

            // Purple colors
            ['name' => 'Lavender', 'hex' => '#E6E6FA', 'rgb' => '230,230,250', 'category' => 'Purple'],
            ['name' => 'Thistle', 'hex' => '#D8BFD8', 'rgb' => '216,191,216', 'category' => 'Purple'],
            ['name' => 'Plum', 'hex' => '#DDA0DD', 'rgb' => '221,160,221', 'category' => 'Purple'],
            ['name' => 'Violet', 'hex' => '#EE82EE', 'rgb' => '238,130,238', 'category' => 'Purple'],
            ['name' => 'Orchid', 'hex' => '#DA70D6', 'rgb' => '218,112,214', 'category' => 'Purple'],
            ['name' => 'Magenta', 'hex' => '#FF00FF', 'rgb' => '255,0,255', 'category' => 'Purple'],
            ['name' => 'Medium Orchid', 'hex' => '#BA55D3', 'rgb' => '186,85,211', 'category' => 'Purple'],
            ['name' => 'Medium Purple', 'hex' => '#9370DB', 'rgb' => '147,112,219', 'category' => 'Purple'],
            ['name' => 'Blue Violet', 'hex' => '#8A2BE2', 'rgb' => '138,43,226', 'category' => 'Purple'],
            ['name' => 'Dark Violet', 'hex' => '#9400D3', 'rgb' => '148,0,211', 'category' => 'Purple'],
            ['name' => 'Dark Orchid', 'hex' => '#9932CC', 'rgb' => '153,50,204', 'category' => 'Purple'],
            ['name' => 'Dark Magenta', 'hex' => '#8B008B', 'rgb' => '139,0,139', 'category' => 'Purple'],
            ['name' => 'Purple', 'hex' => '#800080', 'rgb' => '128,0,128', 'category' => 'Purple'],
            ['name' => 'Rebecca Purple', 'hex' => '#663399', 'rgb' => '102,51,153', 'category' => 'Purple'],
            ['name' => 'Indigo', 'hex' => '#4B0082', 'rgb' => '75,0,130', 'category' => 'Purple'],
            ['name' => 'Medium Slate Blue', 'hex' => '#7B68EE', 'rgb' => '123,104,238', 'category' => 'Purple'],
            ['name' => 'Slate Blue', 'hex' => '#6A5ACD', 'rgb' => '106,90,205', 'category' => 'Purple'],
            ['name' => 'Dark Slate Blue', 'hex' => '#483D8B', 'rgb' => '72,61,139', 'category' => 'Purple'],

            // Green colors
            ['name' => 'Green Yellow', 'hex' => '#ADFF2F', 'rgb' => '173,255,47', 'category' => 'Green'],
            ['name' => 'Chartreuse', 'hex' => '#7FFF00', 'rgb' => '127,255,0', 'category' => 'Green'],
            ['name' => 'Lawn Green', 'hex' => '#7CFC00', 'rgb' => '124,252,0', 'category' => 'Green'],
            ['name' => 'Lime', 'hex' => '#00FF00', 'rgb' => '0,255,0', 'category' => 'Green'],
            ['name' => 'Lime Green', 'hex' => '#32CD32', 'rgb' => '50,205,50', 'category' => 'Green'],
            ['name' => 'Pale Green', 'hex' => '#98FB98', 'rgb' => '152,251,152', 'category' => 'Green'],
            ['name' => 'Light Green', 'hex' => '#90EE90', 'rgb' => '144,238,144', 'category' => 'Green'],
            ['name' => 'Medium Spring Green', 'hex' => '#00FA9A', 'rgb' => '0,250,154', 'category' => 'Green'],
            ['name' => 'Spring Green', 'hex' => '#00FF7F', 'rgb' => '0,255,127', 'category' => 'Green'],
            ['name' => 'Medium Sea Green', 'hex' => '#3CB371', 'rgb' => '60,179,113', 'category' => 'Green'],
            ['name' => 'Sea Green', 'hex' => '#2E8B57', 'rgb' => '46,139,87', 'category' => 'Green'],
            ['name' => 'Forest Green', 'hex' => '#228B22', 'rgb' => '34,139,34', 'category' => 'Green'],
            ['name' => 'Green', 'hex' => '#008000', 'rgb' => '0,128,0', 'category' => 'Green'],
            ['name' => 'Dark Green', 'hex' => '#006400', 'rgb' => '0,100,0', 'category' => 'Green'],
            ['name' => 'Yellow Green', 'hex' => '#9ACD32', 'rgb' => '154,205,50', 'category' => 'Green'],
            ['name' => 'Olive Drab', 'hex' => '#6B8E23', 'rgb' => '107,142,35', 'category' => 'Green'],
            ['name' => 'Olive', 'hex' => '#808000', 'rgb' => '128,128,0', 'category' => 'Green'],
            ['name' => 'Dark Olive Green', 'hex' => '#556B2F', 'rgb' => '85,107,47', 'category' => 'Green'],
            ['name' => 'Medium Aquamarine', 'hex' => '#66CDAA', 'rgb' => '102,205,170', 'category' => 'Green'],
            ['name' => 'Dark Sea Green', 'hex' => '#8FBC8F', 'rgb' => '143,188,143', 'category' => 'Green'],
            ['name' => 'Light Sea Green', 'hex' => '#20B2AA', 'rgb' => '32,178,170', 'category' => 'Green'],
            ['name' => 'Dark Cyan', 'hex' => '#008B8B', 'rgb' => '0,139,139', 'category' => 'Green'],
            ['name' => 'Teal', 'hex' => '#008080', 'rgb' => '0,128,128', 'category' => 'Green'],

            // Blue colors
            ['name' => 'Aqua', 'hex' => '#00FFFF', 'rgb' => '0,255,255', 'category' => 'Blue'],
            ['name' => 'Cyan', 'hex' => '#00FFFF', 'rgb' => '0,255,255', 'category' => 'Blue'],
            ['name' => 'Light Cyan', 'hex' => '#E0FFFF', 'rgb' => '224,255,255', 'category' => 'Blue'],
            ['name' => 'Pale Turquoise', 'hex' => '#AFEEEE', 'rgb' => '175,238,238', 'category' => 'Blue'],
            ['name' => 'Aquamarine', 'hex' => '#7FFFD4', 'rgb' => '127,255,212', 'category' => 'Blue'],
            ['name' => 'Turquoise', 'hex' => '#40E0D0', 'rgb' => '64,224,208', 'category' => 'Blue'],
            ['name' => 'Medium Turquoise', 'hex' => '#48D1CC', 'rgb' => '72,209,204', 'category' => 'Blue'],
            ['name' => 'Dark Turquoise', 'hex' => '#00CED1', 'rgb' => '0,206,209', 'category' => 'Blue'],
            ['name' => 'Cadet Blue', 'hex' => '#5F9EA0', 'rgb' => '95,158,160', 'category' => 'Blue'],
            ['name' => 'Steel Blue', 'hex' => '#4682B4', 'rgb' => '70,130,180', 'category' => 'Blue'],
            ['name' => 'Light Steel Blue', 'hex' => '#B0C4DE', 'rgb' => '176,196,222', 'category' => 'Blue'],
            ['name' => 'Powder Blue', 'hex' => '#B0E0E6', 'rgb' => '176,224,230', 'category' => 'Blue'],
            ['name' => 'Light Blue', 'hex' => '#ADD8E6', 'rgb' => '173,216,230', 'category' => 'Blue'],
            ['name' => 'Sky Blue', 'hex' => '#87CEEB', 'rgb' => '135,206,235', 'category' => 'Blue'],
            ['name' => 'Light Sky Blue', 'hex' => '#87CEFA', 'rgb' => '135,206,250', 'category' => 'Blue'],
            ['name' => 'Deep Sky Blue', 'hex' => '#00BFFF', 'rgb' => '0,191,255', 'category' => 'Blue'],
            ['name' => 'Dodger Blue', 'hex' => '#1E90FF', 'rgb' => '30,144,255', 'category' => 'Blue'],
            ['name' => 'Cornflower Blue', 'hex' => '#6495ED', 'rgb' => '100,149,237', 'category' => 'Blue'],
            ['name' => 'Royal Blue', 'hex' => '#4169E1', 'rgb' => '65,105,225', 'category' => 'Blue'],
            ['name' => 'Blue', 'hex' => '#0000FF', 'rgb' => '0,0,255', 'category' => 'Blue'],
            ['name' => 'Medium Blue', 'hex' => '#0000CD', 'rgb' => '0,0,205', 'category' => 'Blue'],
            ['name' => 'Dark Blue', 'hex' => '#00008B', 'rgb' => '0,0,139', 'category' => 'Blue'],
            ['name' => 'Navy', 'hex' => '#000080', 'rgb' => '0,0,128', 'category' => 'Blue'],
            ['name' => 'Midnight Blue', 'hex' => '#191970', 'rgb' => '25,25,112', 'category' => 'Blue'],

            // Brown colors
            ['name' => 'Cornsilk', 'hex' => '#FFF8DC', 'rgb' => '255,248,220', 'category' => 'Brown'],
            ['name' => 'Blanched Almond', 'hex' => '#FFEBCD', 'rgb' => '255,235,205', 'category' => 'Brown'],
            ['name' => 'Bisque', 'hex' => '#FFE4C4', 'rgb' => '255,228,196', 'category' => 'Brown'],
            ['name' => 'Navajo White', 'hex' => '#FFDEAD', 'rgb' => '255,222,173', 'category' => 'Brown'],
            ['name' => 'Wheat', 'hex' => '#F5DEB3', 'rgb' => '245,222,179', 'category' => 'Brown'],
            ['name' => 'Burly Wood', 'hex' => '#DEB887', 'rgb' => '222,184,135', 'category' => 'Brown'],
            ['name' => 'Tan', 'hex' => '#D2B48C', 'rgb' => '210,180,140', 'category' => 'Brown'],
            ['name' => 'Rosy Brown', 'hex' => '#BC8F8F', 'rgb' => '188,143,143', 'category' => 'Brown'],
            ['name' => 'Sandy Brown', 'hex' => '#F4A460', 'rgb' => '244,164,96', 'category' => 'Brown'],
            ['name' => 'Goldenrod', 'hex' => '#DAA520', 'rgb' => '218,165,32', 'category' => 'Brown'],
            ['name' => 'Dark Goldenrod', 'hex' => '#B8860B', 'rgb' => '184,134,11', 'category' => 'Brown'],
            ['name' => 'Peru', 'hex' => '#CD853F', 'rgb' => '205,133,63', 'category' => 'Brown'],
            ['name' => 'Chocolate', 'hex' => '#D2691E', 'rgb' => '210,105,30', 'category' => 'Brown'],
            ['name' => 'Saddle Brown', 'hex' => '#8B4513', 'rgb' => '139,69,19', 'category' => 'Brown'],
            ['name' => 'Sienna', 'hex' => '#A0522D', 'rgb' => '160,82,45', 'category' => 'Brown'],
            ['name' => 'Brown', 'hex' => '#A52A2A', 'rgb' => '165,42,42', 'category' => 'Brown'],
            ['name' => 'Maroon', 'hex' => '#800000', 'rgb' => '128,0,0', 'category' => 'Brown'],

            // White colors
            ['name' => 'White', 'hex' => '#FFFFFF', 'rgb' => '255,255,255', 'category' => 'White'],
            ['name' => 'Snow', 'hex' => '#FFFAFA', 'rgb' => '255,250,250', 'category' => 'White'],
            ['name' => 'Honeydew', 'hex' => '#F0FFF0', 'rgb' => '240,255,240', 'category' => 'White'],
            ['name' => 'Mint Cream', 'hex' => '#F5FFFA', 'rgb' => '245,255,250', 'category' => 'White'],
            ['name' => 'Azure', 'hex' => '#F0FFFF', 'rgb' => '240,255,255', 'category' => 'White'],
            ['name' => 'Alice Blue', 'hex' => '#F0F8FF', 'rgb' => '240,248,255', 'category' => 'White'],
            ['name' => 'Ghost White', 'hex' => '#F8F8FF', 'rgb' => '248,248,255', 'category' => 'White'],
            ['name' => 'White Smoke', 'hex' => '#F5F5F5', 'rgb' => '245,245,245', 'category' => 'White'],
            ['name' => 'Seashell', 'hex' => '#FFF5EE', 'rgb' => '255,245,238', 'category' => 'White'],
            ['name' => 'Beige', 'hex' => '#F5F5DC', 'rgb' => '245,245,220', 'category' => 'White'],
            ['name' => 'Old Lace', 'hex' => '#FDF5E6', 'rgb' => '253,245,230', 'category' => 'White'],
            ['name' => 'Floral White', 'hex' => '#FFFAF0', 'rgb' => '255,250,240', 'category' => 'White'],
            ['name' => 'Ivory', 'hex' => '#FFFFF0', 'rgb' => '255,255,240', 'category' => 'White'],
            ['name' => 'Antique White', 'hex' => '#FAEBD7', 'rgb' => '250,235,215', 'category' => 'White'],
            ['name' => 'Linen', 'hex' => '#FAF0E6', 'rgb' => '250,240,230', 'category' => 'White'],
            ['name' => 'Lavender Blush', 'hex' => '#FFF0F5', 'rgb' => '255,240,245', 'category' => 'White'],
            ['name' => 'Misty Rose', 'hex' => '#FFE4E1', 'rgb' => '255,228,225', 'category' => 'White'],

            // Gray colors
            ['name' => 'Gainsboro', 'hex' => '#DCDCDC', 'rgb' => '220,220,220', 'category' => 'Gray'],
            ['name' => 'Light Gray', 'hex' => '#D3D3D3', 'rgb' => '211,211,211', 'category' => 'Gray'],
            ['name' => 'Silver', 'hex' => '#C0C0C0', 'rgb' => '192,192,192', 'category' => 'Gray'],
            ['name' => 'Dark Gray', 'hex' => '#A9A9A9', 'rgb' => '169,169,169', 'category' => 'Gray'],
            ['name' => 'Gray', 'hex' => '#808080', 'rgb' => '128,128,128', 'category' => 'Gray'],
            ['name' => 'Dim Gray', 'hex' => '#696969', 'rgb' => '105,105,105', 'category' => 'Gray'],
            ['name' => 'Light Slate Gray', 'hex' => '#778899', 'rgb' => '119,136,153', 'category' => 'Gray'],
            ['name' => 'Slate Gray', 'hex' => '#708090', 'rgb' => '112,128,144', 'category' => 'Gray'],
            ['name' => 'Dark Slate Gray', 'hex' => '#2F4F4F', 'rgb' => '47,79,79', 'category' => 'Gray'],
            ['name' => 'Black', 'hex' => '#000000', 'rgb' => '0,0,0', 'category' => 'Black']
        ];
    }

    /**
     * Display color details page for a specific hex color
     */
    public function show($hex)
    {
        // Validate hex format (6 characters, hex digits)
        if (!preg_match('/^[0-9A-Fa-f]{6}$/', $hex)) {
            abort(404, 'Invalid color hex code');
        }

        $hex = strtolower($hex);

        // Convert to uppercase for display
        $hexUpper = strtoupper($hex);

        // Calculate color properties
        $rgb = $this->hexToRgb('#' . $hex);
        $hsl = $this->rgbToHsl($rgb['r'], $rgb['g'], $rgb['b']);
        $hsv = $this->rgbToHsv($rgb['r'], $rgb['g'], $rgb['b']);
        $cmyk = $this->rgbToCmyk($rgb['r'], $rgb['g'], $rgb['b']);
        $brightness = $this->calculateBrightness('#' . $hex);

        // Get color name from database
        $colorName = $this->getColorName($hex);

        // Calculate harmonies
        $harmonies = [
            'complementary' => $this->calculateComplementary($hex),
            'analogous' => $this->calculateAnalogous($hex),
            'triadic' => $this->calculateTriadic($hex),
            'split_complementary' => $this->calculateSplitComplementary($hex),
            'tetradic' => $this->calculateTetradic($hex),
            'monochromatic' => $this->calculateMonochromatic($hex)
        ];

        // Get related colors (darker/lighter variations)
        $relatedColors = $this->getRelatedColors($hex);

        // Calculate contrast ratios with common backgrounds
        $contrasts = [
            'white' => $this->calculateContrastRatio('#' . $hex, '#FFFFFF'),
            'black' => $this->calculateContrastRatio('#' . $hex, '#000000'),
            'light_gray' => $this->calculateContrastRatio('#' . $hex, '#F8F9FA'),
            'dark_gray' => $this->calculateContrastRatio('#' . $hex, '#343A40')
        ];

        // Calculate color temperature
        $temperature = $this->calculateTemperature($hex);

        // Find similar colors from database
        $similarColors = $this->findSimilarColors($hex, 6);

        return view('pages.color-details', [
            'hex' => $hex,
            'hexUpper' => $hexUpper,
            'rgb' => $rgb,
            'hsl' => $hsl,
            'hsv' => $hsv,
            'cmyk' => $cmyk,
            'brightness' => $brightness,
            'colorName' => $colorName,
            'harmonies' => $harmonies,
            'relatedColors' => $relatedColors,
            'contrasts' => $contrasts,
            'temperature' => $temperature,
            'similarColors' => $similarColors,
            'color' => '#' . $hex // For compatibility with existing functions
        ]);
    }

    /**
     * Calculate complementary color
     */
    private function calculateComplementary($hex)
    {
        $hsl = $this->hexToHsl($hex);
        $hsl['h'] = ($hsl['h'] + 180) % 360;
        return [$this->hslToHex($hsl)];
    }

    /**
     * Calculate analogous colors
     */
    private function calculateAnalogous($hex)
    {
        $hsl = $this->hexToHsl($hex);
        return [
            $this->hslToHex(['h' => ($hsl['h'] + 330) % 360, 's' => $hsl['s'], 'l' => $hsl['l']]),
            $hex,
            $this->hslToHex(['h' => ($hsl['h'] + 30) % 360, 's' => $hsl['s'], 'l' => $hsl['l']])
        ];
    }

    /**
     * Calculate triadic colors
     */
    private function calculateTriadic($hex)
    {
        $hsl = $this->hexToHsl($hex);
        return [
            $hex,
            $this->hslToHex(['h' => ($hsl['h'] + 120) % 360, 's' => $hsl['s'], 'l' => $hsl['l']]),
            $this->hslToHex(['h' => ($hsl['h'] + 240) % 360, 's' => $hsl['s'], 'l' => $hsl['l']])
        ];
    }

    /**
     * Calculate split complementary colors
     */
    private function calculateSplitComplementary($hex)
    {
        $hsl = $this->hexToHsl($hex);
        return [
            $hex,
            $this->hslToHex(['h' => ($hsl['h'] + 150) % 360, 's' => $hsl['s'], 'l' => $hsl['l']]),
            $this->hslToHex(['h' => ($hsl['h'] + 210) % 360, 's' => $hsl['s'], 'l' => $hsl['l']])
        ];
    }

    /**
     * Calculate tetradic colors
     */
    private function calculateTetradic($hex)
    {
        $hsl = $this->hexToHsl($hex);
        return [
            $hex,
            $this->hslToHex(['h' => ($hsl['h'] + 90) % 360, 's' => $hsl['s'], 'l' => $hsl['l']]),
            $this->hslToHex(['h' => ($hsl['h'] + 180) % 360, 's' => $hsl['s'], 'l' => $hsl['l']]),
            $this->hslToHex(['h' => ($hsl['h'] + 270) % 360, 's' => $hsl['s'], 'l' => $hsl['l']])
        ];
    }

    /**
     * Calculate monochromatic colors
     */
    private function calculateMonochromatic($hex)
    {
        $hsl = $this->hexToHsl($hex);
        return [
            $this->hslToHex(['h' => $hsl['h'], 's' => $hsl['s'], 'l' => max(0, $hsl['l'] - 30)]),
            $this->hslToHex(['h' => $hsl['h'], 's' => $hsl['s'], 'l' => max(0, $hsl['l'] - 10)]),
            $this->hslToHex(['h' => $hsl['h'], 's' => $hsl['s'], 'l' => min(100, $hsl['l'] + 10)]),
            $this->hslToHex(['h' => $hsl['h'], 's' => $hsl['s'], 'l' => min(100, $hsl['l'] + 30)])
        ];
    }

    /**
     * Convert hex to HSL
     */
    private function hexToHsl($hex)
    {
        $rgb = $this->hexToRgb('#' . $hex);
        return $this->rgbToHsl($rgb['r'], $rgb['g'], $rgb['b']);
    }

    /**
     * Convert HSL to hex
     */
    private function hslToHex($hsl)
    {
        $rgb = $this->hslToRgb($hsl['h'], $hsl['s'], $hsl['l']);
        $hex = $this->rgbToHex($rgb['r'], $rgb['g'], $rgb['b']);
        return str_replace('#', '', $hex);
    }

    /**
     * Get related colors (darker/lighter variations)
     */
    private function getRelatedColors($hex)
    {
        $hsl = $this->hexToHsl($hex);

        $variations = [];

        // Lightness variations
        for ($i = -2; $i <= 2; $i++) {
            if ($i === 0) continue; // Skip the original color
            $lightness = max(0, min(100, $hsl['l'] + ($i * 10)));
            $variationHsl = ['h' => $hsl['h'], 's' => $hsl['s'], 'l' => $lightness];
            $variationHex = $this->hslToHex($variationHsl);

            $variations[] = [
                'hex' => $variationHex,
                'name' => $i < 0 ? 'Darker' . abs($i) : 'Lighter' . $i
            ];
        }

        // Saturation variations
        for ($i = -1; $i <= 1; $i++) {
            if ($i === 0) continue;
            $saturation = max(0, min(100, $hsl['s'] + ($i * 20)));
            $variationHsl = ['h' => $hsl['h'], 's' => $saturation, 'l' => $hsl['l']];
            $variationHex = $this->hslToHex($variationHsl);

            $variations[] = [
                'hex' => $variationHex,
                'name' => $i < 0 ? 'Desaturated' : 'Saturated'
            ];
        }

        return $variations;
    }

    /**
     * Get color name from database
     */
    private function getColorName($hex)
    {
        $colors = $this->getColorDatabase();

        foreach ($colors as $color) {
            if (strtolower(str_replace('#', '', $color['hex'])) === $hex) {
                return $color['name'];
            }
        }

        // If no exact match, find similar
        $hsl = $this->hexToHsl($hex);

        if ($hsl['l'] < 10) return 'Near Black';
        if ($hsl['l'] > 90) return 'Near White';
        if ($hsl['s'] < 10) return 'Gray';

        // Return color family based on hue
        $hue = $hsl['h'];
        if ($hue < 15 || $hue > 345) return 'Red';
        if ($hue >= 15 && $hue < 45) return 'Orange';
        if ($hue >= 45 && $hue < 75) return 'Yellow';
        if ($hue >= 75 && $hue < 165) return 'Green';
        if ($hue >= 165 && $hue < 195) return 'Cyan';
        if ($hue >= 195 && $hue < 255) return 'Blue';
        if ($hue >= 255 && $hue < 285) return 'Purple';
        if ($hue >= 285 && $hue < 345) return 'Pink';

        return 'Unknown Color';
    }

    /**
     * Find similar colors from database
     */
    private function findSimilarColors($hex, $limit = 6)
    {
        $targetRgb = $this->hexToRgb('#' . $hex);
        $colors = $this->getColorDatabase();

        $similar = [];

        foreach ($colors as $color) {
            $colorRgb = $this->hexToRgb($color['hex']);

            // Calculate Euclidean distance in RGB space
            $distance = sqrt(
                pow($targetRgb['r'] - $colorRgb['r'], 2) +
                pow($targetRgb['g'] - $colorRgb['g'], 2) +
                pow($targetRgb['b'] - $colorRgb['b'], 2)
            );

            $similar[] = [
                'hex' => str_replace('#', '', $color['hex']),
                'name' => $color['name'],
                'distance' => $distance
            ];
        }

        // Sort by distance (closest first)
        usort($similar, function($a, $b) {
            return $a['distance'] <=> $b['distance'];
        });

        // Return closest matches (excluding exact match)
        return array_slice($similar, 1, $limit);
    }

    /**
     * Calculate contrast ratio between two colors
     */
    private function calculateContrastRatio($color1, $color2)
    {
        $l1 = $this->calculateRelativeLuminance($color1);
        $l2 = $this->calculateRelativeLuminance($color2);

        if ($l1 > $l2) {
            return round(($l1 + 0.05) / ($l2 + 0.05), 2);
        }
        return round(($l2 + 0.05) / ($l1 + 0.05), 2);
    }

    /**
     * Calculate relative luminance for contrast ratio
     */
    private function calculateRelativeLuminance($color)
    {
        $rgb = $this->hexToRgb($color);

        $r = $rgb['r'] / 255;
        $g = $rgb['g'] / 255;
        $b = $rgb['b'] / 255;

        $r = $r <= 0.03928 ? $r / 12.92 : pow(($r + 0.055) / 1.055, 2.4);
        $g = $g <= 0.03928 ? $g / 12.92 : pow(($g + 0.055) / 1.055, 2.4);
        $b = $b <= 0.03928 ? $b / 12.92 : pow(($b + 0.055) / 1.055, 2.4);

        return 0.2126 * $r + 0.7152 * $g + 0.0722 * $b;
    }

    /**
     * Calculate color temperature (-1 = cool, 0 = neutral, 1 = warm)
     */
    private function calculateTemperature($hex)
    {
        $rgb = $this->hexToRgb('#' . $hex);

        // Simple temperature calculation based on red-blue balance
        $temperature = ($rgb['r'] - $rgb['b']) / 255;

        // Normalize to 0-1 range
        return ($temperature + 1) / 2;
    }

    /**
     * Convert RGB to HSV
     */
    private function rgbToHsv($r, $g, $b)
    {
        $r = $r / 255;
        $g = $g / 255;
        $b = $b / 255;

        $max = max($r, $g, $b);
        $min = min($r, $g, $b);
        $d = $max - $min;

        $h = 0;
        if ($d != 0) {
            switch($max) {
                case $r:
                    $h = 60 * fmod((($g - $b) / $d), 6);
                    if ($b > $g) $h += 360;
                    break;
                case $g:
                    $h = 60 * ((($b - $r) / $d) + 2);
                    break;
                case $b:
                    $h = 60 * ((($r - $g) / $d) + 4);
                    break;
            }
        }

        $s = $max == 0 ? 0 : ($d / $max) * 100;
        $v = $max * 100;

        return [
            'h' => round($h),
            's' => round($s),
            'v' => round($v)
        ];
    }

    /**
     * Convert RGB to CMYK
     */
    private function rgbToCmyk($r, $g, $b)
    {
        if ($r == 0 && $g == 0 && $b == 0) {
            return ['c' => 0, 'm' => 0, 'y' => 0, 'k' => 100];
        }

        $c = 1 - ($r / 255);
        $m = 1 - ($g / 255);
        $y = 1 - ($b / 255);

        $k = min($c, $m, $y);

        if ($k == 1) {
            return ['c' => 0, 'm' => 0, 'y' => 0, 'k' => 100];
        }

        return [
            'c' => round((($c - $k) / (1 - $k)) * 100),
            'm' => round((($m - $k) / (1 - $k)) * 100),
            'y' => round((($y - $k) / (1 - $k)) * 100),
            'k' => round($k * 100)
        ];
    }

    /**
     * Calculate brightness (0-1)
     */
    private function calculateBrightness($hex)
    {
        $rgb = $this->hexToRgb($hex);
        return (0.299 * $rgb['r'] + 0.587 * $rgb['g'] + 0.114 * $rgb['b']) / 255;
    }
}
