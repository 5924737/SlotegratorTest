<?php
namespace console\controllers;

use Yii;
use yii\console\Controller;

class SendController extends Controller
{
    public function actionRun()
    {
        // Если нет токена то получаем

        $client = new Client();
        $response = $client->createRequest()
            ->setMethod('POST')
            ->setUrl('http://example.com/api/1.0/users')
            ->setData(['name' => 'John Doe', 'email' => 'johndoe@example.com'])
            ->send();
        if ($response->isOk) {
            $newUserId = $response->data['id'];
        }
        var_dump($response);
    }
}
?>
