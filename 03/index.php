<?php

function oxygen($bits, $pos, $winner, $looser) {
    // calculate most common value
    $lines = $set = 0;
    for($i=0 ; $i<sizeof($bits) ; ++$i) {
        ++$lines;
        if ($bits[$i][$pos] === '1') {
            ++$set;
        }
    }

    // which is the relevant bis
    $relevant = (2*$set >= $lines) ? $winner : $looser;

    // only keep the matching ones
    $res = [];
    for($i=0 ; $i<sizeof($bits) ; ++$i) {
        if ($bits[$i][$pos] === $relevant) {
            $res[] = $bits[$i];
        }
    }

    if (sizeof($res) > 1) {
        $res = oxygen($res, $pos+1, $winner, $looser);
    } else {
        $res = $res[0];
    }

    return $res;
}

$handle = fopen("input.txt", "r");
if ($handle) {
    $lines = 0;
    $allBits = [];
    $gammaBits = [];
    while (($line = fgets($handle)) !== false) {
        ++$lines;
        $bits = str_split(trim($line));
        $allBits[] = $bits;
        if ($lines == 1) {
            for($i=0 ; $i<sizeof($bits) ; ++$i) {
                $gammaBits[$i] = 0;
            }
        }

        for($i=0 ; $i<sizeof($bits) ; ++$i) {
            if ($bits[$i] === '1') {
                ++$gammaBits[$i];
            }
        }
    }
    fclose($handle);

    $gamma = '';
    $epsilon = '';
    for($i=0 ; $i<sizeof($gammaBits) ; ++$i) {
        $gamma .= (2*$gammaBits[$i] > $lines) ? '1' : '0';
        $epsilon .= (2*$gammaBits[$i] <= $lines) ? '1' : '0';
    }

    $gammaDec = bindec($gamma);
    $epsilonDec = bindec($epsilon);

    $result1 = $gammaDec * $epsilonDec;

    echo "Lines ..............: $lines\n";
    echo "Gamma ..............: $gamma\n";
    echo "Gamma (dec) ........: $gammaDec\n";
    echo "Epsilon ............: $epsilon\n";
    echo "Epsilon (dec) ......: $epsilonDec\n";
    echo "Solution #1 ........: $result1\n";

    $oxygenGeneratorRating = join("", oxygen($allBits, 0, '1', '0'));
    $oxygenGeneratorRatingDec = bindec($oxygenGeneratorRating);
    $co2ScrubberRating = join("", oxygen($allBits, 0, '0', '1'));
    $co2ScrubberRatingDec = bindec($co2ScrubberRating);

    $result2 = $oxygenGeneratorRatingDec * $co2ScrubberRatingDec;

    echo "O2 gen .............: $oxygenGeneratorRating\n";
    echo "O2 gen (dec) .......: $oxygenGeneratorRatingDec\n";
    echo "CO2 scrubber .......: $co2ScrubberRating\n";
    echo "CO2scrubber (dec) ..: $co2ScrubberRatingDec\n";
    echo "Solution #2 ........: $result2\n";
}

