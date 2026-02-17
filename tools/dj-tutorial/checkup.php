<?php

require_once "../crud/webauto.php";

?>
<h1>Checkup Assignment</h1>
<p>
    This assignment is to run the checkup script as described below in your PythonAnywhere bash console. 
     This script looks for common errors in your PythonAnywhere environment and tells you to fix them.
     Here are the instructions for running the checkup script:
     </p>
        <p>
     <a href="../../assn/dj4e_checkup52.md" target="_blank">https://www.dj4e.com/assn/dj4e_checkup52.md</a>
     </p>
     <p>
        When the checkup script is run, it will output a string that you need to copy and paste into the form below.
        </p>
        <p>
        The format of the checkup string is:
        <code>
        Checkup complete &lt;path&gt; &lt;signature&gt;
        </code>
        </p>
<?php

if ( ! isset($_POST['checkup']) || trim($_POST['checkup']) === '' ) {
    ?>
    <form method="post">
    <p>
    <label for="checkup">Checkup string: </label>
    <input type="text" id="checkup" name="checkup" size="60" placeholder="Checkup complete /home/account_name xxxxxx"/>
    </p>
    <input type="submit" class="btn btn-primary" value="Submit">
    </form>
    <?php
    return;
}

$input = trim($_POST['checkup']);
$parts = preg_split('/\s+/', $input, 4);

if ( count($parts) < 4 ) {
    error_out("Invalid format. Expected: Checkup complete &lt;path&gt; &lt;hash&gt; (four space-separated values)");
    line_out("You entered: ".htmlentities($input));
    ?>
    <p><a href="javascript:history.back()" class="btn btn-default">Try again</a></p>
    <?php
    return;
}

$word1 = $parts[0];
$word2 = $parts[1];
$param3 = $parts[2];
$value4 = $parts[3];

if ( $word1 !== 'Checkup' || $word2 !== 'complete' ) {
    error_out("First two words must be 'Checkup complete'");
    line_out("You entered: ".htmlentities($input));
    ?>
    <p><a href="javascript:history.back()" class="btn btn-default">Try again</a></p>
    <?php
    return;
}

if ( strpos($param3, '/home/') !== 0 ) {
    error_out("The third parameter (path) must start with /home/");
    line_out("You entered: ".htmlentities($param3));
    ?>
    <p><a href="javascript:history.back()" class="btn btn-default">Try again</a></p>
    <?php
    return;
}

$expected_hash = substr(md5($param3), 0, 6);
if ( $value4 !== $expected_hash ) {
    error_out("The fourth value must be the first six characters of the MD5 hash of the third parameter.");
    line_out("For path '".htmlentities($param3)."', expected hash: ".$expected_hash);
    line_out("You provided: ".htmlentities($value4));
    ?>
    <p><a href="javascript:history.back()" class="btn btn-default">Try again</a></p>
    <?php
    return;
}

success_out("Checkup string validated correctly");
webauto_test_passed(1.0, "checkup:".$param3);
