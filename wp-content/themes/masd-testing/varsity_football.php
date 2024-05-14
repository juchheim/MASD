<?php
// Fetch all items from the 'football_schedule' pod with sorting by date
$params = array(
    'limit' => -1, // Retrieve all records
    'orderby' => 'date_of_the_game ASC' // Sort by game date in ascending order
);
$games = pods('football_schedule', $params);

// Output the beginning of the scoreboard
echo "<div class='scoreboard-container'>"; // Start the container

echo "<table>"; // Start the table
echo "<tr>
        <th>Date</th>
        <th>Matchup</th>
        <th>Score</th>
        <th>Result</th>
      </tr>";

// Check if there are any games
if (0 == $games->total_found()) {
    echo '<tr><td colspan="4">No games found.</td></tr>';
} else {
    while ($games->fetch()) {
        // Retrieve fields
        $home_game = $games->field('home_game');
        $opposing_team = $games->field('opposing_team');
        $our_score = $games->field('our_score');
        $their_score = $games->field('their_score');
        $postponed = $games->field('postponed');
        $game_date = date('M j, Y', strtotime($games->field('date_of_the_game'))); // Format date

        // Check if the scores are not available or are in array format
        if (!is_numeric($our_score) || !is_numeric($their_score)) {
            $our_score = 0;
            $their_score = 0;
            $score_display = ''; // Clear scores
        } else {
            // Format the score display
            $score_display = "$our_score - $their_score";
        }

        // Determine game result and appropriate color
        if ($postponed == '1') {
            $game_result = "PPD"; // Postponed
            $result_class = "details"; // Use the same style for postponed
        } else {
            if ($our_score > $their_score) {
                $game_result = "Win";
                $result_class = "win";
            } elseif ($our_score == $their_score && $our_score != 0) {
                $game_result = "Tie";
                $result_class = "draw";
            } elseif ($our_score == $their_score && $our_score == 0) {
                $game_result = "TBD";
                $result_class = "details"; // Use the same style for TBD
                $score_display = ''; // Clear scores
            } else {
                $game_result = "Loss";
                $result_class = "loss";
            }
        }

        // Determine matchup format based on home or away game
        $matchup = $home_game ? "vs" : "at";

        // Output the scoreboard row for each game
        echo "<tr>
                <td>$game_date</td>
                <td>Yazoo City High School $matchup $opposing_team</td>
                <td class='$result_class'>$score_display</td>
                <td>$game_result</td>
              </tr>";
    }
}

echo "</table>"; // End the table

echo "</div>"; // End the container
?>
