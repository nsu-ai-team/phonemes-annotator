<?php error_reporting(E_ALL & ~E_NOTICE); ?>
<?php if (empty($_GET['name'])) {header('Location: /');} ?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Assessors</title>
</head>
<body>

<?php
$conn = new mysqli('localhost', 'username', 'password', 'dbname');


// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!empty($_GET['trans']) && !empty($_GET['wav_id']) && !empty($_GET['dir_id'])){
    // saving
    $sql = "INSERT INTO trans (wav_id, transcription, assessor_name, dir_name) 
    VALUES ('".addslashes($_GET['wav_id'])."', '".addslashes($_GET['trans'])."', '".addslashes($_GET['name'])."', '".addslashes($_GET['dir_id'])."')";

    if ($conn->query($sql) === TRUE) {
//        echo "New record created successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

}

$sql_count = "SELECT dir_name, wav_id, COUNT(*) FROM `trans` GROUP BY dir_name, wav_id HAVING COUNT(*) > 2";
$result_count = $conn->query($sql_count);
$already_c = array();
if ($result_count->num_rows > 0) {
    while($row = $result_count->fetch_assoc()) {
        $already_c[] = $row["dir_name"].'__'.$row["wav_id"];
    }
}


$sql = "SELECT dir_name, wav_id FROM trans WHERE assessor_name = '".addslashes($_GET['name'])."' ";
$result = $conn->query($sql);

$already_seen = array();
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $already_seen[] = $row["dir_name"].'__'.$row["wav_id"];
    }
}
$conn->close();
$all_dirs = scandir('data');
$wav_id = '';
foreach ($all_dirs as $cur_dir) {
    if ($cur_dir == '.' || $cur_dir == '..') {
        continue;
    }


    $all_files = scandir('data/'.$cur_dir.'/wav');
    //$not_seen = array();
    //$wav_id = 'the_end';
    //print_r($already_seen);
    $wav_id = '';
    foreach ($all_files as $el) {
        if ($el == '.' || $el == '..') {
            continue;
        }
        # print_r($cur_dir.' !! '.$el.'</br>');

        if (strpos($el, '.txt') || strpos($el, '.phn')) {
            continue;
        }
        //    echo str_replace('.wav', '', $el);
        if (in_array($cur_dir.'__'.str_replace('.wav', '', $el), $already_seen)) {
            continue;
        }
        if (in_array($cur_dir.'__'.str_replace('.wav', '', $el), $already_c)) {
            continue;
        }
        //    $not_seen[] = str_replace('.wav', '', $el);
        $wav_id = str_replace('.wav', '', $el);
    }

    # next folder search
    if (!$wav_id){
        continue;
    }
    //$wav_id = 'ru_0022';
    $wav_text = file_get_contents('data/'.$cur_dir.'/wav/' . $wav_id . '.txt');
    $wav_tr = file_get_contents('data/'.$cur_dir.'/wav/' . $wav_id . '.phn');
    break;
}
if (!$wav_id) {
    exit('FINISHED');
}
//var_dump($already_c);
//var_dump($cur_dir.'__'.str_replace('.wav', '', $el));
//var_dump(in_array($cur_dir.'__'.str_replace('.wav', '', $el), $already_c));
//RCPUhHTEwXLdmlbQ
?>
<!--<p>Please correct transcription for audio and given text</p>-->

<form action="wav.php">
    <audio controls>
        <source src="data/<?php echo $cur_dir; ?>/wav/<?php echo $wav_id; ?>.wav" type="audio/wav">
        Your browser does not support the audio element.
    </audio>

    <input name="name" type="hidden" value="<?php echo $_GET['name']; ?>" />
    <input name="wav_id" value="<?php echo $wav_id ?>" type="hidden">
    <input name="dir_id" value="<?php echo $cur_dir ?>" type="hidden">
    <p style="margin: 5px; width: 100%;font-size: x-large"><?php echo $wav_text?></p>
    <br />
    <input type="text" value="<?php echo $wav_tr ?>" style="width: 100%;" name="trans" />
    <br />
    <br />
    <input type="submit" value="Continue">
</form>

</body>
</html>