<?php

class Day14 {
    private $template;
    private $rules;

    public function read($file) {
        $this->rules = [];
        $handle = fopen($file, "r");
        if ($handle) {
            // read template
            $this->template = trim(fgets($handle));
            // ignore empty line
            fgets($handle);
            // read rules
            while ($str = fgets($handle)) {
                list ($pattern, $insert) = explode(" -> ", trim($str));
                $this->rules[$pattern] = $insert;
            };
        }
    }

    public function transformOnce($template) {
        $newTemplate = '';
        for($i=0 ; $i<strlen($template)-1 ; ++$i) {
            $search = $template[$i] . $template[$i+1];
            $newTemplate .= $template[$i] . $this->rules[$search];
        }
        $newTemplate .= $template[strlen($template)-1];
        //echo "Transformed: $newTemplate\n";
        $count = count_chars($newTemplate, 1);
        sort($count);
        echo "Result: " . ($count[count($count)-1] - $count[0]) . "\n";

        return $newTemplate;
    }

    /*
    public function transform40($steps) {
        $tmp = str_split($this->template);

        for($i=0 ; $i<count($tmp)-1 ; ++$i) {
            $start = $template = $tmp[$i] . $tmp[$i + 1];
            for($j=0 ; $j<$steps ; ++$j) {
                $template = $this->transform($template);
                echo "$start => $template\n";
            }
        }
    }
    */


    public function transform($template) {
        $todo = str_split($template);
        $pairs = [];
        for($i=0 ; $i<count($todo)-1 ; ++$i) {
            $this->setPair($pairs, $todo[$i].$todo[$i+1]);
        }
        for($i=0 ; $i<40 ; ++$i) {
            $pairs = $this->process($pairs);
        }
        $this->countChars($pairs);
    }
    public function setPair(&$pairs, $pattern, $value=1) {
        if (isset($pairs[$pattern])) {
            $pairs[$pattern] = $pairs[$pattern] + $value;
        } else {
            $pairs[$pattern] = $value;

        }
    }
    public function process($pairs) {
        //echo "In > "; $this->showPairs($pairs);
        foreach($pairs as $pair => $value) {
            $next = $this->rules[$pair];
            $this->setPair($newPairs, $pair[0] . $next, $value);
            $this->setPair($newPairs, $next . $pair[1], $value);
        }
        //echo "Out> "; $this->showPairs($newPairs);
        return $newPairs;
    }
    public function showPairs($pairs) {
        foreach($pairs as $pair => $value) {
            echo "$value x $pair  ";
        }
        echo "\n";
    }
    public function countChars($pairs) {
        $count = [];
        foreach($pairs as $pair => $value) {
            $this->setPair($count, $pair[1], $value);
        }
        // Add first char
        $this->setPair($count, $this->template[0], 1);
        sort($count);
        echo "Result: " . ($count[count($count)-1] - $count[0]) . "\n";
    }

    public function start() {
        $this->read("input2.txt");
        $this->transform($this->template);
    }
}

(new Day14())->start();
