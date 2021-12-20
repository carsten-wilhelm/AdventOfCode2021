<?php

class Day17 {
    private $x1;
    private $x2;
    private $y1;
    private $y2;

    private $velX;
    private $velY;
    private $currX;
    private $currY;

    public function read($file) {
        $handle = fopen($file, "r");
        if ($handle) {
            while ($str = fgets($handle)) {
                if(preg_match('/.*x=([-0-9]+)\.\.([-0-9]+), y=([-0-9]+)\.\.([-0-9]+).*/', $str, $matches)) {
                    $this->process(intval($matches[1]), intval($matches[2]), intval($matches[3]), intval($matches[4]));
                }
            };
        }
    }

    public function process($x1, $x2, $y1, $y2) {
        echo "Target Area [$x1,$y1] - [$x2,$y2]\n";
        $this->x1 = min($x1, $x1);
        $this->x2 = max($x1, $x2);
        $this->y1 = min($y1, $y2);
        $this->y2 = max($y1, $y2);
        echo "Target Area [" . $this->x1 ."," . $this->y1 ."] - [" . $this->x2 ."," . $this->y2 ."]\n";

        $minY = $maxY = 0;
        $minX = $maxX = 0;
        if ($x1>0) {
            echo "Target is to the right - X vel must be > 0 and <= $x2\n";
            $minX = 1;
            $maxX = $x2;
        /*} else if ($x2<0) {
            echo "Target is to the left - X vel must be < 0 and >= $x2\n";
            $minX = $x2;
            $maxX = -1;*/
        } else {
            echo "X not supported\n";
            exit;
        }
        if ($y2<0) {
            echo "Target is below - Y vel must be > $y2\n";
            $minY = min($y1, $y2);
            $maxY = 1000;
        } else {
            echo "Y not supported\n";
            exit;
        }

        $highest = -INF;
        $found = [];
        for($sy=$minY ; $sy<$maxY ; ++$sy) {
            for($sx=$minX ; $sx<=$maxX ; ++$sx) {
                $h = $this->fire($sx,$sy);
                if ($h > -INF) {
                    $found["$sx,$sy"] = true;
                }
                if ($h > $highest) {
                    echo "New highest: ($sx,$sy) => $h\n";
                    $highest = $h;
                }
            }
        }
        echo "Found: " .count($found) . " combinations\n";
    }

    public function fire($vx, $vy) {
        $this->velX = $vx;
        $this->velY = $vy;
        $this->currX = 0;
        $this->currY = 0;
        $highest = -INF;
        while($this->possible()) {
            $this->step();
            $highest = max($highest, $this->currY);
            if ($this->hit()) {
                //echo "Location: " . $this->currX . "," . $this->currY . ", next vel " . $this->velX . "," . $this->velY . " (hit:" . ($this->hit() ? "Yes":"No") . " (possible:" . ($this->possible() ? "Yes":"No") . ")\n";
                return $highest;
            }
        }
        //echo "------------------------------------------\n";
        return -INF;
    }

    public function step() {
        $this->currX += $this->velX;
        $this->currY += $this->velY;
        $this->velX = ($this->velX > 0) ? $this->velX-1 : (($this->velX < 0) ? $this->velX+1 : 0);
        --$this->velY;
    }

    public function hit() {
        if ($this->currX < $this->x1) return false;
        if ($this->currX > $this->x2) return false;
        if ($this->currY < $this->y1) return false;
        if ($this->currY > $this->y2) return false;
        return true;
    }

    public function possible() {
        // probe is already too low
        if ($this->velY<0 && $this->currY < $this->y1) return false;
        // cannot reach in X axis
        if ($this->velX==0 && $this->currX < $this->x1) return false;
        // probe is already too far away
        if ($this->currX > $this->x2) return false;
        return true;
    }

    public function start() {
        $this->read("input2.txt");
    }
}

(new Day17())->start();
