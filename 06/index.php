<?php

ini_set("memory_limit", 0);
class Day06 {
    private $fish;

    public function read() {
        $line = file_get_contents("input2.txt");
        $this->fish = [0, 0, 0, 0, 0, 0, 0, 0, 0];
        foreach(explode(",", trim($line)) as $f) {
            ++$this->fish[$f];
        }
    }

    public function day() {
        $next = [0, 0, 0, 0, 0, 0, 0, 0, 0];
        $new = $this->fish[0];

        for($i=1 ; $i<= 8 ; ++$i) {
            $next[$i-1] = $this->fish[$i];
        }
        $next[6] += $new;
        $next[8] = $new;
        $this->fish = $next;
    }

    public function sum() {
        $sum = 0;
        for($i=0 ; $i<= 8 ; ++$i) {
            $sum += $this->fish[$i];
        }
        return $sum;
    }

    public function show($day) {
        echo "After $day day: " . join(",", $this->fish) . " (" . $this->sum() . ")\n";
    }

    public function start() {
        $this->read();
        for($i=1 ; $i<=256 ; ++$i) {
            $this->day();
            $this->show($i);
        }
    }

}

(new Day06())->start();
