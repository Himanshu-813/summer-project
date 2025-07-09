<?php
session_start();

// Handle mode selection
if (isset($_POST['mode'])) {
    $_SESSION['mode'] = $_POST['mode'];
    $_SESSION['board'] = array_fill(0, 9, '');
    $_SESSION['turn'] = 'X';
    $_SESSION['winner'] = '';
}

// Handle reset
if (isset($_POST['reset'])) {
    $_SESSION['board'] = array_fill(0, 9, '');
    $_SESSION['turn'] = 'X';
    $_SESSION['winner'] = '';
    header("Location: index.php");
    exit;
}

// Set default mode if not selected yet
if (!isset($_SESSION['mode'])) {
    $_SESSION['mode'] = '';
}

function checkWinner() {
    $b = $_SESSION['board'];
    $lines = [
        [0,1,2], [3,4,5], [6,7,8],
        [0,3,6], [1,4,7], [2,5,8],
        [0,4,8], [2,4,6]
    ];
    foreach ($lines as [$a, $b1, $c]) {
        if ($b[$a] && $b[$a] === $b[$b1] && $b[$b1] === $b[$c]) {
            $_SESSION['winner'] = $b[$a];
            return;
        }
    }
    if (!in_array('', $b)) {
        $_SESSION['winner'] = 'Draw';
    }
}

// Player move
if (isset($_POST['move']) && $_SESSION['winner'] === '') {
    $i = intval($_POST['move']);
    if ($_SESSION['board'][$i] === '') {
        $_SESSION['board'][$i] = $_SESSION['turn'];
        checkWinner();

        // If Single Player and game not over, let computer play
        if ($_SESSION['mode'] === 'single' && $_SESSION['turn'] === 'X' && $_SESSION['winner'] === '') {
            $_SESSION['turn'] = 'O';
            computerMove();
            checkWinner();
            $_SESSION['turn'] = 'X';
        } elseif ($_SESSION['mode'] === 'double' && $_SESSION['winner'] === '') {
            $_SESSION['turn'] = ($_SESSION['turn'] === 'X') ? 'O' : 'X';
        }
    }
}

// Basic Computer AI: Random empty cell
function computerMove() {
    $emptyCells = [];
    foreach ($_SESSION['board'] as $index => $val) {
        if ($val === '') $emptyCells[] = $index;
    }
    if ($emptyCells) {
        $choice = $emptyCells[array_rand($emptyCells)];
        $_SESSION['board'][$choice] = 'O';
    }
}
?>

<!-- HTML Starts Here -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Tic Tac Toe</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center flex-col px-4 py-6">
  <h1 class="text-4xl font-bold mb-4">Tic Tac Toe</h1>

  <?php if ($_SESSION['mode'] === ''): ?>
    <!-- Mode selection -->
    <form method="post" class="flex flex-col items-center gap-4">
      <button name="mode" value="single" class="px-6 py-3 bg-blue-600 text-white rounded hover:bg-blue-700">
        Single Player (vs Computer)
      </button>
      <button name="mode" value="double" class="px-6 py-3 bg-green-600 text-white rounded hover:bg-green-700">
        Double Player (2 Players)
      </button>
    </form>

  <?php else: ?>
    <!-- Game Status -->
    <div class="text-xl mb-6">
      <?php
        if ($_SESSION['winner']) {
            echo $_SESSION['winner'] === 'Draw'
              ? "<span class='text-yellow-600 font-semibold'>It's a Draw!</span>"
              : "<span class='text-green-600 font-semibold'>Winner: " . $_SESSION['winner'] . "</span>";
        } else {
            echo "<span class='text-blue-700 font-semibold'>Turn: " . $_SESSION['turn'] . "</span>";
        }
      ?>
    </div>

    <!-- Game Board -->
    <form method="post">
      <div class="grid grid-cols-3 gap-2">
        <?php foreach ($_SESSION['board'] as $i => $cell): ?>
          <button
            class="w-24 h-24 bg-white text-3xl font-bold border border-gray-400 flex items-center justify-center hover:bg-gray-100 disabled:opacity-60"
            name="move"
            value="<?= $i ?>"
            <?= $cell || $_SESSION['winner'] ? 'disabled' : '' ?>
          ><?= $cell ?></button>
        <?php endforeach; ?>
      </div>

      <!-- Buttons -->
      <div class="mt-6 flex gap-4 justify-center">
        <button name="reset" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
          Restart
        </button>
        <button name="mode" value="" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
          Change Mode
        </button>
      </div>
    </form>
  <?php endif; ?>
</body>
</html>
