<?php
// Fetch all items from the 'volleyball_schedule' pod with sorting by date
$params = array(
    'limit' => -1, // Retrieve all records
    'orderby' => 'date_of_the_game ASC' // Sort by game date in ascending order
);
$games = pods('volleyball_schedule', $params);

// Output the beginning of the scoreboard
echo "<div class='scoreboard-container'>"; // Start the container

echo "<table class='table_shadow'>"; // Start the table
echo "<tr>
        <th>Date</th>
        <th>Matchup</th>
        <th>Set 1</th>
        <th>Set 2</th>
        <th>Set 3</th>
        <th>Set 4</th>
        <th>Set 5</th>
        <th>Final Score</th>
        <th>Result</th>
      </tr>";

// Check if there are any games
if (0 == $games->total_found()) {
    echo '<tr><td colspan="9">No games found.</td></tr>';
} else {
    while ($games->fetch()) {
        // Retrieve fields
        $home_game = $games->field('home_game');
        $opposing_team = $games->field('opposing_team');
        $postponed = $games->field('postponed');
        $game_date = date('M j, Y', strtotime($games->field('date_of_the_game'))); // Format date

        // Retrieve set scores and set to 0 if not provided
        $set_scores = [];
        $our_sets_won = 0;
        $their_sets_won = 0;
        for ($i = 1; $i <= 5; $i++) {
            $our_set_score = $games->field("set{$i}_score_our");
            $their_set_score = $games->field("set{$i}_score_their");

            // Set scores to 0 if they are not numeric
            $our_set_score = is_numeric($our_set_score) ? $our_set_score : 0;
            $their_set_score = is_numeric($their_set_score) ? $their_set_score : 0;

            // Determine the winner of the set
            if ($our_set_score > $their_set_score) {
                $our_sets_won++;
                $set_scores[] = "<strong>$our_set_score</strong> - $their_set_score";
            } elseif ($their_set_score > $our_set_score) {
                $their_sets_won++;
                $set_scores[] = "$our_set_score - <strong>$their_set_score</strong>";
            } else {
                $set_scores[] = "$our_set_score - $their_set_score";
            }
        }

        // Determine game result and appropriate color
        if ($postponed == '1') {
            $game_result = "PPD"; // Postponed
            $result_class = "details"; // Use the same style for postponed
        } else {
            if ($our_sets_won > $their_sets_won) {
                $game_result = "Win";
                $result_class = "win";
            } elseif ($our_sets_won == $their_sets_won && $our_sets_won != 0) {
                $game_result = "Tie";
                $result_class = "draw";
            } elseif ($our_sets_won == $their_sets_won && $our_sets_won == 0) {
                $game_result = "TBD";
                $result_class = "details"; // Use the same style for TBD
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
                <td>{$set_scores[0]}</td>
                <td>{$set_scores[1]}</td>
                <td>{$set_scores[2]}</td>
                <td>{$set_scores[3]}</td>
                <td>{$set_scores[4]}</td>
                <td><strong>$our_sets_won - $their_sets_won</strong></td>
                <td class='$result_class'>$game_result</td>
              </tr>";
    }
}

echo "</table>"; // End the table

echo "</div>"; // End the container
?>
