<?php

ini_set("memory_limit", -1);

class Day12 {
    private $connections;
    private $caves;

    public function read($file) {
        $this->connections = [];
        $this->caves = [];

        $handle = fopen($file, "r");
        if ($handle) {
            $cavesMap = [];
            while ($str = fgets($handle)) {
                list ($from, $to) = explode("-", trim($str), 2);
                $cavesMap[$from] = true;
                $cavesMap[$to] = true;
                // add both ways
                if (isset($this->connections[$from]) && is_array($this->connections[$from])) {
                    $this->connections[$from][] = $to;
                } else {
                    $this->connections[$from] = [$to];
                }
                if (isset($this->connections[$to]) && is_array($this->connections[$to])) {
                    $this->connections[$to][] = $from;
                } else {
                    $this->connections[$to] = [$from];
                }

            };
            $this->caves = array_keys($cavesMap);
        }
    }

    public function startSearch() {
        $todo = [ ['start'] ];
        $found = [];

        while(($next = array_shift($todo)) != null) {
            $current = $next[sizeof($next)-1];
            if ($current === 'end') {
                echo "Found Path1: " . $this->getPath($next) . "\n";
                $found[] = $next;
            }
            if (isset($this->connections[$current])) {
                foreach ($this->connections[$current] as $to) {
                    if ($this->canVisit($to, $next, 1)) {
                        $todo[] = array_merge($next, [$to]);
                    }
                }
            }
        }
        return $found;
    }

    public function startSearch2() {
        $found = [];

        $lowerCaves = array_filter($this->caves, function($i) { return ctype_lower($i) && $i != 'start' && $i != 'end'; });
        foreach($lowerCaves as $twice) {
            $todo = [ ['start'] ];

            while (($next = array_shift($todo)) != null) {
                $current = $next[sizeof($next) - 1];
                $hash = $this->getPath($next);
                if ($current === 'end' && !isset($found[$hash])) {
                    echo "Found Path2 ($twice): " . $this->getPath($next) . "\n";
                    $found[$hash] = $next;
                }
                if (isset($this->connections[$current])) {
                    foreach ($this->connections[$current] as $to) {
                        if ($this->canVisit($to, $next, ($to == $twice) ? 2 : 1)) {
                            $todo[] = array_merge($next, [$to]);
                        }
                    }
                }
            }
        }

        return array_values($found);
    }

    public function canVisit($cave, $parentPath, $maxCount) {
        $counts = count(array_keys($parentPath, $cave));
        return ctype_upper($cave) || $counts < $maxCount;
    }
    public function getPath($path) {
        return join(" => ", $path);
    }
    public function showPaths($paths) {
        foreach($paths as $path) {
            echo $this->showPaths($path) . "\n";
        }
    }

    public function start() {
        $this->read("input4.txt");
        //$found1 = $this->startSearch();
        $found2 = $this->startSearch2();
        //echo "Found: " . sizeof($found1) . " paths (result 1)\n";
        echo "Found: " . sizeof($found2) . " paths (result 2)\n";
    }
}

(new Day12())->start();

