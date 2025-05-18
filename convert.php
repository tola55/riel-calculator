<?php
function numberToWordsEnglish($number) {
    $ones = ["", "one", "two", "three", "four", "five", "six", "seven", "eight", "nine",
             "ten", "eleven", "twelve", "thirteen", "fourteen", "fifteen",
             "sixteen", "seventeen", "eighteen", "nineteen"];
    $tens = ["", "", "twenty", "thirty", "forty", "fifty", "sixty", "seventy", "eighty", "ninety"];
    $thousands = ["", " thousand", " million", " billion"];

    if ($number == 0) return "zero";

    $word = "";
    $i = 0;

    while ($number > 0) {
        $n = $number % 1000;
        if ($n != 0) {
            $str = "";
            if ($n >= 100) {
                $str .= $ones[intval($n / 100)] . " hundred ";
                $n %= 100;
            }
            if ($n < 20) {
                $str .= $ones[$n];
            } else {
                $str .= $tens[intval($n / 10)];
                if ($n % 10 > 0) {
                    $str .= "-" . $ones[$n % 10];
                }
            }
            $word = $str . $thousands[$i] . " " . $word;
        }
        $number = intval($number / 1000);
        $i++;
    }
    return ucfirst(trim($word)) . " Riel";
}

function numberToWordsKhmer($number) {
    $ones = ["", "មួយ", "ពីរ", "បី", "បួន", "ប្រាំ", "ប្រាំមួយ", "ប្រាំពីរ", "ប្រាំបី", "ប្រាំបួន"];
    $tens = ["", "ដប់", "ម្ភៃ", "សាមសិប", "សែសិប", "ហាសិប", "ហុកសិប", "ចិតសិប", "ប៉ែតសិប", "កៅសិប"];
    $units = ["", "ពាន់", "លាន", "ពាន់លាន"];

    if ($number == 0) return "សូន្យ រៀល";

    $result = "";
    $i = 0;

    while ($number > 0) {
        $chunk = $number % 1000;
        if ($chunk > 0) {
            $result = chunkKhmer($chunk, $ones, $tens) . " " . $units[$i] . " " . $result;
        }
        $number = intval($number / 1000);
        $i++;
    }

    return trim(preg_replace('/\s+/', ' ', $result)) . " រៀល";
}

function chunkKhmer($number, $ones, $tens) {
    $words = "";

    if ($number >= 100) {
        $words .= $ones[intval($number / 100)] . " រយ ";
        $number %= 100;
    }

    if ($number >= 10) {
        $words .= $tens[intval($number / 10)] . " ";
        $number %= 10;
    }

    if ($number > 0) {
        $words .= $ones[$number] . " ";
    }

    return trim($words);
}

// Handle POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $riel = trim($_POST["riel"]);

    if (!is_numeric($riel)) {
        echo "<script>alert('Only numbers are allowed.'); window.location.href='index.html';</script>";
        exit();
    }

    $riel = intval($riel);
    $usd = number_format($riel / 4000, 2);
    $english = numberToWordsEnglish($riel);
    $khmer = numberToWordsKhmer($riel);

    // Save to file
    $line = "Riel: $riel | English: $english | Khmer: $khmer | USD: $usd $\n";
    file_put_contents("current_projects.txt", $line, FILE_APPEND);

    // Display result and redirect
    echo <<<HTML
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Conversion Result</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>
        <div class="container mt-5">
            <div class="card shadow-sm p-4">
                <h4 class="text-success mb-3">Conversion Result:</h4>
                <ul class='list-group mb-3'>
                    <li class='list-group-item'><strong>a.</strong> English: $english</li>
                    <li class='list-group-item'><strong>b.</strong> Khmer: $khmer</li>
                    <li class='list-group-item'><strong>c.</strong> Dollar: $usd $</li>
                </ul>
            </div>
        </div>
    </body>
    </html>
    HTML;
}
?>
