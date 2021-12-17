<?php

function mylog($msg) {
    //echo $msg;
}

class Day16 {
    private $sumVersion;

    public function read($file) {
        $handle = fopen($file, "r");
        if ($handle) {
            while ($str = fgets($handle)) {
                if ($str[0] == '#') continue;

                $raw = trim($str);
                $binary = $this->hex2bin($raw);

                $this->sumVersion = 0;
                //echo "Raw: $raw => $binary\n";
                list($result) = $this->processPacket($binary);
                echo "Version sum: " . $this->sumVersion . "\n";
                echo "Result: " . join(", ", $result) . "\n";
                echo "--------------------------------------------------\n";
            };
        }

    }

    public function hex2bin($hex) {
        return array_reduce(str_split($hex), function($bin,$h){
            switch($h) {
                case '0': return $bin . '0000';
                case '1': return $bin . '0001';
                case '2': return $bin . '0010';
                case '3': return $bin . '0011';
                case '4': return $bin . '0100';
                case '5': return $bin . '0101';
                case '6': return $bin . '0110';
                case '7': return $bin . '0111';
                case '8': return $bin . '1000';
                case '9': return $bin . '1001';
                case 'A': return $bin . '1010';
                case 'B': return $bin . '1011';
                case 'C': return $bin . '1100';
                case 'D': return $bin . '1101';
                case 'E': return $bin . '1110';
                case 'F': return $bin . '1111';
            }
        }, "");
    }

    public function processPacket($packet, $prefix="", $max=INF) {
        $literals = [];
        do {
            $versionStr = substr($packet, 0, 3);
            $version = bindec($versionStr);
            $this->sumVersion += $version;
            $typeIdStr = substr($packet, 3, 3);
            $typeId = bindec($typeIdStr);
            $data = substr($packet, 6);
            //mylog($prefix . "Input: version=$version ($versionStr) - type=$typeId ($typeIdStr) => $data\n");
            switch ($typeIdStr) {
                case '100': // 100 = 4 (literal)
                    list($literal, $remaining) = $this->processLiteral($data,$prefix."  ");
                    $literals[] = $literal;
                    break;
                default:
                    list($literal, $remaining) = $this->processOperator($typeId, $data,$prefix."  ");
                    $literals[] = $literal;
                    break;
            }
            $packet = $remaining;
            --$max;
        } while($max>0 && strpos($packet, "1") !== FALSE);
        return [$literals, $packet];
    }

    public function processLiteral($data, $prefix) {
        // split to 4 byte parts
        mylog($prefix . "> Literal\n");
        $numberStr = "";
        for(;;) {
            $g = substr($data, 0, 5);
            //mylog($prefix . "> Group: $g\n");
            $data = substr($data, 5);
            $numberStr .= substr($g, 1);
            if ($g[0] === "0") {
                break;
            }
        }
        $number = bindec($numberStr);
        mylog($prefix . ">> Literal: $number\n");
        return [$number, $data];
    }

    public function processOperator($typeId, $data, $prefix) {
        $lengthTypeID = substr($data, 0, 1);
        $subdata = substr($data, 1);
        if ($lengthTypeID === "0") {
            list ($literals, $remaining) = $this->processOperatorLength($subdata, $prefix);
        } else {
            list ($literals, $remaining) = $this->processOperatorCount($subdata, $prefix);
        }
        return [$this->applyOperator($typeId, $literals), $remaining];
    }

    public function processOperatorLength($data, $prefix) {
        mylog($prefix . "> Operator (length)\n");
        $lengthStr = substr($data, 0, 15);
        $length = bindec($lengthStr);
        $subdata = substr($data, 15, $length);
        $remaining = substr($data, 15+$length);
        mylog($prefix . "> Length operator: $length data\n");
        list ($literals) = $this->processPacket($subdata, $prefix."  ");
        return [$literals, $remaining];
    }

    public function processOperatorCount($data, $prefix) {
        mylog($prefix . "> Operator (count)\n");
        $countStr = substr($data, 0, 11);
        $count = bindec($countStr);
        $subdata = substr($data, 11);
        mylog($prefix . "> Count operator: $count x data\n");
        list($literals, $remaining) = $this->processPacket($subdata, $prefix."  ", $count);
        return [$literals, $remaining];
    }

    public function applyOperator($type, $values) {
        switch($type) {
            case 0: // sum
                $result = array_reduce($values, function($sum,$next){return $sum+$next;}, 0);
                mylog("Sum: " . join("+", $values) . " = $result\n");
                return $result;
            case 1: // product
                $result = array_reduce($values, function($prod,$next){return $prod*$next;}, 1);
                mylog("Product: " . join("*", $values) . " = $result\n");
                return $result;
            case 2: // min
                $result = array_reduce($values, function($min,$next){return min($min,$next);}, INF);
                mylog("Min: " . join(",", $values) . " = $result\n");
                return $result;
            case 3: // max
                $result = array_reduce($values, function($max,$next){return max($max,$next);}, -INF);
                mylog("Max: " . join(",", $values) . " = $result\n");
                return $result;
            case 5: // greater
                $result = ($values[0] > $values[1]) ? 1 : 0;
                mylog("Greater: " . join(" > ", $values) . " = $result\n");
                return $result;
            case 6: // less than
                $result = ($values[0] < $values[1]) ? 1 : 0;
                mylog("Less then: " . join(" > ", $values) . " = $result\n");
                return $result;
            case 7: // equals
                $result = ($values[0] == $values[1]) ? 1 : 0;
                mylog("Equals: " . join(" > ", $values) . " = $result\n");
                return $result;
        }
        return 0;
    }


    public function start() {
        $this->read("input2.txt");
    }
}

(new Day16())->start();
