<?php

include_once 'Dijkstra.php';

ini_set("memory_limit", -1);

class Day15 {

    public function read($file) {
        $map = [];
        $maxX = $maxY = 0;
        $handle = fopen($file, "r");
        if ($handle) {
            while ($str = fgets($handle)) {
                ++$maxY;
                $line = str_split(trim($str));
                $maxX = max($maxX, count($line));
                $map[] = $line;
            };
        }

        for($y=0 ; $y<$maxY ; ++$y) {
            for($x=0 ; $x<$maxX ; ++$x) {
                $map["$x:$y"] = $map[$y][$x];
            }
        }
        return [ $maxX, $maxY, $map];
    }

    public function dijkstra($maxX, $maxY, &$map) {
        $g = new Graph();

        for($y=0 ; $y<$maxY ; ++$y) {
            for($x=0 ; $x<$maxX ; ++$x) {
                if ($x > 0) $g->addedge("$x:$y", ($x - 1) . ":$y", $map[($x - 1) . ":$y"]);
                if ($x < $maxX - 1) $g->addedge("$x:$y", ($x + 1) . ":$y", $map[($x + 1) . ":$y"]);
                if ($y > 0) $g->addedge("$x:$y", "$x:" . ($y - 1), $map["$x:" . ($y - 1)]);
                if ($y < $maxY - 1) $g->addedge("$x:$y", "$x:" . ($y + 1), $map["$x:" . ($y + 1)]);
            }
        }
        $last = ($maxX-1).":".($maxY-1);
        $g->addedge($last, $last, 0);

        list($distances, $prev) = $g->paths_from("0:0");

        $path = $g->paths_to($prev, ($maxX-1).":".($maxY-1));
        array_shift($path);
        $sum = 0;
        foreach($path as $segment) {
            $sum += $map[$segment];
        }
        echo "Cost: $sum\n";
    }

    public function enlarge($maxX, $maxY, $map, $factor = 5) {
        for($y=0 ; $y<$maxY ; ++$y) {
            for($x=0 ; $x<$maxX ; ++$x) {
                $cost = $map["$x:$y"];
                for($fx=0 ; $fx<$factor ; ++$fx) {
                    for($fy=0 ; $fy<$factor ; ++$fy) {
                        $newcost = ($cost + $fx + $fy);
                        while ($newcost > 9) $newcost -= 9;
                        $newmap[($x + $fx * $maxX) . ":" . ($y + $fy * $maxY)] = $newcost;
                    }
                }
            }
        }
        return [ $factor * $maxX, $factor * $maxY, $newmap];
    }

    public function show($maxX, $maxY, &$map) {
        for($y=0 ; $y<$maxY ; ++$y) {
            for ($x = 0; $x < $maxX; ++$x) {
                echo $map["$x:$y"];
            }
            echo "\n";
        }
    }

    public function start() {
        list($maxX, $maxY, $map) = $this->read("input2.txt");
        echo "$maxX x $maxY\n";
        //$this->show($maxX, $maxY, $map);
        $this->dijkstra($maxX, $maxY, $map);
        list($nmaxX, $nmaxY, $nmap) = $this->enlarge($maxX, $maxY, $map);
        echo "$nmaxX x $nmaxY\n";
        //$this->show($nmaxX, $nmaxY, $nmap);
        $this->dijkstra($nmaxX, $nmaxY, $nmap);
    }
}

(new Day15())->start();
