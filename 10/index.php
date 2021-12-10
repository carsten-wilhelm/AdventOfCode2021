<?php

class Chunk {
    private $chars;
    private $opened;
    private $firstCorrupt;

    public function __construct($line) {
        $this->chars = $line;
        $this->firstCorrupt = null;
        $this->opened = [];

        for($i=0 ; $i<strlen($line) ; ++$i) {
            $char = $line[$i];
            switch($char) {
                case '(':
                case '[':
                case '{':
                case '<':
                    // opened a new chunk
                    $this->opened[] = $char;
                    break;
                case ')';
                    $last = array_pop($this->opened);
                    if ($last != '(') {
                        $this->firstCorrupt = $char;
                        return;
                    }
                    break;
                case ']';
                    if (array_pop($this->opened) != '[') {
                        $this->firstCorrupt = $char;
                        return;
                    }
                    break;
                case '}';
                    if (array_pop($this->opened) != '{') {
                        $this->firstCorrupt = $char;
                        return;
                    }
                    break;
                case '>';
                    if (array_pop($this->opened) != '<') {
                        $this->firstCorrupt = $char;
                        return;
                    }
                    break;
            }
        }
    }

    public function getChunk() {
        return $this->chars;
    }

    public function getFirstCorrupt() {
        return $this->firstCorrupt;
    }

    public function getFirstCorruptScore() {
        switch($this->firstCorrupt) {
            case ')': return 3;
            case ']': return 57;
            case '}': return 1197;
            case '>': return 25137;
            case null: return 0;
        }
        echo "Invalid corrupt char: " . $this->firstCorrupt . "\n";
        exit(1);
    }

    public function getRemainingScore() {
        $sum = 0;
        if ($this->firstCorrupt == null) {
            foreach(array_reverse($this->opened) as $char) {
                $sum *= 5;
                switch($char) {
                    case '(': $sum += 1; break;
                    case '[': $sum += 2; break;
                    case '{': $sum += 3; break;
                    case '<': $sum += 4; break;
                }
            }
        }
        return $sum;
    }

}

class Day10 {
    private $chunks;

    public function read() {
        $this->chunks = [];
        $handle = fopen("input2.txt", "r");
        if ($handle) {
            while ($str = fgets($handle)) {
                $this->chunks[] = new Chunk(trim($str));
            };
        }
    }

    public function result1() {
        $sum = 0;
        foreach ($this->chunks as $chunk) {
            $sum += $chunk->getFirstCorruptScore();
        }
        echo "Score #1: $sum\n";
    }

    public function result2() {
        $scores = [];
        foreach ($this->chunks as $chunk) {
            if (($score = $chunk->getRemainingScore()) > 0) {
                $scores[] = $score;
            }
        }
        sort($scores);
        echo "Median score #2: " . $scores[(int)(sizeof($scores)/2)] . "\n";
    }

    public function start() {
        $this->read();
        $this->result1();
        $this->result2();
    }

}

(new Day10())->start();

