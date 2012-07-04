<?php
    include_once '../model/php/Root.php';
    Root::$inGame = false;
    include_once 'include/php/header.php';

    echo 'TEST<pre>';

    $levelPerRow = 4;
    $RowsNews = array();
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
    function parcoursNews(&$TabNews){
        global $levelPerRow, $RowsNews, $countLevel, $numRow;

        foreach($TabNews as $index => $News){
            if($countLevel + $News['level'] <= $levelPerRow){
                $RowsNews[$numRow][] = $News;
                $countLevel += $News['level'];
                unset($TabNews[$index]);
                if($countLevel == $levelPerRow) break;
            }
        }
        if($countLevel < $levelPerRow){
            /*$reste = $levelPerRow - $countLevel;
            foreach($RowsNews[$numRow] as &$News){
                if($News['level'] < )
            }*/
        }
        $countLevel = 0;
        $numRow++;
    }
    usort($TabNews, 'triLevels');
    usort($TabNews, 'triCategorys');

    

    $countLevel = 0;
    $numRow = 0;


    echo '<pre>'.print_r($TabNews, true).'</pre>';
    foreach($TabNews as $index => $News){

        if($countLevel + $News['level'] <= $levelPerRow && isset($TabNews[$index])){
            $RowsNews[$numRow][] = $News;
            $countLevel += $News['level'];
            unset($TabNews[$index]);
            parcoursNews($TabNews);
        }
    }
    echo '<pre>'.print_r($RowsNews, true).'</pre>';
?>

<div class="Row">

</div>

<?
    include_once 'include/php/footer.php';
?>  