<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Assessors</title>
</head>
<body>

<form action="wav.php">
    <input name="name" type="text" placeholder="Your name" />
    <input type="submit" value="Continue">
</form>

<br/>
<br/>
<h2>Впервые здесь? Тогда обязательно предварительно <a href="readme/instruction.html" target="_blank">прочитайте инструкцию</a></h2>
<!--<h2>Инструкция</h2>-->
<!--<ol>-->
<!--    <li>Имя вводите пожалуйста латинскими буквами</li>-->
<!--    <li>Имя используйте всегда одно, иначе вам будут показаны уже просмотренные аудиозаписи</li>-->
<!--    <li>Полная инструкция здесь <a href="https://vk.com/doc52036357_467213578?hash=74877c42a4a64b1d9e&dl=2a78b465580a022fdb"-->
<!--                                   target="_blank">(Документ из ВК)</a></li>-->
<!--    <li>Обозначения фонем <a href="https://github.com/nsu-ai/russian_g2p/blob/master/phoneme_description.pdf" target="_blank">PDF</a> </li>-->
<!--</ol>-->


<?php
//$conn = new mysqli('localhost', 'username', 'password', 'dbname');


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

echo '<br /><b>Размечено с 3ёх кратным пересечением: '.$result_count->num_rows.'</b>';

?>

</body>
</html>