<?php

class Day13 {
    private $dots;
    private $maxX;
    private $maxY;
    private $folds;

    public function read($file) {
        $tmp = [];
        $this->maxX = 0;
        $this->maxY = 0;

        $handle = fopen($file, "r");
        if ($handle) {
            while ($str = fgets($handle)) {
                if (ctype_digit($str[0])) {
                    // dot
                    list ($x, $y) = explode(",", trim($str), 2);
                    $this->maxX = max($this->maxX, $x);
                    $this->maxY = max($this->maxY, $y);
                    $tmp[] = [ "x" => $x, "y" => $y ];
                } else if (substr($str, 0, 13) == "fold along y=") {
                    // fold Y
                    $at = substr(trim($str), 13);
                    $this->folds[] = [ "along" => "y", "at" => $at];
                } else if (substr($str, 0, 13) == "fold along x=") {
                    // fold X
                    $at = substr(trim($str), 13);
                    $this->folds[] = [ "along" => "x", "at" => $at];
                }
            };
        }

        $this->dots = array_fill(0, $this->maxY+1, array_fill(0, $this->maxX+1, false));
        foreach($tmp as $dot) {
            $this->dots[$dot["y"]][$dot["x"]] = true;
        }
    }

    public function show() {
        for($y=0 ; $y<=$this->maxY ; ++$y) {
            for($x=0 ; $x<=$this->maxX ; ++$x) {
                echo $this->dots[$y][$x] ? "■" : "▫";
            }
            echo "\n";
        }
        echo "----------------------------------------\n";
    }

    public function fold($fold) {
        if ($fold["along"] == "y") {
            $newMaxY = $fold["at"] - 1;
            $new = array_fill(0, $newMaxY+1, array_fill(0, $this->maxX+1, false));

            for($y=0 ; $y<$fold["at"] ; ++$y) {
                for ($x = 0; $x <= $this->maxX; ++$x) {
                    $new[$y][$x] = $this->dots[$y][$x];
                }
            }
            for($y=$fold["at"]+1 ; $y<=$this->maxY ; ++$y) {
                $newY = 2*$fold["at"] - $y;
                for ($x = 0; $x <= $this->maxX; ++$x) {
                    $new[$newY][$x] = $new[$newY][$x] || $this->dots[$y][$x];
                }
            }
            $this->dots = $new;
            $this->maxY = $newMaxY;
        } else {
            $newMaxX = $fold["at"] - 1;
            $new = array_fill(0, $this->maxY+1, array_fill(0, $newMaxX+1, false));

            for($y=0 ; $y<=$this->maxY ; ++$y) {
                for ($x = 0; $x < $fold["at"]; ++$x) {
                    $new[$y][$x] = $this->dots[$y][$x];
                }
                for ($x = $fold["at"]+1; $x <= $this->maxX; ++$x) {
                    $newX = 2*$fold["at"] - $x;
                    $new[$y][$newX] = $new[$y][$newX] || $this->dots[$y][$x];
                }
            }
            $this->dots = $new;
            $this->maxX = $newMaxX;
        }
    }

    public function count() {
        $count = 0;
        for($y=0 ; $y<=$this->maxY ; ++$y) {
            for($x=0 ; $x<=$this->maxX ; ++$x) {
                if ($this->dots[$y][$x]) ++$count;
            }
        }
        return $count;
    }

    public function start() {
        $this->read("input2.txt");
        //$this->show();
        $fold = array_shift($this->folds);
        $this->fold($fold);
        echo "Dots visible: " . $this->count() ."\n";

        while($fold = array_shift($this->folds)) {
            $this->fold($fold);
        }
        $this->show();
    }
}

(new Day13())->start();

