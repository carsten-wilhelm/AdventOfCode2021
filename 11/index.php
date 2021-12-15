<?php

class Day11 {
    private $level;
    private $mx;
    private $my;
    private $flashed;

    public function read($file) {
        $this->level = [];

        $handle = fopen($file, "r");
        if ($handle) {
            while ($str = fgets($handle)) {
                $this->level[] = array_map("intval", str_split(trim($str)));
            };
        }
        $this->mx = count($this->level[0]);
        $this->my = count($this->level);
    }

    public function iterate() {
        $this->flashed = array_fill(0, $this->my, array_fill(0, $this->mx, false));
        $flashing = 0;

        // increase all by 1
        for($y=0 ; $y<$this->my ; ++$y) {
            for($x=0 ; $x<$this->mx ; ++$x) {
                ++$this->level[$y][$x];
            }
        }

        // look for flashing ones
        for ($y = 0; $y < $this->my; ++$y) {
            for ($x = 0; $x < $this->mx; ++$x) {
                $flashing += $this->flash($x, $y);
            }
        }

        // reset flashing ones
        for ($y = 0; $y < $this->my; ++$y) {
            for ($x = 0; $x < $this->mx; ++$x) {
                if ($this->flashed[$y][$x]) {
                  $this->level[$y][$x] = 0;
                }
            }
        }

        //$this->show();
        return $flashing;
    }

    public function flash($x, $y) {
        $flashing = 0;
        if ($this->level[$y][$x]>9 && !$this->flashed[$y][$x]) {
            $this->flashed[$y][$x] = true;
            ++$flashing;
            // process neighbours
            $flashing += $this->increase($x-1, $y-1);
            $flashing += $this->increase($x, $y-1);
            $flashing += $this->increase($x+1, $y-1);
            $flashing += $this->increase($x-1, $y);
            $flashing += $this->increase($x+1, $y);
            $flashing += $this->increase($x-1, $y+1);
            $flashing += $this->increase($x, $y+1);
            $flashing += $this->increase($x+1, $y+1);
        }
        return $flashing;
    }

    public function increase($x, $y) {
        if ($x>=0 && $x<$this->mx && $y>=0 &&$y<$this->my) {
            ++$this->level[$y][$x];
            return $this->flash($x, $y);
        }
    }

    public function allFlashed() {
        for($y=0 ; $y<$this->my ; ++$y) {
            for($x=0 ; $x<$this->mx ; ++$x) {
                if (!$this->flashed[$y][$x]) return false;
            }
        }
        return true;
    }

    public function show() {
        // increase all by 1
        for($y=0 ; $y<$this->my ; ++$y) {
            for($x=0 ; $x<$this->mx ; ++$x) {
                echo $this->level[$y][$x] . " ";
            }
            echo "\n";
        }
    }

    public function start() {
        $flashing = 0;
        $this->read("input2.txt");
        $step = 0;
        for($i=0 ; $i<100 ; ++$i) {
            ++$step;
            $flashing += $this->iterate();
            if ($this->allFlashed()) {
                echo "All flashes at $step\n";
            }
        }
        for(;;) {
            ++$step;
            $this->iterate();
            if ($this->allFlashed()) {
                echo "All flashes at $step\n";
                break;
            }
        }
        echo "Result 1: $flashing\n";
    }
}

(new Day11())->start();

