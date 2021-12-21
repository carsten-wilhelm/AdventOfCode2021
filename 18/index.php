<?php
ob_implicit_flush();

const NODE = "*";

class Node
{
    private $left;
    private $right;
    private $parent;
    private $value;

    private static $root;

    public function __construct($parent)
    {
        $this->parent = $parent;
        $this->value = NODE;
    }

    public static function parse($str) {
        Node::$root = new Node(null);
        $curr = Node::$root;

        foreach(str_split($str) as $char) {
            switch ($char) {
                case "[":
                    $curr->left = new Node($curr);
                    $curr = $curr->left;
                    break;
                case "]":
                    $curr = $curr->parent;
                    break;
                case ",":
                    $curr = $curr->parent;
                    $curr->right = new Node($curr);
                    $curr = $curr->right;
                    break;
                default:
                    $curr->value = $char;
                    break;
            }
        }

        return Node::$root;
    }

    public function outputTree()
    {
        return $this->_outputTree(0);
    }
    public function _outputTree($depth)
    {
        $prefix = str_repeat("    ", $depth);
        if ($this->value === NODE) {
            return $this->left->_outputTree($depth+1)
                . "$prefix" . $this->value . " (" . spl_object_id($this) . ")\n"
                . $this->right->_outputTree($depth+1);
        } else {
            return $prefix . $this->value . " (" . spl_object_id($this) . ")\n";
        }
    }

    public function outputString()
    {
        if ($this->value === NODE) {
            return "[" . $this->left->outputString() . "," . $this->right->outputString() . "]";
        } else {
            return $this->value;
        }
    }

    public function add($node) {
        Node::$root = new Node(null);
        Node::$root->left = $this;
        Node::$root->left->parent = Node::$root;
        Node::$root->right = $node;
        Node::$root->right->parent = Node::$root;
        //echo ">>> Result before reduce: " . Node::$root->outputString() . "\n";
        Node::$root->reduceAll();
        //echo ">>> Result after add: " . Node::$root->outputString() . "\n";
        return Node::$root;
    }

    private function updateLeft($value) {
        // Go up while this is the left node
        $curr = $this;
        do {
            $child = $curr;
            $curr = $curr->parent;
        } while($curr != null && $curr->left === $child);
        if ($curr === null) {
            // no left
            return;
        }
        // go to left once
        $curr = $curr->left;
        // Go down right as lng as there are nodes
        while($curr->value === NODE) {
            $curr = $curr->right;
        }
        $curr->value += $value;
    }

    private function updateRight($value) {
        // Go up while this is the right node
        $curr = $this;
        do {
            $child = $curr;
            $curr = $curr->parent;
        } while($curr != null && $curr->right === $child);
        if ($curr === null) {
            // no right
            return;
        }
        // go to right once
        $curr = $curr->right;
        // Go down left as lng as there are nodes
        while($curr->value === NODE) {
            $curr = $curr->left;
        }
        $curr->value += $value;
    }

    public function reduceAll() {
        do {
            $found = $this->reduceExplode();
            if (!$found) {
                $found = $this->reduceSplit();
            }
        } while($found);
    }

    public function reduceExplode($depth = 1) {
        if ($this->value === NODE) {
            if ($depth === 5) {
                //echo "Explode pair " . $this->outputString() . "\n";
                $this->updateLeft($this->left->value);
                $this->updateRight($this->right->value);
                $this->value = 0;
                unset($this->left);
                unset($this->right);
                //echo "Result: " . Node::$root->outputString() . "\n";
                return true;
            } else {
                if ($this->left->reduceExplode($depth + 1)) {
                    return true;
                }
                if ($this->right->reduceExplode($depth + 1)) {
                    return true;
                }
            }
        }
        return false;
    }
    public function reduceSplit($depth = 1) {
        if ($this->value === NODE) {
            if ($this->left->reduceSplit($depth + 1)) {
                return true;
            }
            if ($this->right->reduceSplit($depth + 1)) {
                return true;
            }
        } else if ($this->value >= 10) {
            //echo "Split value " . $this->outputString() . "\n";
            $this->left = new Node($this);
            $this->left->value = (int)($this->value / 2);
            $this->right = new Node($this);
            $this->right->value = $this->value - $this->left->value;
            $this->value = NODE;
            //echo "Result: " . Node::$root->outputString() . "\n";
            return true;
        }
        return false;
    }

    public function magnitude() {
        if ($this->value === NODE) {
            return 3 * $this->left->magnitude() + 2 * $this->right->magnitude();
        } else {
            return $this->value;
        }
    }
}

class Day18 {

    public function read($file) {
        $handle = fopen($file, "r");
        if ($handle) {
            $numbers = [];
            while ($str = fgets($handle)) {
                $numbers[] = trim($str);
            };

            // Part 1
            foreach($numbers as $number) {
                $next = Node::parse($number);
                $node = (isset($node)) ? $node->add($next) : $next;
                echo $node->outputString() . "\n";
                echo "---------------------------------------------\n";
            }
            echo "Final result: " . $node->outputString() . "\n";
            echo "Magnitude: " . $node->magnitude() . "\n";

            // Part 2
            $highest = -INF;
            for($i=0 ; $i<sizeof($numbers); ++$i) {
                for($j=0 ; $j<sizeof($numbers); ++$j) {
                    if ($i !== $j) {
                        $node1 = Node::parse($numbers[$i]);
                        $node2 = Node::parse($numbers[$j]);
                        $node = $node1->add($node2);
                        if ($node->magnitude() > $highest) {
                            $highest = $node->magnitude();
                            echo "New highest: $highest\n";
                            //echo "    " . $numbers[$i] . "\n";
                            //echo "    " . $numbers[$j] . "\n";
                            //echo "    ==> " . $node->outputString() . "\n";
                        }
                    }
                }
            }
        }
    }

    public function start() {
        $this->read("input2.txt");
    }
}

(new Day18())->start();
