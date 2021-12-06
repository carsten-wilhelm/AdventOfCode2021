<?php

class Line {
    private static $REGEX = '/([0-9]+),([0-9]+) -> ([0-9]+),([0-9]+)/';
    public $x1;
    public $y1;
    public $x2;
    public $y2;
    public $dx;
    public $dy;

    public function __construct($str)
    {
        if (preg_match(self::$REGEX, $str, $matches)) {
            $this->x1 = (int)$matches[1];
            $this->y1 = (int)$matches[2];
            $this->x2 = (int)$matches[3];
            $this->y2 = (int)$matches[4];
            $this->dx = $this->sign($this->x2-$this->x1);
            $this->dy = $this->sign($this->y2-$this->y1);
        }
    }

    public function sign($a) {
        return $a < 0 ? -1 : ($a > 0 ? 1 : 0);
    }

    public function isStraight() {
        return $this->dx == 0 || $this->dy == 0;
    }

    public function draw(&$matrix) {
        //echo $this;
        $x = $this->x1;
        $y = $this->y1;

        ++$matrix[$x][$y];
        while ($x != $this->x2 || $y != $this->y2) {
            $x += $this->dx;
            $y += $this->dy;
            ++$matrix[$x][$y];
        };
    }

    public function __toString()
    {
        return "(" . $this->x1 . "," . $this->y1 . ") => (" . $this->x2 . "," . $this->y2 . ") [". $this->dx . "," . $this->dy . "]\n";
    }


}

class Day05 {
    private $lines;
    private $matrix;
    private $w;
    private $h;

    public function read() {
        $this->lines = [];
        $this->w = 0;
        $this->h = 0;
        $handle = fopen("input.txt", "r");
        if ($handle) {
            while ($str = fgets($handle)) {
                $line = new Line($str);
                $this->lines[] = $line;
                $this->w = max($this->w, $line->x1, $line->x2);
                $this->h = max($this->h, $line->y1, $line->y2);
            };
        }
        $this->matrix = [];
        for($x=0 ; $x<=$this->w ; ++$x) {
            $col = [];
            for($y=0 ; $y<=$this->h ; ++$y) {
                $col[] = 0;
            }
            $this->matrix[] = $col;
        }
        echo "Matrix: " . sizeof($this->matrix) . " x " . sizeof($this->matrix[0]) . "\n";
    }

    public function showMatrix() {
        $output = "";
        for($x=0 ; $x<=$this->w ; ++$x) {
            for($y=0 ; $y<=$this->h ; ++$y) {
                $output .= $this->matrix[$x][$y];
            }
            $output .= "\n";
        }
        $handle = fopen("output.txt", "w");
        fwrite($handle, $output);
        fclose($handle);
    }

    public function drawLines() {
        foreach ($this->lines as $line) {
            if ($line->isStraight()) {
                $line->draw($this->matrix);
            }
        }
        //$this->showMatrix();
        echo "Count1 ..." . $this->countIntersections() . "\n";

        foreach ($this->lines as $line) {
            if (!$line->isStraight()) {
                $line->draw($this->matrix);
            }
        }
        echo "Count2 ..." . $this->countIntersections() . "\n";

    }

    public function countIntersections() {
        $count = 0;
        for($x=0 ; $x<=$this->w ; ++$x) {
            for($y=0 ; $y<=$this->h ; ++$y) {
                if ($this->matrix[$x][$y] > 1) {
                    ++$count;
                }
            }
        }
        return $count;
    }

    public function start() {
        $this->read();
        //$this->showMatrix();
        $this->drawLines();
    }
}

(new Day05())->start();
