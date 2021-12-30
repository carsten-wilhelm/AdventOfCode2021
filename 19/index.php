<?php
ob_implicit_flush();

require_once 'vendor/autoload.php';

class Point {
    private $matrix;

    public function __construct($x, $y, $z)
    {
        $this->matrix = new \mcordingley\LinearAlgebra\Matrix([[$x, $y, $z, 1]]);
    }

    public function x() {
        return $this->matrix->get(0, 0);
    }

    public function y() {
        return $this->matrix->get(0, 1);
    }

    public function z() {
        return $this->matrix->get(0, 2);
    }

    public function apply($matrix) {
        $new = $this->matrix->multiplyMatrix($matrix);
        return new Point($new->get(0, 0), $new->get(0, 1), $new->get(0, 2));
    }

    public function getDistanceTranslation($p2) {
        $dx = $this->x() - $p2->x();
        $dy = $this->y() - $p2->y();
        $dz = $this->z() - $p2->z();
        return Transformations::translate($dx, $dy, $dz);
    }

    public function __toString()
    {
        return "[" . $this->x() . "," . $this->y() . "," . $this->z() . "]";
    }
}

class Transformations {

    public static function translate($x, $y, $z) {
        return new \mcordingley\LinearAlgebra\Matrix([
            [  1,  0,  0,  0],
            [  0,  1,  0,  0],
            [  0,  0,  1,  0],
            [ $x, $y, $z,  1]
        ]);
    }

    public static function rotate($type) {
        switch($type) {
            case 0: // (x, y, z) => (x, y, z)
                return new \mcordingley\LinearAlgebra\Matrix([
                    [ 1,  0,  0,  0],
                    [ 0,  1,  0,  0],
                    [ 0,  0,  1,  0],
                    [ 0,  0,  0,  1]
                ]);
            case 1: // (x, y, z) => (-y, x, z)
                return new \mcordingley\LinearAlgebra\Matrix([
                    [ 0,  1,  0,  0],
                    [-1,  0,  0,  0],
                    [ 0,  0,  1,  0],
                    [ 0,  0,  0,  1]
                ]);
            case 2: // (x, y, z) => (-x, -y, z)
                return new \mcordingley\LinearAlgebra\Matrix([
                    [-1,  0,  0,  0],
                    [ 0, -1,  0,  0],
                    [ 0,  0,  1,  0],
                    [ 0,  0,  0,  1]
                ]);
            case 3: // (x, y, z) => (y, -x, z)
                return new \mcordingley\LinearAlgebra\Matrix([
                    [ 0, -1,  0,  0],
                    [ 1,  0,  0,  0],
                    [ 0,  0,  1,  0],
                    [ 0,  0,  0,  1]
                ]);

            case 4: // (x, y, z) => (-x, y, -z)
                return new \mcordingley\LinearAlgebra\Matrix([
                    [-1,  0,  0,  0],
                    [ 0,  1,  0,  0],
                    [ 0,  0, -1,  0],
                    [ 0,  0,  0,  1]
                ]);
            case 5: // (x, y, z) => (-y, -x, -z)
                return new \mcordingley\LinearAlgebra\Matrix([
                    [ 0, -1,  0,  0],
                    [-1,  0,  0,  0],
                    [ 0,  0, -1,  0],
                    [ 0,  0,  0,  1]
                ]);
            case 6: // (x, y, z) => (x, -y, -z)
                return new \mcordingley\LinearAlgebra\Matrix([
                    [ 1,  0,  0,  0],
                    [ 0, -1,  0,  0],
                    [ 0,  0, -1,  0],
                    [ 0,  0,  0,  1]
                ]);
            case 7: // (x, y, z) => (y, x, -z)
                return new \mcordingley\LinearAlgebra\Matrix([
                    [ 0,  1,  0,  0],
                    [ 1,  0,  0,  0],
                    [ 0,  0, -1,  0],
                    [ 0,  0,  0,  1]
                ]);

            case  8: // (x, y, z) => (x,-z, y)
                return new \mcordingley\LinearAlgebra\Matrix([
                    [ 1,  0,  0,  0],
                    [ 0,  0,  1,  0],
                    [ 0, -1,  0,  0],
                    [ 0,  0,  0,  1]
                ]);
            case  9: // (x, y, z) => (z, x, y)
                return new \mcordingley\LinearAlgebra\Matrix([
                    [ 0,  1,  0,  0],
                    [ 0,  0,  1,  0],
                    [ 1,  0,  0,  0],
                    [ 0,  0,  0,  1]
                ]);
            case 10: // (x, y, z) => (-x,z, y)
                return new \mcordingley\LinearAlgebra\Matrix([
                    [-1,  0,  0,  0],
                    [ 0,  0,  1,  0],
                    [ 0,  1,  0,  0],
                    [ 0,  0,  0,  1]
                ]);
            case 11: // (x, y, z) => (-z,-x, y)
                return new \mcordingley\LinearAlgebra\Matrix([
                    [ 0, -1,  0,  0],
                    [ 0,  0,  1,  0],
                    [-1,  0,  0,  0],
                    [ 0,  0,  0,  1]
                ]);

            case 12: // (x, y, z) => (-x,-z,-y)
                return new \mcordingley\LinearAlgebra\Matrix([
                    [-1,  0,  0,  0],
                    [ 0,  0, -1,  0],
                    [ 0, -1,  0,  0],
                    [ 0,  0,  0,  1]
                ]);
            case 13: // (x, y, z) => (z, -x, -y)
                return new \mcordingley\LinearAlgebra\Matrix([
                    [ 0, -1,  0,  0],
                    [ 0,  0, -1,  0],
                    [ 1,  0,  0,  0],
                    [ 0,  0,  0,  1]
                ]);
            case 14: // (x, y, z) => (x,z, -y)
                return new \mcordingley\LinearAlgebra\Matrix([
                    [ 1,  0,  0,  0],
                    [ 0,  0, -1,  0],
                    [ 0,  1,  0,  0],
                    [ 0,  0,  0,  1]
                ]);
            case 15: // (x, y, z) => (-z,x, -y)
                return new \mcordingley\LinearAlgebra\Matrix([
                    [ 0,  1,  0,  0],
                    [ 0,  0, -1,  0],
                    [-1,  0,  0,  0],
                    [ 0,  0,  0,  1]
                ]);

            case 16: // (x, y, z) => (-z, y, x)
                return new \mcordingley\LinearAlgebra\Matrix([
                    [ 0,  0,  1,  0],
                    [ 0,  1,  0,  0],
                    [-1,  0,  0,  0],
                    [ 0,  0,  0,  1]
                ]);
            case 17: // (x, y, z) => (-y,-z, x)
                return new \mcordingley\LinearAlgebra\Matrix([
                    [ 0,  0,  1,  0],
                    [-1,  0,  0,  0],
                    [ 0, -1,  0,  0],
                    [ 0,  0,  0,  1]
                ]);
            case 18: // (x, y, z) => (z,-y, x)
                return new \mcordingley\LinearAlgebra\Matrix([
                    [ 0,  0,  1,  0],
                    [ 0, -1,  0,  0],
                    [ 1,  0,  0,  0],
                    [ 0,  0,  0,  1]
                ]);
            case 19: // (x, y, z) => (y,z, x)
                return new \mcordingley\LinearAlgebra\Matrix([
                    [ 0,  0,  1,  0],
                    [ 1,  0,  0,  0],
                    [ 0,  1,  0,  0],
                    [ 0,  0,  0,  1]
                ]);

            case 20: // (x, y, z) => (z, y, -x)
                return new \mcordingley\LinearAlgebra\Matrix([
                    [ 0,  0, -1,  0],
                    [ 0,  1,  0,  0],
                    [ 1,  0,  0,  0],
                    [ 0,  0,  0,  1]
                ]);
            case 21: // (x, y, z) => (-y, z, -x)
                return new \mcordingley\LinearAlgebra\Matrix([
                    [ 0,  0, -1,  0],
                    [-1,  0,  0,  0],
                    [ 0,  1,  0,  0],
                    [ 0,  0,  0,  1]
                ]);
            case 22: // (x, y, z) => (-z, -y, -x)
                return new \mcordingley\LinearAlgebra\Matrix([
                    [ 0,  0, -1,  0],
                    [ 0, -1,  0,  0],
                    [-1,  0,  0,  0],
                    [ 0,  0,  0,  1]
                ]);
            case 23: // (x, y, z) => (y, -z, -x)
                return new \mcordingley\LinearAlgebra\Matrix([
                    [ 0,  0, -1,  0],
                    [ 1,  0,  0,  0],
                    [ 0, -1,  0,  0],
                    [ 0,  0,  0,  1]
                ]);
        }
        die("Unknown rotation type $type");
    }
}
class Scanner {
    public $number;
    private $points = [];

    public function __construct($num)
    {
        $this->number = $num;
    }

    public function add($point) {
        $this->points[] = $point;
    }

    public function translate($matrix) {
        for($i=0 ; $i<count($this->points) ; ++$i) {
            $this->points[$i] = $this->points[$i]->apply($matrix);
        }
        return $this;
    }

    public function rotate($type) {
        $rotated = new Scanner($this->number);
        $t = Transformations::rotate($type);
        foreach($this->points as $p) {
            $rotated->add($p->apply($t));
        }
        return $rotated;
    }

    public function compare($other) {
        for($r=0 ; $r<24 ; ++$r) {
            $rother = $other->rotate($r);

            $comb = [];
            // try all point combinations
            for ($i = 0; $i < count($this->points); ++$i) {
                for ($j = 0; $j < count($rother->points); ++$j) {
                    $t = $this->points[$i]->getDistanceTranslation($rother->points[$j]);
                    $tStr = $t->__toString();
                    if (isset($comb[$tStr])) {
                        $comb[$tStr]['count']++;
                    } else {
                        $comb[$tStr] = ["count" => 1, "t" => $t];
                    }
                }
            }
            foreach ($comb as $c) {
                if ($c["count"] >= 12) {
                    echo $this->number . " <=> " . $other->number . ": " . $c["t"] . "\n";
                    return [$c["t"], $rother->translate($c["t"])];
                }
            }
        }
        return [null, null];
    }

    public function merge($other) {
        $tmp = [];
        foreach($this->points as $p) {
            $tmp[$p->__toString()] = $p;
        }
        foreach($other->points as $p) {
            $tmp[$p->__toString()] = $p;
        }
        $this->points = array_values($tmp);
    }

    public function size() {
        return count($this->points);
    }

    public function __toString()
    {
        return $this->points[0]->__toString();
    }
}

class Day19 {

    private $scanners = [];
    private $positions = [];

    public function read($file) {
        $handle = fopen($file, "r");
        if ($handle) {
            $scanner = null;
            while ($str = fgets($handle)) {
                if (preg_match('/^--- scanner ([0-9]+) ---/', $str, $matches)) {
                    $scanner = new Scanner(intval($matches[1]));
                    $this->scanners[$scanner->number] = $scanner;
                } else if (preg_match('/([-0-9]+),([-0-9]+),([-0-9]+)/', $str, $matches)) {
                    $point = new Point($matches[1], $matches[2], $matches[3]);
                    $scanner->add($point);
                }
            };
        }
    }

    public function merge() {
        for($j=1 ; $j<count($this->scanners); ++$j) {
            //echo "Compare $i with $j\n";
            $s0 = $this->scanners[0];
            $s1 = $this->scanners[$j];
            list ($t, $r1)= $s0->compare($s1);
            if ($r1 !== null) {
                $this->positions[] = [$t->get(3, 0), $t->get(3, 1), $t->get(3, 2)];
                array_splice($this->scanners, $j, 1);
                $s0->merge($r1);
                return;
            }
        }
    }

    public function start() {
        $this->read("input2.txt");

        $this->positions[] = [0, 0, 0];
        while(count($this->scanners) > 1) {
            $this->merge();
        }
        echo "Count: " . $this->scanners[0]->size() . "\n";

        $max = -INF;
        for($i=0 ; $i<count($this->positions)-1 ; ++$i) {
            $p1 = $this->positions[$i];
            for($j=$i+1 ; $j<count($this->positions) ; ++$j) {
                $p2 = $this->positions[$j];
                $dist = abs($p1[0] - $p2[0]) + abs($p1[1] - $p2[1]) + abs($p1[2] - $p2[2]);
                if ($dist > $max) {
                    echo "Max dist: $dist\n";
                    $max = $dist;
                }
            }
        }
    }
}

(new Day19())->start();
