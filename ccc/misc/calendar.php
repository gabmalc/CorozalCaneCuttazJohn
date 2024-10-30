<!DOCTYPE html>
<html>
<head>
    <title>Example 2</title>
    <style>
        table {
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid black;
            padding: 8px;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h3>Using PHP's DateTime Object</h3>
    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <label for="month">Enter Month (1-12):</label>
        <input type="number" id="month"
               name="month" min="1" 
               max="12" required>
        <label for="year">Enter Year:</label>
        <input type="number" id="year" 
               name="year" min="1900"
               max="2100" required>
        <input type="submit" value="Show Calendar">
    </form>
    <br>
    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $month = $_POST["month"];
        $year = $_POST["year"];
        $dateString = $year . "-" . $month . "-01";
        $firstDay = (new DateTime($dateString))->format("N");
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        echo "<h3>Calendar for " . date("F Y", strtotime($dateString)) . "</h3>";
        echo "<table>";
        echo "<tr><th>Mon</th><th>Tue</th><th>Wed</th>
              <th>Thu</th><th>Fri</th><th>Sat</th>
              <th>Sun</th></tr>";
        $dayCount = 1;
        echo "<tr>";
        for ($i = 1; $i <= 7; $i++) {
            if ($i < $firstDay) {
                echo "<td></td>";
            } else {
                echo "<td>$dayCount</td>";
                $dayCount++;
            }
        }
        echo "</tr>";
        while ($dayCount <= $daysInMonth) {
            echo "<tr>";
            for ($i = 1; $i <= 7 && $dayCount <= $daysInMonth; $i++) {
                echo "<td>$dayCount</td>";
                $dayCount++;
            }
            echo "</tr>";
        }

        echo "</table>";
    }
    ?>
</body>
</html>