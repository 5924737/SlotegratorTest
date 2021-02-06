<?php

/* @var $this yii\web\View */

$this->title = 'SlotegratorTest';
?>
<div class="site-index">
    <div class="row">
    <div class="col-xs-6">
        <table>
            <thead>
                <tr>
                    <td collspan="2"><b>Игровой фонд</b></td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Бонусы</td>
                    <td><?=$bonusInfo['bonus']?></td>
                </tr>
                <tr>
                    <td>Деньги</td>
                    <td><?=$bonusInfo['cash']?></td>
                </tr>
                <tr>
                    <td>Подарки</td>
                    <td><?=$bonusInfo['items']?></td>
                </tr>
            </tbody>
        </table>
        <p><a class="btn btn-lg btn-success" href="/site/play?action=refresh">Start</a></p>
    </div>
    <div class="col-xs-4">
        <table>
            <thead>
            <tr>
                <td collspan="2"><b>Баланс пользователя</b></td>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>Бонусы</td>
                <td><?=$userInfo['bonus']?></td>
            </tr>
            <tr>
                <td>Деньги</td>
                <td><?=$userInfo['cash']?></td>
            </tr>
            <tr>
                <td>Подарки</td>
                <td><?=$userInfo['items']?></td>
            </tr>
            </tbody>
        </table>
        <p><a class="btn btn-lg btn-success" onclick="SendMoney()">Вывести деньги на карту</a></p>
    </div>
</div>
    <div class="jumbotron">
        <?php if($result):?>
            <h1>Вы выиграли приз!!!</h1>
            <h2><?=$result['message']?></h2>
            <?php foreach($result['actions'] as $key=>$value):?>
                <h3><a href="<?=$value?>"><?=$key?></a></h3>
            <?php endforeach;?>
        <?php endif;?>
        <p><a style="margin-top:130px;" class="btn btn-lg btn-success" href="/site/play">Играть</a></p>
    </div>

</div>

<script>
    function SendMoney() {
      $.get( "/site/sendmoney?uid=<?=$uid?>", function( data ) {
        $( ".result" ).html( data );
        alert( data );
      });
    }
</script>

