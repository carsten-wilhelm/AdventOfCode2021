<?php

class Day04 {
    private $random;
    private $boxes;

    function read() {
        $handle = fopen("input.txt", "r");
        if ($handle) {
            $this->random = explode(",", trim(fgets($handle)));
            fgets($handle);
            $this->boxes = [];
            while ($box = $this->readBox($handle)) {
                $this->boxes[] = $box;
            };
        }
    }
    function readLine($handle) {
        $read = fgets($handle);
        if ($read) {
            $line = [];
            foreach (preg_split('/\s+/', trim($read)) as $n) {
                $line[] = [$n, false];
            }
            return $line;
        }
    }
    function readBox($handle) {
        $line1 = $this->readLine($handle);
        if ($line1) {
            $box = array(
                $line1,
                $this->readLine($handle),
                $this->readLine($handle),
                $this->readLine($handle),
                $this->readLine($handle)
            );
            fgets($handle);
            return $box;
        }
    }

    public function play() {
        while ($draw = array_shift($this->random)) {
            echo "Drawn .......... $draw\n";
            $this->set($draw);
            if ($box = $this->check()) {
                return [$draw, $box];
            }
        }
    }

    public function set($draw) {
        for($i=0 ; $i<sizeof($this->boxes) ; ++$i) {
            for($x=0 ; $x<5 ; ++$x) {
                for($y=0 ; $y<5 ; ++$y) {
                    if($this->boxes[$i][$x][$y][0] == $draw) {
                        $this->boxes[$i][$x][$y][1] = true;
                    }
                }
            }
        }
    }
    public function check() {
        $result = null;

        $left = [];
        for($i=0 ; $i<sizeof($this->boxes) ; ++$i) {
            $won = false;
            // check rows
            for($y=0 ; $y<5 ; ++$y) {
                $stateRows = true;
                for($x=0 ; $x<5 ; ++$x) {
                    $stateRows &= $this->boxes[$i][$x][$y][1];
                }
                $won |= $stateRows;
            }
            // check cols
            for($x=0 ; $x<5 ; ++$x) {
                $stateCols = true;
                for($y=0 ; $y<5 ; ++$y) {
                    $stateCols &= $this->boxes[$i][$x][$y][1];
                }
                $won |= $stateCols;
            }
            if ($won) {
                if ($result === null) {
                    // remember first
                    $result = $this->boxes[$i];
                }
            } else {
                $left[] = $this->boxes[$i];
            }
        }
        $this->boxes = $left;
        return $result;
    }

    public function result1($box, $draw) {
        $sum = 0;
        for($x=0 ; $x<5 ; ++$x) {
            for($y=0 ; $y<5 ; ++$y) {
                if (!$box[$x][$y][1]) {
                    $sum += (int)$box[$x][$y][0];
                }
            }
        }
        return $sum * $draw;
    }

    public function showAll() {
        foreach($this->boxes as $box) {
            $this->show($box);
        }
        echo "===========================================\n";
    }
    public function show($box) {
        for($y=0 ; $y<5 ; ++$y) {
            for($x=0 ; $x<5 ; ++$x) {
                if ($box[$y][$x][1]) {
                    echo sprintf("[%2d] ", $box[$y][$x][0]);
                } else {
                    echo sprintf(" %2d  ", $box[$y][$x][0]);
                }
            }
            echo "\n";
        }
        echo "-------------------------------------------\n";
    }

    public function start() {
        $this->read();
        $winner = $this->play();
        $result1 = $this->result1($winner[1], $winner[0]);
        echo "Result1 .......... $result1\n";

        while (sizeof($this->boxes) > 0 && sizeof($this->random) > 0) {
            echo "Before ............: " . sizeof($this->boxes) . "\n";
            $winner = $this->play();
            echo "After .............: " . sizeof($this->boxes) . "\n";
        }

        $this->show($winner[1]);
        $result2 = $this->result1($winner[1], $winner[0]);
        echo "Result2 .......... $result2\n";

    }
}

$day = new Day04();
$day->start();
