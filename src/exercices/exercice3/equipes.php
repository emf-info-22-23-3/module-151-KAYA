<!doctype html>
<html>
  <header>
    <link rel="stylesheet" type="text/css" href="stylesheets/main.css" />
</header>
  <body>
    <div id="conteneur">
      <h1>Les équipes de National League</h1>    
      <table border= "1">
      <tr>
        <td>ID</td>
        <td>Club</td>
      </tr>
      <?php
        require('ctrl.php');
        // A compléter....
        $equipes = getEquipes();
         
        foreach ($equipes as $index => $equipe) {
          echo "<tr>";
          echo "<td>" . ($index + 1) . "</td>";
          echo "<td>" . htmlspecialchars($equipe) . "</td>";
          echo "</tr>";
        }
      ?>
      </table>
    </div>
  </body>
</html>