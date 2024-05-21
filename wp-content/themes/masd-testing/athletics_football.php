<?php
// Fetch all items from the 'football_schedule' pod with sorting by date
$params = array(
    'limit' => -1, // Retrieve all records
    'orderby' => 'date_of_the_game ASC' // Sort by game date in ascending order
);
$games = pods('football_schedule', $params);

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
        $our_score = $games->field('our_score');
        $their_score = $games->field('their_score');
        $postponed = $games->field('postponed');

        // Check if the game was not postponed and scores are valid and not 0-0
        if ($postponed != '1' && is_numeric($our_score) && is_numeric($their_score) && !($our_score == 0 && $their_score == 0)) {
            $played_games++;
            $total_games++;
            if ($our_score > $their_score) {
                $total_wins++;
                if ($home_game) {
                    $total_home_wins++;
                } else {
                    $total_away_wins++;
                }
            } elseif ($our_score < $their_score) {
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
    $win_percentage = ($total_wins / $played_games) * 100;
    $win_percentage = intval($win_percentage); // Ensure no decimals
} else {
    $win_percentage = 0;
}

// Debugging: Check the values
echo "<!-- Debugging: total_wins=$total_wins, played_games=$played_games, win_percentage=$win_percentage -->";

// Output the statistics
echo "<div class='headline'>";
echo "<h1 class='statistics-headline'>Season Statistics and Schedule</h1>";
echo "</div>";
echo "<div class='statistics-container-wrapper'>"; // Start the statistics container wrapper
echo "<div class='statistics-container'>"; // Start the statistics container
echo "<div class='statistics-row'>";
echo "<div class='stat-item'><span>Total<br>Games:</span> $total_games</div>";
echo "<div class='stat-item'><span>Total<br>Wins:</span> $total_wins</div>";
echo "<div class='stat-item'><span>Total<br>Losses:</span> $total_losses</div>";
echo "<div class='stat-item'><span>Win<br>Percentage:</span> $win_percentage%</div>";
echo "<div class='stat-item'><span>Home<br>Wins:</span> $total_home_wins</div>";
echo "<div class='stat-item'><span>Home<br>Losses:</span> $total_home_losses</div>";
echo "<div class='stat-item'><span>Away<br>Wins:</span> $total_away_wins</div>";
echo "<div class='stat-item'><span>Away<br>Losses:</span> $total_away_losses</div>";
echo "</div>";
echo "</div>"; // End the statistics container
echo "</div>"; // End the statistics container wrapper

// Reset the pointer and iterate again to display the game results
$games->reset();
echo "<div class='scoreboard-container'>"; // Start the container
echo "<table class='rounded-corners'>"; // Start the table
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