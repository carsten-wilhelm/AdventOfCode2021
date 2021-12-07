<?php

class Day07 {
    private $crabs;

    public function read() {
        $line = file_get_contents("input2.txt");
        $tmp = array_map('intval' , explode(",", trim($line)));
        $max = max($tmp);
        echo "max: $max\n";

        $this->crabs = array_fill(0, ($max+1), 0);
        foreach($tmp as $c) {
            ++$this->crabs[$c];
        }
    }

    public function move() {
        $minFuel = PHP_INT_MAX;
        $minPos = 0;
        for($pos = 0; $pos<sizeof($this->crabs) ; ++$pos) {
            $fuel = 0;
            for($i = 0; $i<sizeof($this->crabs) ; ++$i) {
                $len = abs($pos - $i);
                $fuel += $this->crabs[$i] * $len;
            }
            if ($fuel < $minFuel) {
                $minFuel = $fuel;
                $minPos = $pos;
            }
            //echo "Move to position $pos ......: $fuel\n";
        }
        echo "Best position .....: $minPos ($minFuel)\n";
    }

    public function move2() {
        $minFuel = PHP_INT_MAX;
        $minPos = 0;
        for($pos = 0; $pos<sizeof($this->crabs) ; ++$pos) {
            $fuel = 0;
            for($i = 0; $i<sizeof($this->crabs) ; ++$i) {
                $len = abs($pos - $i);
                $fuel += $this->crabs[$i] * ($len * ($len + 1) / 2);
            }
            if ($fuel < $minFuel) {
                $minFuel = $fuel;
                $minPos = $pos;
            }
            //echo "Move to position $pos ......: $fuel\n";
        }
        echo "Best position .....: $minPos ($minFuel)\n";
    }

    public function start() {
        $this->read();
        $this->move();
        $this->move2();
    }

}

(new Day07())->start();
