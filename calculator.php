<?php
// English number to words (using basic logic)
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

// Khmer number to words (basic representation)
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Calculator</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

</head>
<body>
    <div class="container mt-5">
        <div class="card shadow-sm p-4">
            <form method="post" onsubmit="return validateInput();">
                <div class="mb-3">
                    <label for="riel" class="form-label">Please input your data: </label>
                    <input type="text" class="form-control" name="riel" id="riel" placeholder="Enter Riel amount">
                    <div id="error" class="form-text text-danger mt-1"></div>
                </div>
                <button type="submit" class="btn btn-success">Convert</button>
            </form>
        </div>
    </div>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $riel = trim($_POST["riel"]);

    echo '<div class="container mt-4">';
    echo '<div class="card p-4 shadow-sm">';

    if (!is_numeric($riel)) {
        echo '<div class="alert alert-danger" role="alert">';
        echo 'Only numbers are allowed. Please try again.';
        echo '</div>';
    } else {
        $riel = intval($riel);
        $usd = number_format($riel / 4000, 2);

        $english = numberToWordsEnglish($riel);
        $khmer = numberToWordsKhmer($riel);

        echo '<h4 class="text-success mb-3">Conversion Result:</h4>';
        echo "<ul class='list-group'>";
        echo "<li class='list-group-item'><strong>a.</strong> English: $english</li>";
        echo "<li class='list-group-item'><strong>b.</strong> Khmer: $khmer</li>";
        echo "<li class='list-group-item'><strong>c.</strong> Dollar: $usd $</li>";
        echo "</ul>";

        // Save to file
        $line = "Riel: $riel | English: $english | Khmer: $khmer | USD: $usd $\n";
        file_put_contents("current_projects.txt", $line, FILE_APPEND);
    }

    echo '</div>';
    echo '</div>';
}
?>

</body>
</html>
