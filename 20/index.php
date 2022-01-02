<?php
ob_implicit_flush();

class Algorithm {
    private $map;

    public function __construct($line)
    {
        $this->map = str_split(trim($line));
    }
    public function map($idx) {
        return $this->map[$idx];
    }
}

class Image {
    private $width;
    private $height;
    private $pixel = [];
    private $bg = ".";

    public function addLine($line) {
        $this->pixel[] = str_split(trim($line));
        $this->height = count($this->pixel);
        $this->width = count($this->pixel[0]);
    }

    public function show() {
        for($y=0 ; $y<$this->height ; ++$y) {
            for($x=0 ; $x<$this->width ; ++$x) {
                echo ($this->pixel[$y][$x] == "#") ? "▮" : "▯";
            }
            echo "\n";
        }
        echo "Background: " . $this->bg . "\n";
        echo "Lit: " . $this->lit() . "\n";
    }

    public function lit() {
        if ($this->bg == "#") return INF;

        $lit = 0;
        for($y=0 ; $y<$this->height ; ++$y) {
            for($x=0 ; $x<$this->width ; ++$x) {
                if ($this->pixel[$y][$x] == '#') {
                    ++$lit;
                }
            }
        }
        return $lit;
    }


    public function area($cx, $cy) {
        $bin = 0;
        for($y=$cy-1 ; $y<=$cy+1 ; ++$y) {
            for($x=$cx-1 ; $x<=$cx+1 ; ++$x) {
                $bin *= 2;
                if (($this->pixel[$y][$x] ?? $this->bg) == '#') {
                    $bin++;
                }
            }
        }
        return $bin;
    }

    public function enhance($algo) {
        $image = new Image();
        for($y=-2 ; $y<$this->height+2 ; ++$y) {
            $line = "";
            for($x=-2 ; $x<$this->width+2 ; ++$x) {
                $area = $this->area($x, $y);
                $line .= $algo->map($area);
            }
            $image->addLine($line);
        }
        if ($this->bg == "." && $algo->map(0) == "#") {
            $image->bg = "#";
        } else if ($this->bg == "#" && $algo->map(511) == ".") {
            $image->bg = ".";
        }
        return $image;
    }
}

class Day20 {
    private $algo;

    public function read($file) {
        $handle = fopen($file, "r");
        if ($handle) {
            $this->algo = new Algorithm(fgets($handle));
            fgets($handle);
            $image = new Image();
            while ($str = fgets($handle)) {
                $image->addLine($str);
            }
            return $image;
        }
    }

    public function start() {
        $image = $this->read("input2.txt");
        $image->show();
        for($i=0 ; $i<50 ; ++$i) {
            $image = $image->enhance($this->algo);
            $image->show();
        }
    }
}

(new Day20())->start();
