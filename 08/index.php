<?php

class Entry {
    private $inputs;
    private $outputs;

    public function __construct($line)
    {
        list($input, $output) = explode(" | ", trim($line), 2);
        $this->inputs = explode(" ", $input);
        $this->outputs = explode(" ", $output);
    }

    public function countOutput() {
        $count = 0;
        foreach ($this->outputs as $o) {
            switch(strlen($o)) {
                case 2: // 1
                case 3: // 7
                case 4: // 4
                case 7: // 8
                    ++$count;
            }
        }
        return $count;
    }

    public function getDigit($on) {
        switch ($on) {
            case 'abcefg':
                return 0;
            case 'cf':
                return 1;
            case 'acdeg':
                return 2;
            case 'acdfg':
                return 3;
            case 'bcdf':
                return 4;
            case 'abdfg':
                return 5;
            case 'abdefg':
                return 6;
            case 'acf':
                return 7;
            case 'abcdefg':
                return 8;
            case 'abcdfg':
                return 9;
        }
        return -1;
    }

    public function outputSegments($combinations) {
        foreach($combinations as $comb) {
            list ($a, $b, $c, $d, $e, $f, $g) = str_split($comb);
            $map = array_flip(['a' => $a, 'b' => $b, 'c' => $c, 'd' => $d, 'e' => $e, 'f' => $f, 'g' => $g]);

            $found = true;
            foreach ($this->inputs as $input) {
                $mapped = [];
                foreach (str_split($input) as $c) {
                    $mapped[] = $map[$c];
                }
                sort($mapped);
                $on = join("", $mapped);
                $digit = $this->getDigit($on);
                if ($digit === -1) {
                    $found = false;
                    break;
                }
            }
            if ($found) {
                $num = 0;
                foreach ($this->outputs as $output) {
                    $mapped = [];
                    foreach (str_split($output) as $c) {
                        $mapped[] = $map[$c];
                    }
                    sort($mapped);
                    $on = join("", $mapped);
                    $digit = $this->getDigit($on);

                    $num = 10 * $num + intval($digit);
                }
                return $num;
            }
        }
    }
}

class Day08 {
    private $entries;

    public function read() {
        $this->entries = [];
        $handle = fopen("input2.txt", "r");
        if ($handle) {
            while ($str = fgets($handle)) {
                $this->entries[] = new Entry($str);
            };
        }
    }

    public function count1() {
        $count = 0;
        foreach ($this->entries as $e) {
            $count += $e->countOutput();
        }
        echo "Count: $count\n";
    }

    public function analyze() {
        $all = $this->combine("abcdefg");
        $sum = 0;
        foreach ($this->entries as $e) {
            $num = $e->outputSegments($all);
            $sum += $num;
            echo "Output: $num\n";
        }
        echo "Sum: $sum\n";
    }

    private function combine($chars) {
        if (strlen($chars) == 1) {
            return [$chars];
        }

        $list = [];
        for($i=0 ; $i<strlen($chars) ; ++$i) {
            foreach ($this->combine(substr($chars, 0, $i) . substr($chars, $i+1)) as $item) {
                $list[] = $chars[$i] . $item;
            }
        }
        return $list;
    }

    public function start() {
        $this->read();
        $this->count1();
        $this->analyze();
    }

}

(new Day08())->start();
