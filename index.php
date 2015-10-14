<?php
  // handle the form actions here

  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);
  error_reporting(-1);

  session_start();
  if (isGameStarted() == false || isset($_GET['newGame']) && $_GET['newGame'] === "true")
  {
    startGame();
  }
  else if (isset($_GET['guess']))
  {
    $guess = $_GET['guess'][0];
    if (stripos($_SESSION['lettersGuessed'], $guess) !== false)
    {
      $message = "That letter has already been guessed.";
    }
    else if (stripos($_SESSION['wordToGuess'], $guess) == false)
    {
      $_SESSION['guessesRemaining'] = guessesRemaining() - 1;
      $_SESSION['lettersGuessed'] = $_SESSION['lettersGuessed'] . $guess;
      $message = "That guess is incorrect.";
    }
    else
    {
      $_SESSION['lettersGuessed'] = $_SESSION['lettersGuessed'] . $guess;
      for ($index = 0; $index < strlen($_SESSION['wordToGuess']); $index++)
      {
        if ($_SESSION['wordToGuess'][$index] == $guess)
        {
          $_SESSION['wordGuessed'][$index] = $guess;
        }
      }
    }
  }
?>

<html>
  <head>
    <title>Hangman</title>
  </head>
  <body>
    <h1>Hangman!</h1>

    <form action="index.php" method="get">
      <input type="hidden" name="newGame" value="true" />
      <input type="submit" value="New Game" />
    </form>

    <img src=<?php echo '"'.imageForGuessesRemaining(guessesRemaining()).'"'; ?> />

    <?php outputWordToGuess(); ?>
    <br/>
    <p style="color:red"> <?php if (isset($message)) { echo $message; } ?> </p>

    <?php
      if (didWin())
      {
        echo '<p style="color:green">WINNER!!!!! :-D<p>';
      }
      else
      {
        if ($_SESSION['guessesRemaining'] == 0)
        {
        echo '<p style="color:brown">LUSER! >:-(<p>';
        }
      }
    ?>

    <form action="index.php" method="get" <?php if (guessesRemaining() == 0 || didWin()) {echo 'hidden="true"';} ?> >
      <input type="text" name="guess" />
      <input type="submit" value="Guess" />
    </form>
  </body>
</html>

<?php
  function isGameStarted() {
    //return true if a game has been started, else return false
    return isset($_SESSION['wordToGuess']);
  }

  function startGame() {
    //set all the intial values for a new game
    include("words.php");
    $_SESSION['wordToGuess'] = $words[rand(0, (count($words) - 1))];
    $_SESSION['wordGuessed'] = str_repeat("_", strlen($_SESSION['wordToGuess']));
    $_SESSION['lettersGuessed'] = "";
    $_SESSION['guessesRemaining'] = 6;
  }

  function guessesRemaining() {
    //return the number of guesses the user has remaining for the current game
    if (isset($_SESSION['guessesRemaining']))
    {
      return $_SESSION['guessesRemaining'];
    }
    else
    {
      return 0;
    }
  }

  function imageForGuessesRemaining($numRemaining) {
    //return the path to the image file based on the number of guesses remaining
    if ($numRemaining >= 0 && $numRemaining <= 6)
    {
      return "images/hangman$numRemaining.jpeg";
    }
    else
    {
      return "";
    }
  }

  function outputWordToGuess() {
    //return the html code to display the word to guess showing only the characters that the user has guessed correctly
      $outputTable = "<table><tr>";
      // for loop building word
      for ($i = 0; $i < strlen($_SESSION['wordGuessed']); $i++)
      {
        if ($_SESSION['wordGuessed'][$i] == $_SESSION['wordToGuess'][$i])
        {
          $currentChar = $_SESSION['wordGuessed'][$i];
          $outputTable .= "<td>$currentChar</td>";
        }
        else
        {
          $outputTable .= "<td>_</td>";
        }
      }
      $outputTable .= "</tr></table>";
      echo $outputTable;
  }

  function didWin() {
    //return true if a game has been started and the user correctly guessed the word, else return false
    if (isGameStarted() && ($_SESSION['wordGuessed'] === $_SESSION['wordToGuess']))
    {
      return true;
    }
    else
    {
      return false;
    }

  }
 ?>
