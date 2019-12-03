<?php

namespace App\Services\LineBot\ActionHandler\Help;

use App\Services\LineBot\ActionHandler\LineBotActionHandler;
use App\Services\LineBot\Router\LineBotRouter;

class LineBotCommandHelper extends LineBotActionHandler
{

    /**
     * LineBotCommandHelper constructor.
     */
    public function __construct()
    {
    }

    public function handle()
    {
        $delimiter = str_replace('|', ' 或 ', LineBotRouter::DELIMITER_USE);

        $message = <<<EOD
#help 幫助文件
## 分段符 ： {$delimiter} 
    ex : 提醒;今天早上九點;吃早餐 
    ex : 提醒 x 今天早上九點 x 吃早餐 
    ex : 提醒，今天早上九點，吃早餐 
## 指令 : 
  ### 提醒指令： 再指定的時間跳出訊息提示
      關鍵字 => [提醒、reminder、rem]
      新增提醒 
        - 提醒;今天早上九點;吃早餐 
        - reminder;今天早上9:00;吃早餐 
        - rem;後天早上九點半;吃早餐 
        - rem;2019-07-02 09:00;吃早餐 
      查詢所有提醒 
        - rem;all 
      刪除某一筆提醒 
        - rem;del;{提醒Id} 
  ### 學習關鍵字回應指令： 輸入 apple 機器人回應你蘋果
      關鍵字 => [學、learn]
        - 學;apple;蘋果 
        - learn;apple;蘋果 
EOD;

        return $this->reply($message);
    }
}
