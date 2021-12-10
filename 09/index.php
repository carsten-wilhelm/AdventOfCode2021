<?php

class Day09 {
    private $map;
    private $maxX;
    private $maxY;
    private $lows;
    private $basin;

    public function __construct()
    {
        $this->map = [];
        $this->maxX = $this->maxY = 0;
    }

    public function addRow($row) {
        $tmp = str_split(trim($row));
        $this->maxX = max($this->maxX, sizeof($tmp));
        $this->map[$this->maxY++] = $tmp;
    }

    public function output() {
        for($y=0 ; $y<$this->maxY ; ++$y) {
            for($x=0 ; $x<$this->maxX ; ++$x) {
                echo $this->map[$y][$x];
            }
            echo "\n";
        }
    }

    public function calcLowPoints() {
        $this->lows = [];
        $id = 1;
        $riskLevel = 0;
        for($y=0 ; $y<$this->maxY ; ++$y) {
            for($x=0 ; $x<$this->maxX ; ++$x) {
                $me = $this->map[$y][$x];

                // upper point is lower
                if ($y > 0 && $me >= $this->map[$y-1][$x]) continue;
                // lower point is lower
                if ($y < $this->maxY-1 && $me >= $this->map[$y+1][$x]) continue;
                // left point is lower
                if ($x > 0 && $me >= $this->map[$y][$x-1]) continue;
                // right point is lower
                if ($x < $this->maxX-1 && $me >= $this->map[$y][$x+1]) continue;

                echo "Low point: ($x, $y) = $me ($id)\n";
                $this->lows[] = ["id" => $id++, "x" => $x, "y" => $y, "value" => $me];
                $riskLevel += $me + 1;
            }
        }
        echo "Risk level: $riskLevel\n";
    }

    public function createBasins() {
        $this->basin = array_fill(0, $this->maxY, array_fill(0, $this->maxX, 0));
        foreach($this->lows as &$low) {
            $low["filled"] = $this->fillBasin($low["x"], $low["y"], $low["id"]);
        }
        $this->showBasins();

        usort($this->lows, function ($a,$b) { return ($b["filled"] <=> $a["filled"]); });
        $top = array_splice($this->lows, 0, 3);
        $result = 1;
        foreach ($top as $t) {
            $result *= $t["filled"];
        }
        echo "Result: $result\n";
    }

    public function fillBasin($x, $y, $id, $parent = -1) {
        if ($x>=0 && $x<$this->maxX && $y>=0 && $y<$this->maxY) {
            // in matrix
            $me = $this->map[$y][$x];
            if ($parent < $me && $me != 9 && $this->basin[$y][$x] === 0) {
                // lower and not 9
                $this->basin[$y][$x] = $id;

                return 1
                    + $this->fillBasin($x+1, $y, $id, $me)
                    + $this->fillBasin($x-1, $y, $id, $me)
                    + $this->fillBasin($x, $y+1, $id, $me)
                    + $this->fillBasin($x, $y-1, $id, $me);
            }
        }
        return 0;
    }
    public function showBasins() {
        for($y=0 ; $y<$this->maxY ; ++$y) {
            for ($x = 0; $x < $this->maxX; ++$x) {
                echo $this->basin[$y][$x];
            }
            echo "\n";
        }
    }

    public function read() {
        $handle = fopen("input2.txt", "r");
        if ($handle) {
            while ($str = fgets($handle)) {
                $this->addRow($str);
            };
        }
    }

    public function start() {
        $this->read();
        $this->output();
        $this->calcLowPoints();
        $this->createBasins();
    }

}

(new Day09())->start();
