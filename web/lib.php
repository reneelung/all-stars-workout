<?php
    require('wordnik/Swagger.php');
    const WORDNIK_API_KEY = 'f0a2ef63198e524dac0080a2bf106182fb048283b81da8dc6';
    const USERNAME = 'ReneeLung';
    const PASSWORD = 'pagerdutydoge';
    const TEMPLATE = 8072285;
    const IMG_WIDTH = 620;
    const IMG_HEIGHT = 620;
    const SLACK_TOKEN = 'J1FdWUtcy5sZZMvUK9dNZ1Jz';

    $bounding_boxes = array();
    $colors = array();
    $sizes = array();
    $positions = array();

    function extract_phrases($data)
    {
        if ($data['text']) {
            $rtn = array();
            $phrases = explode(',', $data['text']);
            foreach ($phrases as $phrase) {
                $rtn[] = trim($phrase);
            }
        } else {
            $rtn = false;
        }

        return $rtn;
    }

    function generate_phrases()
    {
        $phrases = array();

        $phrases[] = "so " . _get_random_word('noun');
        $phrases[] = "very " . _get_random_word('verb');
        $phrases[] = "much " . _get_random_word('adjective');
        $phrases[] = "WOW";
        $phrases[] = "amaze";

        return $phrases;
    }

    function _get_random_word($part_of_speech)
    {
        $client = new APIClient(WORDNIK_API_KEY, 'http://api.wordnik.com/v4');
        $wordsApi = new WordsApi($client);

        return $wordsApi->getRandomWord($part_of_speech)->word;
    }

    function img_request_data($phrases)
    {
        $data = array(
            'username' => USERNAME,
            'password' => PASSWORD,
            'template_id' => TEMPLATE,
            'font' => 'arial',
            'text0' => 'foo',
            'text1' => 'bar',
        );

        $boxes = array('boxes' => _text_boxes($phrases));

        return array_merge($data, $boxes);
    }

    function is_ajax()
    {
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            return true;
        } else {
            return false;
        }
    }

    function is_slack($token)
    {
        return $token === SLACK_TOKEN;
    }

    function _text_boxes($phrases)
    {
        $boxes = array();

        foreach ($phrases as $phrase) {
            $box = array();
            $color = _set_colour();
            $size_position = _set_size_position();

            $box['text'] = $phrase;
            $box['color'] = '#' . $color;
            $box['outline_color'] = '#FFFFFF';
            $boxes[] = array_merge($box, $size_position);
        }

        return $boxes;
    }

    function _set_size_position()
    {
        $bb = _bounding_box();
        $pos = $bb['pos'];
        $size = $bb['size'];

        if ($pos['x'] + $size['width'] > IMG_WIDTH) {
            $pos['x'] = IMG_WIDTH - $size['width'] - 10;
        }

        if ($pos['y'] + $size['height'] > IMG_HEIGHT) {
            $pos['y'] = IMG_WIDTH - $size['height'] - 10;
        }

        return array_merge($pos, $size);
    }

    function _bounding_box()
    {
        $pos = _set_position();
        $size = _set_size();

        $x1 = $pos['x'];
        $x2 = $pos['x'] + $size['width'];
        $y1 = $pos['y'];
        $y2 = $pos['y'] + $size['height'];

        $coords = array('x1' => $x1, 'x2' => $x2, 'y1' => $y1, 'y2' => $y2);

        if (!in_array($coords, $bounding_boxes)) {
            $bounding_boxes[] = $coords;
            return array('pos' => $pos, 'size' => $size);
        } else {
            _check_bounding_box();
        }
    }

    function _set_position()
    {
        $x = rand(0, 620);
        $y = rand(0, 620);
        $position = array('x' => $x, 'y' => $y);
        if (!in_array($position, $position)) {
            $positions[] = $position;
            return $position;
        } else {
            _set_position();
        }
    }

    function _set_colour()
    {
        $color = _random_color();
        if (!in_array($color, $colors)) {
            $colors[] = $color;
            return $color;
        } else {
            _set_colour();
        }
    }

    function _set_size()
    {
        $width = rand(50, 300);
        $height = rand(25, 50);

        $dimensions = array('width' => $width, 'height' => $height);

        if (!in_array($dimensions, $sizes)) {
            $sizes[] = $dimensions;
            return $dimensions;
        } else {
            _set_size();
        }
    }

    function _random_color_part()
    {
        return str_pad(dechex(mt_rand(0, 255)), 2, '0', STR_PAD_LEFT);
    }

    function _random_color()
    {
        return _random_color_part() . _random_color_part() . _random_color_part();
    }
?>