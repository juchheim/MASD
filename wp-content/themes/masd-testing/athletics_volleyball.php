
<?php
// Fetch all items from the 'volleyball_schedule' pod with sorting by date
$params = array(
    'limit' => -1, // Retrieve all records
    'orderby' => 'date_of_the_game ASC' // Sort by game date in ascending order
);
$games = pods('volleyball_schedule', $params);

// Initialize counters
$total_games = 0;
$total_wins = 0;
$total_losses = 0;
$total_home_wins = 0;
$total_home_losses = 0;
$total_away_wins = 0;
$total_away_losses = 0;
$played_games = 0; // Track games that have been played and scored

// Iterate through games to calculate statistics
if ($games->total_found() > 0) {
    while ($games->fetch()) {
        $home_game = $games->field('home_game');
        $postponed = $games->field('postponed');

        // Retrieve set scores and calculate sets won
        $our_sets_won = 0;
        $their_sets_won = 0;
        for ($i = 1; $i <= 5; $i++) {
            $our_set_score = $games->field("set{$i}_score_our");
            $their_set_score = $games->field("set{$i}_score_their");

            if (is_numeric($our_set_score) && is_numeric($their_set_score)) {
                if ($our_set_score > $their_set_score) {
                    $our_sets_won++;
                } elseif ($their_set_score > $our_set_score) {
                    $their_sets_won++;
                }
            }
        }

        // Check if the game was not postponed and sets won are valid and not 0-0
        if ($postponed != '1' && ($our_sets_won > 0 || $their_sets_won > 0)) {
            $played_games++;
            $total_games++;
            if ($our_sets_won > $their_sets_won) {
                $total_wins++;
                if ($home_game) {
                    $total_home_wins++;
                } else {
                    $total_away_wins++;
                }
            } elseif ($our_sets_won < $their_sets_won) {
                $total_losses++;
                if ($home_game) {
                    $total_home_losses++;
                } else {
                    $total_away_losses++;
                }
            }
        }
    }
}

// Calculate win percentage based on played games
if ($played_games > 0) {
    $win_percentage = (int)(($total_wins / $played_games) * 100);
} else {
    $win_percentage = 0;
}

// Output the statistics
echo "<div class='headline'>";
echo "<h1 class='statistics-headline'>Season Statistics and Schedule</h1>";
echo "</div>";
echo "<div class='statistics-container-wrapper'>"; // Start the statistics container wrapper
echo "<div class='statistics-container'>"; // Start the statistics container
echo "<div class='statistics-row'>";
echo "<div class='stat-item'>Total<br>Games: <span class='bold-value'>$total_games</span></div>";
echo "<div class='stat-item'>Total<br>Wins: <span class='bold-value'>$total_wins</span></div>";
echo "<div class='stat-item'>Total<br>Losses: <span class='bold-value'>$total_losses</span></div>";
echo "<div class='stat-item'>Win<br>Percentage: <span class='bold-value'>$win_percentage%</span></div>";
echo "<div class='stat-item'>Home<br>Wins: <span class='bold-value'>$total_home_wins</span></div>";
echo "<div class='stat-item'>Home<br>Losses: <span class='bold-value'>$total_home_losses</span></div>";
echo "<div class='stat-item'>Away<br>Wins: <span class='bold-value'>$total_away_wins</span></div>";
echo "<div class='stat-item'>Away<br>Losses: <span class='bold-value'>$total_away_losses</span></div>";
echo "</div>";
echo "</div>"; // End the statistics container
echo "</div>"; // End the statistics container wrapper

// Reset the pointer and iterate again to display the game results
$games->reset();
echo "<div class='scoreboard-container'>"; // Start the container
echo "<table class='rounded-corners'>"; // Start the table
echo "<thead>";
echo "<tr class='table-header'>
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
echo "</thead>";
echo "<tbody>";

// Check if there are any games
if (0 == $games->total_found()) {
    echo '<tr><td colspan="9">No games found.</td></tr>';
} else {
    $row_class = '';
    while ($games->fetch()) {
        // Alternate row color
        $row_class = ($row_class == 'lightgray-row') ? '' : 'lightgray-row';

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
        echo "<tr class='$row_class'>
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

echo "</tbody>";
echo "</table>"; // End the table
echo "</div>"; // End the container
?>
