<?php
    include_once $_SERVER['DOCUMENT_ROOT'].'/model/php/Root.php';
    _VAR::$ROOT->inGame = false;
    include_once 'include/php/header.php';

    $levelPerRow = 4;
    $TabNews = array(
        array(
            'category'      => 'economique',
            'title'         => '',
            'subtitle'      => '',
            'tags'          => '',
            'level'         => 2),
        array(
            'category'      => 'politique',
            'title'         => '',
            'subtitle'      => '',
            'tags'          => '',
            'level'         => 4),
        array(
            'category'      => 'politique',
            'title'         => '',
            'subtitle'      => '',
            'tags'          => '',
            'level'         => 3),
        array(
            'category'      => 'demographique',
            'title'         => '',
            'subtitle'      => '',
            'tags'          => '',
            'level'         => 1),
        array(
            'category'      => 'economique',
            'title'         => '',
            'subtitle'      => '',
            'tags'          => '',
            'level'         => 1)
    );
    function triLevels($NewA, $NewsB){
        return $NewA['level'] == $NewsB['level'] ? 0 : $NewA['level'] > $NewsB['level'] ? 1 : -1;
    }
    function triCategorys($NewA, $NewsB){
        return $NewA['category'] == $NewsB['category'] ? 0 : $NewA['category'] < $NewsB['category'] ? 1 : -1;
    }
    usort($TabNews, triNews);
    usort($TabNews, triCategorys);

    foreach($TabNews as $News){
        
    }
?>

<div class="Row">

</div>

<?
    include_once 'include/php/footer.php';
?>  